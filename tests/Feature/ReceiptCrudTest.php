<?php

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use App\Models\User;
use Database\Seeders\MaterialTypeSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->seed(MaterialTypeSeeder::class);
});

test('can view receipts list', function () {
    $receipt = Receipt::factory()->create();

    $this->get(route('receipts.index'))
        ->assertOk()
        ->assertSee($receipt->vehicle_number);
});

test('can search receipts', function () {
    $receipt1 = Receipt::factory()->create(['vehicle_number' => 'GJ-01-AA-1111']);
    $receipt2 = Receipt::factory()->create(['vehicle_number' => 'GJ-02-BB-2222']);

    Livewire::test('pages::receipts.index')
        ->set('search', 'GJ-01')
        ->assertSee('GJ-01-AA-1111')
        ->assertDontSee('GJ-02-BB-2222');
});

test('can create a receipt', function () {
    $client = Client::factory()->create();
    $material = MaterialType::first();

    Livewire::test('pages::receipts.create')
        ->set('client_id', $client->id)
        ->set('vehicle_number', 'GJ-01-XX-9999')
        ->set('material_type_id', $material->id)
        ->set('gross_weight', 30.5)
        ->set('tare_weight', 10.2)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('receipts', [
        'vehicle_number' => 'GJ-01-XX-9999',
        'net_weight' => 20.3,
    ]);
});

test('validation works for receipt creation', function () {
    Livewire::test('pages::receipts.create')
        ->set('client_id', '')
        ->call('save')
        ->assertHasErrors(['client_id' => 'required']);
});

test('tare weight must be less than gross weight', function () {
    Livewire::test('pages::receipts.create')
        ->set('gross_weight', 10)
        ->set('tare_weight', 20)
        ->call('save')
        ->assertHasErrors(['tare_weight']);
});

test('can update a receipt', function () {
    $receipt = Receipt::factory()->create(['vehicle_number' => 'OLD-VEHICLE']);

    Livewire::test('pages::receipts.edit', ['receipt' => $receipt])
        ->set('vehicle_number', 'NEW-VEHICLE')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('receipts.index'));

    $this->assertDatabaseHas('receipts', [
        'id' => $receipt->id,
        'vehicle_number' => 'NEW-VEHICLE',
    ]);
});

test('can delete a receipt', function () {
    $receipt = Receipt::factory()->create();

    Livewire::test('pages::receipts.index')
        ->call('delete', $receipt->id);

    $this->assertDatabaseMissing('receipts', [
        'id' => $receipt->id,
    ]);
});

test('can view receipt details', function () {
    $receipt = Receipt::factory()->create();

    $this->get(route('receipts.show', $receipt))
        ->assertOk()
        ->assertSee($receipt->vehicle_number);
});

test('can download receipt pdf', function () {
    $receipt = Receipt::factory()->create();

    $this->get(route('receipts.pdf', $receipt))
        ->assertOk()
        ->assertHeader('content-disposition', "inline; filename=receipt-{$receipt->id}.pdf");
});
