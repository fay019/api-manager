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
        return view('contact.index');
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

        // Validate the actual form data
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\p{L}\s\-\']+$/u'],
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'subject' => ['required', 'string', 'min:5', 'max:100'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        // Sanitize inputs
        $validated['name'] = strip_tags($validated['name']);
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['subject'] = strip_tags($validated['subject']);
        $validated['message'] = strip_tags($validated['message']);

        // Store the message
        $message = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'language' => $request->input('language', 'en'),
            'honeypot_triggered' => false,
            'timestamp_check_valid' => true,
        ]);

        // Send email to admin
        try {
            $contactEmail = Setting::get('contact_email', 'admin@moussouni.dev');

            // Check daily email limit
            $maxPerDay = (int) config('mail.max_per_day', 50);
            $emailsSentToday = \DB::table('contact_messages')
                ->whereDate('created_at', now())
                ->count();

            if ($emailsSentToday < $maxPerDay) {
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
