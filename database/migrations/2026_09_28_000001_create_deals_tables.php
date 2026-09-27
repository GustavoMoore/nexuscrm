<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'phone']);
        });
        DB::statement('ALTER TABLE people ADD CONSTRAINT people_contact_check CHECK (phone IS NOT NULL OR email IS NOT NULL)');
        DB::statement("ALTER TABLE people ADD CONSTRAINT people_phone_check CHECK (phone IS NULL OR phone ~ '^[0-9]{8,15}$')");
        DB::statement('CREATE UNIQUE INDEX people_project_email_lower_unique ON people (project_id, lower(email))');

        Schema::create('loss_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
        });
        DB::statement('CREATE UNIQUE INDEX loss_reasons_project_name_lower_unique ON loss_reasons (project_id, lower(name))');

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained()->restrictOnDelete();
            $table->foreignId('person_id')->constrained('people')->restrictOnDelete();
            $table->decimal('value', 12, 2)->nullable();
            $table->string('next_step');
            $table->date('next_step_date');
            $table->string('status')->default('open');
            $table->foreignId('loss_reason_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['funnel_id', 'status', 'next_step_date']);
        });
        DB::statement("ALTER TABLE deals ADD CONSTRAINT deals_status_check CHECK (status IN ('open', 'won', 'lost'))");
        DB::statement("CREATE UNIQUE INDEX deals_one_open_per_person_funnel ON deals (funnel_id, person_id) WHERE status = 'open'");

        Schema::create('deal_user', function (Blueprint $table) {
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['deal_id', 'user_id']);
        });
        Schema::create('deal_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->text('body');
            $table->timestamps();
        });
        foreach (DB::table('projects')->pluck('id') as $projectId) {
            foreach (['Preço', 'Sem resposta', 'Comprou de outro', 'Sem interesse', 'Outro'] as $name) {
                DB::table('loss_reasons')->insert(['project_id' => $projectId, 'name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        // Este projeto não remove tabelas em reversões automáticas.
    }
};
