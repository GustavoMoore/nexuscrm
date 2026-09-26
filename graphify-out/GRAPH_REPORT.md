# Graph Report - nexuscrm  (2026-09-26)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 654 nodes · 1383 edges · 41 communities (28 shown, 13 thin omitted)
- Extraction: 100% EXTRACTED · 0% INFERRED · 0% AMBIGUOUS · INFERRED: 2 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Community 0
- Community 1
- Community 2
- Community 3
- Community 4
- Community 5
- Community 6
- Community 7
- Community 8
- Community 9
- Community 10
- Community 11
- Community 12
- Community 13
- Community 14
- Community 15
- Community 16
- Community 17
- Community 18
- Community 19
- Community 20
- Community 21
- Community 22
- Community 23
- Community 24
- Community 25
- Community 26
- Community 27
- Community 28
- Community 29
- Community 30

## God Nodes (most connected - your core abstractions)
1. `cn()` - 118 edges
2. `react` - 41 edges
3. `User` - 37 edges
4. `@inertiajs/react` - 25 edges
5. `lucide-react` - 24 edges
6. `Controller` - 21 edges
7. `TestCase` - 20 edges
8. `Button` - 16 edges
9. `compilerOptions` - 15 edges
10. `BreadcrumbItem` - 14 edges

## Surprising Connections (you probably didn't know these)
- `AlertDescription` --calls--> `cn()`  [EXTRACTED]
  resources/js/components/ui/alert.tsx → resources/js/lib/utils.ts
- `AlertTitle` --calls--> `cn()`  [EXTRACTED]
  resources/js/components/ui/alert.tsx → resources/js/lib/utils.ts
- `BreadcrumbEllipsis()` --calls--> `cn()`  [EXTRACTED]
  resources/js/components/ui/breadcrumb.tsx → resources/js/lib/utils.ts
- `CardFooter` --calls--> `cn()`  [EXTRACTED]
  resources/js/components/ui/card.tsx → resources/js/lib/utils.ts
- `DropdownMenuCheckboxItem` --calls--> `cn()`  [EXTRACTED]
  resources/js/components/ui/dropdown-menu.tsx → resources/js/lib/utils.ts

## Import Cycles
- None detected.

## Communities (41 total, 13 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.07
Nodes (33): AuthenticatedSessionController, ConfirmablePasswordController, EmailVerificationNotificationController, EmailVerificationPromptController, NewPasswordController, PasswordResetLinkController, RegisteredUserController, VerifyEmailController (+25 more)

### Community 1 - "Community 1"
Cohesion: 0.06
Nodes (52): AppContent(), AppContentProps, AppHeaderProps, AppShell(), AppShellProps, AppSidebar(), footerNavItems, AppSidebarHeader() (+44 more)

### Community 2 - "Community 2"
Cohesion: 0.09
Nodes (35): @headlessui/react, @inertiajs/react, lucide-react, @radix-ui/react-separator, react, AppearanceToggleTab(), DeleteUser(), Heading() (+27 more)

### Community 3 - "Community 3"
Cohesion: 0.06
Nodes (23): User, DatabaseSeeder, Illuminate\Auth\Events\Verified, Illuminate\Auth\Notifications\ResetPassword, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase (+15 more)

### Community 4 - "Community 4"
Cohesion: 0.04
Nodes (46): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+38 more)

### Community 5 - "Community 5"
Cohesion: 0.06
Nodes (33): dependencies, class-variance-authority, clsx, concurrently, globals, @headlessui/react, @inertiajs/react, laravel-vite-plugin (+25 more)

### Community 6 - "Community 6"
Cohesion: 0.13
Nodes (18): @radix-ui/react-avatar, @radix-ui/react-tooltip, mainNavItems, rightNavItems, Icon(), IconProps, Avatar, AvatarFallback (+10 more)

### Community 7 - "Community 7"
Cohesion: 0.10
Nodes (20): private, type, clsx, concurrently, eslint, lightningcss-linux-x64-gnu, prettier, prettier-plugin-organize-imports (+12 more)

### Community 8 - "Community 8"
Cohesion: 0.14
Nodes (15): class-variance-authority, @radix-ui/react-toggle, @radix-ui/react-toggle-group, Alert, AlertDescription, AlertTitle, alertVariants, Badge() (+7 more)

### Community 9 - "Community 9"
Cohesion: 0.16
Nodes (14): react-dom, ref_ziggy_js, resources_css_app, AppearanceToggleDropdown(), resources_js_components_ui_dropdown_menu_dropdownmenu, DropdownMenuContent, resources_js_components_ui_dropdown_menu_dropdownmenutrigger, Appearance (+6 more)

### Community 10 - "Community 10"
Cohesion: 0.11
Nodes (17): aliases, components, hooks, lib, ui, utils, iconLibrary, rsc (+9 more)

### Community 11 - "Community 11"
Cohesion: 0.11
Nodes (17): compilerOptions, allowJs, baseUrl, esModuleInterop, forceConsistentCasingInFileNames, isolatedModules, jsx, module (+9 more)

### Community 12 - "Community 12"
Cohesion: 0.21
Nodes (15): @radix-ui/react-dialog, DialogContent, DialogDescription, DialogFooter(), DialogHeader(), DialogOverlay, DialogTitle, SelectContent (+7 more)

### Community 13 - "Community 13"
Cohesion: 0.16
Nodes (14): @radix-ui/react-dropdown-menu, DropdownMenuCheckboxItem, resources_js_components_ui_dropdown_menu_dropdownmenugroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuRadioItem, DropdownMenuSeparator, DropdownMenuShortcut() (+6 more)

### Community 14 - "Community 14"
Cohesion: 0.17
Nodes (8): HandleInertiaRequests, Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware, Illuminate\Foundation\Inspiring, Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets, Illuminate\Support\Facades\Artisan, Inertia\Middleware

### Community 15 - "Community 15"
Cohesion: 0.23
Nodes (3): Illuminate\Database\Migrations\Migration, Illuminate\Database\Schema\Blueprint, Illuminate\Support\Facades\Schema

### Community 16 - "Community 16"
Cohesion: 0.24
Nodes (4): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Str, static

### Community 17 - "Community 17"
Cohesion: 0.20
Nodes (10): devDependencies, eslint, eslint-config-prettier, @eslint/js, eslint-plugin-react, eslint-plugin-react-hooks, prettier, prettier-plugin-organize-imports (+2 more)

### Community 18 - "Community 18"
Cohesion: 0.22
Nodes (9): @radix-ui/react-navigation-menu, AppHeader(), NavigationMenu, NavigationMenuContent, NavigationMenuIndicator, NavigationMenuList, NavigationMenuTrigger, navigationMenuTriggerStyle (+1 more)

### Community 19 - "Community 19"
Cohesion: 0.33
Nodes (8): @radix-ui/react-slot, Breadcrumb, BreadcrumbEllipsis(), BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator()

### Community 20 - "Community 20"
Cohesion: 0.27
Nodes (5): AppLogo(), AppLogoIcon(), AuthLayoutProps, AuthSimpleLayout(), AuthLayoutProps

### Community 21 - "Community 21"
Cohesion: 0.36
Nodes (6): Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle

### Community 22 - "Community 22"
Cohesion: 0.25
Nodes (8): SheetContent, SheetContentProps, SheetDescription, SheetFooter(), SheetHeader(), SheetOverlay, SheetTitle, sheetVariants

### Community 23 - "Community 23"
Cohesion: 0.25
Nodes (6): eslint-config-prettier, @eslint/js, eslint-plugin-react, eslint-plugin-react-hooks, globals, typescript-eslint

### Community 24 - "Community 24"
Cohesion: 0.29
Nodes (7): scripts, build, build:ssr, dev, format, format:check, lint

### Community 26 - "Community 26"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 27 - "Community 27"
Cohesion: 0.40
Nodes (4): laravel-vite-plugin, @tailwindcss/vite, vite, @vitejs/plugin-react

### Community 28 - "Community 28"
Cohesion: 0.50
Nodes (4): optionalDependencies, lightningcss-linux-x64-gnu, @rollup/rollup-linux-x64-gnu, @tailwindcss/oxide-linux-x64-gnu

## Knowledge Gaps
- **161 isolated node(s):** `AppContentProps`, `AppShellProps`, `PlaceholderPatternProps`, `SidebarContext`, `Auth` (+156 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 243 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **13 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `cn()` connect `Community 12` to `Community 1`, `Community 2`, `Community 6`, `Community 8`, `Community 9`, `Community 13`, `Community 18`, `Community 19`, `Community 21`, `Community 22`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Why does `react` connect `Community 2` to `Community 1`, `Community 6`, `Community 7`, `Community 8`, `Community 9`, `Community 12`, `Community 13`, `Community 18`, `Community 19`, `Community 20`, `Community 21`, `Community 22`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._
- **Why does `dependencies` connect `Community 5` to `Community 7`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **What connects `AppContentProps`, `AppShellProps`, `PlaceholderPatternProps` to the rest of the system?**
  _161 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Community 0` be split into smaller, more focused modules?**
  _Cohesion score 0.06522522522522523 - nodes in this community are weakly interconnected._
- **Should `Community 1` be split into smaller, more focused modules?**
  _Cohesion score 0.05672926447574335 - nodes in this community are weakly interconnected._
- **Should `Community 2` be split into smaller, more focused modules?**
  _Cohesion score 0.08695652173913043 - nodes in this community are weakly interconnected._