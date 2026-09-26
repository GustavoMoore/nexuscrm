import { ConfirmDialog } from '@/components/confirm-dialog';
import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { PageHeader } from '@/components/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ManagedUser, UserFormSheet } from '@/components/user-form-sheet';
import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
export default function Usuarios({ users }: { users: ManagedUser[]; can: { create: boolean } }) {
    const [sheet, setSheet] = useState<{ mode: 'create' | 'edit' | 'reset'; user?: ManagedUser } | null>(null);
    const [confirm, setConfirm] = useState<ManagedUser | null>(null);
    const toggle = (user: ManagedUser) => {
        router.post(`/usuarios/${user.id}/${user.deactivated_at ? 'reativar' : 'desativar'}`, {}, { onSuccess: () => setConfirm(null) });
    };
    return (
        <AppLayout breadcrumbs={[{ title: 'Usuários', href: '/usuarios' }]}>
            <Head title="Usuários" />
            <main className="space-y-6 p-6">
                <PageHeader
                    title="Usuários"
                    description="Gerencie o acesso dos gestores"
                    action={<Button onClick={() => setSheet({ mode: 'create' })}>Novo gestor</Button>}
                />
                <FlashMessage />
                {users.length === 0 ? (
                    <EmptyState title="Nenhum gestor ainda" action={<Button onClick={() => setSheet({ mode: 'create' })}>Novo gestor</Button>} />
                ) : (
                    <div className="overflow-x-auto rounded-lg border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>E-mail</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.map((user) => (
                                    <TableRow
                                        key={user.id}
                                        tabIndex={0}
                                        role="button"
                                        aria-label={`Editar ${user.name}`}
                                        onClick={(event) => {
                                            if (!(event.target as HTMLElement).closest('button,a')) setSheet({ mode: 'edit', user });
                                        }}
                                        onKeyDown={(event) => {
                                            if (event.target === event.currentTarget && event.key === 'Enter') setSheet({ mode: 'edit', user });
                                        }}
                                    >
                                        <TableCell>{user.id}</TableCell>
                                        <TableCell>
                                            <button
                                                className="text-left font-medium underline-offset-4 hover:underline"
                                                onClick={() => setSheet({ mode: 'edit', user })}
                                            >
                                                {user.name}
                                            </button>
                                        </TableCell>
                                        <TableCell>{user.email}</TableCell>
                                        <TableCell>
                                            <Badge variant={user.deactivated_at ? 'secondary' : 'default'}>
                                                {user.deactivated_at ? 'Inativo' : 'Ativo'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="space-x-2 whitespace-nowrap">
                                            {user.role === 'gestor' && (
                                                <>
                                                    <Button size="sm" variant="outline" onClick={() => setSheet({ mode: 'edit', user })}>
                                                        Editar
                                                    </Button>
                                                    <Button size="sm" variant="outline" onClick={() => setSheet({ mode: 'reset', user })}>
                                                        Redefinir senha
                                                    </Button>
                                                    <Button size="sm" variant="outline" onClick={() => setConfirm(user)}>
                                                        {user.deactivated_at ? 'Reativar' : 'Desativar'}
                                                    </Button>
                                                </>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
            </main>
            {sheet && (
                <UserFormSheet
                    key={sheet?.user?.id + '-' + sheet?.mode}
                    user={sheet?.user}
                    mode={sheet?.mode ?? 'create'}
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
                title={confirm?.deactivated_at ? 'Reativar gestor' : 'Desativar gestor'}
                description={confirm?.deactivated_at ? `${confirm.name} poderá entrar novamente.` : `${confirm?.name} não poderá mais entrar.`}
                onConfirm={() => confirm && toggle(confirm)}
            />
        </AppLayout>
    );
}
