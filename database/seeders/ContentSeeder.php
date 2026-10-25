<?php

namespace Database\Seeders;

use App\Models\CalculatorService;
use App\Models\Certificate;
use App\Models\Page;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Texts from the old WordPress site (export of 25-10-2026), with a few typos fixed.
 * Photos point to the temporary low-resolution copies in public/images/tijdelijk.
 * Only fills empty tables, so it never overwrites edits made in the admin.
 */
class ContentSeeder extends Seeder
{
    private const IMG = 'images/tijdelijk/fotos/';

    public function run(): void
    {
        $this->services();
        $this->pages();
        $this->reviews();
        $this->certificates();
    }

    private function services(): void
    {
        if (Service::query()->exists()) {
            return;
        }

        $calculator = fn (string $slug) => CalculatorService::where('slug', $slug)->value('id');

        $services = [
            [
                'title' => 'Snoeien', 'slug' => 'snoeien', 'part' => 'kroon', 'calc' => 'boom-snoeien',
                'summary' => 'Begeleidings- of onderhoudssnoei, klimmend of met hoogwerker.',
                'image' => 'bas-dag-2-33.jpg', 'alt' => 'Bas snoeit hoog in de kroon van een boom',
                'body' => '<p>Bomen snoeien zich van nature zelf, maar met huizen, wegen, wandelpaden en andere obstakels is het soms nodig om in te grijpen en snoeiwerkzaamheden toe te passen bij bomen.</p>'
                    . '<p>Naast advies over het snoeitype en het uitvoeren van de snoeiwerkzaamheden (zowel klimmend als met hoogwerker) kunnen wij uiteraard ook zorgen voor de afvoer van het restmateriaal.</p>',
            ],
            [
                'title' => 'Rooien', 'slug' => 'rooien', 'part' => 'kroon', 'calc' => 'boom-verwijderen',
                'summary' => 'Veilig en vakkundig, met aandacht voor omgeving en milieu.',
                'image' => 'bas-dag-1-100.jpg', 'alt' => 'Bas zaagt een gevelde stam in stukken',
                'body' => '<p>Indien er een boom moet worden verwijderd, kunnen wij voor u bomen rooien vanaf de grond, klimmend en met behulp van hoogwerker en/of telescoopkraan of verreiker.</p>'
                    . '<p>Welke rooitechniek wordt gebruikt is afhankelijk van het type boom, de omstandigheden waarin de boom verkeert, maar ook de locatie van de boom en de valmogelijkheden.</p>'
                    . '<p>Daarom komen wij altijd vooraf vrijblijvend kijken om een goede prijsinschatting van de velling te kunnen maken.</p>',
            ],
            [
                'title' => 'Planten', 'slug' => 'planten', 'part' => 'wortels', 'calc' => 'boom-planten',
                'summary' => 'Advies over planten en verplanten. We voeren het ook graag uit.',
                'image' => 'bas-dag-2-26.jpg', 'alt' => 'Bas in werkkleding in een tuin',
                'body' => '<p>Wilt u graag een nieuwe boom planten in uw tuin, of is er bomenadvies nodig in een groot project? Wij kunnen u advies geven over het planten en verplanten van bomen.</p>'
                    . '<p>En is het plan gemaakt? Dan kunnen wij de bomen voor u leveren en planten, inclusief goede bodemvoorzieningen om de boom optimaal te laten groeien.</p>',
            ],
            [
                'title' => 'Veiligheidscontrole', 'slug' => 'veiligheidscontrole', 'part' => 'stam', 'calc' => 'veiligheidscontrole',
                'summary' => 'Inspectie volgens VTA, met schriftelijk advies per boom.',
                'image' => '20211227-135926.jpg', 'alt' => 'Werkbus van Vermeer Boomtechniek bij een rij bomen',
                'body' => '<p>Met certificeringen als boomveiligheidscontroleur (BVC), boomveiligheidsinspecteur (BVI) en Data Inspecteur Bomen (DIB) kunnen wij voor u de veiligheid van uw bomen controleren en inspecteren.</p>'
                    . '<p>De controle gebeurt volgens de methode VTA (Visual Tree Assessment). Na de controle ontvangt u van ons een advies voor iedere boom, met eventuele gebreken en acties die u kunt of moet ondernemen als eigenaar van de boom.</p>',
            ],
            [
                'title' => 'Boomtechnisch advies', 'slug' => 'boomtechnisch-advies', 'part' => 'wortels', 'calc' => 'rapport-over-een-boom',
                'summary' => 'Onafhankelijk advies voor gezonde, duurzame bomen.',
                'image' => '20211227-132603.jpg', 'alt' => 'Grondmonsters naast een meetlint',
                'intro' => 'We gaan er vaak vanuit dat bomen altijd en overal maar groeien. Helaas is dat niet zo. Een boom groot laten worden is één ding, een boom ook gezond groot en oud laten worden is een tweede.',
                'body' => '<p>Of het nu gaat om het <strong>behouden en toekomstbestendig maken van bestaande bomen</strong> of om het <strong>aanplanten van nieuwe bomen</strong>, bijvoorbeeld in een nieuwe wijk of bij een vernieuwde weg: bomen groeien niet zomaar. Zeker in de stad is het belangrijk dat er een <strong>goed plan</strong> wordt gemaakt, zodat bomen een goede toekomst hebben en hun volle waarde kunnen laten zien. Investeren in een goed plan en advies voor bomen bespaart uiteindelijk kosten en is beter voor klimaat en omgeving.</p>'
                    . '<p>Vermeer Boomtechniek is gecertificeerd als <strong>European Tree Technician (ETT)</strong> en is een onafhankelijk boomtechnisch adviesbureau dat u kan ondersteunen en advies kan uitbrengen over de volgende onderwerpen:</p>'
                    . '<ul><li>Boom Effect Analyse (BEA)</li><li>Boomtechnisch onderzoek</li><li>Nader boomtechnisch onderzoek</li><li>Boomtechnisch advies bij het maken van plannen en ontwerpen</li><li>Advies over sortimentskeuze</li><li>Advies en toezicht bij werkzaamheden rond bomen</li><li>Boomtechnisch toezicht</li><li>Groeiplaatsadvies</li><li>Groeiplaatscontrole</li><li>Verplantbaarheidsonderzoek</li><li>Boomtechnisch advies bij het opstellen van bestekken</li></ul>',
            ],
            [
                'title' => 'Boom Effect Analyse', 'slug' => 'boom-effect-analyse', 'part' => 'wortels', 'calc' => 'boom-effect-analyse',
                'summary' => 'Kunnen de bomen bij een bouwproject duurzaam behouden blijven?',
                'image' => 'bas-dag-2-73.jpg', 'alt' => 'Bas legt een grondboring uit op een meetlint',
                'intro' => 'Goed inzicht in de effecten van een bouw- of reconstructieproject op de bomen.',
                'body' => '<p>Bas Vermeer is, naast ETW\'er, ook gecertificeerd ETT\'er en kan voor u een Boom Effect Analyse (BEA) uitvoeren.</p>'
                    . '<p>Als er ergens een <strong>bouw- of reconstructieproject</strong> start waar al bomen staan, kan dat nadelige gevolgen hebben voor de bomen in het projectgebied. Om goed inzicht te krijgen in de effecten van het project op de bomen, kan Vermeer Boomtechniek een BEA voor u uitvoeren.</p>'
                    . '<p>Ligt er een eerste ontwerp, dan kan worden getoetst of de plannen nadelige effecten op de bomen hebben, en of de bomen behouden kunnen blijven of dat het ontwerp moet worden aangepast. Het uitgangspunt van iedere BEA is:</p>'
                    . '<blockquote><p>Kunnen de bomen op hun huidige standplaats duurzaam behouden blijven?</p></blockquote>'
                    . '<p>Bij de BEA brengen we een of meerdere bomen in kaart en kijken we naar de conditie en vitaliteit, om een goede toekomstverwachting te bepalen. Daarnaast beoordelen we de effecten die de werkzaamheden op de bomen kunnen hebben.</p>'
                    . '<p>Als alle gegevens verzameld zijn en er een duidelijk beeld is van het project, de bomen en de effecten, kan worden besloten of de bomen op hun huidige standplaats behouden kunnen blijven. Dat besluit is altijd weloverwogen en in samenspraak met de opdrachtgever. Zijn er nog vragen, dan kan een uitgebreider onderzoek een nog duidelijker beeld geven.</p>'
                    . '<p>Aan de hand van de uitkomsten kan het definitieve ontwerp nog worden aangepast. Bij een BEA geven we ook altijd randvoorwaarden mee voor vóór, tijdens en na de werkzaamheden, om de bomen duurzaam te behouden.</p>'
                    . '<p>Alle gegevens, conclusies en adviezen ontvangt u in een <strong>duidelijke en overzichtelijke rapportage</strong>, op papier en digitaal. Daarbij gebruiken we onder andere de Richtlijn Bomen Effect Analyse van de Bomenstichting en CROW.</p>',
            ],
            [
                'title' => 'Boomverankering', 'slug' => 'boomverankering', 'part' => 'stam', 'calc' => 'kroonverankering',
                'summary' => 'Praktische oplossingen voor de stabiliteit en veiligheid van bomen.',
                'image' => 'bas-dag-2-63.jpg', 'alt' => 'Bas met helm in de kroon van een boom',
                // TODO: more text from Bas, the old site only had this one sentence
                'body' => '<p>Vermeer Boomtechniek levert praktische oplossingen voor boomverankering, waardoor de stabiliteit en veiligheid van bomen worden gegarandeerd.</p>',
            ],
            [
                'title' => 'Hulp bij stormschade', 'slug' => 'stormschade', 'part' => 'kroon', 'calc' => 'stormschade',
                'summary' => 'Snel advies en herstel na storm of zware wind.',
                'image' => 'bas-dag-1-63.jpg', 'alt' => 'Luchtfoto van een gevelde boom in een tuin',
                'body' => '<p>Bomen kunnen helaas bij storm of zware wind problemen veroorzaken. Takken of toppen kunnen uitbreken, de stam kan beschadigd raken of de complete boom kan omvallen.</p>'
                    . '<p>Stormschade is te voorkomen door tijdig een veiligheidscontrole (VTA) uit te laten voeren. Zo signaleren we zwakke punten op tijd en kunnen we daar actie op ondernemen.</p>'
                    . '<p>Is er toch stormschade? Neem dan contact met ons op om de mogelijkheden te bespreken.</p>',
            ],
        ];

        foreach ($services as $sort => $s) {
            Service::create([
                'title' => $s['title'],
                'slug' => $s['slug'],
                'summary' => $s['summary'],
                'tree_part' => $s['part'],
                'intro' => $s['intro'] ?? null,
                'body' => $s['body'],
                'image' => self::IMG . $s['image'],
                'image_alt' => $s['alt'],
                'calculator_service_id' => $calculator($s['calc']),
                'sort' => $sort,
            ]);
        }
    }

    private function pages(): void
    {
        Page::firstOrCreate(['slug' => 'het-bedrijf'], [
            'title' => 'Het bedrijf',
            'intro' => 'Vermeer Boomtechniek is in 2018 opgericht door Bas Vermeer. Ruim 15 jaar ervaring in het groen, een jarenlange passie voor bomen en alle benodigde papieren: dat is de basis waar Vermeer Boomtechniek voor staat.',
            'body' => '<p>In 2019 behaalde Bas zijn <strong>European Tree Worker-certificering (ETW)</strong> om deze passie en kwaliteit naar een hoger niveau te brengen. Inmiddels is er een grote basis aan ervaring met snoeien, rooien, planten en controleren.</p>'
                . '<p>In mei 2022 behaalde Bas de certificering <strong>European Tree Technician (ETT)</strong>, waarmee zijn theoretische kennis is uitgebreid op het gebied van onder meer boombeheer, beleid, nader onderzoek, Boom Effect Analyse (BEA) en het uitbrengen van boomtechnisch advies.</p>'
                . '<p>Aan de Van Hall Larenstein University of Applied Sciences in Velp rondde hij in 2021 de minor "Bomen in stedelijke omgeving" af.</p>'
                . '<p>Naast het uitvoeren van boomwerkzaamheden verzorgt Vermeer Boomtechniek, samen met verschillende instituten, trainingen in boomverzorging en machinegebruik.</p>'
                . '<h2>Kernwaarden</h2><p>In zijn manier van boomverzorging en boomtechnisch advies staan voor Bas vier kernwaarden centraal:</p>'
                . '<ul><li>Passie voor bomen</li><li>Veiligheid</li><li>Professioneel gereedschap</li><li>Vakkundig opgeleid</li></ul>',
            'image' => self::IMG . 'bas-dag-1-18.jpg',
            'image_alt' => 'Bas Vermeer lacht, met zijn klimuitrusting en zaag',
            'meta_description' => 'Vermeer Boomtechniek uit Wouw is in 2018 opgericht door Bas Vermeer, European Tree Worker en European Tree Technician.',
        ]);

        // Matches what the site really does: contact form, calculator answers, only necessary cookies,
        // messages removed after 12 months (ContactMessage is prunable, see routes/console.php).
        Page::firstOrCreate(['slug' => 'privacy'], [
            'title' => 'Privacyverklaring',
            'intro' => 'Hoe Vermeer Boomtechniek omgaat met de gegevens die u via deze website achterlaat. Kort gezegd: we gebruiken ze alleen om uw vraag te beantwoorden.',
            'body' => '<h2>Wie zijn wij?</h2>'
                . '<p>Vermeer Boomtechniek (Bas Vermeer), Omgang 16, Wouw, is verantwoordelijk voor de verwerking van persoonsgegevens via deze website. Vragen over privacy kunt u mailen naar <a href="mailto:info@vermeerboomtechniek.nl">info@vermeerboomtechniek.nl</a>.</p>'
                . '<h2>Welke gegevens verwerken we?</h2>'
                . '<p>Alleen de gegevens die u zelf invult in het contactformulier:</p>'
                . '<ul><li>naam en e-mailadres</li><li>telefoonnummer en onderwerp, als u die invult</li><li>uw bericht</li><li>uw antwoorden en de prijsindicatie uit de offertecalculator, als u die meestuurt</li></ul>'
                . '<p>De offertecalculator zelf slaat niets op. Pas als u op "Vraag een vrijblijvende offerte aan" klikt en daarna het contactformulier verstuurt, gaan uw antwoorden met uw bericht mee.</p>'
                . '<h2>Waarvoor gebruiken we ze?</h2>'
                . '<p>Om uw vraag te beantwoorden, een opname in te plannen en u een offerte te sturen. Dat doen we omdat u ons daar zelf om vraagt (artikel 6 lid 1 sub b AVG). We gebruiken uw gegevens niet voor nieuwsbrieven of reclame en verkopen ze nooit.</p>'
                . '<h2>Hoe lang bewaren we ze?</h2>'
                . '<p>Berichten via het contactformulier worden na 12 maanden automatisch verwijderd. Wordt u klant, dan bewaren we de gegevens die nodig zijn voor de offerte en de factuur zo lang als de wet voorschrijft (voor de administratie 7 jaar).</p>'
                . '<h2>Met wie delen we ze?</h2>'
                . '<p>Met niemand, behalve de partijen die nodig zijn om deze website en e-mail te laten werken: ons hostingbedrijf en onze e-mailprovider. Zij mogen uw gegevens alleen voor ons verwerken en niet voor zichzelf gebruiken.</p>'
                . '<h2>Cookies</h2>'
                . '<p>Deze website gebruikt alleen cookies die nodig zijn om de site te laten werken:</p>'
                . '<ul><li><strong>vermeer-boomtechniek-session</strong>: onthoudt tijdens uw bezoek bijvoorbeeld uw antwoorden uit de calculator. Verdwijnt na 2 uur.</li><li><strong>XSRF-TOKEN</strong>: beveiligt het contactformulier tegen misbruik. Verdwijnt na 2 uur.</li></ul>'
                . '<p>We gebruiken geen analytische of tracking cookies en geen advertenties. De lettertypes staan op onze eigen server. De video op de homepagina is een link naar YouTube: pas als u erop klikt, gaat u naar YouTube en gelden de regels van YouTube.</p>'
                . '<h2>Uw rechten</h2>'
                . '<p>U mag ons altijd vragen welke gegevens we van u hebben, en vragen om ze te verbeteren of te verwijderen. U kunt ook bezwaar maken tegen het gebruik ervan. Mail daarvoor naar <a href="mailto:info@vermeerboomtechniek.nl">info@vermeerboomtechniek.nl</a>; we reageren binnen een maand.</p>'
                . '<p>Bent u niet tevreden over hoe we met uw gegevens omgaan? Dan kunt u een klacht indienen bij de <a href="https://autoriteitpersoonsgegevens.nl" rel="noopener">Autoriteit Persoonsgegevens</a>.</p>'
                . '<h2>Beveiliging</h2>'
                . '<p>De website gebruikt een beveiligde verbinding (https). Alleen Vermeer Boomtechniek kan de berichten in het beheer lezen.</p>'
                . '<p><em>Laatst bijgewerkt: oktober 2026.</em></p>',
            'meta_description' => 'Hoe Vermeer Boomtechniek omgaat met uw gegevens: alleen voor het beantwoorden van uw vraag, geen tracking cookies.',
        ]);

        // Optional side gig: switch off with "Zichtbaar" in the admin, or delete the page.
        // Gallery photos are free-licence examples (CC BY 2.0, via Openverse/Flickr), not Bas's own work.
        $wood = 'images/tijdelijk/houtbewerking/';
        $flickr = fn (string $user, string $id) => "https://www.flickr.com/photos/$user/$id";
        Page::firstOrCreate(['slug' => 'houtbewerking'], [
            'title' => 'Houtbewerking met epoxy',
            'menu_label' => 'Houtbewerking',
            'show_in_menu' => true,
            'intro' => 'Een boom die weg moet, hoeft niet in de versnipperaar te verdwijnen. Van het hout maakt Bas unieke stukken voor in huis, gecombineerd met epoxy.',
            'body' => '<h2>Wat kan er?</h2>'
                . '<ul>'
                . '<li><strong>Bijzettafeltje</strong>: van een boomschijf of stamdeel, met een "rivier" of vulling van epoxy in de kleur die u kiest.</li>'
                . '<li><strong>Lamp, staand of hangend</strong>: een stamdeel als staande lamp, of een boomschijf als hanglamp boven de eettafel, met epoxy waar het licht doorheen schijnt.</li>'
                . '<li><strong>Borrelplank</strong>: een stevige plank met de natuurlijke rand van de boom, eventueel met een rand of inleg van epoxy.</li>'
                . '<li><strong>Iets anders?</strong> Een wandklok, onderzetters of een salontafel: vraag gerust wat er kan.</li>'
                . '</ul>'
                . '<h2>Van uw eigen boom</h2>'
                . '<p>Moet er bij u een boom weg? Dan kan Bas van dat hout iets maken, zodat de boom een herinnering blijft. Hout moet wel eerst goed drogen voordat het verwerkt kan worden. Dat kost tijd, soms maanden. Bas vertelt u vooraf wat er met uw hout kan en hoe lang het duurt.</p>'
                . '<h2>Wat kost het?</h2>'
                . '<p>Elk stuk is uniek en wordt op maat gemaakt. De prijs hangt af van het formaat, het hout en de hoeveelheid epoxy. Neem contact op, dan maken we samen een plan.</p>'
                . '<p><em>De foto\'s hieronder zijn voorbeelden van internet, om een indruk te geven van de mogelijkheden. Ze zijn niet door Vermeer Boomtechniek gemaakt.</em></p>',
            'image' => $wood . 'tafelblad-rond-epoxy.jpg',
            'image_alt' => 'Rond tafelblad van hout met epoxy',
            'gallery' => [
                ['image' => $wood . 'bijzettafels-epoxy.jpg', 'caption' => 'Bijzettafels met turquoise epoxy', 'credit' => 'IndoGemstone, CC BY 2.0', 'credit_url' => $flickr('47544036@N03', '16237958925')],
                ['image' => $wood . 'tafelblad-rond-epoxy.jpg', 'caption' => 'Rond tafelblad van teakwortel en epoxy', 'credit' => 'IndoGemstone, CC BY 2.0', 'credit_url' => $flickr('47544036@N03', '16237958475')],
                ['image' => $wood . 'borrelplank-boomschijf.jpg', 'caption' => 'Borrelplank met natuurlijke rand', 'credit' => 'Afzalive, CC BY 2.0', 'credit_url' => $flickr('34837514@N04', '34318059923')],
                ['image' => $wood . 'tafel-teak-epoxy.jpg', 'caption' => 'Tafel van teakwortel en epoxy', 'credit' => 'IndoGemstone, CC BY 2.0', 'credit_url' => $flickr('47544036@N03', '16237958795')],
                ['image' => $wood . 'serveerplanken.jpg', 'caption' => 'Serveerplanken', 'credit' => 'Tradlands, CC BY 2.0', 'credit_url' => $flickr('87743767@N07', '13043182645')],
            ],
            'meta_description' => 'Bijzettafels, lampen en borrelplanken van hout en epoxy, op maat gemaakt door Vermeer Boomtechniek. Ook van het hout van uw eigen boom.',
        ]);
    }

    private function reviews(): void
    {
        if (Review::query()->exists()) {
            return;
        }

        foreach ([
            'Bas heeft mijn oude eikenboom vakkundig gesnoeid en opnieuw tot leven gebracht. Hij werkt snel, efficiënt en met veel kennis. Zeer aan te raden!',
            'Het advies van Vermeer Boomtechniek over onze tuinbomen was onmisbaar. Hij is deskundig en vriendelijk, echt een topper!',
            'Na een storm heeft Bas ons snel en professioneel geholpen met het verwijderen van omgevallen bomen.',
        ] as $sort => $quote) {
            Review::create(['quote' => $quote, 'sort' => $sort]);
        }
    }

    private function certificates(): void
    {
        if (Certificate::query()->exists()) {
            return;
        }

        $logo = 'images/tijdelijk/logos/';

        foreach ([
            ['European Tree Technician', 'ETT', 2022, $logo . 'ett.png', true],
            ['European Tree Worker', 'ETW', 2019, $logo . 'etw.png', true],
            ['Data Inspecteur Bomen - veiligheid en onderhoud', 'DIBvo', null, null, false],
            ['Directievoering en toezicht met UAV', null, null, null, false],
            ['Boomveiligheidsinspecteur', 'BVI', null, $logo . 'bvc.png', false],
            ['Wet natuurbescherming niveau 3', null, null, null, false],
            ['Specialist veteraan boombeheer', null, null, null, false],
            ['BHV, VCA-VOL en veilig werken langs de weg', 'VCA-VOL', null, $logo . 'vca-vol.jpg', false],
            ['European Chainsaw Certification 1 & 2', 'ECC', null, null, false],
        ] as $sort => [$name, $abbr, $year, $file, $timeline]) {
            Certificate::create([
                'name' => $name, 'abbreviation' => $abbr, 'year' => $year,
                'logo' => $file, 'show_in_timeline' => $timeline, 'sort' => $sort,
            ]);
        }
    }
}
