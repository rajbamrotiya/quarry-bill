<?php

use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can view clients list', function () {
    $client = Client::factory()->create(['name' => 'John Doe']);

    $this->get(route('clients.index'))
        ->assertOk()
        ->assertSee('John Doe');
});

test('can search clients', function () {
    Client::factory()->create(['name' => 'John Doe']);
    Client::factory()->create(['name' => 'Jane Smith']);

    Livewire::test('pages::clients.index')
        ->set('search', 'John')
        ->assertSee('John Doe')
        ->assertDontSee('Jane Smith');
});

test('can create a client', function () {
    Livewire::test('pages::clients.create')
        ->set('name', 'New Client')
        ->set('mobile_number', '1234567890')
        ->set('email', 'new@client.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'name' => 'New Client',
        'mobile_number' => '1234567890',
        'email' => 'new@client.com',
    ]);
});

test('validation works for client creation', function () {
    Livewire::test('pages::clients.create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('can update a client', function () {
    $client = Client::factory()->create(['name' => 'Old Name']);

    Livewire::test('pages::clients.edit', ['client' => $client])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Updated Name',
    ]);
});

test('can delete a client', function () {
    $client = Client::factory()->create();

    Livewire::test('pages::clients.index')
        ->call('delete', $client->id);

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
});

test('can view client details', function () {
    $client = Client::factory()->create([
        'name' => 'Detail View Client',
        'email' => 'detail@view.com',
    ]);

    $this->get(route('clients.show', $client))
        ->assertOk()
        ->assertSee('Detail View Client')
        ->assertSee('detail@view.com');
});
