import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { PageHeader } from '@/components/page-header';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
export default function Projeto({ project }: { project: { id: number; name: string; users: { id: number; name: string }[] } }) {
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
                    <p className="text-muted-foreground mt-2 text-sm">{project.users.map((u) => u.name).join(', ') || 'Nenhum gestor atribuído'}</p>
                </section>
                <EmptyState title="Funis chegam na próxima etapa" description="Este projeto ainda não tem funis." />
            </main>
        </AppLayout>
    );
}
