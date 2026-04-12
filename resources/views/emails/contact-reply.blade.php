<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.reply_title') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; }
        .greeting { font-size: 16px; line-height: 1.6; margin-bottom: 20px; }
        .reply-section { background: #f0fdf4; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #10b981; }
        .reply-title { font-weight: 600; color: #10b981; margin-top: 0; margin-bottom: 12px; }
        .reply-text { white-space: pre-wrap; color: #4b5563; line-height: 1.6; margin: 0; }
        .original-section { background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #6b7280; }
        .original-title { font-weight: 600; color: #1f2937; margin-top: 0; margin-bottom: 12px; }
        .original-text { font-size: 14px; color: #6b7280; white-space: pre-wrap; margin: 0; line-height: 1.5; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 30px 0; }
        .footer { background: #f3f4f6; padding: 20px 30px; border-radius: 0 0 8px 8px; color: #6b7280; font-size: 13px; text-align: center; border: 1px solid #e5e7eb; border-top: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ __('contact.reply_title') }}</h2>
        </div>

        <div class="content">
            <!-- Greeting -->
            <p class="greeting">
                {{ __('contact.reply_greeting') }} {{ $name }},
            </p>

            <!-- Reply Message -->
            <div class="reply-section">
                <h3 class="reply-title">{{ __('contact.reply_label') }}</h3>
                <p class="reply-text">{{ $replyMessage }}</p>
            </div>

            <hr class="divider">

            <!-- Original Message -->
            <div class="original-section">
                <h3 class="original-title">{{ __('contact.original_message_label') }}</h3>
                <p class="original-text">{{ $originalMessage }}</p>
            </div>

            <hr class="divider">
        </div>

        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
