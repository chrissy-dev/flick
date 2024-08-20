<!-- views/home.blade.php -->

@extends('layouts.layout')

@section('content')
    <div class="mx-auto max-w-4xl px-4">
        <h1 class="font-medium text-4xl">Photos</h1>
        <ul class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8 mt-16">
            @foreach ($albums as $album)
                <li class="flex flex-col gap-2">
                    @if (isset($album['cover']))
                        <a href="/album/{{ urlencode($album['name']) }}" class="font-medium hover:font-bold transition-all">
                            <figure>
                                <img class="w-full aspect-square object-cover" src="{{ $album['cover'] }}" alt="">
                            </figure>
                        </a>
                    @else
                        <a href="/album/{{ urlencode($album['name']) }}" class="font-medium hover:font-bold transition-all">
                            <figure class="bg-gray-200 aspect-square"></figure>
                        </a>
                    @endif
                   <p class="grid">
                     <a href="/album/{{ urlencode($album['name']) }}" class="font-medium hover:font-bold transition-all">
                        {{ $album['title'] }}
                    </a>
                    @if ($album['date'])
                        <span class="uppercase text-sm">{{ \Carbon\Carbon::parse($album['date'])->format('M, Y') }}</span>
                    @endif
                   </p>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
