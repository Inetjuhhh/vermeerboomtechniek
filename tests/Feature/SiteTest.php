<?php

namespace Tests\Feature;

use App\Livewire\QuoteCalculatorForm;
use App\Mail\NewContactMessage;
use App\Models\CalculatorService;
use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\CalculatorSeeder;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CalculatorSeeder::class, ContentSeeder::class]);
    }

    public function test_public_pages_load_with_their_content(): void
    {
        $this->get('/')->assertOk()->assertSee('vakkundig')->assertSee('Boom Effect Analyse')->assertSee('2022')
            ->assertSeeInOrder(['In de kroon', 'Snoeien', 'Aan de stam', 'Veiligheidscontrole', 'Onder de grond', 'Planten']);
        $this->get('/diensten')->assertOk()->assertSee('Hulp bij stormschade');
        $this->get('/diensten/boom-effect-analyse')->assertOk()->assertSee('Richtlijn Bomen Effect Analyse')->assertSee('Bereken je prijs');
        $this->get('/diensten/stormschade')->assertOk()->assertDontSee('Bereken je prijs');
        $this->get('/het-bedrijf')->assertOk()->assertSee('Kernwaarden')->assertSee('European Chainsaw Certification');
        $this->get('/contact')->assertOk()->assertSee('Verstuur bericht');
        $this->get('/bereken-je-offerte')->assertOk()->assertSee('Wat moet er gebeuren?');
    }

    public function test_unknown_pages_and_inactive_services_give_404(): void
    {
        $this->get('/bestaat-niet')->assertNotFound();
        \App\Models\Service::where('slug', 'planten')->update(['is_active' => false]);
        $this->get('/diensten/planten')->assertNotFound();
    }

    public function test_old_wordpress_addresses_redirect(): void
    {
        $this->get('/aanbod-boomverzorging')->assertRedirect('/diensten')->assertStatus(301);
        $this->get('/advies')->assertRedirect('/diensten/boomtechnisch-advies');
        $this->get('/boom-effect-analyse')->assertRedirect('/diensten/boom-effect-analyse');
    }

    public function test_contact_form_saves_and_mails_the_message(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Jan de Vries', 'email' => 'jan@example.com', 'phone' => '0612345678',
            'subject' => 'Eik snoeien', 'message' => 'Kunt u eens komen kijken?',
        ])->assertRedirect('/contact')->assertSessionHas('sent');

        $this->assertDatabaseHas('contact_messages', ['email' => 'jan@example.com', 'subject' => 'Eik snoeien']);
        Mail::assertSent(NewContactMessage::class, fn ($mail) => $mail->hasTo('info@vermeerboomtechniek.nl') && $mail->hasReplyTo('jan@example.com'));
    }

    public function test_contact_form_shows_dutch_errors(): void
    {
        $this->post('/contact', ['email' => 'geen-adres'])
            ->assertSessionHasErrors(['name' => 'Vul uw naam in.', 'message' => 'Schrijf kort waar we u mee kunnen helpen.']);
    }

    public function test_spam_trap_saves_nothing(): void
    {
        Mail::fake();

        $this->post('/contact', ['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Spam', 'website' => 'http://spam.example'])
            ->assertRedirect('/contact');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_calculator_answers_travel_with_the_contact_message(): void
    {
        Mail::fake();

        Livewire::test(QuoteCalculatorForm::class, ['dienst' => 'boom-effect-analyse'])
            ->assertSee('Op uurbasis')
            ->assertSee('inclusief 21% btw')
            ->call('requestQuote')
            ->assertRedirect(route('contact', ['onderwerp' => 'Offerte boom effect analyse']));

        $this->get('/contact')->assertSee('Uw antwoorden uit de calculator')->assertSee('Vaste projectprijs');

        $this->post('/contact', ['name' => 'Gemeente', 'email' => 'bomen@example.com', 'message' => 'Graag een offerte'])->assertRedirect('/contact');

        $summary = ContactMessage::first()->calculator_summary;
        $this->assertSame('Boom Effect Analyse', $summary['service']);
        $this->assertCount(3, $summary['estimates']);
        $this->assertNull(session('calculator_summary'));
    }

    public function test_calculator_switches_service_and_resets_answers(): void
    {
        $storm = CalculatorService::where('slug', 'stormschade')->first();

        Livewire::test(QuoteCalculatorForm::class)
            ->set('serviceId', $storm->id)
            ->assertSee('Bij stormschade geven we geen indicatie')
            ->assertSet('answers', []);
    }

    public function test_admin_content_pages_load(): void
    {
        config(['site.admin_emails' => ['admin@example.com']]);
        $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));

        foreach (['/admin/berichten', '/admin/website/diensten', '/admin/website/diensten/snoeien/edit', '/admin/website/paginas/het-bedrijf/edit',
            '/admin/website/reviews', '/admin/website/certificaten', '/admin/website/instellingen'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
