import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useForm } from '@inertiajs/react';
import { FormEvent, useRef } from 'react';

export type StageData = { id: number; name: string; position: number; deals_count?: number };

export function StageFormSheet({
    projectId,
    funnelId,
    stage,
    open,
    onOpenChange,
}: {
    projectId: number;
    funnelId: number;
    stage?: StageData;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const form = useForm({ name: stage?.name ?? '' });
    const inputRef = useRef<HTMLInputElement>(null);
    const base = `/projetos/${projectId}/funis/${funnelId}/etapas`;
    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = {
            onSuccess: () => onOpenChange(false),
            onError: () => setTimeout(() => document.querySelector<HTMLInputElement>('[aria-invalid="true"]')?.focus(), 10),
        };
        if (stage) form.patch(`${base}/${stage.id}`, options);
        else form.post(base, options);
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
                    <SheetTitle>{stage ? 'Editar etapa' : 'Nova etapa'}</SheetTitle>
                    <SheetDescription>{stage ? 'Altere o nome da etapa.' : 'A nova etapa será adicionada ao fim do funil.'}</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    <div className="space-y-2">
                        <Label htmlFor="stage-name">Nome</Label>
                        <Input
                            ref={inputRef}
                            id="stage-name"
                            required
                            maxLength={255}
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            aria-invalid={!!form.errors.name}
                            aria-describedby={form.errors.name ? 'stage-name-error' : undefined}
                        />
                        <InputError id="stage-name-error" message={form.errors.name} />
                    </div>
                    <SheetFooter className="mt-auto">
                        <Button disabled={form.processing}>Salvar</Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
