import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
export function NavMain({ items = [] }: { items: NavItem[] }) {
    const { url } = usePage();
    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>Navegação</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    const active = url === item.url || url.startsWith(item.url + '/');
                    return (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton asChild isActive={active}>
                                <Link href={item.url} prefetch aria-current={active ? 'page' : undefined}>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
