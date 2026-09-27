import { Manager } from '@/components/deal-form-sheet';
import InputError from '@/components/input-error';
import { LossReason } from '@/components/loss-reason-form-sheet';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { router, useForm } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

export type Deal = {
    id: number;
    person: { id: number; name: string; phone: string | null; email: string | null };
    stage_id: number;
    stage: { id: number; name: string };
    value: string | null;
    next_step: string;
    next_step_date: string;
    overdue: boolean;
    status: 'open' | 'won' | 'lost';
    closed_at: string | null;
    loss_reason: LossReason | null;
    assignees: { id: number; name: string; initials: string }[];
    notes?: { id: number; body: string; created_at: string; author: { id: number; name: string } }[];
};

export function DealSheet({
    base,
    deal,
    stages,
    reasons,
    managers,
    canManage,
    canManageAssignees,
    onClose,
}: {
    base: string;
    deal: Deal;
    stages: { id: number; name: string }[];
    reasons: LossReason[];
    managers: Manager[];
    canManage: boolean;
    canManageAssignees: boolean;
    onClose: () => void;
}) {
    const [losing, setLosing] = useState(false);
    const edit = useForm({
        name: deal.person.name,
        phone: deal.person.phone ?? '',
        email: deal.person.email ?? '',
        stage_id: deal.stage_id,
        value: deal.value ?? '',
        next_step: deal.next_step,
        next_step_date: deal.next_step_date,
    });
    const note = useForm({ body: '' });
    const loss = useForm({ loss_reason_id: '' });
    const assignees = useForm({ user_ids: deal.assignees.map((user) => user.id) });
    const managerOptions = [
        ...managers,
        ...deal.assignees
            .filter((user) => !managers.some((manager) => manager.id === user.id))
            .map((user) => ({ id: user.id, name: `${user.name} (anterior)`, deactivated_at: null })),
    ];
    const route = `${base}/${deal.id}`;
    const save = (event: FormEvent) => {
        event.preventDefault();
        edit.patch(route, { preserveScroll: true });
    };
    const addNote = (event: FormEvent) => {
        event.preventDefault();
        note.post(`${route}/anotacoes`, { preserveScroll: true, onSuccess: () => note.reset() });
    };
    const lose = (event: FormEvent) => {
        event.preventDefault();
        loss.post(`${route}/perder`, { preserveScroll: true, onSuccess: () => setLosing(false) });
    };
    const action = (name: 'ganhar' | 'reabrir') => router.post(`${route}/${name}`, {}, { preserveScroll: true });

    return (
        <Sheet
            open
            onOpenChange={(open) => {
                if (!open) onClose();
            }}
        >
            <SheetContent side="right" className="flex w-full flex-col gap-6 overflow-y-auto sm:max-w-2xl">
                <SheetHeader>
                    <SheetTitle>{deal.person.name}</SheetTitle>
                    <SheetDescription>
                        Negócio #{deal.id} · {deal.status === 'open' ? deal.stage.name : deal.status === 'won' ? 'Ganho' : 'Perdido'}
                    </SheetDescription>
                </SheetHeader>
                {deal.status !== 'open' && (
                    <p className="rounded-md border p-3 text-sm">
                        Encerrado em {deal.closed_at ? new Date(deal.closed_at).toLocaleString('pt-BR') : '—'}
                        {deal.loss_reason ? ` · ${deal.loss_reason.name}` : ''}
                    </p>
                )}
                <form onSubmit={save} className="space-y-5">
                    <div className="grid gap-4 sm:grid-cols-2">
                        {(['name', 'phone', 'email', 'value', 'next_step', 'next_step_date'] as const).map((field) => (
                            <div key={field} className={field === 'next_step' ? 'space-y-2 sm:col-span-2' : 'space-y-2'}>
                                <Label htmlFor={`deal-${field}`}>
                                    {
                                        {
                                            name: 'Nome',
                                            phone: 'Telefone',
                                            email: 'E-mail',
                                            value: 'Valor (R$)',
                                            next_step: 'Próximo passo',
                                            next_step_date: 'Data do próximo passo',
                                        }[field]
                                    }
                                </Label>
                                <Input
                                    id={`deal-${field}`}
                                    disabled={!canManage || deal.status !== 'open'}
                                    type={field === 'value' ? 'number' : field === 'next_step_date' ? 'date' : field === 'email' ? 'email' : 'text'}
                                    step={field === 'value' ? '0.01' : undefined}
                                    min={field === 'value' ? '0' : undefined}
                                    value={edit.data[field]}
                                    onChange={(event) => edit.setData(field, event.target.value)}
                                    aria-invalid={!!edit.errors[field]}
                                />
                                <InputError message={edit.errors[field]} />
                            </div>
                        ))}
                    </div>
                    <div className="space-y-2">
                        <Label>Etapa</Label>
                        <Select
                            disabled={!canManage || deal.status !== 'open'}
                            value={String(edit.data.stage_id)}
                            onValueChange={(value) => edit.setData('stage_id', Number(value))}
                        >
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {stages.map((stage) => (
                                    <SelectItem key={stage.id} value={String(stage.id)}>
                                        {stage.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={edit.errors.stage_id} />
                    </div>
                    <InputError message={(edit.errors as Record<string, string>).status} />
                    {canManage && deal.status === 'open' && <Button disabled={edit.processing}>Salvar alterações</Button>}
                </form>
                {canManage && (
                    <div className="flex flex-wrap gap-2 border-t pt-4">
                        {deal.status === 'open' ? (
                            <>
                                <Button type="button" onClick={() => action('ganhar')}>
                                    Ganhar
                                </Button>
                                <Button type="button" variant="destructive" onClick={() => setLosing(true)}>
                                    Perder
                                </Button>
                            </>
                        ) : (
                            <Button type="button" variant="outline" onClick={() => action('reabrir')}>
                                Reabrir
                            </Button>
                        )}
                    </div>
                )}
                {canManageAssignees && (
                    <form
                        className="space-y-3 border-t pt-4"
                        onSubmit={(event) => {
                            event.preventDefault();
                            assignees.put(`${route}/responsaveis`, { preserveScroll: true });
                        }}
                    >
                        <h3 className="font-medium">Responsáveis</h3>
                        {managerOptions
                            .filter((manager) => !manager.deactivated_at || assignees.data.user_ids.includes(manager.id))
                            .map((manager) => (
                                <div key={manager.id} className="flex items-center gap-2">
                                    <Checkbox
                                        id={`assignee-${manager.id}`}
                                        checked={assignees.data.user_ids.includes(manager.id)}
                                        onCheckedChange={(checked) =>
                                            assignees.setData(
                                                'user_ids',
                                                checked
                                                    ? [...assignees.data.user_ids, manager.id]
                                                    : assignees.data.user_ids.filter((id) => id !== manager.id),
                                            )
                                        }
                                    />
                                    <Label htmlFor={`assignee-${manager.id}`}>
                                        {manager.name}
                                        {manager.deactivated_at ? ' (Inativo)' : ''}
                                    </Label>
                                </div>
                            ))}
                        <InputError message={assignees.errors.user_ids} />
                        <Button type="submit" variant="outline" disabled={assignees.processing}>
                            Salvar responsáveis
                        </Button>
                    </form>
                )}
                <section className="space-y-4 border-t pt-4">
                    <h3 className="font-medium">Anotações</h3>
                    {canManage && (
                        <form onSubmit={addNote} className="space-y-2">
                            <Label htmlFor="note-body">Nova anotação</Label>
                            <textarea
                                id="note-body"
                                rows={4}
                                maxLength={5000}
                                value={note.data.body}
                                onChange={(event) => note.setData('body', event.target.value)}
                                aria-invalid={!!note.errors.body}
                                className="border-input bg-background ring-offset-background focus-visible:ring-ring flex w-full rounded-md border px-3 py-2 text-base focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden md:text-sm"
                            />
                            <InputError message={note.errors.body} />
                            <Button disabled={note.processing}>Adicionar anotação</Button>
                        </form>
                    )}
                    {(deal.notes ?? []).map((item) => (
                        <article key={item.id} className="rounded-md border p-3 text-sm">
                            <p className="whitespace-pre-wrap">{item.body}</p>
                            <p className="text-muted-foreground mt-2">
                                {item.author.name} · {new Date(item.created_at).toLocaleString('pt-BR')}
                            </p>
                        </article>
                    ))}
                </section>
            </SheetContent>
            <Dialog open={losing} onOpenChange={setLosing}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Marcar como perdido</DialogTitle>
                        <DialogDescription>Escolha o motivo de perda.</DialogDescription>
                    </DialogHeader>
                    <form onSubmit={lose} className="space-y-4">
                        <Select value={loss.data.loss_reason_id} onValueChange={(value) => loss.setData('loss_reason_id', value)}>
                            <SelectTrigger aria-label="Motivo de perda">
                                <SelectValue placeholder="Selecione um motivo" />
                            </SelectTrigger>
                            <SelectContent>
                                {reasons.map((reason) => (
                                    <SelectItem key={reason.id} value={String(reason.id)}>
                                        {reason.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <InputError message={loss.errors.loss_reason_id} />
                        <DialogFooter>
                            <Button type="submit" variant="destructive" disabled={loss.processing}>
                                Confirmar perda
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </Sheet>
    );
}
