import { FlashMessage } from '@/components/flash-message';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { SharedData } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';
export default function Profile() {
    const { auth } = usePage<SharedData>().props;
    const form = useForm({ name: auth.user?.name ?? '' });
    const submit = (e: FormEvent) => {
        e.preventDefault();
        form.patch('/settings/profile');
    };
    return (
        <AppLayout breadcrumbs={[{ title: 'Perfil', href: '/settings/profile' }]}>
            <Head title="Perfil" />
            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Perfil" description="Atualize seu nome" />
                    <FlashMessage />
                    <form onSubmit={submit} className="space-y-4">
                        <Label htmlFor="name">Nome</Label>
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            aria-invalid={!!form.errors.name}
                            aria-describedby={form.errors.name ? 'name-error' : undefined}
                        />
                        <InputError id="name-error" message={form.errors.name} />
                        <Button disabled={form.processing}>Salvar</Button>
                    </form>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
