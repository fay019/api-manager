<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.email_title') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: 600; color: #4f46e5; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .info-box { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .info-row { margin-bottom: 10px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-weight: 600; color: #1f2937; }
        .info-value { color: #6b7280; }
        .message-box { background: #ffffff; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; }
        .message-text { white-space: pre-wrap; color: #4b5563; line-height: 1.6; }
        .action-button { display: inline-block; background: #4f46e5; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 30px; border-radius: 0 0 8px 8px; color: #6b7280; font-size: 13px; text-align: center; border: 1px solid #e5e7eb; border-top: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ __('contact.email_new_message') }}</h2>
        </div>

        <div class="content">
            <!-- Sender Information -->
            <div class="section">
                <div class="section-title">{{ __('contact.from') }}</div>
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.name') }}:</span>
                        <span class="info-value">{{ $contactMessage->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.email') }}:</span>
                        <span class="info-value"><a href="mailto:{{ $contactMessage->email }}" style="color: #4f46e5; text-decoration: none;">{{ $contactMessage->email }}</a></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.subject') }}:</span>
                        <span class="info-value">{{ $contactMessage->subject }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.received') }}:</span>
                        <span class="info-value">{{ $contactMessage->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.ip_address') }}:</span>
                        <span class="info-value">{{ $contactMessage->ip_address }}</span>
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div class="section">
                <div class="section-title">{{ __('contact.message') }}</div>
                <div class="message-box">
                    <p class="message-text">{{ $contactMessage->message }}</p>
                </div>
            </div>

            <!-- Action -->
            <div style="text-align: center;">
                <a href="{{ url('/admin/contact-messages/' . $contactMessage->id . '/edit') }}" class="action-button">
                    {{ __('contact.view_in_admin') }}
                </a>
            </div>
        </div>

        <div class="footer">
            {{ __('contact.email_signature') }}
        </div>
    </div>
</body>
</html>
