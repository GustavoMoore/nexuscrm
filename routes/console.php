<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('nexus:criar-adm', function () {
    if (User::where('role', 'adm')->exists()) {
        $this->error('Já existe um administrador.');

        return 1;
    }
    $name = $this->ask('Nome');
    $email = Str::lower($this->ask('E-mail'));
    $password = $this->secret('Senha');
    if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8 || User::where('email', $email)->exists()) {
        $this->error('Dados inválidos ou e-mail já cadastrado.');

        return 1;
    }
    $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
    $user->forceFill(['role' => 'adm', 'must_change_password' => false])->save();
    $this->info('Administrador criado.');
})->purpose('Criar o primeiro administrador');
