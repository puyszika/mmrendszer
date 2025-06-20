@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Konzol log – {{ $instance }}</h2>
    <pre class="bg-black text-green-400 p-4 rounded overflow-auto h-[500px]">{{ $log }}</pre>
</div>
@endsection
