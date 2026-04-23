<?php

namespace App\Http\Controllers;

use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function show()
    {
        $client = auth('client')->user();

        return view('contact.index', compact('client'));
    }

    public function store(Request $request)
    {
        // Rate limiting: max messages per IP per day (configurable)
        $key = 'contact_'.$request->ip();
        $maxAttempts = (int) config('mail.max_per_day', 50);
        $decayMinutes = 1440; // 24 hours

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return back()
                ->withErrors(['error' => __('contact.rate_limit_exceeded')])
                ->withInput();
        }

        RateLimiter::hit($key, $decayMinutes);

        // Check honeypot (if filled = bot)
        $honeypotTriggered = ! empty($request->input('website'));

        // Check timestamp (form must take at least 2 seconds to fill)
        $formTimestamp = $request->input('form_timestamp');
        $timestampValid = true;
        if ($formTimestamp) {
            $timeDiff = time() - intval($formTimestamp);
            $timestampValid = $timeDiff >= 2;
        }

        // If honeypot or timestamp check fails, store as spam silently
        if ($honeypotTriggered || ! $timestampValid) {
            ContactMessage::create([
                'name' => $request->input('name', 'Unknown'),
                'email' => $request->input('email', 'unknown@example.com'),
                'subject' => $request->input('subject', '(spam)'),
                'message' => $request->input('message', '(spam detected)'),
                'status' => 'spam',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'language' => $request->input('language', 'en'),
                'honeypot_triggered' => $honeypotTriggered,
                'timestamp_check_valid' => $timestampValid,
            ]);

            // Return success anyway (don't reveal spam detection to attacker)
            return back()
                ->with('success', __('contact.message_sent'));
        }

        // Check if client is authenticated
        $client = auth('client')->user();

        // Validate the actual form data
        $rules = [
            'subject' => ['required', 'string', 'min:5', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];

        // Determine type
        $type = $request->input('type') ?? ($client?->type ?? 'person');

        // If not authenticated, require name, email and type
        if (!$client) {
            $rules['type'] = ['required', 'in:person,company'];
            $rules['email'] = ['required', 'email:rfc,dns', 'max:100'];

            if ($type === 'company') {
                $rules['company_name'] = ['required', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\-\'0-9]+$/u'];
            } else {
                $rules['name'] = ['required', 'string', 'min:3', 'max:50', 'regex:/^[\p{L}\s\-\']+$/u'];
            }
        } else {
            // For authenticated clients, make these optional/override-able
            if ($client->type === 'company') {
                $rules['company_name'] = ['nullable', 'string', 'min:3', 'max:100', 'regex:/^[\p{L}\s\-\'0-9]+$/u'];
            } else {
                $rules['name'] = ['nullable', 'string', 'min:3', 'max:50', 'regex:/^[\p{L}\s\-\']+$/u'];
            }
            $rules['email'] = ['nullable', 'email:rfc,dns', 'max:100'];
        }

        // Company-specific fields
        if ($type === 'company') {
            $rules['contact_name'] = ['nullable', 'string', 'min:3', 'max:100'];
            // contact_email is required only if not using same email as main
            $useSameEmail = $request->input('use_same_email') === 'on' || $request->boolean('use_same_email');
            $rules['contact_email'] = $useSameEmail
                ? ['nullable', 'email:rfc,dns', 'max:100']
                : ['required', 'email:rfc,dns', 'max:100'];
        }

        $validated = $request->validate($rules);

        // Prepare data for storage
        $messageData = [
            'subject' => strip_tags($validated['subject']),
            'message' => strip_tags($validated['message']),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'language' => $request->input('language', 'en'),
            'honeypot_triggered' => false,
            'timestamp_check_valid' => true,
        ];

        // Handle authenticated client
        if ($client) {
            $messageData['client_id'] = $client->id;
            $messageData['type'] = $client->type;
            $messageData['email'] = $client->email;

            if ($client->type === 'company') {
                $messageData['name'] = $validated['company_name'] ?? $client->company_name;
                $messageData['contact_name'] = $validated['contact_name'] ?? $client->contact_name;
                // Use provided contact_email, fallback to client's contact_email, or use main email if checkbox was checked
                $useSameEmail = $request->input('use_same_email') === 'on' || $request->boolean('use_same_email');
                $messageData['contact_email'] = $validated['contact_email'] ??
                    ($useSameEmail ? $client->email : $client->contact_email);
                $messageData['billing_email'] = $client->billing_email;
                $messageData['phone'] = $client->phone;
            } else {
                $messageData['name'] = $validated['name'] ?? trim($client->first_name.' '.$client->last_name);
            }
        } else {
            // Guest user
            $messageData['type'] = $validated['type'];
            $messageData['name'] = strip_tags($type === 'company' ? $validated['company_name'] : $validated['name']);
            $messageData['email'] = strtolower(trim($validated['email']));

            if ($type === 'company') {
                $messageData['contact_name'] = $validated['contact_name'] ?? null;
                $messageData['contact_email'] = $validated['contact_email'] ?? null;
            }
        }

        // Store the message
        $message = ContactMessage::create($messageData);

        // Send email to admin
        try {
            $contactEmail = Setting::get('contact_email', 'admin@moussouni.dev');

            // Check daily email limit
            $maxPerDay = (int) config('mail.max_per_day', 50);
            $emailsSentToday = \DB::table('contact_messages')
                ->whereDate('created_at', now())
                ->count();

            if ($emailsSentToday < $maxPerDay) {
                // Reload message with client relation for email
                $message = $message->load('client');
                Mail::to($contactEmail)->send(new ContactNotification($message));
                \Log::info("Contact email sent to {$contactEmail}. Total today: {$emailsSentToday} / {$maxPerDay}");
            } else {
                \Log::warning("Daily email limit reached ({$maxPerDay} emails). Message stored but not sent.");
            }
        } catch (\Exception $e) {
            // Log error but don't expose to user
            \Log::error('Contact email failed: '.$e->getMessage());
        }

        return back()
            ->with('success', __('contact.message_sent'));
    }
}
