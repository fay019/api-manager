@component('mail::message')
{{ __('client.client_auth.hello') }}, {{ $name }}

{{ __('client.client_auth.activation_message') }}

{{ __('client.client_auth.activation_expires', ['date' => $expiresAt]) }}

@component('mail::button', ['url' => $activationUrl])
{{ __('client.client_auth.activate_button') }}
@endcomponent

{{ __('client.client_auth.or_copy_link') }}

{{ $activationUrl }}

{{ __('client.client_auth.thank_you') }},
{{ config('app.name') }}
@endcomponent
