<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('queue.join') }}">
    @csrf
    <button type="submit">Csatlakozás Matchmakingbe</button>
</form>

<form method="POST" action="{{ route('queue.leave') }}">
    @csrf
    <button type="submit">Kilépés Matchmakingből</button>
</form>

@if(isset($lobby))
    <form method="POST" action="{{ route('lobby.accept', $lobby->id) }}">
        @csrf
        <button type="submit">Elfogadom a meccset</button>
    </form>
@endif



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
