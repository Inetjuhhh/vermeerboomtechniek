{{-- Result of "Rekensom testen". Plain inline styles: the default Filament theme only has its own classes. --}}
@php
    $muted = 'opacity:.65';
    $cell = 'padding:.35rem .5rem;border-bottom:1px solid rgb(128 128 128 / .2);vertical-align:top';
    $num = $cell . ';text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums';
    $range = fn ($min, $max) => $min == $max ? $money($min) : $money($min) . ' – ' . $money($max);
@endphp

@if (! $service)
    <p style="{{ $muted }}">Kies een dienst.</p>
@elseif ($service->contact_only)
    <div style="padding:1rem;border-radius:.5rem;background:rgb(224 97 27 / .12)">
        <strong>Geen prijs: de bezoeker ziet deze tekst</strong>
        <p style="margin-top:.5rem">{{ $service->contact_message ?: 'Neem contact op.' }}</p>
    </div>
@elseif (empty($estimates))
    <p style="{{ $muted }}">Deze dienst heeft nog geen rekenmethode die meerekent. Voeg er een toe bij Diensten en factoren.</p>
@else
    @if (count($estimates) > 1)
        <p style="{{ $muted }};margin-bottom:1rem">Deze dienst heeft {{ count($estimates) }} rekenmethodes. Op de website kiezen we er later één, of tonen we ze naast elkaar.</p>
    @endif

    <div style="display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fit,minmax(16rem,1fr))">
        @foreach ($estimates as $estimate)
            <div style="display:grid;gap:.75rem;align-content:start;min-width:0">
                <div>
                    <div style="font-weight:600">{{ $estimate->name }}</div>
                    @if ($estimate->description)
                        <div style="{{ $muted }};font-size:.875rem">{{ $estimate->description }}</div>
                    @endif
                </div>

                <div style="padding:.75rem 1rem;border-radius:.5rem;background:rgb(47 107 58 / .12)">
                    <div style="font-size:1.35rem;font-weight:700;font-variant-numeric:tabular-nums">{{ $range($estimate->inclMin, $estimate->inclMax) }}</div>
                    <div style="font-size:.85rem">inclusief {{ rtrim(rtrim(number_format($estimate->vatPercentage, 2, ',', ''), '0'), ',') }}% btw</div>
                    <div style="margin-top:.35rem;font-variant-numeric:tabular-nums">{{ $range($estimate->exclMin, $estimate->exclMax) }} <span style="font-size:.85rem">exclusief btw</span></div>
                </div>

                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                        <tbody>
                            @foreach ($estimate->workLines as $line)
                                <tr>
                                    <td style="{{ $cell }}">{{ $line->label }}@if ($line->note)<div style="{{ $muted }};font-size:.8rem">{{ $line->note }}</div>@endif</td>
                                    <td style="{{ $num }}">{{ $range($line->min, $line->max) }}</td>
                                </tr>
                            @endforeach
                            @if (count($estimate->otherCostLines))
                                <tr><td colspan="2" style="padding:.75rem .5rem .25rem;font-weight:600">Overige kosten</td></tr>
                                @foreach ($estimate->otherCostLines as $line)
                                    <tr>
                                        <td style="{{ $cell }}">{{ $line->label }}@if ($line->note)<div style="{{ $muted }};font-size:.8rem">{{ $line->note }}</div>@endif</td>
                                        <td style="{{ $num }}">{{ $range($line->min, $line->max) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td style="{{ $cell }};font-weight:600">Totaal excl. btw (afgerond)</td>
                                <td style="{{ $num }};font-weight:600">{{ $range($estimate->exclMin, $estimate->exclMax) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endif
