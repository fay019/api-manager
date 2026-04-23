<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('contact.email_title') }}</title>
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
        .message-text { white-space: pre-wrap; color: #4b5563; line-height: 1.6; }
        .action-button { display: inline-block; background: #4f46e5; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 20px; }
        .footer { background: #f3f4f6; padding: 20px 30px; border-radius: 0 0 8px 8px; color: #6b7280; font-size: 13px; text-align: center; border: 1px solid #e5e7eb; border-top: none; }
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
            <h2>{{ __('contact.email_new_message') }}</h2>
        </div>

        <div class="content">
            <!-- Sender Information -->
            <div class="section">
                <div class="section-title">{{ __('contact.from') }}</div>
                <div class="info-box">
                    @if($contactMessage->client_id)
                        <div class="info-row">
                            <span class="info-label">🔐 {{ __('contact.authenticated_user') }}:</span>
                            <span class="info-value">Client #{{ $contactMessage->client_id }}</span>
                        </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">
                            @if($contactMessage->type === 'company')
                                {{ __('contact.company_name') }}
                            @else
                                {{ __('contact.name') }}
                            @endif
                            :
                        </span>
                        <span class="info-value">{{ $contactMessage->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('contact.email') }}:</span>
                        <span class="info-value"><a href="mailto:{{ $contactMessage->email }}" style="color: #4f46e5; text-decoration: none;">{{ $contactMessage->email }}</a></span>
                    </div>
                    @if($contactMessage->contact_name)
                        <div class="info-row">
                            <span class="info-label">{{ __('contact.contact_person') }}:</span>
                            <span class="info-value">{{ $contactMessage->contact_name }}</span>
                        </div>
                    @endif
                    @if($contactMessage->contact_email)
                        <div class="info-row">
                            <span class="info-label">{{ __('contact.contact_email') }}:</span>
                            <span class="info-value"><a href="mailto:{{ $contactMessage->contact_email }}" style="color: #4f46e5; text-decoration: none;">{{ $contactMessage->contact_email }}</a></span>
                        </div>
                    @endif
                    @if($contactMessage->billing_email)
                        <div class="info-row">
                            <span class="info-label">{{ __('contact.billing_email') }}:</span>
                            <span class="info-value"><a href="mailto:{{ $contactMessage->billing_email }}" style="color: #4f46e5; text-decoration: none;">{{ $contactMessage->billing_email }}</a></span>
                        </div>
                    @endif
                    @if($contactMessage->phone)
                        <div class="info-row">
                            <span class="info-label">{{ __('contact.phone') }}:</span>
                            <span class="info-value"><a href="tel:{{ $contactMessage->phone }}" style="color: #4f46e5; text-decoration: none;">{{ $contactMessage->phone }}</a></span>
                        </div>
                    @endif
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
            <p style="margin: 0 0 12px 0; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px;">
                {{ __('contact.email_signature') }}
            </p>
            <p style="margin: 0; font-size: 11px; color: #9ca3af;">
                © {{ date('Y') }} {{ config('app.name') }}. {{ __('app.all_rights_reserved') ?? 'All rights reserved.' }}<br>
                <a href="{{ config('app.url') }}" style="color: #6b7280; text-decoration: none;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
