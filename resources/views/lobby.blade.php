<div x-data="pickBanLobby('{{ $lobby->code }}', @json(auth()->user()))" x-init="init()">
    <h2 class="text-white text-xl mb-4">Válassz ki egy pályát bannolásra</h2>

    <template x-for="map in maps" :key="map.name">
        <button
            class="px-4 py-2 m-2 rounded border transition"
            :class="{
                'bg-red-600 text-white': map.status === 'banned',
                'bg-green-600 text-white': map.status === 'picked',
                'bg-gray-200': map.status === 'available'
            }"
            @click="banMap(map.name)"
            x-text="map.name.toUpperCase()"
            :disabled="!canBan || map.status !== 'available'"
        ></button>
    </template>

    <template x-if="finalMap">
        <div class="mt-6 text-white">
            <strong>Kiválasztott pálya:</strong> <span x-text="finalMap.toUpperCase()"></span>
        </div>
    </template>
</div>
