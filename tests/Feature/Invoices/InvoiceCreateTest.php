<?php

namespace Tests\Feature\Invoices;

use App\Enums\CategoryEnum;
use App\Enums\CurrencyEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Livewire\Pages\Invoices\Create;
use App\Models\Family;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\User;
use App\Services\FileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Mockery\MockInterface;
use Tests\TestCase;

class InvoiceCreateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected MockInterface $fileStorageService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->fileStorageService = $this->mock(FileStorageService::class);
        $this->fileStorageService->shouldReceive('getFileInfo')->andReturn([
            'name' => 'test-invoice.pdf',
            'extension' => 'pdf',
            'size' => 1024,
            'sizeFormatted' => '1 KB',
            'isImage' => false,
            'isPdf' => true,
            'isDocx' => false,
            'isCsv' => false,
            'status' => 'success',
            'statusMessage' => 'Import du fichier validé',
        ]);
    }

    public function test_invoice_is_persisted_in_database_after_creation(): void
    {
        // Utiliser une vraie instance de FileStorageService au lieu d'un mock
        $this->app->instance(FileStorageService::class, new FileStorageService);

        $file = UploadedFile::fake()->create('test-invoice.pdf', 1024);

        // Agir directement comme un utilisateur réel
        Livewire::actingAs($this->user)
            ->test(Create::class)
            ->set('form.uploadedFile', $file)
            ->set('form.name', 'Integration Test Invoice')
            ->set('form.amount', 100)
            ->call('createInvoice');

        $this->assertDatabaseHas('invoices', [
            'name' => 'Integration Test Invoice',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_fill_all_invoice_fields_and_submit_the_form(): void
    {
        $file = UploadedFile::fake()->create('test-invoice.pdf', 1024);

        $this->fileStorageService->shouldReceive('processInvoiceFile')
            ->andReturn(new InvoiceFile([
                'file_path' => 'invoices/test-invoice.pdf',
                'file_name' => 'test-invoice.pdf',
                'file_extension' => 'pdf',
                'file_size' => 1024,
                'is_primary' => true,
            ]));

        $this->fileStorageService->shouldReceive('store')->andReturn(
            new Invoice([
                'id' => 1,
                'name' => 'Facture Internet - Avril 2025',
                'user_id' => $this->user->id,
            ])
        );

        $component = Livewire::test(Create::class)
            ->set('form.uploadedFile', $file)
            ->set('form.name', 'Facture Internet - Avril 2025')
            ->set('form.reference', 'INV-12345')
            ->set('form.type', TypeEnum::ABONNEMENTS->value)
            ->set('form.availableCategories', [
                CategoryEnum::ABO_INTERNET_TELECOM->value => CategoryEnum::ABO_INTERNET_TELECOM->labelWithEmoji(),
                CategoryEnum::ABO_STREAMING_VIDEO->value => CategoryEnum::ABO_STREAMING_VIDEO->labelWithEmoji(),
            ])
            ->set('form.category', CategoryEnum::ABO_INTERNET_TELECOM->value)
            ->set('form.issuer_name', 'Fournisseur Internet')
            ->set('form.issuer_website', 'https://fournisseur-internet.com')
            ->set('form.amount', 59.99)
            ->set('form.currency', CurrencyEnum::EUR->value)
            ->set('form.paid_by_user_id', $this->user->id)
            ->set('form.user_shares', [
                [
                    'id' => $this->user->id,
                    'amount' => 59.99,
                    'percentage' => 100,
                ],
            ])
            ->set('form.issued_date', '2025-04-10')
            ->set('form.payment_due_date', '2025-04-25')
            ->set('form.payment_reminder', '2025-04-20')
            ->set('form.payment_frequency', PaymentFrequencyEnum::Monthly->value)
            ->set('form.payment_status', PaymentStatusEnum::Unpaid->value)
            ->set('form.payment_method', PaymentMethodEnum::DirectDebit->value)
            ->set('form.priority', PriorityEnum::Medium->value)
            ->set('form.notes', 'Facture mensuelle pour la connexion internet fibre')
            ->set('form.tags', ['internet', 'mensuel', 'abonnement'])
            ->call('createInvoice');

        $component->assertHasNoErrors();

        $invoice = Invoice::where('name', 'Facture Internet - Avril 2025')
            ->where('reference', 'INV-12345')
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals('Facture Internet - Avril 2025', $invoice->name);
        $this->assertEquals('INV-12345', $invoice->reference);
        $this->assertEquals(TypeEnum::ABONNEMENTS->value, $invoice->type->value);
        $this->assertEquals(CategoryEnum::ABO_INTERNET_TELECOM->value, $invoice->category->value);
        $this->assertEquals('Fournisseur Internet', $invoice->issuer_name);
        $this->assertEquals('https://fournisseur-internet.com', $invoice->issuer_website);
        $this->assertEquals(59.99, (float) $invoice->amount);
        $this->assertEquals(CurrencyEnum::EUR->value, $invoice->currency);
        $this->assertEquals($this->user->id, $invoice->paid_by_user_id);
        $this->assertEquals(PaymentFrequencyEnum::Monthly->value, $invoice->payment_frequency->value);
        $this->assertEquals(PaymentStatusEnum::Unpaid->value, $invoice->payment_status->value);
        $this->assertEquals(PaymentMethodEnum::DirectDebit->value, $invoice->payment_method->value);
        $this->assertEquals(PriorityEnum::Medium->value, $invoice->priority->value);
        $this->assertEquals('Facture mensuelle pour la connexion internet fibre', $invoice->notes);
        $this->assertEquals($this->user->id, $invoice->user_id);
        $this->assertEquals('2025-04-10', $invoice->issued_date->format('Y-m-d'));
        $this->assertEquals('2025-04-25', $invoice->payment_due_date->format('Y-m-d'));
        $this->assertEquals('2025-04-20', $invoice->payment_reminder->format('Y-m-d'));
        $this->assertTrue(in_array('internet', $invoice->tags));
        $this->assertTrue(in_array('mensuel', $invoice->tags));
        $this->assertTrue(in_array('abonnement', $invoice->tags));
    }

    public function test_validates_required_fields_before_submission(): void
    {
        Livewire::test(Create::class)
            ->call('createInvoice')
            ->assertHasErrors(['form.uploadedFile', 'form.name', 'form.amount']);
    }

    public function test_updates_available_categories_when_type_is_selected(): void
    {
        $component = Livewire::test(Create::class);

        $this->assertEquals([], $component->get('form.availableCategories'));

        $component->set('form.type', TypeEnum::ABONNEMENTS->value);
        $availableCategories = $component->get('form.availableCategories');

        $this->assertNotEmpty($availableCategories);
        $this->assertArrayHasKey(CategoryEnum::ABO_INTERNET_TELECOM->value, $availableCategories);
        $this->assertArrayHasKey(CategoryEnum::ABO_STREAMING_VIDEO->value, $availableCategories);
        $this->assertArrayHasKey(CategoryEnum::ABO_STREAMING_MUSIQUE->value, $availableCategories);
    }

    public function test_can_distribute_shares_evenly_between_family_members(): void
    {
        $family = Family::factory()->create();
        $familyMember = User::factory()->create();

        $family->users()->attach([$this->user->id, $familyMember->id]);
        $this->user->refresh();
        $familyMember->refresh();

        $component = Livewire::actingAs($this->user)->test(Create::class)
            ->set('form.amount', 100)
            ->set('form.paid_by_user_id', $this->user->id)
            ->set('form.family_id', $family->id)
            ->set('family_members', collect([$this->user, $familyMember]))
            ->call('distributeEvenly', [$this->user->id, $familyMember->id]);

        $userShares = $component->get('form.user_shares');
        $this->assertCount(2, $userShares);

        $userShare = collect($userShares)->firstWhere('id', $this->user->id);
        $memberShare = collect($userShares)->firstWhere('id', $familyMember->id);

        $this->assertGreaterThanOrEqual(49.9, (float) $userShare['percentage']);
        $this->assertLessThanOrEqual(50.1, (float) $userShare['percentage']);
        $this->assertGreaterThanOrEqual(49.9, (float) $userShare['amount']);
        $this->assertLessThanOrEqual(50.1, (float) $userShare['amount']);
        $this->assertGreaterThanOrEqual(49.9, (float) $memberShare['percentage']);
        $this->assertLessThanOrEqual(50.1, (float) $memberShare['percentage']);
        $this->assertGreaterThanOrEqual(49.9, (float) $memberShare['amount']);
        $this->assertLessThanOrEqual(50.1, (float) $memberShare['amount']);
    }

    public function test_can_remove_an_uploaded_file(): void
    {
        $file = UploadedFile::fake()->create('test-invoice.pdf', 1024);

        $component = Livewire::test(Create::class)
            ->set('form.uploadedFile', $file);

        $this->assertNotNull($component->get('form.uploadedFile'));

        $component->call('removeUploadedFile');

        $this->assertNull($component->get('form.uploadedFile'));
        $this->assertNull($component->get('form.existingFilePath'));
        $this->assertFalse($component->get('showOcrButton'));
    }

    public function test_switches_oc_r_button_visibility_when_file_is_uploaded(): void
    {
        $component = Livewire::test(Create::class);
        $this->assertFalse($component->get('showOcrButton'));

        $file = UploadedFile::fake()->create('test-invoice.pdf', 1024);
        $component->set('form.uploadedFile', $file);

        $this->assertTrue($component->get('showOcrButton'));
    }

    public function test_can_add_and_remove_tags(): void
    {
        $component = Livewire::test(Create::class);
        $this->assertEquals([], $component->get('form.tags'));

        $component->set('form.tags', ['internet'])
            ->set('form.tags', ['internet', 'facture']);

        $this->assertEquals(['internet', 'facture'], $component->get('form.tags'));

        $component->set('form.tags', ['facture']);

        $this->assertEquals(['facture'], $component->get('form.tags'));
        $this->assertNotContains('internet', $component->get('form.tags'));
    }

    public function test_calculates_remaining_shares_correctly(): void
    {
        $component = Livewire::test(Create::class)
            ->set('form.amount', 100)
            ->set('form.user_shares', [
                [
                    'id' => $this->user->id,
                    'amount' => 30,
                    'percentage' => 30,
                ],
            ])
            ->call('calculateRemainingShares');

        $this->assertGreaterThanOrEqual(69.9, $component->get('remainingAmount'));
        $this->assertLessThanOrEqual(70.1, $component->get('remainingAmount'));
        $this->assertGreaterThanOrEqual(69.9, $component->get('remainingPercentage'));
        $this->assertLessThanOrEqual(70.1, $component->get('remainingPercentage'));

        $component->set('form.user_shares', [
            [
                'id' => $this->user->id,
                'amount' => 30,
                'percentage' => 30,
            ],
            [
                'id' => 999,
                'amount' => 40,
                'percentage' => 40,
            ],
        ])->call('calculateRemainingShares');

        $this->assertGreaterThanOrEqual(30.0, $component->get('remainingAmount'));
        $this->assertLessThanOrEqual(30.1, $component->get('remainingAmount'));
        $this->assertGreaterThanOrEqual(30.0, $component->get('remainingPercentage'));
        $this->assertLessThanOrEqual(30.1, $component->get('remainingPercentage'));
    }

    public function test_can_update_individual_user_share(): void
    {
        $familyMember = User::factory()->create();

        $component = Livewire::test(Create::class)
            ->set('form.amount', 100)
            ->set('form.user_shares', [])
            ->set('family_members', collect([$this->user, $familyMember]))
            ->call('updateShare', $this->user->id, 30, 'percentage');

        $userShares = $component->get('form.user_shares');
        $this->assertCount(1, $userShares);

        $userShare = collect($userShares)->firstWhere('id', $this->user->id);
        $this->assertGreaterThanOrEqual(29.9, (float) $userShare['percentage']);
        $this->assertLessThanOrEqual(30.1, (float) $userShare['percentage']);
        $this->assertGreaterThanOrEqual(29.9, (float) $userShare['amount']);
        $this->assertLessThanOrEqual(30.1, (float) $userShare['amount']);

        $component->call('updateShare', $this->user->id, 50, 'amount');
        $userShares = $component->get('form.user_shares');
        $userShare = collect($userShares)->firstWhere('id', $this->user->id);

        $this->assertGreaterThanOrEqual(49.9, (float) $userShare['amount']);
        $this->assertLessThanOrEqual(50.1, (float) $userShare['amount']);
        $this->assertGreaterThanOrEqual(49.9, (float) $userShare['percentage']);
        $this->assertLessThanOrEqual(50.1, (float) $userShare['percentage']);
        $this->assertGreaterThanOrEqual(50.0, $component->get('remainingAmount'));
        $this->assertLessThanOrEqual(50.1, $component->get('remainingAmount'));
        $this->assertGreaterThanOrEqual(50.0, $component->get('remainingPercentage'));
        $this->assertLessThanOrEqual(50.1, $component->get('remainingPercentage'));
    }

    public function test_can_remove_user_share(): void
    {
        $familyMember = User::factory()->create();

        $component = Livewire::test(Create::class)
            ->set('form.amount', 100)
            ->set('form.user_shares', [
                [
                    'id' => $this->user->id,
                    'amount' => 30,
                    'percentage' => 30,
                ],
                [
                    'id' => $familyMember->id,
                    'amount' => 40,
                    'percentage' => 40,
                ],
            ])
            ->set('family_members', collect([$this->user, $familyMember]));

        $this->assertCount(2, $component->get('form.user_shares'));

        $component->call('removeShare', $familyMember->id);
        $userShares = $component->get('form.user_shares');

        $this->assertCount(1, $userShares);
        $this->assertTrue(in_array($this->user->id, collect($userShares)->pluck('id')->toArray()));
        $this->assertFalse(in_array($familyMember->id, collect($userShares)->pluck('id')->toArray()));
        $this->assertGreaterThanOrEqual(70.0, $component->get('remainingAmount'));
        $this->assertLessThanOrEqual(70.1, $component->get('remainingAmount'));
        $this->assertGreaterThanOrEqual(70.0, $component->get('remainingPercentage'));
        $this->assertLessThanOrEqual(70.1, $component->get('remainingPercentage'));
    }

    public function test_switches_between_percentage_and_amount_share_modes(): void
    {
        $component = Livewire::test(Create::class)
            ->set('form.amount', 100);

        $this->assertEquals('amount', $component->get('shareMode'));

        $component->set('shareMode', 'percentage');
        $this->assertEquals('percentage', $component->get('shareMode'));

        $component->set('shareMode', 'amount');
        $this->assertEquals('amount', $component->get('shareMode'));
    }

    public function test_restricts_file_types_to_those_allowed(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.txt', 1024);

        Livewire::test(Create::class)
            ->set('form.uploadedFile', $invalidFile)
            ->assertHasErrors(['form.uploadedFile']);

        $validExtensions = ['pdf', 'docx', 'jpg', 'jpeg', 'png'];

        foreach ($validExtensions as $extension) {
            $validFile = UploadedFile::fake()->create("document.{$extension}", 1024);
            Livewire::test(Create::class)
                ->set('form.uploadedFile', $validFile)
                ->assertHasNoErrors(['form.uploadedFile']);
        }
    }

    public function test_enforces_maximum_file_size(): void
    {
        $largeFile = UploadedFile::fake()->create('large-document.pdf', 11000);
        $normalFile = UploadedFile::fake()->create('normal-document.pdf', 5000);

        Livewire::test(Create::class)
            ->set('form.uploadedFile', $largeFile)
            ->assertHasErrors(['form.uploadedFile']);

        Livewire::test(Create::class)
            ->set('form.uploadedFile', $normalFile)
            ->assertHasNoErrors(['form.uploadedFile']);
    }

    public function test_notes_field_has_character_limit_validation(): void
    {
        $longNote = str_repeat('a', 501);
        $validNote = str_repeat('a', 499);

        Livewire::test(Create::class)
            ->set('form.notes', $longNote)
            ->assertHasErrors(['form.notes']);

        Livewire::test(Create::class)
            ->set('form.notes', $validNote)
            ->assertHasNoErrors(['form.notes']);
    }

    public function test_form_summary_shows_correct_information(): void
    {
        $component = Livewire::test(Create::class)
            ->set('form.name', 'Facture Test')
            ->set('form.amount', 100)
            ->set('form.currency', CurrencyEnum::EUR->value)
            ->set('form.type', TypeEnum::ABONNEMENTS->value)
            ->set('form.category', CategoryEnum::ABO_INTERNET_TELECOM->value)
            ->set('form.tags', ['test1', 'test2']);

        $html = $component->html();

        $this->assertStringContainsString('Facture Test', $html);
        $this->assertStringContainsString('100', $html);
        $this->assertStringContainsString('€', $html);
        $this->assertStringContainsString('test1', $html);
        $this->assertStringContainsString('test2', $html);
    }
}
