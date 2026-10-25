@props(['title' => null, 'description' => null])
@php
    use App\Support\Site;
    $fullTitle = $title ? "$title | Vermeer Boomtechniek" : 'Vermeer Boomtechniek | Boomverzorging en boomtechnisch advies in Wouw';
    $adviceActive = request()->is('diensten/boomtechnisch-advies', 'diensten/boom-effect-analyse');
    $nav = [
        ['Home', route('home'), request()->routeIs('home')],
        ['Diensten', route('services.index'), request()->routeIs('services.*') && ! $adviceActive],
        ['Advies & BEA', route('services.show', 'boomtechnisch-advies'), $adviceActive],
        ['Het bedrijf', route('page', 'het-bedrijf'), request()->is('het-bedrijf')],
    ];
    // Optional pages (e.g. Houtbewerking) appear here as long as they are visible and "in het menu"
    foreach (\App\Models\Page::menuPages() as $menuPage) {
        $nav[] = [$menuPage->menu_label ?: $menuPage->title, route('page', $menuPage), request()->is($menuPage->slug)];
    }
    $nav[] = ['Contact', route('contact'), request()->routeIs('contact')];
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1e1913">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description ?? 'Boomverzorging en boomtechnisch advies door gecertificeerd boomverzorger Bas Vermeer (ETW en ETT) uit Wouw: snoeien, rooien, planten, veiligheidscontrole en Boom Effect Analyse.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('images/logo/beeldmerk.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#inhoud" class="btn btn-primary sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50">Naar de inhoud</a>

    <header class="border-b border-line">
        <div class="wrap flex items-center justify-between gap-6 py-3">
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('images/logo/logo-licht.png') }}" alt="Vermeer Boomtechniek" width="1800" height="537" class="h-12 w-auto sm:h-14">
            </a>

            <nav class="hidden items-center gap-6 lg:flex" aria-label="Hoofdmenu">
                @foreach ($nav as [$label, $url, $active])
                    <a href="{{ $url }}" @class(['font-brand text-[.98rem] font-semibold no-underline hover:text-accent', 'text-accent' => $active, 'text-muted' => ! $active]) @if ($active) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
                <a href="{{ route('calculator') }}" class="btn btn-primary py-2">Bereken je offerte</a>
            </nav>

            {{-- Phone menu without JavaScript --}}
            <details class="relative lg:hidden">
                <summary class="cursor-pointer list-none border border-dim px-4 py-2 font-brand font-bold text-text [&::-webkit-details-marker]:hidden">Menu</summary>
                <nav class="absolute right-0 z-40 mt-2 grid w-64 gap-1 border border-line bg-surface p-3 shadow-2xl" aria-label="Hoofdmenu">
                    @foreach ($nav as [$label, $url, $active])
                        <a href="{{ $url }}" @class(['px-3 py-2 font-brand font-semibold no-underline hover:bg-bg', 'text-accent' => $active, 'text-text' => ! $active])>{{ $label }}</a>
                    @endforeach
                    <a href="{{ route('calculator') }}" class="btn btn-primary mt-1">Bereken je offerte</a>
                    <a href="tel:{{ Site::phoneLink() }}" class="btn btn-outline">{{ Site::get('phone') }}</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="inhoud" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-surface text-muted">
        <div class="wrap grid gap-10 py-12 sm:grid-cols-2 lg:grid-cols-4">
            <div class="grid content-start gap-4">
                <img src="{{ asset('images/logo/logo-licht.png') }}" alt="Vermeer Boomtechniek" width="1800" height="537" class="h-12 w-auto" loading="lazy">
                <p class="m-0 text-sm">Boomverzorging en boomtechnisch advies door Bas Vermeer, European Tree Worker en European Tree Technician.</p>
            </div>
            <div class="grid content-start gap-1">
                <span class="label mb-1">Contact</span>
                <a href="tel:{{ Site::phoneLink() }}" class="text-muted no-underline hover:text-accent">{{ Site::get('phone') }}</a>
                <a href="mailto:{{ Site::get('email') }}" class="text-muted no-underline hover:text-accent">{{ Site::get('email') }}</a>
                <span>{{ Site::get('street') }}, {{ Site::get('city') }}</span>
            </div>
            <div class="grid content-start gap-1">
                <span class="label mb-1">Diensten</span>
                @foreach (\App\Models\Service::query()->active()->get(['title', 'slug']) as $service)
                    <a href="{{ route('services.show', $service) }}" class="text-muted no-underline hover:text-accent">{{ $service->title }}</a>
                @endforeach
            </div>
            <div class="grid content-start gap-1">
                <span class="label mb-1">Meer</span>
                <a href="{{ route('calculator') }}" class="text-muted no-underline hover:text-accent">Bereken je offerte</a>
                <a href="{{ route('page', 'het-bedrijf') }}" class="text-muted no-underline hover:text-accent">Het bedrijf</a>
                @foreach (\App\Models\Page::menuPages() as $menuPage)
                    <a href="{{ route('page', $menuPage) }}" class="text-muted no-underline hover:text-accent">{{ $menuPage->menu_label ?: $menuPage->title }}</a>
                @endforeach
                <a href="{{ route('contact') }}" class="text-muted no-underline hover:text-accent">Contact</a>
            </div>
        </div>
        <div class="wrap flex flex-wrap justify-between gap-2 border-t border-line py-5 text-sm text-dim">
            <span>© {{ date('Y') }} Vermeer Boomtechniek</span>
            <span class="flex flex-wrap gap-x-5">
                @if (Site::get('kvk'))
                    <span>KvK {{ Site::get('kvk') }}</span>
                @endif
                <a href="{{ route('page', 'privacy') }}" class="text-dim hover:text-accent">Privacyverklaring</a>
            </span>
        </div>
    </footer>
</body>
</html>
