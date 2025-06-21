@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-gray-900 text-white p-6 rounded-lg shadow-md">

    <h1 class="text-2xl font-bold mb-4">Lobby: {{ $lobby->code }}</h1>

    <p class="text-sm mb-2">Állapot: <span class="text-yellow-300">{{ $lobby->status }}</span></p>
    <p class="text-sm mb-4">Map: <span class="text-green-400">{{ $lobby->map ?? 'Még nincs kiválasztva' }}</span></p>

    <h2 class="text-xl font-semibold mb-2">Játékosok:</h2>
    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        @foreach ($lobby->players as $player)
            <li class="bg-gray-800 px-4 py-2 rounded">
                <strong>{{ $player->user->name }}</strong>
                @if ($player->is_captain)
                    <span class="text-yellow-500">(Kapitány)</span>
                @endif
                – <span class="uppercase">{{ $player->team ?? 'N/A' }}</span>
                @if ($player->user_id === auth()->id())
                    <span class="text-blue-400">(Te)</span>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <!-- CT csapat -->
    <div>
        <h2 class="text-xl font-bold text-blue-400 mb-2">CT csapat</h2>
        <ul class="space-y-2">
            @foreach ($lobby->players->where('team', 'ct') as $player)
                <li class="bg-blue-900 px-4 py-2 rounded">
                    {{ $player->user->name }}
                    @if ($player->is_captain)
                        <span class="text-yellow-400 font-semibold">(Kapitány)</span>
                    @endif
                    @if ($player->user_id === auth()->id())
                        <span class="text-green-300">(Te)</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <!-- T csapat -->
    <div>
        <h2 class="text-xl font-bold text-red-400 mb-2">T csapat</h2>
        <ul class="space-y-2">
            @foreach ($lobby->players->where('team', 't') as $player)
                <li class="bg-red-900 px-4 py-2 rounded">
                    {{ $player->user->name }}
                    @if ($player->is_captain)
                        <span class="text-yellow-400 font-semibold">(Kapitány)</span>
                    @endif
                    @if ($player->user_id === auth()->id())
                        <span class="text-green-300">(Te)</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>


    @if ($lobby->map && $lobby->status === 'accepted' && $lobby->server)
    <div class="mt-6 text-center">
        <a href="steam://connect/server.versuscs.hu:{{ $lobby->server->port }}"
           class="bg-green-600 hover:bg-green-700 px-6 py-3 rounded text-white text-lg font-semibold">
           Csatlakozás a szerverhez
        </a>
    </div>
@endif

    @endif
</div>
@endsection

@push('scripts')
<script>
    function PickBan(lobby) {
        return {
            maps: [
                { name: 'Inferno', status: 'available' },
                { name: 'Mirage', status: 'available' },
                { name: 'Nuke', status: 'available' },
                { name: 'Ancient', status: 'available' },
                { name: 'Anubis', status: 'available' },
                { name: 'Vertigo', status: 'available' },
                { name: 'Overpass', status: 'available' },
            ],
            currentCaptain: 'ct',
            init() {
                console.log("PickBan UI elindult");
            },
            canBan(map) {
                return map.status === 'available';
            },
            banMap(name) {
                const map = this.maps.find(m => m.name === name);
                if (map && map.status === 'available') {
                    map.status = 'banned';
                    const available = this.maps.filter(m => m.status === 'available');
                    if (available.length === 1) {
                        available[0].status = 'picked';
                    } else {
                        this.currentCaptain = this.currentCaptain === 'ct' ? 't' : 'ct';
                    }
                }
            }
        }
    }
</script>
@endpush

