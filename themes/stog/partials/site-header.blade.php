<header
    class="z-10 p-4 sticky top-0 bg-white/95 backdrop-blur lg:bg-inherit lg:backdrop-blur-none lg:bg-gradient-to-b lg:from-white lg:to-transparent">
    @if (isset($theme->logo_path))
        <a href="{{ $theme->site_url }}">
            <img src="{{ $theme->logo_path }}" alt="{{ $theme->site_name }}" class="h-6 lg:h-8">
        </a>
    @else
        <a class="font-medium" href="/">{{ $theme->site_title }}</a>
    @endif
</header>
