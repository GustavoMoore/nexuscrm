import { Alert, AlertDescription } from '@/components/ui/alert';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
export function FlashMessage() {
    const { flash } = usePage<SharedData>().props;
    return flash?.success || flash?.person_notice ? (
        <Alert>
            <AlertDescription>
                {flash.success}
                {flash.person_notice && <> {flash.person_notice}</>}
            </AlertDescription>
        </Alert>
    ) : null;
}
