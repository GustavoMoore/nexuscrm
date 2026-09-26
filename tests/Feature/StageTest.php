<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StageTest extends TestCase
{
    use DatabaseTransactions;

    private function setupFunnel(): array
    {
        $adm = User::factory()->create();
        $adm->forceFill(['role' => 'adm'])->save();
        $project = Project::create(['name' => 'Alfa']);
        $this->actingAs($adm)->post("/projetos/{$project->id}/funis", ['name' => 'Lançamento'])->assertRedirect();
        $funnel = $project->funnels()->firstOrFail();

        return [$project, $funnel, "/projetos/{$project->id}/funis/{$funnel->id}/etapas"];
    }

    // SPECSFY: US-002 FR-003
    // SPECSFY: AC-009
    public function test_a_c_009_adiciona_etapa_no_fim(): void
    {
        [, $funnel, $base] = $this->setupFunnel();
        $this->post($base, ['name' => 'Fechamento'])->assertRedirect();
        $this->assertSame(['Novo contato', 'Qualificação', 'Proposta', 'Negociação', 'Fechamento'], $funnel->fresh()->stages->pluck('name')->all());
        $this->assertSame(5, $funnel->fresh()->stages->last()->position);
    }

    // SPECSFY: US-002 FR-003 NFR-002
    // SPECSFY: AC-010
    public function test_a_c_010_renomeia_e_valida_nome_unico(): void
    {
        [, $funnel, $base] = $this->setupFunnel();
        $proposta = $funnel->stages->firstWhere('name', 'Proposta');
        $this->patch("$base/{$proposta->id}", ['name' => 'Proposta enviada'])->assertRedirect();
        $this->assertSame('Proposta enviada', $proposta->fresh()->name);
        $this->patch("$base/{$proposta->id}", ['name' => ''])->assertSessionHasErrors('name');
        $this->patch("$base/{$proposta->id}", ['name' => 'qualificação'])->assertSessionHasErrors('name');
    }

    // SPECSFY: US-002 FR-003
    // SPECSFY: AC-011
    public function test_a_c_011_move_para_vizinha_e_pontas_nao_falham(): void
    {
        [, $funnel, $base] = $this->setupFunnel();
        $proposta = $funnel->stages->firstWhere('name', 'Proposta');
        $this->post("$base/{$proposta->id}/mover", ['direction' => 'up'])->assertRedirect();
        $expected = ['Novo contato', 'Proposta', 'Qualificação', 'Negociação'];
        $this->assertSame($expected, $funnel->fresh()->stages->pluck('name')->all());
        $first = $funnel->fresh()->stages->first();
        $last = $funnel->fresh()->stages->last();
        $this->post("$base/{$first->id}/mover", ['direction' => 'up'])->assertRedirect();
        $this->post("$base/{$last->id}/mover", ['direction' => 'down'])->assertRedirect();
        $this->assertSame($expected, $funnel->fresh()->stages->pluck('name')->all());
        $this->post("$base/{$proposta->id}/mover", ['direction' => 'sideways'])->assertSessionHasErrors('direction');
    }

    // SPECSFY: US-002 FR-004
    // SPECSFY: AC-012
    public function test_a_c_012_apaga_renumera_e_recusa_ultima_etapa(): void
    {
        [, $funnel, $base] = $this->setupFunnel();
        $proposta = $funnel->stages->firstWhere('name', 'Proposta');
        $this->delete("$base/{$proposta->id}")->assertRedirect();
        $this->assertSame(['Novo contato', 'Qualificação', 'Negociação'], $funnel->fresh()->stages->pluck('name')->all());
        $this->assertSame([1, 2, 3], $funnel->fresh()->stages->pluck('position')->all());
        foreach ($funnel->fresh()->stages->take(2) as $stage) {
            $this->delete("$base/{$stage->id}")->assertRedirect();
        }
        $last = $funnel->fresh()->stages->first();
        $this->delete("$base/{$last->id}")->assertSessionHasErrors();
        $this->assertSame(1, $funnel->fresh()->stages()->count());
    }

    // SPECSFY: US-002 FR-003 NFR-001 FR-004
    // SPECSFY: AC-013
    public function test_a_c_013_etapa_de_outro_funil_retorna_404(): void
    {
        [$project, $funnel, $base] = $this->setupFunnel();
        $this->post("/projetos/{$project->id}/funis", ['name' => 'Outro'])->assertRedirect();
        $other = $project->funnels()->where('id', '!=', $funnel->id)->firstOrFail();
        $stage = $funnel->stages->first();
        $wrong = "/projetos/{$project->id}/funis/{$other->id}/etapas/{$stage->id}";
        $this->patch($wrong, ['name' => 'Alterada'])->assertNotFound();
        $this->delete($wrong)->assertNotFound();
        $this->assertSame('Novo contato', $stage->fresh()->name);
    }
}
