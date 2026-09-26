<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

function admForSpec(): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => 'adm'])->save();

    return $user;
}

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    // SPECSFY: US-001 FR-002
    // SPECSFY: AC-004
    public function test_a_c_004_adm_cria_gestor_com_senha_provisória(): void
    {
        $this->actingAs(admForSpec())->post('/usuarios', ['name' => 'Ana', 'email' => 'ANA@CLIENTE.COM', 'password' => 'provisoria1'])->assertRedirect();
        $user = User::where('email', 'ana@cliente.com')->firstOrFail();
        $this->assertSame('gestor', $user->role);
        $this->assertTrue($user->must_change_password);
        $this->assertTrue($user->isActive());
    }

    // SPECSFY: US-001 FR-002 NFR-002
    // SPECSFY: AC-005
    public function test_a_c_005_rejeita_email_duplicado_sem_diferenciar_maiúsculas(): void
    {
        $adm = admForSpec();
        User::factory()->create(['email' => 'ana@cliente.com']);
        $this->actingAs($adm)->post('/usuarios', ['name' => 'Outra', 'email' => 'Ana@Cliente.com', 'password' => 'provisoria1'])->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'ana@cliente.com')->count());
    }

    // SPECSFY: US-001 FR-002 FR-003
    // SPECSFY: AC-006
    public function test_a_c_006_redefine_senha_e_revoga_sessões(): void
    {
        $adm = admForSpec();
        $gestor = User::factory()->create();
        DB::table('sessions')->insert(['id' => 'sessao-gestor', 'user_id' => $gestor->id, 'payload' => '', 'last_activity' => time()]);
        $oldToken = $gestor->remember_token;
        $this->actingAs($adm)->post("/usuarios/{$gestor->id}/redefinir-senha", ['password' => 'nova12345'])->assertRedirect();
        $this->assertTrue($gestor->fresh()->must_change_password);
        $this->assertSame(0, DB::table('sessions')->where('user_id', $gestor->id)->count());
        $this->assertNotSame($oldToken, $gestor->fresh()->remember_token);
    }

    // SPECSFY: FR-004
    // SPECSFY: AC-011
    public function test_a_c_011_adm_não_se_desativa(): void
    {
        $adm = admForSpec();
        $this->actingAs($adm)->post("/usuarios/{$adm->id}/desativar")->assertForbidden();
        $this->get('/usuarios')->assertInertia(fn (Assert $page) => $page->component('usuarios/index')->where('users.0.can.deactivate', false));
        $this->assertNull($adm->fresh()->deactivated_at);
    }

    // SPECSFY: US-001 FR-007 NFR-002
    // SPECSFY: AC-019
    public function test_a_c_019_fornece_tela_e_dados_para_painel_lateral_de_gestores(): void
    {
        $this->actingAs(admForSpec())->get('/usuarios')->assertInertia(fn (Assert $page) => $page->component('usuarios/index')->has('users')->where('can.create', true));
    }
}
