import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useForm } from '@inertiajs/react';
import { FormEvent, useRef } from 'react';

export type FunnelData = { id: number; name: string; archived_at: string | null; stages_count: number };

export function FunnelFormSheet({
    projectId,
    funnel,
    open,
    onOpenChange,
}: {
    projectId: number;
    funnel?: FunnelData;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const form = useForm({ name: funnel?.name ?? '' });
    const inputRef = useRef<HTMLInputElement>(null);
    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = {
            onSuccess: () => onOpenChange(false),
            onError: () => setTimeout(() => document.querySelector<HTMLInputElement>('[aria-invalid="true"]')?.focus(), 10),
        };
        if (funnel) form.patch(`/projetos/${projectId}/funis/${funnel.id}`, options);
        else form.post(`/projetos/${projectId}/funis`, options);
    };

    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent
                side="right"
                className="flex flex-col gap-6 overflow-y-auto sm:max-w-lg"
                onOpenAutoFocus={(event) => {
                    event.preventDefault();
                    inputRef.current?.focus();
                }}
            >
                <SheetHeader>
                    <SheetTitle>{funnel ? 'Editar funil' : 'Novo funil'}</SheetTitle>
                    <SheetDescription>{funnel ? 'Altere o nome do funil.' : 'O funil começa com quatro etapas padrão.'}</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    <div className="space-y-2">
                        <Label htmlFor="funnel-name">Nome</Label>
                        <Input
                            ref={inputRef}
                            id="funnel-name"
                            required
                            maxLength={255}
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            aria-invalid={!!form.errors.name}
                            aria-describedby={form.errors.name ? 'funnel-name-error' : undefined}
                        />
                        <InputError id="funnel-name-error" message={form.errors.name} />
                    </div>
                    <SheetFooter className="mt-auto">
                        <Button disabled={form.processing}>Salvar</Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
