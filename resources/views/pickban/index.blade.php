<h1>Pick & Ban - Lobby #{{ $lobby->code }}</h1>

<h3>Map pool:</h3>
<ul>
    @foreach ($mapPool as $map)
        <li>{{ $map }}</li>
    @endforeach
</ul>

<form method="POST" action="{{ route('pickban.ban', $lobby->id) }}">
    @csrf
    <label for="map">Tiltani kívánt map:</label>
    <select name="map" id="map">
        @foreach ($mapPool as $map)
            <option value="{{ $map }}">{{ $map }}</option>
        @endforeach
    </select>
    <button type="submit">Tiltás</button>
</form>

@if ($lobby->selected_map)
    <h2 style="color: green;">Kiválasztott map: {{ $lobby->selected_map }}</h2>
@endif


<script>
    Echo.private('pickban.{{ $lobby->code }}')
        .listen('.map.banned', (e) => {
            alert('Map tiltva: ' + e.map);
            location.reload(); // vagy update DOM
        });
</script>
