<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

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
            }
        }
    }
}
