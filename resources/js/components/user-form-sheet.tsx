import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useForm } from '@inertiajs/react';
import { FormEvent, useRef } from 'react';
export type ManagedUser = { id: number; name: string; email: string; role: string; deactivated_at: string | null; can: { deactivate: boolean } };
export function UserFormSheet({
    user,
    mode,
    open,
    onOpenChange,
}: {
    user?: ManagedUser;
    mode: 'create' | 'edit' | 'reset';
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const form = useForm({ name: user?.name ?? '', email: user?.email ?? '', password: '' });
    const nameRef = useRef<HTMLInputElement>(null);

    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = {
            onSuccess: () => onOpenChange(false),
            onError: () => setTimeout(() => document.querySelector<HTMLInputElement>('[aria-invalid="true"]')?.focus(), 10),
        };
        if (mode === 'create') form.post('/usuarios', options);
        else if (mode === 'edit') form.patch(`/usuarios/${user?.id}`, options);
        else form.post(`/usuarios/${user?.id}/redefinir-senha`, options);
    };
    const title = mode === 'create' ? 'Novo gestor' : mode === 'edit' ? 'Editar gestor' : 'Redefinir senha';
    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent
                onOpenAutoFocus={(event) => {
                    event.preventDefault();
                    nameRef.current?.focus();
                }}
                side="right"
                className="flex flex-col gap-6 overflow-y-auto sm:max-w-lg"
            >
                <SheetHeader>
                    <SheetTitle>{title}</SheetTitle>
                    <SheetDescription>{mode === 'reset' ? 'Defina uma nova senha provisória.' : 'Preencha os dados do gestor.'}</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    {mode !== 'reset' && (
                        <>
                            <div className="space-y-2">
                                <Label htmlFor="user-name">Nome</Label>
                                <Input
                                    ref={nameRef}
                                    id="user-name"
                                    required
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    aria-invalid={!!form.errors.name}
                                    aria-describedby={form.errors.name ? 'user-name-error' : undefined}
                                />
                                <InputError id="user-name-error" message={form.errors.name} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="user-email">E-mail</Label>
                                <Input
                                    id="user-email"
                                    type="email"
                                    required
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    aria-invalid={!!form.errors.email}
                                    aria-describedby={form.errors.email ? 'user-email-error' : undefined}
                                />
                                <InputError id="user-email-error" message={form.errors.email} />
                            </div>
                        </>
                    )}
                    {mode !== 'edit' && (
                        <div className="space-y-2">
                            <Label htmlFor="user-password">Senha provisória</Label>
                            <Input
                                id="user-password"
                                ref={mode === 'reset' ? nameRef : undefined}
                                type="password"
                                required
                                minLength={8}
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                aria-invalid={!!form.errors.password}
                                aria-describedby={form.errors.password ? 'user-password-error' : undefined}
                            />
                            <InputError id="user-password-error" message={form.errors.password} />
                        </div>
                    )}
                    <SheetFooter className="mt-auto">
                        <Button type="submit" disabled={form.processing}>
                            {mode === 'reset' ? 'Redefinir' : 'Salvar'}
                        </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
