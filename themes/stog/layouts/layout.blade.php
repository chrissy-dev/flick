<!-- views/layout.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset_path('stog.css') }}">
    <meta name="generator" content="flick">
</head>

<body>
    @include('partials.site-header')
    <main>
        @yield('content')
    </main>
</body>
<script src="https://unpkg.com/magic-grid/dist/magic-grid.min.js"></script>
<script>
    let magicGrid = new MagicGrid({
        container: "#album-photos", // Required. Can be a class, id, or an HTMLElement.
        static: true, // Required for static content.
        animate: true, // Optional.
        gutter: 16, // Optional. Space between items.
        maxColumns: 3, // Optional. Maximum number of columns.
        
    });

    magicGrid.listen();
</script>
</html>
