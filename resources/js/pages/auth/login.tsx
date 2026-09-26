import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
export default function Login() {
    const form = useForm<{ email: string; password: string; remember: boolean }>({ email: '', password: '', remember: false });
    const submit = (e: FormEvent) => {
        e.preventDefault();
        form.post('/login', { onFinish: () => form.reset('password') });
    };
    return (
        <AuthLayout title="Entrar no Nexus" description="Use suas credenciais de acesso">
            <Head title="Entrar" />
            <form onSubmit={submit} className="space-y-5">
                <div className="space-y-2">
                    <Label htmlFor="email">E-mail</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autoFocus
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                        aria-invalid={!!form.errors.email}
                        aria-describedby={form.errors.email ? 'email-error' : undefined}
                    />
                    <InputError id="email-error" message={form.errors.email} />
                </div>
                <div className="space-y-2">
                    <Label htmlFor="password">Senha</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        value={form.data.password}
                        onChange={(e) => form.setData('password', e.target.value)}
                        aria-invalid={!!form.errors.password}
                        aria-describedby={form.errors.password ? 'password-error' : undefined}
                    />
                    <InputError id="password-error" message={form.errors.password} />
                </div>
                <div className="flex items-center gap-2">
                    <Checkbox id="remember" checked={form.data.remember} onCheckedChange={(checked) => form.setData('remember', checked === true)} />
                    <Label htmlFor="remember">Lembrar-me</Label>
                </div>
                <Button className="w-full" disabled={form.processing}>
                    Entrar
                </Button>
            </form>
        </AuthLayout>
    );
}
