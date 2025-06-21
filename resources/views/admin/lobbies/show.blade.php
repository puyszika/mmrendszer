@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-gray-900 text-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-4">Lobby: {{ $lobby->code }}</h2>

    <p><strong>Állapot:</strong> {{ $lobby->status }}</p>
   

    <hr class="my-4 border-gray-700">

    @if ($lobby->players->count())
    <h2 class="text-xl mt-4 mb-2 font-bold">Játékosok:</h2>
    <ul class="space-y-2">
        @foreach ($lobby->players as $player)
            <li>
                <strong>{{ $player->user->name }}</strong>
                @if ($player->is_captain) <span class="text-yellow-600">(Kapitány)</span> @endif
                – Csapat: {{ strtoupper($player->team ?? '-') }}
            </li>
        @endforeach
    </ul>
@else
    <p class="text-gray-500">Még nincsenek játékosok a lobbyban.</p>
@endif

    @if ($lobby->map)
    <p><strong>Választott map:</strong> {{ ucfirst($lobby->map) }}</p>
@else
    <p><strong>Map:</strong> Még nincs kiválasztva</p>
@endif
    {{-- Ha már kiválasztották a végső mapet --}}
@if ($lobby->final_map)

    {{-- Ha még nincs szerver hozzárendelve --}}
    @if (!$lobby->server)
        <p class="mt-4 text-gray-400 italic">
            🛠 Szerver előkészítése folyamatban...
        </p>

    {{-- Ha van szerver és az fut is --}}
    @elseif ($lobby->server && $lobby->server->status === 'running')
        <p class="mt-4">
            🎮 Szerver indítva: <strong>{{ $lobby->server->name }}</strong>
        </p>

        <a href="steam://connect/{{ $lobby->server->ip ?? 'server.versuscs.hu' }}:{{ $lobby->server->port }}"
           class="inline-block mt-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Csatlakozás a szerverhez
        </a>

    {{-- Ha van szerver, de még nem fut --}}
    @elseif ($lobby->server)
        <p class="mt-4 text-yellow-500 italic">
            ⏳ Szerver indítása folyamatban: <strong>{{ $lobby->server->name }}</strong>
        </p>
    @endif

@endif



</div>
@endsection
