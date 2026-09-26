<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FunnelTest extends TestCase
{
    use DatabaseTransactions;

    private function adm(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'adm'])->save();

        return $user;
    }

    private function project(string $name = 'Alfa'): Project
    {
        return Project::create(['name' => $name]);
    }

    private function createFunnel(Project $project, string $name = 'Lançamento'): int
    {
        $this->actingAs($this->adm())->post("/projetos/{$project->id}/funis", ['name' => $name])->assertRedirect();

        return (int) $project->funnels()->where('name', $name)->firstOrFail()->id;
    }

    // SPECSFY: US-001 FR-001 FR-006
    // SPECSFY: AC-001
    public function test_a_c_001_funil_nasce_com_quatro_etapas(): void
    {
        $project = $this->project();
        $this->actingAs($this->adm())->post("/projetos/{$project->id}/funis", ['name' => 'Lançamento'])
            ->assertRedirect()->assertSessionHas('success');
        $funnel = $project->funnels()->where('name', 'Lançamento')->firstOrFail();
        $this->assertSame(['Novo contato', 'Qualificação', 'Proposta', 'Negociação'], $funnel->stages->pluck('name')->all());
        $this->assertSame([1, 2, 3, 4], $funnel->stages->pluck('position')->all());
    }

    // SPECSFY: US-001 FR-001 NFR-002
    // SPECSFY: AC-002
    public function test_a_c_002_nome_obrigatorio_e_unico_por_projeto(): void
    {
        $alfa = $this->project();
        $beta = $this->project('Beta');
        $this->createFunnel($alfa);
        $this->post("/projetos/{$alfa->id}/funis", ['name' => ''])->assertSessionHasErrors('name');
        $this->post("/projetos/{$alfa->id}/funis", ['name' => 'lançamento'])->assertSessionHasErrors('name');
        $this->post("/projetos/{$beta->id}/funis", ['name' => 'Lançamento'])->assertRedirect();
        $this->assertSame(1, $alfa->funnels()->count());
        $this->assertSame(1, $beta->funnels()->count());
    }

    // SPECSFY: US-001 FR-001
    // SPECSFY: AC-003
    public function test_a_c_003_renomeia_sem_alterar_etapas(): void
    {
        $project = $this->project();
        $id = $this->createFunnel($project);
        $before = $project->funnels()->findOrFail($id)->stages->pluck('id')->all();
        $this->patch("/projetos/{$project->id}/funis/$id", ['name' => 'Perpétuo'])->assertRedirect();
        $this->assertSame('Perpétuo', $project->funnels()->findOrFail($id)->name);
        $this->assertSame($before, $project->funnels()->findOrFail($id)->stages->pluck('id')->all());
    }

    // SPECSFY: US-001 FR-002
    // SPECSFY: AC-004
    public function test_a_c_004_arquivar_oculta_do_gestor_e_desarquivar_restaura(): void
    {
        $project = $this->project();
        $id = $this->createFunnel($project);
        $gestor = User::factory()->create();
        $project->users()->attach($gestor);
        $this->post("/projetos/{$project->id}/funis/$id/arquivar")->assertRedirect();
        $this->actingAs($gestor)->get("/projetos/{$project->id}")->assertInertia(fn (Assert $page) => $page->component('projetos/show')->has('funnels', 0));
        $this->get("/projetos/{$project->id}/funis/$id")->assertNotFound();
        $this->actingAs($this->adm())->post("/projetos/{$project->id}/funis/$id/desarquivar")->assertRedirect();
        $this->actingAs($gestor)->get("/projetos/{$project->id}")->assertInertia(fn (Assert $page) => $page->component('projetos/show')->has('funnels', 1));
    }

    // SPECSFY: US-001 FR-001
    // SPECSFY: AC-005
    public function test_a_c_005_sem_limite_de_funis(): void
    {
        $project = $this->project();
        $this->actingAs($this->adm());
        foreach (range(1, 5) as $i) {
            $this->post("/projetos/{$project->id}/funis", ['name' => "Funil $i"])->assertRedirect();
        }
        $this->assertSame(5, $project->funnels()->count());
    }

    // SPECSFY: US-003 FR-005 FR-002 FR-006
    // SPECSFY: AC-006
    public function test_a_c_006_gestor_ve_funis_e_etapas_sem_poder_gerir(): void
    {
        $project = $this->project();
        $id = $this->createFunnel($project);
        $gestor = User::factory()->create();
        $project->users()->attach($gestor);
        $this->actingAs($gestor)->get("/projetos/{$project->id}")->assertInertia(fn (Assert $page) => $page->component('projetos/show')->where('funnels.0.name', 'Lançamento')->where('can.manage', false));
        $this->get("/projetos/{$project->id}/funis/$id")->assertInertia(fn (Assert $page) => $page->component('funis/show')->has('stages', 4)->where('stages.0.position', 1)->where('can.manage', false));
    }

    // SPECSFY: US-003 FR-005 NFR-001
    // SPECSFY: AC-007
    public function test_a_c_007_projeto_alheio_403_e_pai_errado_404(): void
    {
        $alfa = $this->project();
        $beta = $this->project('Beta');
        $id = $this->createFunnel($beta, 'X');
        $gestor = User::factory()->create();
        $alfa->users()->attach($gestor);
        $this->actingAs($gestor)->get("/projetos/{$beta->id}/funis/$id")->assertForbidden();
        $this->get("/projetos/{$alfa->id}/funis/$id")->assertNotFound();
    }

    // SPECSFY: US-003 FR-005 NFR-001 FR-004
    // SPECSFY: AC-008
    public function test_a_c_008_gestor_nao_escreve_em_funis_ou_etapas(): void
    {
        $project = $this->project();
        $id = $this->createFunnel($project);
        $stage = $project->funnels()->findOrFail($id)->stages->first();
        $gestor = User::factory()->create();
        $project->users()->attach($gestor);
        $base = "/projetos/{$project->id}/funis";
        $this->actingAs($gestor)->post($base, ['name' => 'X'])->assertForbidden();
        $this->patch("$base/$id", ['name' => 'X'])->assertForbidden();
        $this->post("$base/$id/arquivar")->assertForbidden();
        $this->post("$base/$id/desarquivar")->assertForbidden();
        $this->post("$base/$id/etapas", ['name' => 'X'])->assertForbidden();
        $this->patch("$base/$id/etapas/{$stage->id}", ['name' => 'X'])->assertForbidden();
        $this->post("$base/$id/etapas/{$stage->id}/mover", ['direction' => 'down'])->assertForbidden();
        $this->delete("$base/$id/etapas/{$stage->id}")->assertForbidden();
        $this->assertSame('Lançamento', $project->funnels()->findOrFail($id)->name);
        $this->assertSame('Novo contato', $stage->fresh()->name);
    }

    // SPECSFY: US-001 FR-006 NFR-002 FR-002
    // SPECSFY: AC-014
    public function test_a_c_014_props_de_funis_e_etapas(): void
    {
        $project = $this->project();
        $id = $this->createFunnel($project);
        $archived = $this->createFunnel($project, 'Arquivo');
        $this->post("/projetos/{$project->id}/funis/$archived/arquivar")->assertRedirect();
        $this->get("/projetos/{$project->id}")->assertInertia(fn (Assert $page) => $page->component('projetos/show')->has('funnels', 2)->where('funnels.0.stages_count', 4)->etc()->where('can.manage', true));
        $this->get("/projetos/{$project->id}/funis/$id")->assertInertia(fn (Assert $page) => $page->component('funis/show')->where('project.id', $project->id)->where('funnel.id', $id)->has('stages', 4)->where('stages.0.name', 'Novo contato')->where('stages.0.position', 1));
    }
}
