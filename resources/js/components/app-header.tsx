import { Breadcrumbs } from '@/components/breadcrumbs';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { SharedData, type BreadcrumbItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, FolderKanban, Menu, Users } from 'lucide-react';
import AppLogo from './app-logo';
export function AppHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItem[] }) {
    const page = usePage<SharedData>();
    const { auth } = page.props;
    const { url } = page;
    const items = [
        { title: 'Minha agenda', url: '/agenda', icon: CalendarDays },
        { title: 'Projetos', url: '/projetos', icon: FolderKanban },
        ...(auth.user?.is_adm ? [{ title: 'Usuários', url: '/usuarios', icon: Users }] : []),
    ];
    return (
        <header className="border-b px-4">
            <div className="flex h-16 items-center gap-5">
                <Sheet>
                    <SheetTrigger asChild>
                        <Button variant="ghost" size="icon" className="md:hidden" aria-label="Abrir menu">
                            <Menu />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left">
                        <SheetHeader>
                            <SheetTitle>Nexus</SheetTitle>
                        </SheetHeader>
                        <nav className="mt-8 flex flex-col gap-3">
                            {items.map((item) => (
                                <Link
                                    key={item.url}
                                    href={item.url}
                                    aria-current={url === item.url || url.startsWith(item.url + '/') ? 'page' : undefined}
                                >
                                    {item.title}
                                </Link>
                            ))}
                        </nav>
                    </SheetContent>
                </Sheet>
                <Link href="/agenda">
                    <AppLogo />
                </Link>
                <nav className="hidden gap-4 md:flex">
                    {items.map((item) => (
                        <Link
                            key={item.url}
                            href={item.url}
                            className="text-sm"
                            aria-current={url === item.url || url.startsWith(item.url + '/') ? 'page' : undefined}
                        >
                            {item.title}
                        </Link>
                    ))}
                </nav>
            </div>
            {breadcrumbs.length > 0 && (
                <div className="pb-3">
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                </div>
            )}
        </header>
    );
}
