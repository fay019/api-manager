@extends('errors.client')

@section('title', __('errors.403.title'))
@section('code', '403')
@section('message', __('errors.403.message'))

@section('help')
    <div class="mx-auto max-w-md rounded-lg border border-purple-200 bg-purple-50 p-4 text-left dark:border-purple-900/30 dark:bg-purple-900/10">
        <h3 class="font-semibold text-purple-900 dark:text-purple-200">{{ __('errors.403.help_title') }}</h3>
        <ul class="mt-2 space-y-1 text-sm text-purple-800 dark:text-purple-300">
            <li>• {{ __('errors.403.help_1') }}</li>
            <li>• {{ __('errors.403.help_2') }}</li>
            <li>• {{ __('errors.403.help_3') }}</li>
        </ul>
    </div>
@endsection
