<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
        DB::statement('CREATE UNIQUE INDEX funnels_project_name_lower_unique ON funnels (project_id, lower(name))');

        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('position');
            $table->timestamps();
        });
        DB::statement('CREATE UNIQUE INDEX stages_funnel_name_lower_unique ON stages (funnel_id, lower(name))');
    }

    public function down(): void
    {
        // Reversão estrutural omitida: a política deste projeto proíbe remoção de tabelas.
    }
};
