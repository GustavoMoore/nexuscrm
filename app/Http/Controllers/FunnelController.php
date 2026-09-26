<?php

namespace App\Http\Controllers;

use App\Models\Funnel;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class FunnelController extends Controller
{
    private function validated(Request $request, Project $project, ?Funnel $funnel = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($project, $funnel) {
            if ($project->funnels()->whereRaw('lower(name) = lower(?)', [$value])->when($funnel, fn ($query) => $query->whereKeyNot($funnel->id))->exists()) {
                $fail('Já existe um funil com esse nome neste projeto.');
            }
        }]]);
    }

    public function store(Request $request, Project $project)
    {
        Gate::authorize('create', Funnel::class);
        $data = $this->validated($request, $project);
        $funnel = DB::transaction(function () use ($project, $data) {
            $funnel = $project->funnels()->create($data);
            $funnel->createDefaultStages();

            return $funnel;
        });

        return redirect("/projetos/{$project->id}/funis/{$funnel->id}")->with('success', 'Funil criado.');
    }

    public function show(Request $request, Project $project, Funnel $funnel)
    {
        Gate::authorize('view', $project);
        if (! $request->user()->isAdm() && $funnel->archived_at !== null) {
            abort(404);
        }
        Gate::authorize('view', $funnel);

        return Inertia::render('funis/show', [
            'project' => $project->only(['id', 'name']),
            'funnel' => $funnel->only(['id', 'name', 'archived_at']),
            'stages' => $funnel->stages()->get(['id', 'name', 'position']),
            'can' => ['manage' => $request->user()->isAdm()],
        ]);
    }

    public function update(Request $request, Project $project, Funnel $funnel)
    {
        Gate::authorize('update', $funnel);
        $funnel->update($this->validated($request, $project, $funnel));

        return back()->with('success', 'Funil atualizado.');
    }

    public function archive(Project $project, Funnel $funnel)
    {
        Gate::authorize('archive', $funnel);
        $funnel->forceFill(['archived_at' => now()])->save();

        return back()->with('success', 'Funil arquivado.');
    }

    public function unarchive(Project $project, Funnel $funnel)
    {
        Gate::authorize('unarchive', $funnel);
        $funnel->forceFill(['archived_at' => null])->save();

        return back()->with('success', 'Funil desarquivado.');
    }
}
