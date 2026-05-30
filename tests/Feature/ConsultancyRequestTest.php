<?php

use App\Models\ConsultancyRequest;
use App\Models\User;

test('public can submit consultancy request', function () {
    $response = $this->post(route('consultoria.store'), [
        'name' => 'Empresa Demo SAC',
        'email' => 'contacto@demo.test',
        'phone' => '999888777',
        'company' => 'Demo SAC',
        'area' => 'Finanzas',
        'message' => 'Necesitamos consultoría estratégica para el Q3.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('consultancy_requests', [
        'email' => 'contacto@demo.test',
        'area' => 'Finanzas',
    ]);
});

test('consultancy request requires valid fields', function () {
    $response = $this->post(route('consultoria.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'area', 'message']);
});

test('admin can list consultancy requests', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    ConsultancyRequest::create([
        'name' => 'Lead Test',
        'email' => 'lead@test.com',
        'phone' => null,
        'company' => null,
        'area' => 'RRHH',
        'message' => 'Mensaje de prueba',
        'status' => 'pendiente',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.consultancies.index'));

    $response->assertSuccessful();
});
