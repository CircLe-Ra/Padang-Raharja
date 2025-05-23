<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@auth
    <meta name="user-id" content="{{ auth()->id() }}">
@endauth

<title>{{ $title ?? config('app.name') }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@stack('styles')

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
