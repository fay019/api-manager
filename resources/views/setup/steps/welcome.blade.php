@extends('setup.layout')

@section('content')
<div class="setup-body">
    <h2>✓ Vérification des Prérequis</h2>

    @if($canContinue)
        <div class="alert alert-success">
            ✓ Tous les prérequis sont satisfaits!
        </div>

        <a href="{{ route('setup.app-settings', ['setup_token' => app(\App\Services\Installation\SetupSession::class)->getToken()]) }}" class="btn btn-primary mt-4">
            Continuer vers l'étape 2 →
        </a>
    @else
        <div class="alert alert-danger">
            ❌ Les prérequis suivants ne sont pas satisfaits:
        </div>

        <ul>
            @foreach($checkResults['errors'] as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button class="btn btn-secondary mt-4" onclick="location.reload()">
            Réessayer les vérifications
        </button>
    @endif
</div>
@endsection
