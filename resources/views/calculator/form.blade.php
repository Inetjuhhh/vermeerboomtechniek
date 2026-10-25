@php
    use App\Support\Site;
    $range = fn ($min, $max) => $min == $max ? $money($min) : $money($min) . ' – ' . $money($max);
@endphp
<div class="wrap grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_380px]">
    <div class="grid gap-5">
        {{-- Step 1 --}}
        <fieldset class="m-0 grid gap-4 border border-line bg-surface p-5 sm:p-6">
            <legend class="float-left mb-1 font-display text-3xl font-bold uppercase">1. Wat moet er gebeuren?</legend>
            <div class="clear-both grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->services as $option)
                    <label @class(['grid cursor-pointer gap-1 border p-3 transition', 'border-accent bg-accent/15' => $serviceId === $option->id, 'border-line bg-bg hover:border-dim' => $serviceId !== $option->id])>
                        <input type="radio" wire:model.live="serviceId" value="{{ $option->id }}" class="sr-only">
                        <span class="font-display text-lg font-bold uppercase tracking-wide">{{ $option->name }}</span>
                        <span class="text-sm text-muted">{{ $option->description }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        @if ($this->service && ! $this->service->contact_only)
            {{-- Step 2 --}}
            <fieldset class="m-0 grid gap-6 border border-line p-5 sm:p-6">
                <legend class="float-left font-display text-3xl font-bold uppercase">2. Vertel iets over de situatie</legend>

                @if ($this->service->asks_tree_count)
                    <div class="clear-both grid gap-2">
                        <label for="trees" class="font-semibold">Om hoeveel bomen gaat het?</label>
                        <input id="trees" type="number" min="1" max="50" wire:model.live.debounce.400ms="trees" class="field w-28">
                    </div>
                @endif

                @foreach ($this->service->questions as $question)
                    <div class="clear-both grid gap-2" wire:key="q-{{ $question->id }}" role="radiogroup" aria-labelledby="q-{{ $question->id }}">
                        <span id="q-{{ $question->id }}" class="font-semibold">{{ $question->label }}</span>
                        @if ($question->hint)
                            <span class="text-sm text-dim">{{ $question->hint }}</span>
                        @endif
                        <div class="flex flex-wrap gap-2">
                            @foreach ($question->options as $option)
                                <label @class(['cursor-pointer border px-4 py-2 text-[.95rem] transition', 'border-accent bg-accent font-semibold text-on-accent' => ($answers[$question->id] ?? null) == $option->id, 'border-line bg-surface hover:border-dim' => ($answers[$question->id] ?? null) != $option->id])>
                                    <input type="radio" wire:model.live="answers.{{ $question->id }}" value="{{ $option->id }}" class="sr-only">
                                    {{ $option->label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </fieldset>
        @endif
    </div>

    {{-- Result --}}
    <aside class="grid gap-4 border border-accent bg-surface p-6 lg:sticky lg:top-6" aria-live="polite">
        @if (! $this->service)
            <p class="m-0">Kies een dienst om een indicatie te zien.</p>
        @elseif ($this->service->contact_only)
            <span class="font-display text-3xl font-bold uppercase">Bel direct</span>
            <p class="m-0 text-muted">{{ $this->service->contact_message }}</p>
            <a href="tel:{{ Site::phoneLink() }}" class="btn btn-primary">{{ Site::get('phone') }}</a>
        @else
            <span class="label">Indicatie · {{ mb_strtolower($this->service->name) }}</span>
            @foreach ($this->estimates as $estimate)
                <div class="grid gap-1 @if (! $loop->first) border-t border-line pt-4 @endif">
                    @if (count($this->estimates) > 1)
                        <span class="font-display font-bold uppercase tracking-wide">{{ $estimate->name }}</span>
                    @endif
                    <span class="font-display text-4xl font-bold tabular-nums text-accent">{{ $range($estimate->inclMin, $estimate->inclMax) }}</span>
                    <span class="text-sm text-dim">inclusief {{ rtrim(rtrim(number_format($estimate->vatPercentage, 2, ',', ''), '0'), ',') }}% btw</span>
                    <span class="tabular-nums">{{ $range($estimate->exclMin, $estimate->exclMax) }} <span class="text-sm text-dim">exclusief btw</span></span>
                    <details class="mt-1 text-sm">
                        <summary class="cursor-pointer text-muted">Hoe is dit berekend?</summary>
                        <ul class="m-0 mt-2 grid list-none gap-1 p-0">
                            @foreach (array_merge($estimate->workLines, $estimate->otherCostLines) as $line)
                                <li class="flex justify-between gap-3 border-b border-line pb-1"><span>{{ $line->label }}@if ($line->note) <span class="text-dim">({{ $line->note }})</span>@endif</span><span class="whitespace-nowrap tabular-nums">{{ $range($line->min, $line->max) }}</span></li>
                            @endforeach
                        </ul>
                    </details>
                </div>
            @endforeach
            <button type="button" wire:click="requestQuote" class="btn btn-primary">Vraag een vrijblijvende offerte aan</button>
            <p class="m-0 text-xs leading-relaxed text-dim">{{ $disclaimer }}</p>
        @endif
    </aside>
</div>
