import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export type LossReason = { id: number; name: string; deactivated_at?: string | null };

export function LossReasonFormSheet({
    base,
    reason,
    open,
    onOpenChange,
}: {
    base: string;
    reason?: LossReason;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const form = useForm({ name: reason?.name ?? '' });
    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = { preserveScroll: true, onSuccess: () => onOpenChange(false) };
        if (reason) form.patch(`${base}/${reason.id}`, options);
        else form.post(base, options);
    };
    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent side="right" className="flex w-full flex-col gap-6 sm:max-w-lg">
                <SheetHeader>
                    <SheetTitle>{reason ? 'Editar motivo' : 'Novo motivo de perda'}</SheetTitle>
                    <SheetDescription>Este nome aparece ao marcar um negócio como perdido.</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    <div className="space-y-2">
                        <Label htmlFor="reason-name">Nome</Label>
                        <Input
                            id="reason-name"
                            required
                            maxLength={255}
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                            aria-invalid={!!form.errors.name}
                        />
                        <InputError message={form.errors.name} />
                    </div>
                    <SheetFooter className="mt-auto">
                        <Button disabled={form.processing}>Salvar</Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
