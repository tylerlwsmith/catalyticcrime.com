@props([
    'meta' => null
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <title>Document</title> --}}
    @if ($meta)
        <title>{{ $meta->getPageTitle() }}</title>
        @foreach($meta as $metaTag)
            <meta {{$metaTag->attributeType}}="{{$metaTag->key}}" content="{{$metaTag->content}}" />
        @endforeach
    @endif
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/turbolinks@5.2.0/dist/turbolinks.min.js"></script>
    @production
        <x-google-analytics />
    @endproduction
</head>

<body x-data>
    <x-header />
    {{ $slot }}
    <x-footer />
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script>
</body>

</html>
