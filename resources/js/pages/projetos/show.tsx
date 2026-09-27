import { ConfirmDialog } from '@/components/confirm-dialog';
import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { FunnelData, FunnelFormSheet } from '@/components/funnel-form-sheet';
import { LossReason, LossReasonFormSheet } from '@/components/loss-reason-form-sheet';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Projeto({
    project,
    funnels,
    can,
    loss_reasons,
}: {
    project: { id: number; name: string; users: { id: number; name: string }[] };
    funnels: FunnelData[];
    can: { manage: boolean };
    loss_reasons: LossReason[];
}) {
    const [sheet, setSheet] = useState<{ funnel?: FunnelData } | null>(null);
    const [confirm, setConfirm] = useState<FunnelData | null>(null);
    const [reasonSheet, setReasonSheet] = useState<{ reason?: LossReason } | null>(null);
    const [reasonConfirm, setReasonConfirm] = useState<LossReason | null>(null);
    const [status, setStatus] = useState<'ativos' | 'arquivados'>('ativos');
    const visible = can.manage ? funnels.filter((funnel) => (status === 'arquivados' ? !!funnel.archived_at : !funnel.archived_at)) : funnels;
    const toggle = (funnel: FunnelData) =>
        router.post(
            `/projetos/${project.id}/funis/${funnel.id}/${funnel.archived_at ? 'desarquivar' : 'arquivar'}`,
            {},
            { onSuccess: () => setConfirm(null) },
        );

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Projetos', href: '/projetos' },
                { title: project.name, href: `/projetos/${project.id}` },
            ]}
        >
            <Head title={project.name} />
            <main className="space-y-6 p-6">
                <PageHeader title={project.name} description={`Projeto #${project.id}`} />
                <FlashMessage />
                <section className="rounded-lg border p-5">
                    <h2 className="font-medium">Gestores atendentes</h2>
                    <p className="text-muted-foreground mt-2 text-sm">
                        {project.users.map((user) => user.name).join(', ') || 'Nenhum gestor atribuído'}
                    </p>
                </section>
                <section className="space-y-4">
                    <PageHeader
                        title="Funis"
                        description="Organize os caminhos comerciais deste projeto."
                        action={can.manage && <Button onClick={() => setSheet({})}>Novo funil</Button>}
                    />
                    {can.manage && (
                        <div className="flex gap-2" aria-label="Filtrar funis">
                            <Button variant={status === 'ativos' ? 'default' : 'outline'} onClick={() => setStatus('ativos')}>
                                Ativos
                            </Button>
                            <Button variant={status === 'arquivados' ? 'default' : 'outline'} onClick={() => setStatus('arquivados')}>
                                Arquivados
                            </Button>
                        </div>
                    )}
                    {visible.length === 0 ? (
                        <EmptyState
                            title={status === 'arquivados' ? 'Nenhum funil arquivado' : 'Nenhum funil ainda'}
                            description={status === 'ativos' ? 'Este projeto ainda não tem funis ativos.' : undefined}
                            action={can.manage && status === 'ativos' && <Button onClick={() => setSheet({})}>Novo funil</Button>}
                        />
                    ) : (
                        <div className="overflow-x-auto rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>ID</TableHead>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Etapas</TableHead>
                                        <TableHead>Status</TableHead>
                                        {can.manage && <TableHead>Ações</TableHead>}
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {visible.map((funnel) => (
                                        <TableRow
                                            key={funnel.id}
                                            tabIndex={0}
                                            role="link"
                                            aria-label={`Abrir ${funnel.name}`}
                                            onClick={(event) => {
                                                if (!(event.target as HTMLElement).closest('button,a'))
                                                    router.visit(`/projetos/${project.id}/funis/${funnel.id}`);
                                            }}
                                            onKeyDown={(event) => {
                                                if (event.target === event.currentTarget && event.key === 'Enter')
                                                    router.visit(`/projetos/${project.id}/funis/${funnel.id}`);
                                            }}
                                        >
                                            <TableCell>{funnel.id}</TableCell>
                                            <TableCell>
                                                <Link className="font-medium hover:underline" href={`/projetos/${project.id}/funis/${funnel.id}`}>
                                                    {funnel.name}
                                                </Link>
                                            </TableCell>
                                            <TableCell>{funnel.stages_count}</TableCell>
                                            <TableCell>
                                                <Badge variant={funnel.archived_at ? 'secondary' : 'default'}>
                                                    {funnel.archived_at ? 'Arquivado' : 'Ativo'}
                                                </Badge>
                                            </TableCell>
                                            {can.manage && (
                                                <TableCell className="space-x-2 whitespace-nowrap">
                                                    <Button size="sm" variant="outline" onClick={() => setSheet({ funnel })}>
                                                        Editar
                                                    </Button>
                                                    <Button size="sm" variant="outline" onClick={() => setConfirm(funnel)}>
                                                        {funnel.archived_at ? 'Desarquivar' : 'Arquivar'}
                                                    </Button>
                                                </TableCell>
                                            )}
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    )}
                </section>
                {can.manage && (
                    <section className="space-y-4">
                        <PageHeader
                            title="Motivos de perda"
                            description="Motivos disponíveis ao encerrar negócios."
                            action={<Button onClick={() => setReasonSheet({})}>Novo motivo</Button>}
                        />
                        <div className="overflow-x-auto rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>ID</TableHead>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Ações</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {loss_reasons.map((reason) => (
                                        <TableRow key={reason.id}>
                                            <TableCell>{reason.id}</TableCell>
                                            <TableCell>{reason.name}</TableCell>
                                            <TableCell>{reason.deactivated_at ? 'Inativo' : 'Ativo'}</TableCell>
                                            <TableCell className="space-x-2">
                                                <Button size="sm" variant="outline" onClick={() => setReasonSheet({ reason })}>
                                                    Editar
                                                </Button>
                                                {!reason.deactivated_at && (
                                                    <Button size="sm" variant="outline" onClick={() => setReasonConfirm(reason)}>
                                                        Desativar
                                                    </Button>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    </section>
                )}
            </main>
            {reasonSheet && (
                <LossReasonFormSheet
                    key={reasonSheet.reason?.id ?? 'new'}
                    base={`/projetos/${project.id}/motivos-perda`}
                    reason={reasonSheet.reason}
                    open
                    onOpenChange={(open) => {
                        if (!open) setReasonSheet(null);
                    }}
                />
            )}
            <ConfirmDialog
                open={!!reasonConfirm}
                onOpenChange={(open) => {
                    if (!open) setReasonConfirm(null);
                }}
                title="Desativar motivo"
                description={`O motivo ${reasonConfirm?.name ?? ''} deixará de aparecer na escolha de perda.`}
                onConfirm={() =>
                    reasonConfirm &&
                    router.post(
                        `/projetos/${project.id}/motivos-perda/${reasonConfirm.id}/desativar`,
                        {},
                        { onSuccess: () => setReasonConfirm(null) },
                    )
                }
            />
            {sheet && (
                <FunnelFormSheet
                    key={sheet.funnel?.id ?? 'new'}
                    projectId={project.id}
                    funnel={sheet.funnel}
                    open
                    onOpenChange={(open) => {
                        if (!open) setSheet(null);
                    }}
                />
            )}
            <ConfirmDialog
                open={!!confirm}
                onOpenChange={(open) => {
                    if (!open) setConfirm(null);
                }}
                title={confirm?.archived_at ? 'Desarquivar funil' : 'Arquivar funil'}
                description={confirm?.archived_at ? 'O funil ficará visível novamente para os gestores.' : 'Gestores perderão o acesso a este funil.'}
                onConfirm={() => confirm && toggle(confirm)}
            />
        </AppLayout>
    );
}
