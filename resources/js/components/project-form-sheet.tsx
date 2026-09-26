import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Sheet, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import { useForm } from '@inertiajs/react';
import { FormEvent, useRef } from 'react';
export type ProjectData = {
    id: number;
    name: string;
    archived_at: string | null;
    users: { id: number; name: string; deactivated_at: string | null }[];
};
export type GestorOption = { id: number; name: string; deactivated_at: string | null };
export function ProjectFormSheet({
    project,
    gestores,
    open,
    onOpenChange,
}: {
    project?: ProjectData;
    gestores: GestorOption[];
    open: boolean;
    onOpenChange: (open: boolean) => void;
}) {
    const form = useForm<{ name: string; user_ids: number[] }>({ name: project?.name ?? '', user_ids: project?.users.map((u) => u.id) ?? [] });
    const inputRef = useRef<HTMLInputElement>(null);

    const submit = (e: FormEvent) => {
        e.preventDefault();
        const options = {
            onSuccess: () => onOpenChange(false),
            onError: () => setTimeout(() => document.querySelector<HTMLInputElement>('[aria-invalid="true"]')?.focus(), 10),
        };
        if (project) form.patch(`/projetos/${project.id}`, options);
        else form.post('/projetos', options);
    };
    return (
        <Sheet open={open} onOpenChange={onOpenChange}>
            <SheetContent
                onOpenAutoFocus={(event) => {
                    event.preventDefault();
                    inputRef.current?.focus();
                }}
                side="right"
                className="flex flex-col gap-6 overflow-y-auto sm:max-w-lg"
            >
                <SheetHeader>
                    <SheetTitle>{project ? 'Editar projeto' : 'Novo projeto'}</SheetTitle>
                    <SheetDescription>Defina o nome e os gestores atendentes.</SheetDescription>
                </SheetHeader>
                <form onSubmit={submit} className="flex flex-1 flex-col gap-5">
                    <div className="space-y-2">
                        <Label htmlFor="project-name">Nome</Label>
                        <Input
                            ref={inputRef}
                            id="project-name"
                            required
                            maxLength={255}
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            aria-invalid={!!form.errors.name}
                            aria-describedby={form.errors.name ? 'project-name-error' : undefined}
                        />
                        <InputError id="project-name-error" message={form.errors.name} />
                    </div>
                    <fieldset className="space-y-3">
                        <legend className="font-medium">Gestores</legend>
                        {gestores.map((gestor) => {
                            const checked = form.data.user_ids.includes(gestor.id);
                            const inactive = !!gestor.deactivated_at;
                            if (inactive && !checked) return null;
                            return (
                                <div className="flex items-center gap-2" key={gestor.id}>
                                    <Checkbox
                                        id={`gestor-${gestor.id}`}
                                        checked={checked}
                                        disabled={inactive}
                                        onCheckedChange={(value) =>
                                            form.setData(
                                                'user_ids',
                                                value ? [...form.data.user_ids, gestor.id] : form.data.user_ids.filter((id) => id !== gestor.id),
                                            )
                                        }
                                    />
                                    <Label htmlFor={`gestor-${gestor.id}`}>
                                        {gestor.name}
                                        {inactive ? ' (Inativo)' : ''}
                                    </Label>
                                </div>
                            );
                        })}
                    </fieldset>
                    <SheetFooter className="mt-auto">
                        <Button disabled={form.processing}>Salvar</Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    );
}
