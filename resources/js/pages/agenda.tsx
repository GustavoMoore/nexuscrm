import { EmptyState } from '@/components/empty-state';
import { FlashMessage } from '@/components/flash-message';
import { PageHeader } from '@/components/page-header';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
export default function Agenda() {
    return (
        <AppLayout breadcrumbs={[{ title: 'Minha agenda', href: '/agenda' }]}>
            <Head title="Minha agenda" />
            <main className="space-y-6 p-6">
                <PageHeader title="Minha agenda" description="Seu espaço de trabalho no Nexus" />
                <FlashMessage />
                <EmptyState title="Nenhum negócio ainda" description="A agenda será preenchida nas próximas etapas." />
            </main>
        </AppLayout>
    );
}
