import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
export default function TrocarSenha() {
    const form = useForm({ password: '', password_confirmation: '' });
    const submit = (e: FormEvent) => {
        e.preventDefault();
        form.put('/trocar-senha');
    };
    return (
        <AuthLayout title="Trocar senha" description="Defina uma senha definitiva para continuar">
            <Head title="Trocar senha" />
            <form onSubmit={submit} className="space-y-5">
                <div className="space-y-2">
                    <Label htmlFor="new-password">Nova senha</Label>
                    <Input
                        id="new-password"
                        type="password"
                        autoFocus
                        required
                        minLength={8}
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        aria-invalid={!!form.errors.password}
                        aria-describedby={form.errors.password ? 'password-error' : undefined}
                    />
                    <InputError id="password-error" message={form.errors.password} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="confirm-password">Confirmar senha</Label>
                    <Input
                        id="confirm-password"
                        type="password"
                        required
                        value={form.data.password_confirmation}
                        onChange={(e) => form.setData('password_confirmation', e.target.value)}
                        aria-invalid={!!form.errors.password_confirmation}
                        aria-describedby={form.errors.password_confirmation ? 'confirm-error' : undefined}
                    />
                    <InputError id="confirm-error" message={form.errors.password_confirmation} />
                </div>
                <Button disabled={form.processing}>Salvar senha</Button>
            </form>
        </AuthLayout>
    );
}
