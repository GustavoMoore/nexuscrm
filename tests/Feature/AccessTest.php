<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use DatabaseTransactions;

    // SPECSFY: US-003 FR-001 NFR-001
    // SPECSFY: AC-001
    public function test_a_c_001_não_oferece_cadastro_público(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
        $this->assertFalse(Route::has('register'));
        $this->assertFalse(Route::has('password.request'));
        $this->assertFalse(Route::has('password.reset'));
    }

    // SPECSFY: FR-001 NFR-001
    // SPECSFY: AC-002
    public function test_a_c_002_impede_autoexclusão(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->delete('/settings/profile')->assertStatus(405);
        $this->assertNotNull($user->fresh());
        $this->assertFalse(Route::has('profile.destroy'));
    }

    // SPECSFY: FR-001
    // SPECSFY: AC-003
    public function test_a_c_003_não_oferece_recuperação_por_email(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->get('/reset-password/token')->assertNotFound();
    }

    // SPECSFY: US-003 FR-003 NFR-001
    // SPECSFY: AC-007
    public function test_a_c_007_obriga_troca_antes_de_navegar(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['must_change_password' => true])->save();
        $this->actingAs($user)->get('/agenda')->assertRedirect('/trocar-senha');
        $this->get('/projetos')->assertRedirect('/trocar-senha');
        $this->get('/trocar-senha')->assertOk();
    }

    // SPECSFY: US-003 FR-003
    // SPECSFY: AC-008
    public function test_a_c_008_troca_senha_provisória_por_senha_diferente(): void
    {
        $user = User::factory()->create(['password' => 'provisoria1']);
        $user->forceFill(['must_change_password' => true])->save();
        $this->actingAs($user)->put('/trocar-senha', ['password' => 'provisoria1', 'password_confirmation' => 'provisoria1'])->assertSessionHasErrors('password');
        $this->put('/trocar-senha', ['password' => 'definitiva1', 'password_confirmation' => 'definitiva1'])->assertRedirect('/agenda');
        $this->assertFalse($user->fresh()->must_change_password);
    }

    // SPECSFY: US-001 FR-004
    // SPECSFY: AC-009
    public function test_a_c_009_recusa_login_desativado_e_aceita_após_reativação(): void
    {
        $user = User::factory()->create(['password' => 'password']);
        $user->forceFill(['deactivated_at' => now()])->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => true])->assertSessionHasErrors(['email' => 'Conta desativada. Fale com o administrador.']);
        $this->assertGuest();
        $user->forceFill(['deactivated_at' => null])->save();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/agenda');
    }

    // SPECSFY: FR-004 NFR-001
    // SPECSFY: AC-010
    public function test_a_c_010_encerra_acesso_na_próxima_requisição_após_desativar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/agenda')->assertOk();
        $user->forceFill(['deactivated_at' => now()])->save();
        $this->get('/agenda')->assertRedirect('/login');
        $this->assertGuest();
    }

    // SPECSFY: FR-006 FR-007 NFR-001
    // SPECSFY: AC-017
    public function test_a_c_017_nega_usuários_ao_gestor_e_compartilha_papel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/agenda')->assertInertia(fn (Assert $page) => $page->component('agenda')->where('auth.user.is_adm', false));
        $this->get('/usuarios')->assertForbidden();
    }

    // SPECSFY: FR-007 NFR-002
    // SPECSFY: AC-018
    public function test_a_c_018_redireciona_home_à_agenda_e_compartilha_nexus(): void
    {
        $this->get('/')->assertRedirect('/login');
        $user = User::factory()->create();
        $this->actingAs($user)->get('/')->assertRedirect('/agenda');
        $this->get('/agenda')->assertInertia(fn (Assert $page) => $page->component('agenda')->where('name', 'Nexus')->where('auth.user.is_adm', false));
    }
}
