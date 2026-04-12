@extends('errors.client')

@section('title', __('errors.419.title'))
@section('code', '419')
@section('message', __('errors.419.message'))

@section('help')
    <div class="mx-auto max-w-md rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-left dark:border-yellow-900/30 dark:bg-yellow-900/10">
        <h3 class="font-semibold text-yellow-900 dark:text-yellow-200">{{ __('errors.419.help_title') }}</h3>
        <ul class="mt-2 space-y-1 text-sm text-yellow-800 dark:text-yellow-300">
            <li>• {{ __('errors.419.help_1') }}</li>
            <li>• {{ __('errors.419.help_2') }}</li>
            <li>• {{ __('errors.419.help_3') }}</li>
        </ul>
    </div>
@endsection
