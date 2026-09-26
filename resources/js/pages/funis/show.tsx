import { ConfirmDialog } from '@/components/confirm-dialog';
import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { PageHeader } from '@/components/page-header';
import { StageData, StageFormSheet } from '@/components/stage-form-sheet';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { ArrowDown, ArrowUp } from 'lucide-react';
import { useState } from 'react';

export default function Funil({
    project,
    funnel,
    stages,
    can,
}: {
    project: { id: number; name: string };
    funnel: { id: number; name: string; archived_at: string | null };
    stages: StageData[];
    can: { manage: boolean };
}) {
    const [sheet, setSheet] = useState<{ stage?: StageData } | null>(null);
    const [confirm, setConfirm] = useState<StageData | null>(null);
    const base = `/projetos/${project.id}/funis/${funnel.id}/etapas`;
    const move = (stage: StageData, direction: 'up' | 'down') => {
        const button = document.activeElement as HTMLButtonElement;
        router.post(`${base}/${stage.id}/mover`, { direction }, { preserveScroll: true, onSuccess: () => button?.focus() });
    };
    const destroy = (stage: StageData) => router.delete(`${base}/${stage.id}`, { onSuccess: () => setConfirm(null) });

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Projetos', href: '/projetos' },
                { title: project.name, href: `/projetos/${project.id}` },
                { title: funnel.name, href: `/projetos/${project.id}/funis/${funnel.id}` },
            ]}
        >
            <Head title={funnel.name} />
            <main className="space-y-6 p-6">
                <PageHeader
                    title={funnel.name}
                    description={`Funil #${funnel.id} de ${project.name}`}
                    action={can.manage && <Button onClick={() => setSheet({})}>Nova etapa</Button>}
                />
                <FlashMessage />
                {funnel.archived_at && <Badge variant="secondary">Arquivado</Badge>}
                <section className="space-y-4">
                    <h2 className="font-medium">Etapas</h2>
                    {stages.length === 0 ? (
                        <EmptyState title="Nenhuma etapa ainda" action={can.manage && <Button onClick={() => setSheet({})}>Nova etapa</Button>} />
                    ) : (
                        <div className="overflow-x-auto rounded-lg border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>ID</TableHead>
                                        <TableHead>Posição</TableHead>
                                        <TableHead>Nome</TableHead>
                                        {can.manage && <TableHead>Ações</TableHead>}
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {stages.map((stage, index) => (
                                        <TableRow key={stage.id}>
                                            <TableCell>{stage.id}</TableCell>
                                            <TableCell>{stage.position}</TableCell>
                                            <TableCell className="font-medium">{stage.name}</TableCell>
                                            {can.manage && (
                                                <TableCell className="flex items-center gap-2 whitespace-nowrap">
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        aria-label={`Subir ${stage.name}`}
                                                        disabled={index === 0}
                                                        onClick={() => move(stage, 'up')}
                                                    >
                                                        <ArrowUp className="size-4" />
                                                    </Button>
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        aria-label={`Descer ${stage.name}`}
                                                        disabled={index === stages.length - 1}
                                                        onClick={() => move(stage, 'down')}
                                                    >
                                                        <ArrowDown className="size-4" />
                                                    </Button>
                                                    <Button size="sm" variant="outline" onClick={() => setSheet({ stage })}>
                                                        Editar
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        disabled={stages.length === 1}
                                                        onClick={() => setConfirm(stage)}
                                                    >
                                                        Apagar
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
            </main>
            {sheet && (
                <StageFormSheet
                    key={sheet.stage?.id ?? 'new'}
                    projectId={project.id}
                    funnelId={funnel.id}
                    stage={sheet.stage}
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
                title="Apagar etapa"
                description={`A etapa ${confirm?.name ?? ''} será apagada e as posições restantes serão renumeradas.`}
                onConfirm={() => confirm && destroy(confirm)}
            />
        </AppLayout>
    );
}
