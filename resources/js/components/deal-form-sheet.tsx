import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { SharedData } from '@/types';
import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';

export type Manager = { id: number; name: string; deactivated_at: string | null };

export function DealFormSheet({
    base,
    managers,
    admin,
    open,
    onOpenChange,
}: {
    base: string;
    managers: Manager[];
    admin: boolean;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const { flash } = usePage<SharedData>().props;
    const form = useForm({ name: '', phone: '', email: '', value: '', next_step: '', next_step_date: '', user_ids: [] as number[] });
    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post(base, { preserveScroll: true, onSuccess: () => onOpenChange(false) });
    };

    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent side="right" className="flex w-full flex-col gap-6 overflow-y-auto sm:max-w-xl">
                <SheetHeader>
                    <SheetTitle>Novo negócio</SheetTitle>
                    <SheetDescription>Registre a pessoa e o próximo passo.</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    <div className="grid gap-4 sm:grid-cols-2">
                        {(['name', 'phone', 'email', 'value', 'next_step', 'next_step_date'] as const).map((field) => (
                            <div key={field} className={field === 'next_step' ? 'space-y-2 sm:col-span-2' : 'space-y-2'}>
                                <Label htmlFor={`new-${field}`}>
                                    {
                                        {
                                            name: 'Nome',
                                            phone: 'Telefone',
                                            email: 'E-mail',
                                            value: 'Valor (R$)',
                                            next_step: 'Próximo passo',
                                            next_step_date: 'Data do próximo passo',
                                        }[field]
                                    }
                                </Label>
                                <Input
                                    id={`new-${field}`}
                                    type={field === 'value' ? 'number' : field === 'next_step_date' ? 'date' : field === 'email' ? 'email' : 'text'}
                                    step={field === 'value' ? '0.01' : undefined}
                                    min={field === 'value' ? '0' : undefined}
                                    required={field === 'name' || field === 'next_step' || field === 'next_step_date'}
                                    value={form.data[field]}
                                    onChange={(event) => form.setData(field, event.target.value)}
                                    aria-invalid={!!form.errors[field]}
                                />
                                <InputError message={form.errors[field]} />
                            </div>
                        ))}
                    </div>
                    <InputError message={(form.errors as Record<string, string>).person} />
                    {(form.errors as Record<string, string>).person && flash?.existing_deal_id && (
                        <Link
                            className="text-primary underline"
                            href={`${base}?negocio=${flash.existing_deal_id}`}
                            onClick={() => onOpenChange(false)}
                        >
                            Ver negócio
                        </Link>
                    )}
                    {admin && (
                        <fieldset className="space-y-2">
                            <legend className="font-medium">Responsáveis</legend>
                            {managers
                                .filter((manager) => !manager.deactivated_at)
                                .map((manager) => (
                                    <div key={manager.id} className="flex items-center gap-2">
                                        <Checkbox
                                            id={`new-manager-${manager.id}`}
                                            checked={form.data.user_ids.includes(manager.id)}
                                            onCheckedChange={(checked) =>
                                                form.setData(
                                                    'user_ids',
                                                    checked
                                                        ? [...form.data.user_ids, manager.id]
                                                        : form.data.user_ids.filter((id) => id !== manager.id),
                                                )
                                            }
                                        />
                                        <Label htmlFor={`new-manager-${manager.id}`}>{manager.name}</Label>
                                    </div>
                                ))}
                            <InputError message={form.errors.user_ids} />
                        </fieldset>
                    )}
                    <SheetFooter className="mt-auto">
                        <Button disabled={form.processing}>Criar negócio</Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
