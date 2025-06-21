<div x-data="PickBan({{ $lobby->toJson() }})" x-init="init()" class="mt-10">
    <h2 class="text-xl font-bold mb-4">Map kiválasztás</h2>
    <div class="grid grid-cols-3 gap-4">
        <template x-for="(map, index) in maps" :key="map.name">
            <button
                x-text="map.name"
                @click="banMap(map.name)"
                :class="{
                    'bg-gray-700': map.status === 'available',
                    'bg-red-700': map.status === 'banned',
                    'bg-green-600': map.status === 'picked'
                }"
                class="text-white px-4 py-2 rounded font-semibold"
                :disabled="!canBan(map)"
            ></button>
        </template>
    </div>
</div>
