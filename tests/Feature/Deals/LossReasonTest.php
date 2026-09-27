<?php

namespace Tests\Feature\Deals;

use App\Models\Project;

class LossReasonTest extends DealsTestCase
{
    // SPECSFY: AC-018
    public function test_a_c_018_projeto_novo_recebe_cinco_motivos(): void
    {
        $project = Project::create(['name' => 'Gama']);
        $this->assertEqualsCanonicalizing(['Preço', 'Sem resposta', 'Comprou de outro', 'Sem interesse', 'Outro'], $project->lossReasons->pluck('name')->all());
    }

    // SPECSFY: AC-019
    public function test_a_c_019_adm_cria_renomeia_e_desativa_motivo(): void
    {
        [, $project, $funnel, $board] = $this->context(true);
        $base = "/projetos/{$project->id}/motivos-perda";
        $reason = $project->lossReasons()->where('name', 'Preço')->firstOrFail();
        $deal = $this->deal($funnel, ['status' => 'lost', 'loss_reason_id' => $reason->id, 'closed_at' => now()]);
        $this->post($base, ['name' => 'Prazo'])->assertRedirect();
        $this->patch("$base/{$reason->id}", ['name' => 'Preço alto'])->assertRedirect();
        $this->post("$base/{$reason->id}/desativar")->assertRedirect();
        $this->get($board)->assertInertia(fn ($page) => $page->has('loss_reasons', 5));
        $this->assertSame('Preço alto', $deal->lossReason->fresh()->name);
        $this->post($base, ['name' => 'prazo'])->assertSessionHasErrors('name');
        $this->actingAs($this->user())->post($base, ['name' => 'Outro motivo'])->assertForbidden();
    }
}
