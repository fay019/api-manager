@extends('errors.client')

@section('title', __('errors.404.title'))
@section('code', '404')
@section('message', __('errors.404.message'))

@section('help')
    <div class="mx-auto max-w-md rounded-lg border border-amber-200 bg-amber-50 p-4 text-left dark:border-amber-900/30 dark:bg-amber-900/10">
        <h3 class="font-semibold text-amber-900 dark:text-amber-200">{{ __('errors.404.help_title') }}</h3>
        <ul class="mt-2 space-y-1 text-sm text-amber-800 dark:text-amber-300">
            <li>• {{ __('errors.404.help_1') }}</li>
            <li>• {{ __('errors.404.help_2') }}</li>
            <li>• {{ __('errors.404.help_3') }}</li>
        </ul>
    </div>
@endsection
