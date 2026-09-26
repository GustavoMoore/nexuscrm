import { Alert, AlertDescription } from '@/components/ui/alert';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
export function FlashMessage() {
    const { flash } = usePage<SharedData>().props;
    return flash?.success ? (
        <Alert>
            <AlertDescription>{flash.success}</AlertDescription>
        </Alert>
    ) : null;
}
