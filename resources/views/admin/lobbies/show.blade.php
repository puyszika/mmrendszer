@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-gray-900 text-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-4">Lobby: {{ $lobby->code }}</h2>

    <p><strong>Állapot:</strong> {{ $lobby->status }}</p>
    <p><strong>Map:</strong> {{ $lobby->final_map ?? 'Még nincs kiválasztva' }}</p>

    <hr class="my-4 border-gray-700">

    @if($lobby->final_map && !$lobby->gameServer)
        <form method="POST" action="{{ route('lobby.startServer', $lobby->code) }}">
            @csrf
            <button type="submit" class="bg-yellow-500 px-4 py-2 rounded text-black hover:bg-yellow-600">
                Szerver indítása (tournament.conf)
            </button>
        </form>
    @elseif($lobby->gameServer)
        <p class="mt-4">🎮 Szerver indítva: <strong>{{ $lobby->gameServer->name }}</strong></p>
        <a href="steam://connect/{{ $lobby->gameServer->ip }}:{{ $lobby->gameServer->port }}"
           class="inline-block mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Csatlakozás a szerverhez
        </a>
    @endif

</div>
@endsection
