<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }
        foreach (['adm' => 'Administrador', 'gestor' => 'Gestor'] as $role => $name) {
            $user = User::firstOrCreate(['email' => "$role@nexus.test"], ['name' => $name, 'password' => 'password']);
            $user->forceFill(['role' => $role, 'must_change_password' => false])->save();
            if ($role === 'gestor') {
                $project = Project::firstOrCreate(['name' => 'Projeto Demo']);
                $project->users()->syncWithoutDetaching([$user->id]);
                $funnel = $project->funnels()->firstOrCreate(['name' => 'Funil Demo']);
                foreach (['Novo contato', 'Qualificação', 'Proposta', 'Negociação'] as $index => $stageName) {
                    $funnel->stages()->firstOrCreate(['name' => $stageName], ['position' => $index + 1]);
                }
                $examples = [
                    ['name' => 'Ana Souza', 'phone' => '11988887777', 'stage' => 'Novo contato', 'step' => 'Ligar para apresentar proposta', 'days' => -2, 'value' => '1500.00', 'status' => 'open'],
                    ['name' => 'Bruno Lima', 'phone' => '11977776666', 'stage' => 'Qualificação', 'step' => 'Confirmar orçamento', 'days' => 1, 'value' => '2800.00', 'status' => 'open'],
                    ['name' => 'Carla Mendes', 'phone' => '11966665555', 'stage' => 'Proposta', 'step' => 'Enviar proposta', 'days' => 3, 'value' => '4200.00', 'status' => 'open'],
                    ['name' => 'Daniel Rocha', 'phone' => '11955554444', 'stage' => 'Negociação', 'step' => 'Negociar prazo', 'days' => 5, 'value' => '3100.00', 'status' => 'open'],
                    ['name' => 'Elisa Costa', 'phone' => '11944443333', 'stage' => 'Proposta', 'step' => 'Acompanhar contrato', 'days' => -1, 'value' => '5400.00', 'status' => 'won'],
                ];
                foreach ($examples as $example) {
                    $person = $project->people()->firstOrCreate(['phone' => $example['phone']], ['name' => $example['name']]);
                    $deal = $funnel->deals()->firstOrCreate(
                        ['person_id' => $person->id],
                        ['stage_id' => $funnel->stages()->where('name', $example['stage'])->firstOrFail()->id,
                            'next_step' => $example['step'], 'next_step_date' => Carbon::now('America/Sao_Paulo')->addDays($example['days'])->toDateString(),
                            'value' => $example['value'], 'status' => $example['status'], 'closed_at' => $example['status'] === 'won' ? now() : null],
                    );
                    $deal->users()->syncWithoutDetaching([$user->id]);
                }
            }
        }
    }
}
