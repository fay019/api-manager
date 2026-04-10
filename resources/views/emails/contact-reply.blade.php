<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Re: {{ __('contact.email_title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #667eea;">{{ __('contact.reply_title') }}</h2>

        <p style="font-size: 1rem; line-height: 1.6;">
            {{ __('contact.reply_greeting') }} {{ $name }},
        </p>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #667eea;">{{ __('contact.reply_label') }}</h3>
            <p style="line-height: 1.6; white-space: pre-wrap; color: #333;">{{ $replyMessage }}</p>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

        <div style="background: #f3f4f6; padding: 15px; border-left: 4px solid #667eea; border-radius: 4px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0; font-weight: 600; color: #667eea;">{{ __('contact.original_message_label') }}</p>
            <p style="margin: 0; font-size: 0.9rem; color: #6b7280; white-space: pre-wrap;">{{ $originalMessage }}</p>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

        <p style="font-size: 0.85rem; color: #6b7280; text-align: center;">
            © {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
