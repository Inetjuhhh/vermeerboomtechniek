<x-layouts.site title="Bereken je offerte" description="Bereken in een paar stappen wat snoeien, rooien, planten of een boomrapport ongeveer kost. Een vrijblijvende indicatie, inclusief en exclusief btw.">
    <x-page-header eyebrow="Prijsindicatie" title="Wat gaat het ongeveer kosten?" intro="Beantwoord een paar vragen en zie direct een prijsindicatie. Wilt u een echte offerte? Stuur uw antwoorden mee, dan komt Bas vrijblijvend kijken." />

    <div class="py-12">
        <livewire:quote-calculator-form :dienst="request('dienst')" />
    </div>
</x-layouts.site>
