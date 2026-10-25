@php use App\Support\Site; @endphp
<x-layouts.site title="Contact" description="Neem contact op met Vermeer Boomtechniek in Wouw: bel {{ Site::get('phone') }}, mail of stuur een bericht via het formulier.">
    <x-page-header eyebrow="Contact" title="Contact opnemen" intro="Heeft u een vraag of wilt u een vrijblijvende offerte? Bel of mail, of vul het formulier in. We nemen zo snel mogelijk contact met u op." />

    <div class="wrap grid items-start gap-10 py-12 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="grid gap-6">
            @if (session('sent'))
                <div class="bg-accent p-5 text-on-accent" role="status">
                    <span class="font-display text-2xl font-bold uppercase">Bedankt voor uw bericht!</span>
                    <p class="m-0 mt-1">We nemen zo snel mogelijk contact met u op.</p>
                </div>
            @endif

            @if ($summary)
                <div class="grid gap-2 border border-line bg-surface p-5">
                    <span class="font-display text-2xl font-bold uppercase">Uw antwoorden uit de calculator</span>
                    <p class="m-0 text-sm text-dim">Deze gegevens sturen we mee met uw bericht.</p>
                    <dl class="m-0 grid gap-x-4 gap-y-1 text-sm sm:grid-cols-[auto_1fr]">
                        <dt class="font-semibold">Dienst</dt><dd class="m-0 text-muted">{{ $summary['service'] }}</dd>
                        @if ($summary['trees'])<dt class="font-semibold">Aantal bomen</dt><dd class="m-0 text-muted">{{ $summary['trees'] }}</dd>@endif
                        @foreach ($summary['answers'] as $question => $answer)
                            <dt class="font-semibold">{{ $question }}</dt><dd class="m-0 text-muted">{{ $answer }}</dd>
                        @endforeach
                        @foreach ($summary['estimates'] as $estimate)
                            <dt class="font-semibold">Indicatie{{ count($summary['estimates']) > 1 ? ' ' . $estimate['name'] : '' }}</dt><dd class="m-0 text-muted">{{ $estimate['incl'] }} incl. btw</dd>
                        @endforeach
                    </dl>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}" class="grid gap-4 sm:grid-cols-2" novalidate>
                @csrf
                {{-- Spam trap: people don't see this field, robots fill it in --}}
                <div class="hidden" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>

                @foreach ([
                    ['name', 'Naam', 'text', 'name', true],
                    ['email', 'E-mailadres', 'email', 'email', true],
                    ['phone', 'Telefoonnummer', 'tel', 'tel', false],
                    ['subject', 'Onderwerp', 'text', 'off', false],
                ] as [$field, $label, $type, $autocomplete, $required])
                    <div class="grid gap-1">
                        <label for="{{ $field }}" class="font-semibold">{{ $label }}@if (! $required) <span class="font-normal text-dim">(optioneel)</span>@endif</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}" @if ($required) required @endif
                               value="{{ old($field, $field === 'subject' ? request('onderwerp') : '') }}"
                               @class(['field', 'border-red-400' => $errors->has($field)])
                               @error($field) aria-invalid="true" aria-describedby="{{ $field }}-error" @enderror>
                        @error($field)<span id="{{ $field }}-error" class="text-sm text-red-300">{{ $message }}</span>@enderror
                    </div>
                @endforeach

                <div class="grid gap-1 sm:col-span-2">
                    <label for="message" class="font-semibold">Uw bericht</label>
                    <textarea id="message" name="message" rows="6" required @class(['field', 'border-red-400' => $errors->has('message')])
                              @error('message') aria-invalid="true" aria-describedby="message-error" @enderror>{{ old('message') }}</textarea>
                    @error('message')<span id="message-error" class="text-sm text-red-300">{{ $message }}</span>@enderror
                </div>

                <div class="sm:col-span-2"><button type="submit" class="btn btn-primary">Verstuur bericht</button></div>
            </form>
        </div>

        <aside class="grid gap-3 border border-line bg-surface p-6">
            <span class="font-display text-2xl font-bold uppercase">Vermeer Boomtechniek</span>
            <a href="tel:{{ Site::phoneLink() }}" class="font-semibold no-underline">{{ Site::get('phone') }}</a>
            <a href="mailto:{{ Site::get('email') }}" class="break-all">{{ Site::get('email') }}</a>
            <span class="text-muted">{{ Site::get('street') }}<br>{{ Site::get('city') }}</span>
            <img src="{{ asset('images/tijdelijk/fotos/bas-dag-1-18.jpg') }}" alt="Bas Vermeer, eigenaar van Vermeer Boomtechniek" class="mt-2 aspect-[4/3] w-full object-cover" loading="lazy">
        </aside>
    </div>
</x-layouts.site>
