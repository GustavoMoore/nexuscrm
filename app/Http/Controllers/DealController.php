<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Funnel;
use App\Models\Person;
use App\Models\Project;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DealController extends Controller
{
    private const DUPLICATE = 'Esta pessoa já tem um negócio aberto neste funil.';

    private function authorizeView(Request $request, Funnel $funnel): void
    {
        if (! $request->user()->isAdm() && $funnel->archived_at !== null) {
            abort(404);
        }
        Gate::authorize('view', $funnel);
    }

    private function authorizeWrite(Request $request, Funnel $funnel): void
    {
        Gate::authorize('view', $funnel);
        abort_if($funnel->archived_at !== null || $funnel->project->archived_at !== null, 403);
    }

    private function duplicate(Deal $existing): never
    {
        session()->flash('existing_deal_id', $existing->id);
        throw ValidationException::withMessages(['person' => self::DUPLICATE]);
    }

    private function openDeal(Funnel $funnel, Person $person, ?Deal $except = null): ?Deal
    {
        return $funnel->deals()->where('person_id', $person->id)->where('status', 'open')
            ->when($except, fn ($query) => $query->whereKeyNot($except->id))->first();
    }

    private function personData(Request $request, bool $partial = false): array
    {
        $rules = [
            'name' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
        $data = $request->validate($rules);
        if (array_key_exists('phone', $data)) {
            $data['phone'] = preg_replace('/\D/', '', $data['phone'] ?? '') ?: null;
            if ($data['phone'] !== null && (strlen($data['phone']) < 8 || strlen($data['phone']) > 15)) {
                throw ValidationException::withMessages(['phone' => 'Informe um telefone com 8 a 15 dígitos.']);
            }
        }
        if (array_key_exists('email', $data)) {
            $data['email'] = $data['email'] ? mb_strtolower($data['email']) : null;
        }

        return $data;
    }

    private function resolvePerson(Project $project, array $data, ?Person $current = null): array
    {
        $phone = array_key_exists('phone', $data) ? $data['phone'] : $current?->phone;
        $email = array_key_exists('email', $data) ? $data['email'] : $current?->email;
        if (! $phone && ! $email) {
            throw ValidationException::withMessages(['phone' => 'Informe telefone ou e-mail.']);
        }
        $byPhone = $phone ? $project->people()->where('phone', $phone)->first() : null;
        $byEmail = $email ? $project->people()->whereRaw('lower(email) = ?', [$email])->first() : null;
        if ($byPhone && $byEmail && $byPhone->id !== $byEmail->id) {
            throw ValidationException::withMessages(['email' => 'Telefone e e-mail pertencem a pessoas diferentes.']);
        }
        if ($current) {
            if ($byPhone && $byPhone->id !== $current->id) {
                throw ValidationException::withMessages(['phone' => 'Este telefone já pertence a outra pessoa.']);
            }
            if ($byEmail && $byEmail->id !== $current->id) {
                throw ValidationException::withMessages(['email' => 'Este e-mail já pertence a outra pessoa.']);
            }
            $current->update(array_intersect_key($data, array_flip(['name', 'phone', 'email'])));

            return [$current, false];
        }
        $person = $byPhone ?? $byEmail;
        if ($person) {
            $fill = [];
            foreach (['phone', 'email'] as $field) {
                if (! $person->$field && ! empty($data[$field])) {
                    $fill[$field] = $data[$field];
                }
            }
            if ($fill) {
                $person->update($fill);
            }

            return [$person, true];
        }

        return [$project->people()->create(['name' => $data['name'], 'phone' => $phone, 'email' => $email]), false];
    }

    private function dealData(Request $request, bool $partial = false): array
    {
        return $request->validate([
            'next_step' => [$partial ? 'sometimes' : 'required', 'string', 'max:255'],
            'next_step_date' => [$partial ? 'sometimes' : 'required', 'date'],
            'value' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'stage_id' => ['sometimes', 'integer'],
        ]);
    }

    private function validateStage(Funnel $funnel, int $id): void
    {
        if (! $funnel->stages()->whereKey($id)->exists()) {
            throw ValidationException::withMessages(['stage_id' => 'Escolha uma etapa deste funil.']);
        }
    }

    private function assigneeIds(Project $project, array $ids, array $current = []): array
    {
        if (! $ids) {
            throw ValidationException::withMessages(['user_ids' => 'Selecione ao menos um responsável.']);
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        foreach ($ids as $id) {
            if (in_array($id, $current, true)) {
                continue;
            }
            if (! $project->users()->whereKey($id)->where('role', 'gestor')->whereNull('deactivated_at')->exists()) {
                throw ValidationException::withMessages(['user_ids' => 'Escolha gestores ativos atribuídos ao projeto.']);
            }
        }

        return $ids;
    }

    private function requestedAssignees(Request $request, Project $project): array
    {
        $data = $request->validate(['user_ids' => ['required', 'array', 'min:1'], 'user_ids.*' => ['required', 'integer', 'distinct']]);

        return $this->assigneeIds($project, $data['user_ids']);
    }

    private function handleUnique(UniqueConstraintViolationException $e, Funnel $funnel, array $personData): bool
    {
        $message = $e->getMessage();
        if (str_contains($message, 'deals_one_open_per_person_funnel')) {
            $phone = $personData['phone'] ?? null;
            $email = $personData['email'] ?? null;
            $person = $funnel->project->people()->where(function ($query) use ($phone, $email) {
                if ($phone) {
                    $query->where('phone', $phone);
                }
                if ($email) {
                    $query->orWhereRaw('lower(email) = ?', [$email]);
                }
            })->first();
            if ($person && ($existing = $this->openDeal($funnel, $person))) {
                $this->duplicate($existing);
            }
            throw ValidationException::withMessages(['person' => self::DUPLICATE]);
        }
        if (str_contains($message, 'people_project') || str_contains($message, 'people_project_id_phone_unique')) {
            return true;
        }
        throw $e;
    }

    public function store(Request $request, Project $project, Funnel $funnel)
    {
        $this->authorizeWrite($request, $funnel);
        $personData = $this->personData($request);
        $dealData = $this->dealData($request);
        $ids = $request->user()->isAdm() ? $this->requestedAssignees($request, $project) : [$request->user()->id];
        $stage = $funnel->stages()->firstOrFail();
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                [$deal, $recognized] = DB::transaction(function () use ($project, $funnel, $personData, $dealData, $ids, $stage) {
                    [$person, $recognized] = $this->resolvePerson($project, $personData);
                    if ($existing = $this->openDeal($funnel, $person)) {
                        $this->duplicate($existing);
                    }
                    $deal = $funnel->deals()->create([...$dealData, 'person_id' => $person->id, 'stage_id' => $stage->id, 'status' => 'open']);
                    $deal->users()->sync($ids);

                    return [$deal, $recognized];
                });
                if ($recognized) {
                    session()->flash('person_notice', 'Pessoa existente reutilizada.');
                }

                return redirect("/projetos/{$project->id}/funis/{$funnel->id}/negocios")
                    ->with('success', 'Negócio criado.');
            } catch (UniqueConstraintViolationException $e) {
                if (! $this->handleUnique($e, $funnel, $personData) || $attempt === 1) {
                    throw $e;
                }
            }
        }
    }

    public function index(Request $request, Project $project, Funnel $funnel)
    {
        $this->authorizeView($request, $funnel);
        $closed = $request->query('closed') === '1';
        $view = $closed ? 'lista' : ($request->query('view') === 'lista' ? 'lista' : 'quadro');
        $q = trim((string) $request->query('q', ''));
        $query = $funnel->deals()->with(['person', 'users', 'stage', 'lossReason'])->where('status', $closed ? '!=' : '=', 'open');
        if ($q !== '') {
            $escaped = addcslashes($q, '%_\\');
            $digits = preg_replace('/\D/', '', $q);
            $query->whereHas('person', function ($people) use ($escaped, $digits) {
                $people->where(function ($search) use ($escaped, $digits) {
                    $search->whereRaw("name ILIKE ? ESCAPE '\\'", ["%$escaped%"])
                        ->orWhereRaw("email ILIKE ? ESCAPE '\\'", ["%$escaped%"]);
                    if (strlen($digits) >= 3) {
                        $search->orWhere('phone', 'like', "%$digits%");
                    }
                });
            });
        }
        $deals = $closed ? $query->orderByDesc('closed_at')->orderBy('id')->get() : $query->orderBy('next_step_date')->orderBy('id')->get();
        $today = now('America/Sao_Paulo')->toDateString();
        $map = fn (Deal $deal) => [
            'id' => $deal->id, 'person' => $deal->person->only(['id', 'name', 'phone', 'email']),
            'stage_id' => $deal->stage_id, 'stage' => $deal->stage->only(['id', 'name']),
            'value' => $deal->value, 'next_step' => $deal->next_step,
            'next_step_date' => $deal->next_step_date->toDateString(), 'overdue' => $deal->status === 'open' && $deal->next_step_date->toDateString() < $today,
            'status' => $deal->status, 'closed_at' => $deal->closed_at?->toISOString(),
            'loss_reason' => $deal->lossReason?->only(['id', 'name']),
            'assignees' => $deal->users->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name, 'initials' => collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('')])->values(),
        ];
        $stages = $funnel->stages()->get()->map(function (Stage $stage) use ($deals, $map) {
            $inStage = $deals->where('stage_id', $stage->id);

            return ['id' => $stage->id, 'name' => $stage->name, 'position' => $stage->position,
                'count' => $inStage->count(), 'total' => number_format((float) $inStage->sum('value'), 2, '.', ''),
                'deals' => $inStage->map($map)->values()];
        });
        $detailId = filter_var($request->query('negocio'), FILTER_VALIDATE_INT);
        $detail = $detailId ? $funnel->deals()->with(['person', 'users', 'stage', 'lossReason', 'notes.author'])->find($detailId) : null;
        $detailData = $detail ? [...$map($detail), 'notes' => $detail->notes->map(fn ($note) => [
            'id' => $note->id, 'body' => $note->body, 'created_at' => $note->created_at->toISOString(),
            'author' => $note->author->only(['id', 'name']),
        ])->values()] : null;

        return Inertia::render('negocios/index', [
            'project' => $project->only(['id', 'name']), 'funnel' => $funnel->only(['id', 'name', 'archived_at']),
            'stages' => $stages, 'deals' => $view === 'lista' ? $deals->map($map) : [], 'deal' => $detailData,
            'view' => $view, 'q' => $q, 'closed' => $closed,
            'loss_reasons' => $project->lossReasons()->whereNull('deactivated_at')->orderBy('name')->get(['id', 'name']),
            'project_managers' => $project->users()->where('role', 'gestor')->get(['users.id', 'users.name', 'users.deactivated_at']),
            'can' => ['manage' => $funnel->archived_at === null && $project->archived_at === null,
                'manage_assignees' => $request->user()->isAdm() && $funnel->archived_at === null && $project->archived_at === null],
        ]);
    }

    public function update(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        if ($deal->status !== 'open') {
            throw ValidationException::withMessages(['status' => 'Negócio encerrado não pode ser editado.']);
        }
        $personData = $this->personData($request, true);
        $dealData = $this->dealData($request, true);
        if (isset($dealData['stage_id'])) {
            $this->validateStage($funnel, (int) $dealData['stage_id']);
        }
        DB::transaction(function () use ($project, $deal, $personData, $dealData) {
            if ($personData) {
                $this->resolvePerson($project, $personData, $deal->person);
            }
            $deal->update($dealData);
        });

        return back()->with('success', isset($dealData['stage_id']) && count($dealData) === 1 ? 'Negócio movido.' : 'Negócio atualizado.');
    }

    public function note(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $deal->notes()->create(['user_id' => $request->user()->id, 'body' => $data['body']]);

        return back()->with('success', 'Anotação adicionada.');
    }

    private function requireOpen(Deal $deal): void
    {
        if ($deal->status !== 'open') {
            throw ValidationException::withMessages(['status' => 'O negócio já está encerrado.']);
        }
    }

    public function win(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        $this->requireOpen($deal);
        $deal->update(['status' => 'won', 'loss_reason_id' => null, 'closed_at' => now()]);

        return back()->with('success', 'Negócio ganho.');
    }

    public function lose(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        $this->requireOpen($deal);
        $data = $request->validate(['loss_reason_id' => ['required', 'integer', function ($attribute, $value, $fail) use ($project) {
            if (! $project->lossReasons()->whereKey($value)->whereNull('deactivated_at')->exists()) {
                $fail('Escolha um motivo ativo deste projeto.');
            }
        }]]);
        $deal->update(['status' => 'lost', 'loss_reason_id' => $data['loss_reason_id'], 'closed_at' => now()]);

        return back()->with('success', 'Negócio perdido.');
    }

    public function reopen(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        if ($deal->status === 'open') {
            throw ValidationException::withMessages(['status' => 'O negócio já está aberto.']);
        }
        try {
            DB::transaction(function () use ($funnel, $deal) {
                if ($existing = $this->openDeal($funnel, $deal->person, $deal)) {
                    $this->duplicate($existing);
                }
                $deal->update(['status' => 'open', 'loss_reason_id' => null, 'closed_at' => null]);
            });
        } catch (UniqueConstraintViolationException $e) {
            $this->handleUnique($e, $funnel, ['phone' => $deal->person->phone, 'email' => $deal->person->email]);
            throw $e;
        }

        return back()->with('success', 'Negócio reaberto.');
    }

    public function assignees(Request $request, Project $project, Funnel $funnel, Deal $deal)
    {
        $this->authorizeWrite($request, $funnel);
        $data = $request->validate(['user_ids' => ['required', 'array', 'min:1'], 'user_ids.*' => ['integer', 'distinct']]);
        $ids = $this->assigneeIds($project, $data['user_ids'], $deal->users()->pluck('users.id')->all());
        $deal->users()->sync($ids);

        return back()->with('success', 'Responsáveis atualizados.');
    }
}
