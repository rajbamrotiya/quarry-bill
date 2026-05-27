<?php

use App\Models\MaterialType;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view material types list', function () {
    MaterialType::create(['name' => 'Test Material']);

    $this->get(route('material-types.index'))
        ->assertOk()
        ->assertSee('Test Material');
});

test('can search material types', function () {
    MaterialType::create(['name' => 'Stone']);
    MaterialType::create(['name' => 'Sand']);

    Livewire::test('pages::material-types.index')
        ->set('search', 'Stone')
        ->assertSee('Stone')
        ->assertDontSee('Sand');
});

test('can create a material type', function () {
    Livewire::test('pages::material-types.create')
        ->set('name', 'New Material')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('material-types.index'));

    $this->assertDatabaseHas('material_types', [
        'name' => 'New Material',
    ]);
});

test('validation works for material type creation', function () {
    MaterialType::create(['name' => 'Existing']);

    Livewire::test('pages::material-types.create')
        ->set('name', 'Existing')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});

test('can update a material type', function () {
    $material = MaterialType::create(['name' => 'Old Name']);

    Livewire::test('pages::material-types.edit', ['materialType' => $material])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('material-types.index'));

    $this->assertDatabaseHas('material_types', [
        'id' => $material->id,
        'name' => 'Updated Name',
    ]);
});

test('can delete a material type', function () {
    $material = MaterialType::create(['name' => 'Delete Me']);

    Livewire::test('pages::material-types.index')
        ->call('delete', $material->id);

    $this->assertDatabaseMissing('material_types', [
        'id' => $material->id,
    ]);
});
