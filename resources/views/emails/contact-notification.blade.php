<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('contact.email_title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #667eea;">{{ __('contact.email_new_message') }}</h2>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>{{ __('contact.from') }}:</strong> {{ $contactMessage->name }}</p>
            <p><strong>{{ __('contact.email') }}:</strong> <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a></p>
            <p><strong>{{ __('contact.subject') }}:</strong> {{ $contactMessage->subject }}</p>
            <p><strong>{{ __('contact.received') }}:</strong> {{ $contactMessage->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>{{ __('contact.ip_address') }}:</strong> {{ $contactMessage->ip_address }}</p>
        </div>

        <div style="background: #fff; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>{{ __('contact.message') }}:</h3>
            <p style="line-height: 1.6; white-space: pre-wrap;">{{ $contactMessage->message }}</p>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ url('/admin/contact-messages/' . $contactMessage->id . '/edit') }}" style="background: #667eea; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: bold;">
                {{ __('contact.view_in_admin') }}
            </a>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

        <p style="font-size: 0.85rem; color: #6b7280;">
            {{ __('contact.email_signature') }}
        </p>
    </div>
</body>
</html>
