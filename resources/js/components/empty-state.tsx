import { ReactNode } from 'react';
export function EmptyState({ title, description, action }: { title: string; description?: string; action?: ReactNode }) {
    return (
        <div className="flex min-h-56 flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center">
            <h2 className="font-medium">{title}</h2>
            {description && <p className="text-muted-foreground mt-1 text-sm">{description}</p>}
            {action && <div className="mt-4">{action}</div>}
        </div>
    );
}
