@extends('errors.client')

@section('title', __('errors.401.title'))
@section('code', '401')
@section('message', __('errors.401.message'))

@section('help')
    <div class="mx-auto max-w-md rounded-lg border border-blue-200 bg-blue-50 p-4 text-left dark:border-blue-900/30 dark:bg-blue-900/10">
        <h3 class="font-semibold text-blue-900 dark:text-blue-200">{{ __('errors.401.help_title') }}</h3>
        <ul class="mt-2 space-y-1 text-sm text-blue-800 dark:text-blue-300">
            <li>• {{ __('errors.401.help_1') }}</li>
            <li>• {{ __('errors.401.help_2') }}</li>
            <li>• {{ __('errors.401.help_3') }}</li>
        </ul>
    </div>
@endsection
