<?php

namespace Tests\Feature\Deals;

class DealStageTest extends DealsTestCase
{
    // SPECSFY: AC-020
    public function test_a_c_020_apagar_etapa_com_negocios_exige_destino_e_realoca_todos(): void
    {
        [, , $funnel] = $this->context(true);
        $stage = $funnel->stages->firstWhere('name', 'Proposta');
        $destination = $funnel->stages->firstWhere('name', 'Negociação');
        $open = $this->deal($funnel, ['stage_id' => $stage->id]);
        $closed = $this->deal($funnel, ['stage_id' => $stage->id, 'status' => 'won', 'closed_at' => now()]);
        $url = "/projetos/{$funnel->project_id}/funis/{$funnel->id}/etapas/{$stage->id}";
        $this->delete($url)->assertSessionHasErrors('destination_stage_id');
        $this->delete($url, ['destination_stage_id' => $destination->id])->assertRedirect();
        $this->assertSame($destination->id, $open->fresh()->stage_id);
        $this->assertSame($destination->id, $closed->fresh()->stage_id);
        $this->assertNull($stage->fresh());
    }

    // SPECSFY: AC-021
    public function test_a_c_021_recusa_destino_invalido_e_apaga_etapa_vazia(): void
    {
        [, $project, $funnel] = $this->context(true);
        $stage = $funnel->stages->firstWhere('name', 'Proposta');
        $this->deal($funnel, ['stage_id' => $stage->id]);
        $other = $project->funnels()->create(['name' => 'Outro']);
        $other->createDefaultStages();
        $url = "/projetos/{$project->id}/funis/{$funnel->id}/etapas/{$stage->id}";
        $this->delete($url, ['destination_stage_id' => $stage->id])->assertSessionHasErrors('destination_stage_id');
        $this->delete($url, ['destination_stage_id' => $other->stages->first()->id])->assertSessionHasErrors('destination_stage_id');
        $empty = $funnel->stages->firstWhere('name', 'Qualificação');
        $this->delete("/projetos/{$project->id}/funis/{$funnel->id}/etapas/{$empty->id}")->assertRedirect();
        $this->assertNull($empty->fresh());
    }

    // SPECSFY: AC-024
    public function test_a_c_024_reabre_negocio_na_etapa_destino(): void
    {
        [, $project, $funnel, $board] = $this->context(true);
        $stage = $funnel->stages->firstWhere('name', 'Proposta');
        $destination = $funnel->stages->firstWhere('name', 'Negociação');
        $deal = $this->deal($funnel, ['stage_id' => $stage->id, 'status' => 'lost', 'closed_at' => now(), 'loss_reason_id' => $project->lossReasons()->first()->id]);
        $this->delete("/projetos/{$project->id}/funis/{$funnel->id}/etapas/{$stage->id}", ['destination_stage_id' => $destination->id])->assertRedirect();
        $this->post("$board/{$deal->id}/reabrir")->assertRedirect();
        $this->assertSame('open', $deal->fresh()->status);
        $this->assertSame($destination->id, $deal->fresh()->stage_id);
    }
}
