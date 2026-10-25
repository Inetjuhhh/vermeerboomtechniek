@php use App\Support\Site; @endphp
<x-layouts.site>
    {{-- Hero: large photo, dark from the left so the text stays readable --}}
    <section class="relative isolate grid min-h-[min(80vh,640px)] items-end overflow-hidden">
        <img src="{{ asset('images/tijdelijk/fotos/bas-dag-2-63.jpg') }}" alt="Bas Vermeer met helm en zaag in de kroon van een boom"
             class="absolute inset-0 -z-20 size-full object-cover object-[60%_30%]" fetchpriority="high">
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-bg via-bg/70 to-bg/10 sm:bg-gradient-to-r sm:from-bg sm:via-bg/60 sm:to-transparent"></div>
        <div class="wrap grid gap-5 py-12 sm:py-16">
            <span class="label">Gecertificeerd boomverzorger · Wouw</span>
            <h1 class="m-0 text-5xl leading-[1.02] tracking-tight sm:text-7xl">Bomen,<br><span class="text-accent">vakkundig</span><br>verzorgd</h1>
            <p class="m-0 max-w-xl text-lg text-muted">Hoogwaardige boomverzorging door een gecertificeerde specialist. Bij Vermeer Boomtechniek staan professionaliteit, kwaliteit en persoonlijke aandacht centraal.</p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('calculator') }}" class="btn btn-primary">Bereken je offerte</a>
                <a href="{{ route('services.index') }}" class="btn btn-outline">Bekijk de diensten</a>
            </div>
        </div>
    </section>

    {{-- Facts --}}
    <section class="border-y border-line">
        <dl class="wrap my-0 grid grid-cols-2 gap-x-6 lg:grid-cols-4">
            @foreach ([['2018', 'opgericht door Bas Vermeer'], ['15+ jaar', 'ervaring in het groen'], ['ETW · ETT', 'European Tree Worker en Technician'], ['VTA', 'veiligheidscontrole met rapport']] as [$big, $small])
                <div class="grid gap-1 py-5">
                    <dt class="font-display text-3xl font-bold text-accent">{{ $big }}</dt>
                    <dd class="m-0 text-sm text-dim">{{ $small }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Services as tree rows: crown, trunk, roots --}}
    <section class="pt-14">
        <div class="wrap grid gap-3 pb-8">
            <span class="label">Wat we doen</span>
            <h2 class="m-0 text-4xl sm:text-5xl">Van wortel tot kroon</h2>
            <p class="m-0 max-w-2xl text-muted">Bas kijkt naar de hele boom, boven én onder de grond. Kies waar uw vraag over gaat.</p>
        </div>
        <x-tree-rows :services="$services" />
    </section>

    {{-- Calculator teaser --}}
    <section class="wrap py-14">
        <div class="grid items-center gap-6 border border-line bg-surface p-6 sm:p-10 md:grid-cols-[1fr_auto]">
            <div class="grid gap-2">
                <span class="label">Nieuw</span>
                <h2 class="m-0 text-4xl">Wat gaat uw boom ongeveer kosten?</h2>
                <p class="m-0 max-w-xl text-muted">Beantwoord een paar vragen over de boom en zie direct een prijsindicatie, inclusief en exclusief btw.</p>
            </div>
            <a href="{{ route('calculator') }}" class="btn btn-primary">Bereken je offerte</a>
        </div>
    </section>

    {{-- About Bas --}}
    <section class="bg-surface">
        <div class="grid md:grid-cols-2">
            <img src="{{ asset('images/tijdelijk/fotos/bas-dag-1-100.jpg') }}" alt="Bas zaagt een gevelde stam in stukken" class="aspect-[4/3] size-full object-cover" loading="lazy">
            <div class="grid content-center gap-5 px-4 py-10 sm:px-10 lg:px-14">
                <span class="label">Over Bas</span>
                <h2 class="m-0 text-4xl sm:text-5xl">Ruim 15 jaar in het groen</h2>
                <ol class="m-0 grid list-none gap-0 border-t border-line p-0">
                    @foreach ($timeline as [$year, $text])
                        <li class="grid grid-cols-[4.5rem_1fr] gap-3 border-b border-line py-2"><span class="font-display text-xl font-bold text-accent">{{ $year }}</span><span>{{ $text }}</span></li>
                    @endforeach
                </ol>
                @if ($logos->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($logos as $certificate)
                            <img src="{{ $certificate->imageUrl('logo') }}" alt="{{ $certificate->abbreviation ?? $certificate->name }}" class="h-14 w-auto rounded-full bg-text p-1" loading="lazy">
                        @endforeach
                    </div>
                @endif
                <a href="{{ route('page', 'het-bedrijf') }}" class="font-display font-semibold no-underline">Meer over het bedrijf →</a>
            </div>
        </div>
    </section>

    {{-- Review --}}
    @if ($review)
        <section class="wrap py-16">
            <span class="label">Klanten over Bas</span>
            <blockquote class="m-0 mt-3 max-w-4xl font-display text-3xl font-semibold leading-tight sm:text-4xl">"{{ $review->quote }}"</blockquote>
            @if ($review->name)
                <p class="mt-3 mb-0 text-dim">{{ $review->name }}{{ $review->place ? ', ' . $review->place : '' }}</p>
            @endif
        </section>
    @endif

    {{-- Video --}}
    @if (Site::youtubeId())
        <section class="wrap grid items-center gap-8 md:grid-cols-[2fr_3fr]">
            <div class="grid gap-3">
                <span class="label">Video</span>
                <h2 class="m-0 text-4xl">Een dag op pad met Vermeer Boomtechniek</h2>
                <p class="m-0 text-muted">We laten graag zien wat voor zorg en toewijding we in ons werk stoppen.</p>
            </div>
            {{-- Links to YouTube instead of embedding it, so no YouTube cookies on this site --}}
            <a href="https://www.youtube.com/watch?v={{ Site::youtubeId() }}" target="_blank" rel="noopener" class="group relative block overflow-hidden">
                <img src="{{ asset('images/tijdelijk/fotos/bas-dag-1-58.jpg') }}" alt="" class="aspect-video w-full object-cover transition group-hover:scale-105" loading="lazy">
                <span class="absolute inset-0 grid place-items-center bg-bg/30">
                    <span class="grid size-16 place-items-center bg-accent text-on-accent"><svg viewBox="0 0 24 24" class="size-7 translate-x-0.5" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
                </span>
                <span class="sr-only">Bekijk de video op YouTube</span>
            </a>
        </section>
    @endif

    <x-contact-band />
</x-layouts.site>
