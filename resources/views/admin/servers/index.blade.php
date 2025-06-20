@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6">CS2 Szerverek</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($servers as $server)
        <div class="bg-gray-800 p-4 rounded-xl shadow-lg text-white">
            <h3 class="text-xl font-semibold mb-2">{{ $server['name'] }}</h3>
            <p><strong>IP:</strong> {{ $server['ip'] }}:{{ $server['port'] }}</p>
            <p><strong>Állapot:</strong>
                @if(isset($server['status']) && $server['status'] === 'running')
                    <span class="text-green-400">Fut</span>
                @else
                    <span class="text-red-400">Leállt</span>
                @endif
            </p>

            <div class="mt-4 space-x-2">
                <form method="POST" action="{{ route('admin.servers.start', $server['instance']) }}" class="inline">
                    @csrf
                    <button class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded">Start</button>
                </form>
                <form method="POST" action="{{ route('admin.servers.stop', $server['instance']) }}" class="inline">
                    @csrf
                    <button class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded">Stop</button>
                </form>
                <form method="POST" action="{{ route('admin.servers.restart', $server['instance']) }}" class="inline">
                    @csrf
                    <button class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded">Restart</button>
                </form>
            </div>

            <div class="mt-4 flex flex-col space-y-1">
                <a href="{{ route('admin.servers.config', $server['instance']) }}" class="text-blue-400 hover:underline">⚙️ Config szerkesztés</a>
                <a href="{{ route('admin.servers.whitelist', $server['instance']) }}" class="text-blue-400 hover:underline">📜 Whitelist szerkesztés</a>
                <a href="{{ route('admin.servers.console', $server['instance']) }}" class="text-blue-400 hover:underline">📟 Konzol log</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
