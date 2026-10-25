<?php

namespace Tests\Feature;

use App\Filament\Pages\CalculatorPreview;
use App\Filament\Pages\CalculatorSettings;
use App\Filament\Resources\CalculatorServiceResource\Pages\EditCalculatorService;
use App\Models\CalculatorOption;
use App\Models\CalculatorService;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\CalculatorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CalculatorAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CalculatorSeeder::class);
        $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
        config(['site.admin_emails' => ['admin@example.com']]);
    }

    public function test_admin_pages_load(): void
    {
        $bea = CalculatorService::where('slug', 'boom-effect-analyse')->first();

        foreach (['/admin/calculator/testen', '/admin/calculator/diensten', "/admin/calculator/diensten/{$bea->id}/edit",
            '/admin/calculator/diensten/create', '/admin/calculator/overige-kosten', '/admin/calculator/instellingen'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_others_cannot_log_in_outside_local(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'someone@example.com']));

        $this->get('/admin/calculator/diensten')->assertForbidden();
    }

    public function test_saving_a_service_keeps_models_questions_and_options(): void
    {
        $service = CalculatorService::where('slug', 'boom-verwijderen')->first();
        $options = CalculatorOption::count();

        Livewire::test(EditCalculatorService::class, ['record' => $service->getRouteKey()])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($options, CalculatorOption::count());
        $this->assertSame(6, $service->questions()->count());
    }

    public function test_max_may_not_be_below_min(): void
    {
        $service = CalculatorService::where('slug', 'veiligheidscontrole')->first();
        $component = Livewire::test(EditCalculatorService::class, ['record' => $service->getRouteKey()]);
        $key = array_key_first($component->get('data.priceModels'));

        $component->set("data.priceModels.$key.base_max", 10)
            ->call('save')
            ->assertHasFormErrors(["priceModels.$key.base_max"]);
    }

    public function test_settings_are_saved(): void
    {
        Livewire::test(CalculatorSettings::class)
            ->set('data.volume_discount_percentage', 15)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(15, Setting::get('calculator.volume_discount_percentage'));
    }

    public function test_preview_shows_all_three_bea_estimates(): void
    {
        $bea = CalculatorService::where('slug', 'boom-effect-analyse')->first();

        Livewire::test(CalculatorPreview::class)
            ->set('data.service', $bea->id)
            ->assertSee('Vaste prijs + per boom')
            ->assertSee('Op uurbasis')
            ->assertSee('Vaste projectprijs')
            ->assertSee('inclusief 21% btw')
            ->assertSee('exclusief btw');
    }
}
