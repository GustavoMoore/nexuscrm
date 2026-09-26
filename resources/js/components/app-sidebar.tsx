import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, FolderKanban, Users } from 'lucide-react';
import AppLogo from './app-logo';
export function AppSidebar() {
    const { auth } = usePage<SharedData>().props;
    const items = [
        { title: 'Minha agenda', url: '/agenda', icon: CalendarDays },
        { title: 'Projetos', url: '/projetos', icon: FolderKanban },
        ...(auth.user?.is_adm ? [{ title: 'Usuários', url: '/usuarios', icon: Users }] : []),
    ];
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/agenda">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={items} />
            </SidebarContent>
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
