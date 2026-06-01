<?php

use App\Models\Supplier;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view suppliers list', function () {
    $supplier = Supplier::factory()->create(['name' => 'John Doe']);

    $this->get(route('suppliers.index'))
        ->assertOk()
        ->assertSee('John Doe');
});

test('can search suppliers', function () {
    Supplier::factory()->create(['name' => 'John Doe', 'district' => 'Ahmedabad']);
    Supplier::factory()->create(['name' => 'Jane Smith', 'district' => 'Surat']);

    Livewire::test('pages::suppliers.index')
        ->set('search', 'Ahmedabad')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('can create a supplier', function () {
    Livewire::test('pages::suppliers.create')
        ->set('name', 'New Supplier')
        ->set('mobile_number', '1234567890')
        ->set('email', 'new@supplier.com')
        ->set('state', 'Gujarat')
        ->set('district', 'Ahmedabad')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'name' => 'New Supplier',
        'mobile_number' => '1234567890',
        'email' => 'new@supplier.com',
        'district' => 'Ahmedabad',
    ]);
});

test('validation works for supplier creation', function () {
    Livewire::test('pages::suppliers.create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('can update a supplier', function () {
    $supplier = Supplier::factory()->create(['name' => 'Old Name', 'district' => 'Ahmedabad']);

    Livewire::test('pages::suppliers.edit', ['supplier' => $supplier])
        ->set('name', 'Updated Name')
        ->set('district', 'Surat')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'name' => 'Updated Name',
        'district' => 'Surat',
    ]);
});

test('can delete a supplier', function () {
    $supplier = Supplier::factory()->create();

    Livewire::test('pages::suppliers.index')
        ->call('delete', $supplier->id);

    $this->assertDatabaseMissing('suppliers', [
        'id' => $supplier->id,
    ]);
});

test('can view supplier details', function () {
    $supplier = Supplier::factory()->create([
        'name' => 'Detail View Supplier',
        'email' => 'detail@view.com',
        'gst_number' => '123456789012345',
    ]);

    $this->get(route('suppliers.show', $supplier))
        ->assertOk()
        ->assertSee('Detail View Supplier')
        ->assertSee('detail@view.com')
        ->assertSee('123456789012345');
});
