<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('client.client_auth.password_reset_title') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif; line-height: 1.6; color: #1f2937; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; font-weight: 600; margin-top: 15px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; display: inline-block; object-fit: cover; }
        .avatar-initials { width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 24px; font-weight: bold; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; }
        .section { margin-bottom: 25px; }
        .section-title { font-weight: 600; color: #4f46e5; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .info-box { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .info-row { margin-bottom: 10px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-weight: 600; color: #1f2937; }
        .info-value { color: #6b7280; }
        .message-box { background: #ffffff; border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; }
        .message-text { color: #4b5563; line-height: 1.6; }
        .action-button { display: inline-block; background: #4f46e5; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 30px; border-radius: 0 0 8px 8px; color: #6b7280; font-size: 13px; text-align: center; border: 1px solid #e5e7eb; border-top: none; }
        .alert-box { background: #fef3c7; border: 1px solid #fcd34d; padding: 15px; border-radius: 8px; margin: 20px 0; color: #92400e; }
        .alert-box strong { color: #78350f; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="Avatar" class="avatar">
            @elseif($avatarInitials)
                <div class="avatar-initials">{{ $avatarInitials }}</div>
            @endif
            <h2>{{ __('client.client_auth.password_reset_title') }}</h2>
        </div>

        <div class="content">
            <!-- Message -->
            <div class="section">
                <p>{{ __('client.client_auth.hello') }}, <strong>{{ $name }}</strong></p>
                <div class="message-box">
                    <p class="message-text">{{ __('client.client_auth.password_reset_message') }}</p>
                </div>
            </div>

            <!-- Time Limit -->
            <div class="alert-box">
                <strong>⏱️ {{ __('client.client_auth.password_reset_expires') }}</strong><br>
                {{ $expiresAt }}
            </div>

            <!-- Action -->
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="action-button">
                    {{ __('client.client_auth.password_reset_button') }}
                </a>
            </div>

            <!-- Copy Link -->
            <div class="section" style="margin-top: 30px; text-align: center; color: #6b7280; font-size: 13px;">
                <p>{{ __('client.client_auth.or_copy_link') }}</p>
                <p style="word-break: break-all; color: #4f46e5;">{{ $resetUrl }}</p>
            </div>
        </div>

        <div class="footer">
            <p style="margin-bottom: 16px; font-size: 12px;">
                {{ __('client.client_auth.thank_you') }},<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
            <div style="border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 12px; color: #9ca3af;">
                <p style="margin: 8px 0;">
                    <a href="{{ config('app.url') }}" style="color: #6b7280; text-decoration: none;">{{ config('app.url') }}</a>
                </p>
                <p style="margin: 8px 0;">
                    © {{ date('Y') }} {{ config('app.name') }}. {{ __('app.all_rights_reserved') ?? 'All rights reserved.' }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
