<?php

namespace Tests\Feature\Deals;

use Carbon\Carbon;

class DealBoardTest extends DealsTestCase
{
    // SPECSFY: AC-007
    public function test_a_c_007_quadro_ordena_soma_e_marca_atrasados(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-01 12:00', 'America/Sao_Paulo'));
        [, , $funnel, $url] = $this->context();
        $yesterday = $this->deal($funnel, ['next_step_date' => '2026-09-30', 'value' => '100']);
        $today = $this->deal($funnel, ['next_step_date' => '2026-10-01']);
        $tomorrow = $this->deal($funnel, ['next_step_date' => '2026-10-02', 'value' => '50.50']);
        $won = $this->deal($funnel, ['status' => 'won', 'closed_at' => now()]);
        $this->get($url)->assertInertia(fn ($page) => $page->component('negocios/index')->has('stages', 4)
            ->where('stages.0.count', 3)->where('stages.0.total', '150.50')
            ->where('stages.0.deals.0.id', $yesterday->id)->where('stages.0.deals.0.overdue', true)
            ->where('stages.0.deals.1.id', $today->id)->where('stages.0.deals.1.overdue', false)
            ->where('stages.0.deals.2.id', $tomorrow->id)->where('stages.0.deals.2.overdue', false)
            ->missing('stages.0.deals.3'));
        Carbon::setTestNow();
    }

    // SPECSFY: AC-008
    public function test_a_c_008_busca_nome_telefone_email_e_escapa_curingas(): void
    {
        [, , $funnel, $url] = $this->context();
        $ana = $this->deal($funnel);
        $ana->person->update(['name' => 'Ana Souza', 'phone' => '11988887777']);
        $bia = $this->deal($funnel);
        $bia->person->update(['name' => 'Bia', 'email' => 'bia@x.com']);
        $this->get($url.'?q=souza')->assertInertia(fn ($page) => $page->where('stages.0.count', 1)->where('stages.0.deals.0.id', $ana->id));
        $this->get($url.'?q=98888-77')->assertInertia(fn ($page) => $page->where('stages.0.count', 1)->where('stages.0.deals.0.id', $ana->id));
        $this->get($url.'?q=BIA@')->assertInertia(fn ($page) => $page->where('stages.0.count', 1)->where('stages.0.deals.0.id', $bia->id));
        $this->get($url.'?q=%25')->assertInertia(fn ($page) => $page->where('stages.0.count', 0));
    }

    // SPECSFY: AC-009
    public function test_a_c_009_lista_e_encerrados_em_ordem(): void
    {
        [, $project, $funnel, $url] = $this->context();
        $open = $this->deal($funnel, ['next_step_date' => '2026-10-01']);
        $this->deal($funnel, ['next_step_date' => '2026-10-02']);
        $won = $this->deal($funnel, ['status' => 'won', 'closed_at' => '2026-10-01 10:00:00']);
        $lost = $this->deal($funnel, ['status' => 'lost', 'closed_at' => '2026-10-02 10:00:00', 'loss_reason_id' => $project->lossReasons()->first()->id]);
        $this->get($url.'?view=lista')->assertInertia(fn ($page) => $page->where('view', 'lista')->has('deals', 2)->where('deals.0.id', $open->id));
        $this->get($url.'?closed=1&view=quadro')->assertInertia(fn ($page) => $page->where('view', 'lista')->has('deals', 2)
            ->where('deals.0.id', $lost->id)->where('deals.0.status', 'lost')->where('deals.1.id', $won->id));
    }

    // SPECSFY: AC-023
    public function test_a_c_023_painel_entrega_detalhe_e_isola_outro_funil(): void
    {
        [, $project, $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $deal->notes()->create(['user_id' => auth()->id(), 'body' => 'Primeira']);
        $deal->notes()->create(['user_id' => auth()->id(), 'body' => 'Segunda']);
        $this->get($url.'?negocio='.$deal->id)->assertInertia(fn ($page) => $page->where('deal.id', $deal->id)
            ->where('deal.person.id', $deal->person_id)->has('deal.notes', 2)->where('deal.notes.0.body', 'Segunda')
            ->has('loss_reasons', 5)->has('project_managers')->where('can.manage_assignees', false));
        $other = $project->funnels()->create(['name' => 'Outro']);
        $other->createDefaultStages();
        $this->get("/projetos/{$project->id}/funis/{$other->id}/negocios?negocio={$deal->id}")->assertInertia(fn ($page) => $page->where('deal', null));
    }
}
