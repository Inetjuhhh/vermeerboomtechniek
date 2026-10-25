<x-layouts.site :title="$page->title" :description="$page->meta_description">
    <x-page-header :title="$page->title" :intro="$page->intro" :image="$page->imageUrl()" position="50% 30%" />

    <div class="wrap py-12"><article class="prose-vb">{!! $page->body !!}</article></div>

    @if ($page->slug === 'het-bedrijf' && $certificates->isNotEmpty())
        <section class="bg-surface py-14">
            <div class="wrap grid gap-6">
                <div class="grid gap-2">
                    <span class="label">Papieren</span>
                    <h2 class="m-0 text-4xl">Certificeringen</h2>
                    <p class="m-0 text-muted">Bas heeft de volgende certificeringen, die steeds op tijd worden vernieuwd.</p>
                </div>
                <ul class="m-0 grid list-none gap-x-10 border-t border-line p-0 sm:grid-cols-2">
                    @foreach ($certificates as $certificate)
                        <li class="flex items-center gap-4 border-b border-line py-3">
                            @if ($certificate->logo)
                                <img src="{{ $certificate->imageUrl('logo') }}" alt="" class="size-11 rounded-full bg-text object-contain p-1" loading="lazy">
                            @else
                                <span class="grid size-11 place-items-center rounded-full border border-line font-display text-xs font-bold text-accent" aria-hidden="true">{{ \Illuminate\Support\Str::limit($certificate->abbreviation ?? '', 4, '') ?: '✓' }}</span>
                            @endif
                            <span class="grid">
                                <span class="font-semibold">{{ $certificate->name }}@if ($certificate->abbreviation) ({{ $certificate->abbreviation }})@endif</span>
                                @if ($certificate->year)<span class="text-sm text-dim">sinds {{ $certificate->year }}</span>@endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-contact-band title="Geïnteresseerd?" />
</x-layouts.site>
