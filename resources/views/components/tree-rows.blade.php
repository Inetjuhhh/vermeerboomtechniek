@props(['services'])
@php $groups = $services->groupBy(fn ($s) => $s->tree_part->value); @endphp
{{-- Services from the top of the tree down: two green rows and one brown --}}
<div {{ $attributes }}>
    @foreach (\App\Enums\TreePart::cases() as $part)
        @continue(! $groups->has($part->value))
        <section class="{{ $part->classes() }}" aria-labelledby="part-{{ $part->value }}">
            <div class="wrap grid gap-6 py-8 md:grid-cols-[180px_minmax(0,1fr)]">
                <h3 id="part-{{ $part->value }}" class="m-0 grid gap-1 text-current">
                    <span class="font-display text-xs font-semibold uppercase tracking-[.2em] opacity-80">{{ $part->getLabel() }}</span>
                    <span class="text-4xl">{{ $part->title() }}</span>
                </h3>
                <ul class="m-0 grid list-none gap-x-8 gap-y-5 p-0 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($groups[$part->value] as $service)
                        <li>
                            <a href="{{ route('services.show', $service) }}" class="group grid gap-1 text-current no-underline">
                                <span class="font-display text-xl font-bold">{{ $service->title }} <span aria-hidden="true" class="inline-block transition group-hover:translate-x-1">→</span></span>
                                <span class="text-[.95rem] opacity-85">{{ $service->summary }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endforeach
</div>
