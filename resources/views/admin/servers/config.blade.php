@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Szerkesztés: {{ $title }} – {{ $instance }}</h2>

    @if(session('success'))
        <div class="bg-green-600 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST">
        @csrf
        <textarea name="content" class="w-full h-[400px] p-4 bg-gray-900 text-white rounded" spellcheck="false">{{ $content }}</textarea>
        <div class="mt-4">
            <button class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">Mentés</button>
        </div>
    </form>
</div>
@endsection
