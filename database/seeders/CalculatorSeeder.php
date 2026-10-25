<?php

namespace Database\Seeders;

use App\Enums\OptionEffect;
use App\Models\CalculatorOtherCost;
use App\Models\CalculatorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starting data for the quote calculator. All amounts are EXAMPLE prices (excl. btw),
 * Bas replaces them in the admin. Run again with: php artisan db:seed --class=CalculatorSeeder
 * (only fills an empty calculator, so it never overwrites real prices).
 */
class CalculatorSeeder extends Seeder
{
    public function run(): void
    {
        if (CalculatorService::query()->exists()) {
            $this->command?->warn('De calculator heeft al diensten, er is niets toegevoegd.');

            return;
        }

        $height = fn (array $amounts) => ['Hoe hoog is de boom ongeveer?', 'Een verdieping van een huis is ongeveer 3 meter.', [
            ['Tot 6 m', OptionEffect::BasePerTree, $amounts[0]],
            ['6 tot 12 m', OptionEffect::BasePerTree, $amounts[1], 'default'],
            ['12 tot 20 m', OptionEffect::BasePerTree, $amounts[2]],
            ['Boven 20 m', OptionEffect::BasePerTree, $amounts[3]],
        ]];
        $access = ['Hoe is de boom bereikbaar?', null, [
            ['Direct vanaf straat of oprit', OptionEffect::Factor, 1, 'default'],
            ['Via de tuin of een poort', OptionEffect::Factor, 1.15],
            ['Alleen door het huis', OptionEffect::Factor, 1.35],
        ]];
        $removal = ['Moet het hout of snoeiafval mee?', null, [
            ['Ja, alles afvoeren', OptionEffect::Factor, 1.25, 'default'],
            ['Nee, mag blijven liggen', OptionEffect::None],
        ]];
        $further = ['Is nader onderzoek nodig?', 'Bijvoorbeeld meten met een resistograaf als er twijfel is over rot in de stam.', [
            ['Nee of weet ik niet', OptionEffect::None, null, 'default'],
            ['Ja, nader onderzoek', OptionEffect::SurchargeFixed, [150, 400]],
        ]];

        $services = [
            [
                'name' => 'Boom verwijderen', 'description' => 'Rooien of vellen, met of zonder stobbe',
                'models' => [['name' => 'Prijs per boom']],
                'questions' => [
                    $height([[150, 300], [350, 700], [800, 1500], [1500, 3000]]),
                    ['Hoe dik is de stam?', 'Gemeten op 1,30 m hoogte, zoals boomverzorgers dat doen. Een omtrek van 1 meter is ongeveer 32 cm doorsnede.', [
                        ['Tot 30 cm', OptionEffect::Factor, 0.9],
                        ['30 tot 60 cm', OptionEffect::Factor, 1, 'default'],
                        ['60 tot 90 cm', OptionEffect::Factor, 1.25],
                        ['Meer dan 90 cm', OptionEffect::Factor, 1.5],
                    ]],
                    ['Kan de boom in één keer vrij vallen?', 'Is er genoeg ruimte (minstens de hoogte van de boom) zonder huis, schuur, kabels of weg?', [
                        ['Ja, er is ruimte genoeg', OptionEffect::Factor, 0.8],
                        ['Deels, eerst wat takken eraf', OptionEffect::Factor, 1, 'default'],
                        ['Nee, moet in delen naar beneden', OptionEffect::Factor, 1.4],
                    ]],
                    $access,
                    ['Moet de stobbe (stronk) weg?', null, [
                        ['Nee', OptionEffect::None, null, 'default'],
                        ['Ja, wegfrezen', OptionEffect::SurchargePerTree, [75, 200]],
                    ]],
                    $removal,
                ],
            ],
            [
                'name' => 'Boom snoeien', 'description' => 'Begeleidings- of onderhoudssnoei',
                'models' => [['name' => 'Prijs per boom']],
                'questions' => [
                    $height([[75, 150], [150, 350], [350, 750], [700, 1400]]),
                    ['Wat voor snoei is nodig?', null, [
                        ['Jonge boom in vorm brengen', OptionEffect::Factor, 0.7],
                        ['Alleen dood hout eruit', OptionEffect::Factor, 0.8],
                        ['Onderhoudssnoei', OptionEffect::Factor, 1, 'default'],
                        ['Kroon flink innemen', OptionEffect::Factor, 1.3],
                    ]],
                    $access,
                    $removal,
                ],
            ],
            [
                'name' => 'Boom planten', 'description' => 'Leveren en planten, met goede bodemvoorzieningen',
                'models' => [['name' => 'Prijs per boom']],
                'questions' => [
                    ['Hoe groot moet de nieuwe boom zijn?', 'Kwekers meten de stamomtrek op 1 meter hoogte. Maat 10-12 is een jonge boom van ongeveer 3 meter.', [
                        ['Klein (stamomtrek 8 tot 12 cm)', OptionEffect::BasePerTree, [175, 300], 'default'],
                        ['Middel (12 tot 20 cm)', OptionEffect::BasePerTree, [300, 550]],
                        ['Groot (20 tot 30 cm)', OptionEffect::BasePerTree, [550, 1100]],
                    ]],
                    ['Wie levert de boom?', null, [
                        ['Vermeer Boomtechniek levert de boom', OptionEffect::None, null, 'default'],
                        ['Ik heb de boom zelf al', OptionEffect::Factor, 0.45],
                    ]],
                    ['Verankering en bodem', null, [
                        ['Ondergrondse verankering en bodemverbetering', OptionEffect::SurchargePerTree, [75, 150], 'default'],
                        ['Alleen boompalen', OptionEffect::SurchargePerTree, [30, 60]],
                        ['Niets extra', OptionEffect::None],
                    ]],
                ],
            ],
            [
                'name' => 'Kroonverankering', 'description' => 'Zwakke takken of stammen met elkaar verbinden',
                'models' => [['name' => 'Prijs per verankering']],
                'questions' => [
                    ['Hoeveel verbindingen zijn er nodig?', 'Bas kijkt dit altijd na bij de opname. Kies wat je verwacht.', [
                        ['1 verbinding', OptionEffect::BaseFixed, [250, 400], 'default'],
                        ['2 verbindingen', OptionEffect::BaseFixed, [400, 650]],
                        ['3 of meer', OptionEffect::BaseFixed, [600, 1000]],
                    ]],
                    ['Hoe hoog is de boom ongeveer?', null, [
                        ['Tot 12 m', OptionEffect::Factor, 1, 'default'],
                        ['12 tot 20 m', OptionEffect::Factor, 1.2],
                        ['Boven 20 m', OptionEffect::Factor, 1.4],
                    ]],
                    ['Is de boom al op veiligheid gecontroleerd?', 'Voor een verankering moet duidelijk zijn dat de boom het waard is en het houdt.', [
                        ['Ja, er is een recente controle', OptionEffect::None],
                        ['Nee of weet ik niet', OptionEffect::SurchargeFixed, [95, 95], 'default'],
                    ]],
                ],
                'asks_tree_count' => false,
            ],
            [
                'name' => 'Veiligheidscontrole', 'description' => 'VTA-controle met schriftelijk advies',
                'models' => [['name' => 'Opname + per boom', 'base' => [95, 95], 'per_tree' => [35, 55]]],
                'questions' => [$further],
            ],
            [
                'name' => 'Boom Effect Analyse', 'description' => 'BEA bij bouw of verbouwing',
                // Three methods side by side, so Ine and Bas can choose which one fits best
                'models' => [
                    ['name' => 'Vaste prijs + per boom', 'description' => 'Een vast bedrag voor het rapport en een bedrag per boom.', 'base' => [450, 650], 'per_tree' => [25, 45]],
                    ['name' => 'Op uurbasis', 'description' => 'Uurtarief voor opname, uitwerking en rapport.', 'hourly_rate' => 85, 'hours' => [5, 8], 'hours_per_tree' => [0.25, 0.5]],
                    ['name' => 'Vaste projectprijs', 'description' => 'Eén prijs per project, ongeacht het aantal bomen.', 'base' => [750, 1250]],
                ],
                'questions' => [
                    ['Om wat voor project gaat het?', null, [
                        ['Verbouwing of aanbouw bij een woning', OptionEffect::Factor, 1, 'default'],
                        ['Bouw- of reconstructieproject', OptionEffect::Factor, 1.5],
                    ]],
                    $further,
                ],
            ],
            [
                'name' => 'Rapport over een boom', 'description' => 'Boomtechnisch advies of onderbouwing kapvergunning',
                'models' => [['name' => 'Prijs per rapport', 'per_tree' => [20, 40]]],
                'questions' => [
                    ['Wat voor rapport heb je nodig?', null, [
                        ['Onderbouwing kapvergunning', OptionEffect::BaseFixed, [250, 400], 'default'],
                        ['Boomtechnisch advies', OptionEffect::BaseFixed, [350, 650]],
                        ['Waardebepaling van een boom', OptionEffect::BaseFixed, [300, 500]],
                    ]],
                ],
            ],
            [
                'name' => 'Stormschade', 'description' => 'Spoed: boom of tak gevallen',
                'contact_only' => true,
                'contact_message' => 'Bij stormschade geven we geen indicatie. Elke situatie is anders en vaak is er haast. Bel direct, dan kijken we samen wat er nodig is.',
            ],
        ];

        foreach ($services as $sort => $data) {
            $service = CalculatorService::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'asks_tree_count' => $data['asks_tree_count'] ?? ! ($data['contact_only'] ?? false),
                'contact_only' => $data['contact_only'] ?? false,
                'contact_message' => $data['contact_message'] ?? null,
                'sort' => $sort,
            ]);

            foreach ($data['models'] ?? [] as $i => $m) {
                $service->priceModels()->create([
                    'name' => $m['name'],
                    'description' => $m['description'] ?? null,
                    'base_min' => $m['base'][0] ?? 0, 'base_max' => $m['base'][1] ?? 0,
                    'per_tree_min' => $m['per_tree'][0] ?? 0, 'per_tree_max' => $m['per_tree'][1] ?? 0,
                    'hourly_rate' => $m['hourly_rate'] ?? 0,
                    'hours_min' => $m['hours'][0] ?? 0, 'hours_max' => $m['hours'][1] ?? 0,
                    'hours_per_tree_min' => $m['hours_per_tree'][0] ?? 0, 'hours_per_tree_max' => $m['hours_per_tree'][1] ?? 0,
                    'sort' => $i,
                ]);
            }

            foreach ($data['questions'] ?? [] as $q => [$label, $hint, $options]) {
                $question = $service->questions()->create(['label' => $label, 'hint' => $hint, 'sort' => $q]);

                foreach ($options as $o => $option) {
                    [$optionLabel, $effect] = $option;
                    $value = $option[2] ?? null;
                    $question->options()->create([
                        'label' => $optionLabel,
                        'effect' => $effect,
                        'factor' => $effect === OptionEffect::Factor ? $value : null,
                        'amount_min' => is_array($value) ? $value[0] : null,
                        'amount_max' => is_array($value) ? $value[1] : null,
                        'is_default' => ($option[3] ?? null) === 'default',
                        'sort' => $o,
                    ]);
                }
            }
        }

        CalculatorOtherCost::create([
            'name' => 'Voorrijkosten',
            'description' => 'Afhankelijk van de afstand vanaf Wouw. Binnen 15 km rekenen we niets.',
            'amount_min' => 0, 'amount_max' => 60,
        ]);
    }
}
