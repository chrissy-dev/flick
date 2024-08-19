<!-- views/home.blade.php -->

@extends('layouts.layout')

@section('content')
    <div class="mx-auto max-w-4xl px-4">
        <h1 class="font-medium text-4xl">Photos</h1>
        <ul class="grid mt-16">
            @foreach ($albums as $album)
                <li class="py-4 border-b border-neutral-200  flex justify-between">
                    <a href="/album/{{ urlencode($album['name']) }}" class="font-medium hover:font-bold transition-all">
                        {{ $album['title'] }}
                    </a>
                    @if ($album['date'])
                     <span class="uppercase text-sm">{{ \Carbon\Carbon::parse($album['date'])->format('M, Y') }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endsection
