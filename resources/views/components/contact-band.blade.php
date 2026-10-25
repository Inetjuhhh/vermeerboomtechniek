@props(['title' => 'Laten we samen uw hulpvraag bekijken'])
@php use App\Support\Site; @endphp
<section class="mt-16 bg-accent text-on-accent">
    <div class="wrap flex flex-wrap items-center justify-between gap-6 py-10">
        <div class="grid gap-2">
            <h2 class="m-0 text-3xl text-on-accent sm:text-4xl">{{ $title }}</h2>
            <p class="m-0 font-semibold">
                <a href="tel:{{ Site::phoneLink() }}" class="text-on-accent no-underline">{{ Site::get('phone') }}</a> ·
                <a href="mailto:{{ Site::get('email') }}" class="text-on-accent no-underline">{{ Site::get('email') }}</a> ·
                {{ Site::get('street') }}, {{ Site::get('city') }}
            </p>
        </div>
        <a href="{{ route('contact') }}" class="btn btn-dark">Stuur een bericht</a>
    </div>
</section>
