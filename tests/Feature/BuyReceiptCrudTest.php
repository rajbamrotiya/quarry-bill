<?php

use App\Models\Supplier;
use App\Models\MaterialType;
use App\Models\BuyReceipt;
use App\Models\User;
use Database\Seeders\MaterialTypeSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->seed(MaterialTypeSeeder::class);
});

test('can view buy_receipts list', function () {
    $buy_receipt = BuyReceipt::factory()->create();

    $this->get(route('buy-receipts.index'))
        ->assertOk()
        ->assertSee($buy_receipt->vehicle_number);
});

test('can search buy_receipts', function () {
    $buy_receipt1 = BuyReceipt::factory()->create(['vehicle_number' => 'GJ-01-AA-1111']);
    $buy_receipt2 = BuyReceipt::factory()->create(['vehicle_number' => 'GJ-02-BB-2222']);

    Livewire::test('pages::buy_receipts.index')
        ->set('search', 'GJ-01')
        ->assertSee('GJ-01-AA-1111')
        ->assertDontSee('GJ-02-BB-2222');
});

test('can create a buy_receipt', function () {
    $supplier = Supplier::factory()->create();
    $material = MaterialType::first();

    Livewire::test('pages::buy_receipts.create')
        ->set('supplier_id', $supplier->id)
        ->set('vehicle_number', 'GJ-01-XX-9999')
        ->set('material_type_id', $material->id)
        ->set('gross_weight', 30500)
        ->set('tare_weight', 10000)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('buy_receipts', [
        'vehicle_number' => 'GJ-01-XX-9999',
        'net_weight' => 20500,
    ]);
});

test('validation works for buy_receipt creation', function () {
    Livewire::test('pages::buy_receipts.create')
        ->set('supplier_id', '')
        ->call('save')
        ->assertHasErrors(['supplier_id' => 'required']);
});

test('tare weight must be less than gross weight', function () {
    Livewire::test('pages::buy_receipts.create')
        ->set('gross_weight', 10000)
        ->set('tare_weight', 20000)
        ->call('save')
        ->assertHasErrors(['tare_weight']);
});

test('can update a buy_receipt', function () {
    $buy_receipt = BuyReceipt::factory()->create(['vehicle_number' => 'OLD-VEHICLE']);

    Livewire::test('pages::buy_receipts.edit', ['buy_receipt' => $buy_receipt])
        ->set('vehicle_number', 'NEW-VEHICLE')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('buy-receipts.index'));

    $this->assertDatabaseHas('buy_receipts', [
        'id' => $buy_receipt->id,
        'vehicle_number' => 'NEW-VEHICLE',
    ]);
});

test('can delete a buy_receipt', function () {
    $buy_receipt = BuyReceipt::factory()->create();

    Livewire::test('pages::buy_receipts.index')
        ->call('delete', $buy_receipt->id);

    $this->assertDatabaseMissing('buy_receipts', [
        'id' => $buy_receipt->id,
    ]);
});

test('can view buy_receipt details', function () {
    $buy_receipt = BuyReceipt::factory()->create();

    $this->get(route('buy-receipts.show', $buy_receipt))
        ->assertOk()
        ->assertSee($buy_receipt->vehicle_number);
});

test('can download buy_receipt pdf', function () {
    $buy_receipt = BuyReceipt::factory()->create();

    $this->get(route('buy-receipts.pdf', $buy_receipt))
        ->assertOk()
        ->assertHeader('content-disposition', "inline; filename=buy_receipt-{$buy_receipt->id}.pdf");
});
