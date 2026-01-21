@extends('setup.layout')

@section('title', 'Bienvenue - Installation')

@section('content')
<div class="setup-header">
    <h1>🚀 API Manager</h1>
    <p>Assistant d'installation</p>
</div>

<div class="info-box">
    <strong>Première installation!</strong><br>
    Cet assistant va vous guider pour configurer votre application en 3 étapes simples.
</div>

<div style="text-align: center; margin: 40px 0;">
    <div style="font-size: 64px; margin-bottom: 20px;">📦</div>
    <p style="color: #666; margin-bottom: 30px;">
        Nous allons configurer:
    </p>
    <ul style="text-align: left; color: #555; font-size: 14px; line-height: 2;">
        <li>✅ Infos générales du site</li>
        <li>✅ Accès base de données</li>
        <li>✅ Compte administrateur</li>
    </ul>
</div>

<div class="form-actions">
    <a href="{{ route('setup.general') }}" class="btn btn-primary">
        Commencer →
    </a>
</div>

<div style="text-align: center; margin-top: 20px; font-size: 12px; color: #999;">
    Durée estimée: 2-3 minutes
</div>
@endsection
