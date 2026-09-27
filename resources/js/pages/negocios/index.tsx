import { DealFormSheet, Manager } from '@/components/deal-form-sheet';
import { Deal, DealSheet } from '@/components/deal-sheet';
import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { LossReason } from '@/components/loss-reason-form-sheet';
import { PageHeader } from '@/components/page-header';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';
import { AlertTriangle, Plus } from 'lucide-react';
import { useEffect, useState } from 'react';

type Stage = { id: number; name: string; position: number; count: number; total: string; deals: Deal[] };

export default function Negocios({
    project,
    funnel,
    stages,
    deals,
    deal,
    view,
    q,
    closed,
    loss_reasons,
    project_managers,
    can,
}: {
    project: { id: number; name: string };
    funnel: { id: number; name: string; archived_at: string | null };
    stages: Stage[];
    deals: Deal[];
    deal: Deal | null;
    view: 'quadro' | 'lista';
    q: string;
    closed: boolean;
    loss_reasons: LossReason[];
    project_managers: Manager[];
    can: { manage: boolean; manage_assignees: boolean };
}) {
    const [creating, setCreating] = useState(false);
    const [search, setSearch] = useState(q);
    useEffect(() => setSearch(q), [q]);
    const base = `/projetos/${project.id}/funis/${funnel.id}/negocios`;
    const query = (values: Record<string, string | number | undefined>) => {
        const params = new URLSearchParams(window.location.search);
        Object.entries(values).forEach(([key, value]) => (value === undefined || value === '' ? params.delete(key) : params.set(key, String(value))));
        router.get(`${base}?${params.toString()}`, {}, { preserveState: true, preserveScroll: true });
    };
    useEffect(() => {
        if (search === q) return;
        const timer = window.setTimeout(() => {
            const params = new URLSearchParams(window.location.search);
            if (search) params.set('q', search);
            else params.delete('q');
            params.delete('negocio');
            router.get(`${base}?${params.toString()}`, {}, { preserveState: true, preserveScroll: true });
        }, 300);
        return () => window.clearTimeout(timer);
    }, [search, q, base]);
    const openDeal = (id: number) => query({ negocio: id });
    const move = (id: number, stageId: number) => router.patch(`${base}/${id}`, { stage_id: stageId }, { preserveScroll: true });
    const money = (value: string | null) => (value === null ? '—' : Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
    const step = (item: Deal) => (
        <span className="text-muted-foreground text-sm">
            {item.next_step} · {new Date(`${item.next_step_date}T12:00:00`).toLocaleDateString('pt-BR')}
        </span>
    );
    const assignees = (item: Deal) => (
        <div className="flex -space-x-2">
            {item.assignees.map((user) => (
                <Avatar key={user.id} title={user.name} className="border-background size-7 border-2">
                    <AvatarFallback className="text-[10px]">{user.initials}</AvatarFallback>
                </Avatar>
            ))}
        </div>
    );
    const moveSelect = (item: Deal) => (
        <Select
            value={String(item.stage_id)}
            onValueChange={(value) => move(item.id, Number(value))}
            disabled={!can.manage || item.status !== 'open'}
        >
            <SelectTrigger aria-label={`Mover ${item.person.name} para`} className="h-9 w-40">
                <SelectValue placeholder="Mover para" />
            </SelectTrigger>
            <SelectContent>
                {stages.map((stage) => (
                    <SelectItem key={stage.id} value={String(stage.id)}>
                        {stage.name}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );
    const card = (item: Deal) => (
        <article
            key={item.id}
            draggable={can.manage}
            onDragStart={(event) => event.dataTransfer.setData('text/plain', String(item.id))}
            className="bg-background focus-visible:ring-ring cursor-pointer space-y-3 rounded-lg border p-4 shadow-sm focus-visible:ring-2 focus-visible:outline-hidden"
            tabIndex={0}
            role="button"
            aria-label={`Abrir negócio de ${item.person.name}`}
            onClick={(event) => {
                if (!(event.target as HTMLElement).closest('button,[role=combobox]')) openDeal(item.id);
            }}
            onKeyDown={(event) => {
                if (event.key === 'Enter' && event.target === event.currentTarget) openDeal(item.id);
            }}
        >
            <div className="flex items-start justify-between gap-2">
                <span className="font-medium">{item.person.name}</span>
                <span className="text-sm">{money(item.value)}</span>
            </div>
            <div>{step(item)}</div>
            {item.overdue && (
                <Badge variant="destructive" className="gap-1">
                    <AlertTriangle className="size-3" />
                    Atrasado
                </Badge>
            )}
            <div className="flex items-center justify-between gap-2">
                {assignees(item)}
                {moveSelect(item)}
            </div>
        </article>
    );

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Projetos', href: '/projetos' },
                { title: project.name, href: `/projetos/${project.id}` },
                { title: funnel.name, href: `/projetos/${project.id}/funis/${funnel.id}` },
                { title: 'Quadro', href: base },
            ]}
        >
            <Head title={`Quadro · ${funnel.name}`} />
            <main className="space-y-6 p-6">
                <PageHeader
                    title={funnel.name}
                    description={`Quadro de negócios de ${project.name}`}
                    action={
                        <div className="flex flex-wrap gap-2">
                            <Button variant="outline" asChild>
                                <Link href={`/projetos/${project.id}/funis/${funnel.id}`}>Etapas</Link>
                            </Button>
                            {can.manage && (
                                <Button onClick={() => setCreating(true)}>
                                    <Plus className="mr-2 size-4" />
                                    Novo negócio
                                </Button>
                            )}
                        </div>
                    }
                />
                <FlashMessage />
                {!can.manage && <Badge variant="secondary">Somente leitura</Badge>}
                <div className="flex flex-wrap items-center gap-3">
                    <Input
                        aria-label="Buscar negócios"
                        placeholder="Buscar pessoa, telefone ou e-mail"
                        className="max-w-sm"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                    />
                    <ToggleGroup
                        type="single"
                        value={view}
                        onValueChange={(value) => {
                            if (value) query({ view: value });
                        }}
                        aria-label="Visão"
                        disabled={closed}
                    >
                        <ToggleGroupItem value="quadro">Quadro</ToggleGroupItem>
                        <ToggleGroupItem value="lista">Lista</ToggleGroupItem>
                    </ToggleGroup>
                    <Button
                        variant={closed ? 'default' : 'outline'}
                        onClick={() => query({ closed: closed ? undefined : '1', view: closed ? 'quadro' : 'lista', negocio: undefined })}
                    >
                        Ganhos / Perdidos
                    </Button>
                </div>
                {view === 'quadro' ? (
                    <div className="flex snap-x gap-4 overflow-x-auto pb-4">
                        {stages.map((stage) => (
                            <section
                                key={stage.id}
                                className="bg-muted/40 w-[calc(100vw-3rem)] shrink-0 snap-start space-y-3 rounded-lg p-3 sm:w-72"
                                onDragOver={(event) => {
                                    if (can.manage) event.preventDefault();
                                }}
                                onDrop={(event) => {
                                    event.preventDefault();
                                    const id = Number(event.dataTransfer.getData('text/plain'));
                                    if (id && can.manage) move(id, stage.id);
                                }}
                            >
                                <div className="flex items-center justify-between gap-2">
                                    <h2 className="font-semibold">{stage.name}</h2>
                                    <Badge variant="secondary">{stage.count}</Badge>
                                </div>
                                <p className="text-muted-foreground text-sm">Total: {money(stage.total)}</p>
                                {stage.deals.map(card)}
                                {stage.deals.length === 0 && (
                                    <div className="text-muted-foreground rounded-md border border-dashed p-5 text-center text-sm">
                                        Solte um negócio aqui
                                    </div>
                                )}
                            </section>
                        ))}
                    </div>
                ) : deals.length ? (
                    <div className="overflow-x-auto rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Pessoa</TableHead>
                                    <TableHead>Etapa</TableHead>
                                    <TableHead>Valor</TableHead>
                                    <TableHead>Próximo passo</TableHead>
                                    <TableHead>Responsáveis</TableHead>
                                    {closed ? (
                                        <>
                                            <TableHead>Status</TableHead>
                                            <TableHead>Motivo</TableHead>
                                        </>
                                    ) : (
                                        <TableHead>Mover para</TableHead>
                                    )}
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {deals.map((item) => (
                                    <TableRow
                                        key={item.id}
                                        tabIndex={0}
                                        role="link"
                                        aria-label={`Abrir negócio de ${item.person.name}`}
                                        onClick={(event) => {
                                            if (!(event.target as HTMLElement).closest('button,[role=combobox]')) openDeal(item.id);
                                        }}
                                        onKeyDown={(event) => {
                                            if (event.key === 'Enter' && event.target === event.currentTarget) openDeal(item.id);
                                        }}
                                    >
                                        <TableCell>{item.id}</TableCell>
                                        <TableCell className="font-medium">{item.person.name}</TableCell>
                                        <TableCell>{item.stage.name}</TableCell>
                                        <TableCell>{money(item.value)}</TableCell>
                                        <TableCell>
                                            {step(item)}
                                            {item.overdue && (
                                                <Badge variant="destructive" className="ml-2 gap-1">
                                                    <AlertTriangle className="size-3" />
                                                    Atrasado
                                                </Badge>
                                            )}
                                        </TableCell>
                                        <TableCell>{assignees(item)}</TableCell>
                                        {closed ? (
                                            <>
                                                <TableCell>{item.status === 'won' ? 'Ganho' : 'Perdido'}</TableCell>
                                                <TableCell>{item.loss_reason?.name ?? '—'}</TableCell>
                                            </>
                                        ) : (
                                            <TableCell>{moveSelect(item)}</TableCell>
                                        )}
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                ) : (
                    <EmptyState
                        title={q ? `Nada encontrado para ${q}` : closed ? 'Nenhum negócio encerrado' : 'Nenhum negócio ainda'}
                        action={!q && !closed && can.manage && <Button onClick={() => setCreating(true)}>Novo negócio</Button>}
                    />
                )}
            </main>
            {creating && <DealFormSheet base={base} managers={project_managers} admin={can.manage_assignees} open onOpenChange={setCreating} />}
            {deal && (
                <DealSheet
                    key={deal.id}
                    base={base}
                    deal={deal}
                    stages={stages}
                    reasons={loss_reasons}
                    managers={project_managers}
                    canManage={can.manage}
                    canManageAssignees={can.manage_assignees}
                    onClose={() => query({ negocio: undefined })}
                />
            )}
        </AppLayout>
    );
}
