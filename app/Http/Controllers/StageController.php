<?php

namespace App\Http\Controllers;

use App\Models\Funnel;
use App\Models\Project;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StageController extends Controller
{
    private function validated(Request $request, Funnel $funnel, ?Stage $stage = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($funnel, $stage) {
            if ($funnel->stages()->whereRaw('lower(name) = lower(?)', [$value])->when($stage, fn ($query) => $query->whereKeyNot($stage->id))->exists()) {
                $fail('Já existe uma etapa com esse nome neste funil.');
            }
        }]]);
    }

    public function store(Request $request, Project $project, Funnel $funnel)
    {
        $data = $this->validated($request, $funnel);
        DB::transaction(function () use ($funnel, $data) {
            $funnel->stages()->create(['name' => $data['name'], 'position' => $funnel->stages()->max('position') + 1]);
        });

        return back()->with('success', 'Etapa criada.');
    }

    public function update(Request $request, Project $project, Funnel $funnel, Stage $stage)
    {
        $stage->update($this->validated($request, $funnel, $stage));

        return back()->with('success', 'Etapa atualizada.');
    }

    public function move(Request $request, Project $project, Funnel $funnel, Stage $stage)
    {
        $data = $request->validate(['direction' => ['required', 'in:up,down']]);
        DB::transaction(function () use ($funnel, $stage, $data) {
            $current = $funnel->stages()->whereKey($stage->id)->lockForUpdate()->firstOrFail();
            $neighbor = $funnel->stages()->where('position', $data['direction'] === 'up' ? '<' : '>', $current->position)
                ->reorder('position', $data['direction'] === 'up' ? 'desc' : 'asc')->lockForUpdate()->first();
            if ($neighbor) {
                $position = $current->position;
                $current->update(['position' => $neighbor->position]);
                $neighbor->update(['position' => $position]);
            }
        });

        return back()->with('success', 'Etapa movida.');
    }

    public function destroy(Project $project, Funnel $funnel, Stage $stage)
    {
        DB::transaction(function () use ($funnel, $stage) {
            $stages = $funnel->stages()->lockForUpdate()->get();
            if ($stages->count() <= 1) {
                throw ValidationException::withMessages(['stage' => 'O funil deve manter ao menos uma etapa.']);
            }
            $stage->delete();
            foreach ($stages->where('id', '!=', $stage->id)->values() as $index => $remaining) {
                $remaining->update(['position' => $index + 1]);
            }
        });

        return back()->with('success', 'Etapa apagada.');
    }
}
