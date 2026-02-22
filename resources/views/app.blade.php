<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ $meta['title'] ?? config('app.name', 'Laravel') }}</title>

        @if(isset($meta['description']))
            <meta name="description" content="{{ $meta['description'] }}" />
        @endif
        @if(isset($meta['keywords']))
            <meta name="keywords" content="{{ $meta['keywords'] }}" />
        @endif
        @if(isset($meta['author']))
            <meta name="author" content="{{ $meta['author'] }}" />
        @endif
        <meta name="type" content="website" />
        <meta name="application-name" content="{{ $meta['application_name'] ?? config('app.name', 'Laravel') }}" />

        <!-- Open Graph -->
        <meta property="og:title" content="{{ $meta['title'] ?? config('app.name', 'Laravel') }}" />
        @if(isset($meta['description']))
            <meta property="og:description" content="{{ $meta['description'] }}" />
        @endif
        @if(isset($meta['url']))
            <meta property="og:url" content="{{ $meta['url'] }}" />
            <meta property="canonical" content="{{ $meta['url'] }}" />
        @endif
        @if(isset($meta['image']))
            <meta property="og:image" content="{{ $meta['image'] }}" />
            <meta property="og:image:width" content="1200" />
            <meta property="og:image:height" content="630" />
            <meta property="image" content="{{ $meta['image'] }}" />
        @endif
        <link rel="shortcut icon" href="{{ getSetting('favicon') ?: '/favicon.svg' }}" />
        <link rel="icon" type="image/svg+xml" href="{{ getSetting('favicon') ?: '/favicon.svg' }}" />

        <!-- Twitter -->
        <meta property="twitter:title" content="{{ $meta['title'] ?? config('app.name', 'Laravel') }}" />
        @if(isset($meta['description']))
            <meta property="twitter:description" content="{{ $meta['description'] }}" />
        @endif
        @if(isset($meta['image']))
            <meta property="twitter:image" content="{{ $meta['image'] }}" />
        @endif
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" content="{{ $meta['application_name'] ?? config('app.name', 'Laravel') }}" />

        <meta name="robots" content="index, follow" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
