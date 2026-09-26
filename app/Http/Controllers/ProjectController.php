<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $adm = $request->user()->isAdm();
        $query = Project::visibleTo($request->user())->with('users')->orderBy('id');
        if ($adm) {
            $request->query('status') === 'arquivados' ? $query->whereNotNull('archived_at') : $query->active();
        }
        $projects = $query->get()->map(fn (Project $p) => ['id' => $p->id, 'name' => $p->name, 'archived_at' => $p->archived_at, 'users' => $p->users->map->only(['id', 'name', 'deactivated_at'])]);

        return Inertia::render('projetos/index', ['projects' => $projects, 'gestores' => $adm ? User::where('role', 'gestor')->get(['id', 'name', 'deactivated_at']) : [], 'can' => ['create' => $adm, 'update' => $adm, 'archive' => $adm], 'status' => $request->query('status', 'ativos')]);
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        return Inertia::render('projetos/show', ['project' => ['id' => $project->id, 'name' => $project->name, 'users' => $project->users()->get(['users.id', 'users.name'])], 'can' => ['update' => request()->user()->isAdm()]]);
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('projects', 'name')->ignore($project?->id), function ($attribute, $value, $fail) use ($project) {
            if (Project::whereRaw('lower(name) = lower(?)', [$value])->when($project, fn ($q) => $q->where('id', '!=', $project->id))->exists()) {
                $fail('Já existe um projeto com esse nome.');
            }
        }], 'user_ids' => ['sometimes', 'array'], 'user_ids.*' => ['integer', Rule::exists('users', 'id')->where('role', 'gestor')]]);
    }

    private function sync(Project $project, Request $request, array $data): void
    {
        $keep = $project->users()->whereNotNull('deactivated_at')->pluck('users.id')->all();
        $project->users()->sync(array_unique(array_merge($data['user_ids'] ?? [], $keep)));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);
        $data = $this->validated($request);
        $project = Project::create(['name' => $data['name']]);
        $this->sync($project, $request, $data);

        return back()->with('success', 'Projeto criado.');
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        $data = $this->validated($request, $project);
        $project->update(['name' => $data['name']]);
        $this->sync($project, $request, $data);

        return back()->with('success', 'Projeto atualizado.');
    }

    public function archive(Project $project)
    {
        Gate::authorize('archive', $project);
        $project->forceFill(['archived_at' => now()])->save();

        return back()->with('success', 'Projeto arquivado.');
    }

    public function unarchive(Project $project)
    {
        Gate::authorize('unarchive', $project);
        $project->forceFill(['archived_at' => null])->save();

        return back()->with('success', 'Projeto desarquivado.');
    }
}
