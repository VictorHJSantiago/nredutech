<?php

namespace Tests\Feature\Profile;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed()
    {
        $user = Usuario::factory()->create();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertOk();
        $response->assertViewIs('profile.edit');
    }

    public function test_profile_information_can_be_updated()
    {
        $user = Usuario::factory()->create();
        $response = $this->actingAs($user)->patch('/profile', [
            'nome_completo' => 'Novo Nome',
            'email' => 'novo@email.com',
            'telefone' => '(99) 99999-9999',
        ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'profile-updated');
        
        $user->refresh();
        $this->assertEquals('Novo Nome', $user->nome_completo);
        $this->assertEquals('novo@email.com', $user->email);
        $this->assertEquals('(99) 99999-9999', $user->telefone);
    }

    public function test_password_can_be_updated()
    {
        $user = Usuario::factory()->create(['password' => Hash::make('senha-antiga-123')]);
        $newPassword = 'nova-senha-12345';

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'senha-antiga-123',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'password-updated');
        
        $this->assertTrue(Hash::check($newPassword, $user->refresh()->password));
    }

    public function test_password_update_fails_with_incorrect_current_password()
    {
        $user = Usuario::factory()->create(['password' => Hash::make('senha-antiga-123')]);
        $newPassword = 'nova-senha-12345';

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'senha-errada',
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertFalse(Hash::check($newPassword, $user->refresh()->password));
    }
}