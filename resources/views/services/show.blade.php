@php use App\Support\Site; @endphp
<x-layouts.site :title="$service->title" :description="$service->summary">
    <x-page-header :eyebrow="$service->tree_part->getLabel()" :title="$service->title" :intro="$service->intro ?? $service->summary" :image="$service->imageUrl()" />

    <div class="wrap grid gap-12 py-12 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="prose-vb">{!! $service->body !!}</article>

        <aside class="grid content-start gap-4 lg:sticky lg:top-6 lg:self-start">
            @if ($service->calculatorService?->is_active && ! $service->calculatorService->contact_only)
                <div class="grid gap-3 bg-accent p-5 text-on-accent">
                    <span class="font-display text-2xl font-bold uppercase">Wat kost het ongeveer?</span>
                    <p class="m-0">Beantwoord een paar vragen en zie direct een prijsindicatie.</p>
                    <a href="{{ route('calculator', ['dienst' => $service->calculatorService->slug]) }}" class="btn btn-dark">Bereken je prijs</a>
                </div>
            @endif
            <div class="grid gap-2 border border-line p-5">
                <span class="font-display text-2xl font-bold uppercase">Vragen?</span>
                <a href="tel:{{ Site::phoneLink() }}" class="font-semibold no-underline">{{ Site::get('phone') }}</a>
                <a href="mailto:{{ Site::get('email') }}" class="break-all">{{ Site::get('email') }}</a>
                <a href="{{ route('contact', ['onderwerp' => $service->title]) }}" class="btn btn-outline mt-2">Stuur een bericht</a>
            </div>
            <nav class="grid gap-1 p-1" aria-label="Andere diensten">
                <span class="label mb-1">Andere diensten</span>
                @foreach ($others as $other)
                    <a href="{{ route('services.show', $other) }}" class="text-muted no-underline hover:text-accent">{{ $other->title }}</a>
                @endforeach
            </nav>
        </aside>
    </div>

    <x-contact-band />
</x-layouts.site>
