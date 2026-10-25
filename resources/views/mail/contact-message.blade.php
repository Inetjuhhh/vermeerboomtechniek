Nieuw bericht via vermeerboomtechniek.nl

Naam: {{ $contactMessage->name }}
E-mail: {{ $contactMessage->email }}
Telefoon: {{ $contactMessage->phone ?: '-' }}
Onderwerp: {{ $contactMessage->subject ?: '-' }}

{{ $contactMessage->message }}
@if ($summary = $contactMessage->calculator_summary)

--- Uit de offertecalculator ---
Dienst: {{ $summary['service'] }}
@if ($summary['trees'])
Aantal bomen: {{ $summary['trees'] }}
@endif
@foreach ($summary['answers'] as $question => $answer)
{{ $question }} {{ $answer }}
@endforeach
@foreach ($summary['estimates'] as $estimate)
Indicatie ({{ $estimate['name'] }}): {{ $estimate['incl'] }} incl. btw / {{ $estimate['excl'] }} excl. btw
@endforeach
@endif

Beantwoorden kan direct met "Antwoorden" in uw mailprogramma.
