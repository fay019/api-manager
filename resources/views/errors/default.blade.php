@php
    $statusCode = isset($exception) ? $exception->getStatusCode() : 500;
    $isServerError = $statusCode >= 500;
    $layout = $isServerError ? 'errors.server' : 'errors.client';
@endphp

@extends($layout)

@section('title', __('errors.' . $statusCode . '.title', ['default' => 'Error']))
@section('code', $statusCode)
@section('message', __('errors.' . $statusCode . '.message', ['default' => isset($exception) ? $exception->getMessage() : 'An error occurred.']))
