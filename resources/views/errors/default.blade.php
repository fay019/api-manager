@extends('errors.layout')

@section('title', 'Erreur')
@section('code', isset($exception) ? $exception->getStatusCode() : '500')
@section('message', isset($exception) ? $exception->getMessage() : 'Une erreur est survenue.')
