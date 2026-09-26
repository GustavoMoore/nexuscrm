<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

function projectAdm(): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => 'adm'])->save();

    return $user;
}

class ProjectTest extends TestCase
{
    use DatabaseTransactions;

    // SPECSFY: US-002 FR-005
    // SPECSFY: AC-012
    public function test_a_c_012_adm_cria_e_altera_atribuição_de_projeto(): void
    {
        $adm = projectAdm();
        $ana = User::factory()->create();
        $bruno = User::factory()->create();
        $this->actingAs($adm)->post('/projetos', ['name' => 'Mentoria X', 'user_ids' => [$ana->id]])->assertRedirect();
        $project = Project::where('name', 'Mentoria X')->firstOrFail();
        $this->assertSame([$ana->id], $project->users()->pluck('users.id')->all());
        $this->patch("/projetos/{$project->id}", ['name' => 'Mentoria X', 'user_ids' => [$bruno->id]])->assertRedirect();
        $this->assertSame([$bruno->id], $project->users()->pluck('users.id')->all());
    }

    // SPECSFY: US-002 FR-005 FR-006
    // SPECSFY: AC-013
    public function test_a_c_013_arquivado_some_do_gestor_e_pode_ser_desarquivado(): void
    {
        $adm = projectAdm();
        $ana = User::factory()->create();
        $project = Project::create(['name' => 'Mentoria X']);
        $project->users()->attach($ana);
        $this->actingAs($adm)->post("/projetos/{$project->id}/arquivar")->assertRedirect();
        $this->actingAs($ana)->get("/projetos/{$project->id}")->assertForbidden();
        $this->actingAs($adm)->get('/projetos?status=arquivados')->assertInertia(fn (Assert $page) => $page->component('projetos/index')->where('projects.0.name', 'Mentoria X'));
        $this->post("/projetos/{$project->id}/desarquivar")->assertRedirect();
    }

    // SPECSFY: US-002 FR-005
    // SPECSFY: AC-014
    public function test_a_c_014_exige_nome_único_de_projeto_sem_diferenciar_caixa(): void
    {
        $adm = projectAdm();
        Project::create(['name' => 'Mentoria X']);
        $this->actingAs($adm)->post('/projetos', ['name' => ''])->assertSessionHasErrors('name');
        $this->post('/projetos', ['name' => 'mentoria x'])->assertSessionHasErrors('name');
        $this->assertSame(1, Project::count());
    }

    // SPECSFY: US-003 FR-006
    // SPECSFY: AC-015
    public function test_a_c_015_gestor_vê_apenas_projetos_atribuídos(): void
    {
        $ana = User::factory()->create();
        $bruno = User::factory()->create();
        $one = Project::create(['name' => 'Mentoria X']);
        $one->users()->attach($ana);
        $two = Project::create(['name' => 'Imersão Y']);
        $two->users()->attach($bruno);
        $this->actingAs($ana)->get('/projetos')->assertInertia(fn (Assert $page) => $page->component('projetos/index')->has('projects', 1)->where('projects.0.name', 'Mentoria X')->where('can.create', false)->where('can.update', false)->where('can.archive', false));
    }

    // SPECSFY: US-003 FR-006 NFR-001
    // SPECSFY: AC-016
    public function test_a_c_016_nega_ur_l_de_projeto_alheio(): void
    {
        $ana = User::factory()->create();
        $other = Project::create(['name' => 'Imersão Y']);
        $this->actingAs($ana)->get("/projetos/{$other->id}")->assertForbidden();
    }

    // SPECSFY: US-002 FR-005 NFR-001
    // SPECSFY: AC-020
    public function test_a_c_020_gestor_não_escreve_por_requisição_direta(): void
    {
        $ana = User::factory()->create();
        $project = Project::create(['name' => 'Mentoria X']);
        $project->users()->attach($ana);
        $this->actingAs($ana)->post('/projetos', ['name' => 'Outro'])->assertForbidden();
        $this->patch("/projetos/{$project->id}", ['name' => 'Alterado'])->assertForbidden();
        $this->post("/projetos/{$project->id}/arquivar")->assertForbidden();
        $this->assertSame('Mentoria X', $project->fresh()->name);
    }
}
