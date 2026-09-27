<?php

namespace Tests\Feature\Deals;

use App\Models\Deal;
use App\Models\Funnel;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

abstract class DealsTestCase extends TestCase
{
    use DatabaseTransactions;

    protected function user(string $role = 'gestor'): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    protected function context(bool $admin = false): array
    {
        $user = $this->user($admin ? 'adm' : 'gestor');
        $project = Project::create(['name' => 'Projeto '.uniqid()]);
        if (! $admin) {
            $project->users()->attach($user);
        }
        $funnel = $project->funnels()->create(['name' => 'Lançamento']);
        $funnel->createDefaultStages();
        $this->actingAs($user);

        return [$user, $project, $funnel, "/projetos/{$project->id}/funis/{$funnel->id}/negocios"];
    }

    protected function payload(array $overrides = []): array
    {
        return array_merge(['name' => 'Ana Souza', 'phone' => '(11) 98888-7777', 'next_step' => 'Ligar', 'next_step_date' => now()->addDay()->toDateString()], $overrides);
    }

    protected function deal(Funnel $funnel, array $overrides = []): Deal
    {
        $person = $funnel->project->people()->create(['name' => 'Pessoa '.uniqid(), 'phone' => (string) random_int(10000000000, 99999999999)]);
        $deal = $funnel->deals()->create(array_merge(['stage_id' => $funnel->stages()->firstOrFail()->id, 'person_id' => $person->id, 'next_step' => 'Ligar', 'next_step_date' => now()->addDay()->toDateString(), 'status' => 'open'], $overrides));
        $deal->users()->attach(auth()->id());

        return $deal;
    }
}
