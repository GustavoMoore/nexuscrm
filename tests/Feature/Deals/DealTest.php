<?php

namespace Tests\Feature\Deals;

use App\Models\Deal;
use App\Models\Person;
use App\Models\Project;

class DealTest extends DealsTestCase
{
    // SPECSFY: AC-001
    public function test_a_c_001_cria_pessoa_e_negocio_na_primeira_etapa(): void
    {
        [$user, $project, $funnel, $url] = $this->context();
        $this->post($url, $this->payload())->assertRedirect($url)->assertSessionHas('success', 'Negócio criado.');
        $person = Person::where('project_id', $project->id)->where('phone', '11988887777')->firstOrFail();
        $deal = Deal::where('person_id', $person->id)->firstOrFail();
        $this->assertSame($funnel->stages->first()->id, $deal->stage_id);
        $this->assertSame('open', $deal->status);
        $this->assertNull($deal->value);
        $this->assertSame([$user->id], $deal->users->pluck('id')->all());
    }

    // SPECSFY: AC-002
    public function test_a_c_002_valida_campos_obrigatorios_e_valor(): void
    {
        [, , , $url] = $this->context();
        $this->post($url, $this->payload(['phone' => '', 'email' => '']))->assertSessionHasErrors('phone');
        foreach (['name', 'next_step', 'next_step_date'] as $field) {
            $this->post($url, $this->payload([$field => '']))->assertSessionHasErrors($field);
        }
        $this->post($url, $this->payload(['value' => '-10']))->assertSessionHasErrors('value');
        $this->post($url, $this->payload(['email' => 'nao-e-email']))->assertSessionHasErrors('email');
        $this->assertSame(0, Deal::count());
    }

    // SPECSFY: AC-003
    public function test_a_c_003_reusa_pessoa_pelo_telefone_sem_sobrescrever_nome(): void
    {
        [, $project, , $url] = $this->context();
        $person = $project->people()->create(['name' => 'Ana', 'phone' => '11988887777']);
        $this->post($url, $this->payload(['name' => 'Ana S.', 'phone' => '11 98888-7777', 'email' => 'ana@x.com']))->assertRedirect();
        $this->assertSame(1, $project->people()->count());
        $this->assertSame('Ana', $person->fresh()->name);
        $this->assertSame('ana@x.com', $person->fresh()->email);
        $this->assertSame($person->id, Deal::firstOrFail()->person_id);
    }

    // SPECSFY: AC-004
    public function test_a_c_004_reusa_email_sem_diferenciar_maiusculas_e_isola_projeto(): void
    {
        [, $project, , $url] = $this->context();
        $person = $project->people()->create(['name' => 'Bia', 'email' => 'bia@x.com']);
        $this->post($url, $this->payload(['phone' => '', 'email' => 'BIA@X.com']))->assertRedirect();
        $this->assertSame($person->id, Deal::firstOrFail()->person_id);
        $other = Project::create(['name' => 'Beta']);
        $otherFunnel = $other->funnels()->create(['name' => 'Perpétuo']);
        $otherFunnel->createDefaultStages();
        $other->users()->attach(auth()->id());
        $this->post("/projetos/{$other->id}/funis/{$otherFunnel->id}/negocios", $this->payload(['phone' => '', 'email' => 'BIA@X.com']))->assertRedirect();
        $this->assertSame(2, Person::count());
    }

    // SPECSFY: AC-005
    public function test_a_c_005_bloqueia_segundo_aberto_e_libera_apos_ganho(): void
    {
        [, , , $url] = $this->context();
        $this->post($url, $this->payload())->assertRedirect();
        $first = Deal::firstOrFail();
        $this->post($url, $this->payload())->assertSessionHasErrors('person')->assertSessionHas('existing_deal_id', $first->id);
        $this->assertSame(1, Deal::count());
        $this->post("$url/{$first->id}/ganhar")->assertRedirect();
        $this->post($url, $this->payload())->assertRedirect();
        $this->assertSame(2, Deal::count());
    }

    // SPECSFY: AC-006
    public function test_a_c_006_recusa_telefone_e_email_de_pessoas_diferentes(): void
    {
        [, $project, , $url] = $this->context();
        $project->people()->create(['name' => 'Ana', 'phone' => '11988887777']);
        $project->people()->create(['name' => 'Bia', 'email' => 'bia@x.com']);
        $this->post($url, $this->payload(['email' => 'bia@x.com']))->assertSessionHasErrors('email');
        $this->assertSame(0, Deal::count());
    }

    // SPECSFY: AC-010
    public function test_a_c_010_move_sem_alterar_proximo_passo_e_recusa_etapa_alheia(): void
    {
        [, $project, $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $stage = $funnel->stages->firstWhere('name', 'Proposta');
        $this->patch("$url/{$deal->id}", ['stage_id' => $stage->id])->assertRedirect();
        $this->assertSame($stage->id, $deal->fresh()->stage_id);
        $this->assertSame('Ligar', $deal->fresh()->next_step);
        $other = $project->funnels()->create(['name' => 'Outro']);
        $other->createDefaultStages();
        $this->patch("$url/{$deal->id}", ['stage_id' => $other->stages->first()->id])->assertSessionHasErrors('stage_id');
    }

    // SPECSFY: AC-011
    public function test_a_c_011_edita_negocio_e_pessoa_sem_colidir_contato(): void
    {
        [, $project, $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $project->people()->create(['name' => 'Bia', 'phone' => '11977776666']);
        $this->patch("$url/{$deal->id}", ['name' => 'Ana Souza', 'next_step' => 'Enviar proposta', 'next_step_date' => now()->addDays(2)->toDateString(), 'value' => '900'])->assertRedirect();
        $this->assertSame('Ana Souza', $deal->person->fresh()->name);
        $this->assertSame('Enviar proposta', $deal->fresh()->next_step);
        $this->patch("$url/{$deal->id}", ['phone' => '11977776666'])->assertSessionHasErrors('phone');
        $this->patch("$url/{$deal->id}", ['phone' => '', 'email' => ''])->assertSessionHasErrors('phone');
    }

    // SPECSFY: AC-012
    public function test_a_c_012_anotacoes_somente_acrescentam(): void
    {
        [$user, , $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $this->post("$url/{$deal->id}/anotacoes", ['body' => 'Pediu desconto'])->assertRedirect();
        $this->assertSame($user->id, $deal->notes()->firstOrFail()->user_id);
        $this->post("$url/{$deal->id}/anotacoes", ['body' => ''])->assertSessionHasErrors('body');
        $this->patch("$url/{$deal->id}/anotacoes/{$deal->notes()->first()->id}", ['body' => 'X'])->assertNotFound();
    }

    // SPECSFY: AC-013
    public function test_a_c_013_ganho_preserva_etapa_e_sai_do_quadro(): void
    {
        [, , $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $this->post("$url/{$deal->id}/ganhar")->assertRedirect();
        $this->assertSame('won', $deal->fresh()->status);
        $this->assertNotNull($deal->fresh()->closed_at);
        $this->assertSame($funnel->stages->first()->id, $deal->fresh()->stage_id);
        $this->get($url)->assertInertia(fn ($page) => $page->where('stages.0.count', 0));
    }

    // SPECSFY: AC-014
    public function test_a_c_014_perda_exige_motivo_ativo_do_projeto(): void
    {
        [, $project, $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $reason = $project->lossReasons()->firstOrFail();
        $reason->update(['deactivated_at' => now()]);
        $this->post("$url/{$deal->id}/perder", [])->assertSessionHasErrors('loss_reason_id');
        $this->post("$url/{$deal->id}/perder", ['loss_reason_id' => $reason->id])->assertSessionHasErrors('loss_reason_id');
        $reason->update(['deactivated_at' => null]);
        $this->post("$url/{$deal->id}/perder", ['loss_reason_id' => $reason->id])->assertRedirect();
        $this->assertSame('lost', $deal->fresh()->status);
    }

    // SPECSFY: AC-015
    public function test_a_c_015_reabrir_limpa_fechamento_e_recusa_duplicata(): void
    {
        [, , $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $deal->update(['status' => 'won', 'closed_at' => now()]);
        $this->post("$url/{$deal->id}/reabrir")->assertRedirect();
        $this->assertSame('open', $deal->fresh()->status);
        $this->assertNull($deal->fresh()->closed_at);
        $deal->refresh();
        $deal->update(['status' => 'won', 'closed_at' => now()]);
        $other = $funnel->deals()->create(['person_id' => $deal->person_id, 'stage_id' => $deal->stage_id, 'next_step' => 'Novo', 'next_step_date' => now()->toDateString(), 'status' => 'open']);
        $this->post("$url/{$deal->id}/reabrir")->assertSessionHasErrors('person')->assertSessionHas('existing_deal_id', $other->id);
        $this->assertSame('won', $deal->fresh()->status);
    }

    // SPECSFY: AC-016
    public function test_a_c_016_adm_gerencia_responsaveis_validos_e_preserva_antigos(): void
    {
        [, $project, $funnel, $url] = $this->context(true);
        $manager = $this->user();
        $project->users()->attach($manager);
        $deal = $this->deal($funnel);
        $unassigned = $this->user();
        $route = "$url/{$deal->id}/responsaveis";
        $this->put($route, ['user_ids' => []])->assertSessionHasErrors('user_ids');
        $this->put($route, ['user_ids' => [$unassigned->id]])->assertSessionHasErrors('user_ids');
        $this->put($route, ['user_ids' => [$manager->id]])->assertRedirect();
        $project->users()->detach($manager);
        $manager->forceFill(['deactivated_at' => now()])->save();
        $this->put($route, ['user_ids' => [$manager->id]])->assertRedirect();
        $this->actingAs($unassigned)->put($route, ['user_ids' => [$manager->id]])->assertForbidden();
    }

    // SPECSFY: AC-017
    public function test_a_c_017_adm_escolhe_responsavel_e_gestor_ignora_ids(): void
    {
        [, $project, , $url] = $this->context(true);
        $manager = $this->user();
        $project->users()->attach($manager);
        $this->post($url, $this->payload())->assertSessionHasErrors('user_ids');
        $this->post($url, $this->payload(['user_ids' => [$manager->id]]))->assertRedirect();
        $this->assertSame([$manager->id], Deal::firstOrFail()->users->pluck('id')->all());
        $this->actingAs($manager)->post($url, $this->payload(['name' => 'Bia', 'phone' => '11922223333', 'user_ids' => []]))->assertRedirect();
        $this->assertSame([$manager->id], Deal::latest('id')->firstOrFail()->users->pluck('id')->all());
    }

    // SPECSFY: AC-022
    public function test_a_c_022_isola_funil_e_arquivado_fica_somente_leitura(): void
    {
        [$manager, $alfa, $own] = $this->context();
        $beta = Project::create(['name' => 'Beta']);
        $other = $beta->funnels()->create(['name' => 'Outro']);
        $other->createDefaultStages();
        $deal = $this->deal($other);
        $url = "/projetos/{$beta->id}/funis/{$other->id}/negocios";
        $this->get($url)->assertForbidden();
        $this->patch("$url/{$deal->id}", ['next_step' => 'X'])->assertForbidden();
        $this->patch("/projetos/{$alfa->id}/funis/{$own->id}/negocios/{$deal->id}", ['next_step' => 'X'])->assertNotFound();
        $beta->users()->attach($manager);
        $other->forceFill(['archived_at' => now()])->save();
        $this->get($url)->assertNotFound();
        $this->actingAs($this->user('adm'))->get($url)->assertInertia(fn ($page) => $page->where('can.manage', false));
        $this->post($url, $this->payload(['user_ids' => [$manager->id]]))->assertForbidden();
        $this->patch("$url/{$deal->id}", ['next_step' => 'Outro'])->assertForbidden();
        $this->post("$url/{$deal->id}/anotacoes", ['body' => 'Teste'])->assertForbidden();
        $this->post("$url/{$deal->id}/ganhar")->assertForbidden();
        $this->post("$url/{$deal->id}/perder", ['loss_reason_id' => $beta->lossReasons()->first()->id])->assertForbidden();
        $this->post("$url/{$deal->id}/reabrir")->assertForbidden();
        $this->put("$url/{$deal->id}/responsaveis", ['user_ids' => [$manager->id]])->assertForbidden();
        $other->forceFill(['archived_at' => null])->save();
        $beta->forceFill(['archived_at' => now()])->save();
        $this->get($url)->assertInertia(fn ($page) => $page->where('can.manage', false));
        $this->post("$url/{$deal->id}/anotacoes", ['body' => 'Teste'])->assertForbidden();
    }

    // SPECSFY: AC-025
    public function test_a_c_025_encerrado_recusa_edicao_e_aceita_anotacao(): void
    {
        [, , $funnel, $url] = $this->context();
        $deal = $this->deal($funnel);
        $deal->update(['status' => 'won', 'closed_at' => now()]);
        $name = $deal->person->name;
        $this->patch("$url/{$deal->id}", ['name' => 'Mudado', 'stage_id' => $funnel->stages->last()->id])->assertSessionHasErrors('status');
        $this->post("$url/{$deal->id}/ganhar")->assertSessionHasErrors('status');
        $this->post("$url/{$deal->id}/perder", ['loss_reason_id' => 1])->assertSessionHasErrors('status');
        $this->assertSame($name, $deal->person->fresh()->name);
        $this->post("$url/{$deal->id}/anotacoes", ['body' => 'Depois'])->assertRedirect();
    }
}
