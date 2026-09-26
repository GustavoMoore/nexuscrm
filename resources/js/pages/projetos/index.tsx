import { ConfirmDialog } from '@/components/confirm-dialog';
import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { PageHeader } from '@/components/page-header';
import { GestorOption, ProjectData, ProjectFormSheet } from '@/components/project-form-sheet';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
export default function Projetos({
    projects,
    gestores,
    can,
    status,
}: {
    projects: ProjectData[];
    gestores: GestorOption[];
    can: { create: boolean; update: boolean; archive: boolean };
    status: string;
}) {
    const [sheet, setSheet] = useState<{ project?: ProjectData } | null>(null);
    const [confirm, setConfirm] = useState<ProjectData | null>(null);
    const toggle = (p: ProjectData) =>
        router.post(`/projetos/${p.id}/${p.archived_at ? 'desarquivar' : 'arquivar'}`, {}, { onSuccess: () => setConfirm(null) });
    return (
        <AppLayout breadcrumbs={[{ title: 'Projetos', href: '/projetos' }]}>
            <Head title="Projetos" />
            <main className="space-y-6 p-6">
                <PageHeader
                    title="Projetos"
                    description="Acompanhe a carteira de projetos"
                    action={can.create && <Button onClick={() => setSheet({})}>Novo projeto</Button>}
                />
                <FlashMessage />
                {can.create && (
                    <div className="flex gap-2">
                        <Button variant={status === 'ativos' ? 'default' : 'outline'} asChild>
                            <Link href="/projetos?status=ativos">Ativos</Link>
                        </Button>
                        <Button variant={status === 'arquivados' ? 'default' : 'outline'} asChild>
                            <Link href="/projetos?status=arquivados">Arquivados</Link>
                        </Button>
                    </div>
                )}
                {projects.length === 0 ? (
                    <EmptyState
                        title={can.create ? 'Nenhum projeto ainda' : 'Nenhum projeto atribuído a você'}
                        action={can.create && <Button onClick={() => setSheet({})}>Novo projeto</Button>}
                    />
                ) : (
                    <div className="overflow-x-auto rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Gestores</TableHead>
                                    <TableHead>Status</TableHead>
                                    {can.update && <TableHead>Ações</TableHead>}
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {projects.map((p) => (
                                    <TableRow
                                        key={p.id}
                                        tabIndex={0}
                                        role="link"
                                        aria-label={`Abrir ${p.name}`}
                                        onClick={(event) => {
                                            if (!(event.target as HTMLElement).closest('button,a')) router.visit(`/projetos/${p.id}`);
                                        }}
                                        onKeyDown={(event) => {
                                            if (event.target === event.currentTarget && event.key === 'Enter') router.visit(`/projetos/${p.id}`);
                                        }}
                                    >
                                        <TableCell>{p.id}</TableCell>
                                        <TableCell>
                                            <Link className="font-medium hover:underline" href={`/projetos/${p.id}`}>
                                                {p.name}
                                            </Link>
                                        </TableCell>
                                        <TableCell>{p.users.map((u) => u.name).join(', ') || '—'}</TableCell>
                                        <TableCell>
                                            <Badge variant={p.archived_at ? 'secondary' : 'default'}>{p.archived_at ? 'Arquivado' : 'Ativo'}</Badge>
                                        </TableCell>
                                        {can.update && (
                                            <TableCell className="space-x-2 whitespace-nowrap">
                                                <Button size="sm" variant="outline" onClick={() => setSheet({ project: p })}>
                                                    Editar
                                                </Button>
                                                <Button size="sm" variant="outline" onClick={() => setConfirm(p)}>
                                                    {p.archived_at ? 'Desarquivar' : 'Arquivar'}
                                                </Button>
                                            </TableCell>
                                        )}
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </main>
            {sheet && (
                <ProjectFormSheet
                    key={sheet?.project?.id}
                    project={sheet?.project}
                    gestores={gestores}
                    open={!!sheet}
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
                title={confirm?.archived_at ? 'Desarquivar projeto' : 'Arquivar projeto'}
                description={confirm?.archived_at ? 'O projeto ficará visível novamente.' : 'Gestores perderão o acesso ao projeto.'}
                onConfirm={() => confirm && toggle(confirm)}
            />
        </AppLayout>
    );
}
