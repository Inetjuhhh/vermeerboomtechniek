<x-layouts.site title="Diensten" description="Snoeien, rooien, planten, veiligheidscontrole, boomtechnisch advies, Boom Effect Analyse, boomverankering en hulp bij stormschade.">
    <x-page-header eyebrow="Aanbod boomverzorging" title="Diensten" :image="asset('images/tijdelijk/fotos/bas-dag-1-34.jpg')" position="50% 35%"
        intro="Van snoeien en rooien tot een onafhankelijk boomtechnisch advies. Kies waar uw vraag over gaat: de kroon, de stam of wat er onder de grond gebeurt." />

    <x-tree-rows :services="$services" />

    <x-contact-band title="Meer weten?" />
</x-layouts.site>
