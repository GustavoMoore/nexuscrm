<?php

namespace App\Http\Controllers;

use App\Models\LossReason;
use App\Models\Project;
use Illuminate\Http\Request;

class LossReasonController extends Controller
{
    private function validated(Request $request, Project $project, ?LossReason $reason = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($project, $reason) {
            if ($project->lossReasons()->whereRaw('lower(name) = lower(?)', [$value])
                ->when($reason, fn ($query) => $query->whereKeyNot($reason->id))->exists()) {
                $fail('Já existe um motivo com esse nome neste projeto.');
            }
        }]]);
    }

    public function store(Request $request, Project $project)
    {
        $project->lossReasons()->create($this->validated($request, $project));

        return back()->with('success', 'Motivo criado.');
    }

    public function update(Request $request, Project $project, LossReason $lossReason)
    {
        $lossReason->update($this->validated($request, $project, $lossReason));

        return back()->with('success', 'Motivo atualizado.');
    }

    public function deactivate(Project $project, LossReason $lossReason)
    {
        $lossReason->update(['deactivated_at' => now()]);

        return back()->with('success', 'Motivo desativado.');
    }
}
