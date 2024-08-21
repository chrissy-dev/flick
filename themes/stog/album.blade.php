@extends('layouts.layout')
@section('title', $album['title'] ?? 'Album')

@section('content')
    <div class="w-full px-4 grid gap-8 mt-8 lg:mt-16">
        <div class="mx-auto w-full max-w-3xl grid gap-4 mb-8 lg:mb-16">
            <header class="mb-4 lg:mb-16 flex gap-4 lg:gap-8 items-center">
                <h1 class=" flex-1 font-medium  text-2xl lg:text-4xl">{{ $album['title'] ?? 'Untitled Album' }}</h1>

                <div>
                    @if (isset($album['date']))
                        <time>{{ \Carbon\Carbon::parse($album['date'])->format('M, Y') }}</time>
                    @endif
                </div>
            </header>
            @if (isset($album['content']))
                <div class="album-content">
                    {!! parse_markdown($album['content'] ?? '') !!}
                </div>
            @endif

        </div>

@if(isset($meta['masonry']) && $meta['masonry'])
<main class="mx-auto w-full max-w-[1920px]" id="masonry-grid">
            @foreach ($photos as $photo)
                <figure class="w-full md:w-1/2 lg:w-1/3">
                   <div class="relative">
                     <img width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" src="{{ $photo->path }}"
                        alt="{{ $photo->exif->ImageDescription ?? '' }}">
                        @if(isset($photo->exif->Copyright))

                        <cite class="absolute bottom-0 right-0 py-1 px-1.5 bg-black/95 text-white text-xs not-italic">{{ $photo->exif->Copyright }}</cite>
                        @endif
                   </div>
                   @if(isset($photo->exif->ImageDescription) && $theme->showCaptions)
                        <figcaption class="text-sm py-2">
                
                        {{ $photo->exif->ImageDescription ?? '' }} 
                    </figcaption>
                    @endif
                </figure>
            @endforeach
        </main>
        @else 
<main class="mx-auto w-full max-w-6xl grid gap-4 lg:gap-8">
            @foreach ($photos as $photo)
                <figure class="w-full">
                   <div class="relative">
                     <img width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" src="{{ $photo->path }}"
                        alt="{{ $photo->exif->ImageDescription ?? '' }}">
                        @if(isset($photo->exif->Copyright) && $theme->show_copyright)

                        <cite class="absolute bottom-0 right-0 py-1 px-1.5 bg-black/95 text-white text-xs not-italic">{{ $photo->exif->Copyright }}</cite>
                        @endif
                   </div>

                  @if(isset($photo->exif->ImageDescription) && $theme->show_captions)
                        <figcaption class="text-sm py-2">
                
                        {{ $photo->exif->ImageDescription ?? '' }} 
                    </figcaption>
                    @endif
                </figure>
            @endforeach
        </main>

        @endif
    </div>
@endsection
