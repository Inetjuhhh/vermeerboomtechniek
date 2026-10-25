@props(['title', 'intro' => null, 'eyebrow' => null, 'image' => null, 'position' => '50% 40%'])
<section class="relative isolate overflow-hidden border-b border-line">
    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 -z-20 size-full object-cover" style="object-position: {{ $position }}">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-bg via-bg/85 to-bg/20"></div>
    @endif
    <div @class(['wrap grid gap-4', 'py-16 sm:py-24' => $image, 'py-10 sm:py-14' => ! $image])>
        @if ($eyebrow)
            <span class="label">{{ $eyebrow }}</span>
        @endif
        <h1 class="m-0 max-w-3xl text-4xl tracking-tight sm:text-6xl">{{ $title }}</h1>
        @if ($intro)
            <p class="m-0 max-w-2xl text-lg text-muted">{{ $intro }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
