@component('mail::message')
{{ __('client.client_auth.hello') }}, {{ $name }}

{{ __('client.client_auth.password_reset_message') }}

{{ __('client.client_auth.password_reset_expires', ['date' => $expiresAt]) }}

@component('mail::button', ['url' => $resetUrl])
{{ __('client.client_auth.password_reset_button') }}
@endcomponent

{{ __('client.client_auth.or_copy_link') }}

{{ $resetUrl }}

{{ __('client.client_auth.thank_you') }},
{{ config('app.name') }}
@endcomponent
