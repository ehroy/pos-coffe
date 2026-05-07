export function useAdminMenu() {
    return [
        { name: 'dashboard',  label: 'Dashboard',   href: route('admin.dashboard'),  route: 'admin.dashboard' },
        { name: 'products',   label: 'Produk',       href: route('admin.products'),   route: 'admin.products' },
        { name: 'categories', label: 'Kategori',     href: route('admin.categories'), route: 'admin.categories' },
        { name: 'inventory',  label: 'Inventory',    href: route('admin.inventory'),  route: 'admin.inventory' },
        { name: 'recipes',    label: 'Resep',        href: route('admin.recipes'),    route: 'admin.recipes' },
        { name: 'reports',    label: 'Laporan',      href: route('admin.reports'),    route: 'admin.reports' },
        { name: 'expenses',   label: 'Pengeluaran',  href: route('admin.expenses'),   route: 'admin.expenses' },
        { name: 'settings',   label: 'Settings',     href: route('admin.settings'),   route: 'admin.settings' },
        { name: 'tables',     label: 'Meja',         href: route('admin.tables'),     route: 'admin.tables' },
        { name: 'users',      label: 'Users',        href: route('admin.users'),      route: 'admin.users' },
    ];
}

export function useOwnerMenu() {
    return [
        { name: 'dashboard',  label: 'Dashboard',  href: route('owner.dashboard'),  route: 'owner.dashboard' },
        { name: 'products',   label: 'Produk',      href: route('owner.products'),   route: 'owner.products' },
        { name: 'categories', label: 'Kategori',    href: route('admin.categories'), route: 'admin.categories' },
        { name: 'inventory',  label: 'Inventory',   href: route('owner.inventory'),  route: 'owner.inventory' },
        { name: 'recipes',    label: 'Resep',       href: route('admin.recipes'),    route: 'admin.recipes' },
        { name: 'reports',    label: 'Laporan',     href: route('owner.reports'),    route: 'owner.reports' },
        { name: 'expenses',   label: 'Pengeluaran', href: route('owner.expenses'),   route: 'owner.expenses' },
        { name: 'settings',   label: 'Settings',    href: route('owner.settings'),   route: 'owner.settings' },
        { name: 'tables',     label: 'Meja',        href: route('owner.tables'),     route: 'owner.tables' },
        { name: 'users',      label: 'Users',       href: route('owner.users'),      route: 'owner.users' },
    ];
}

export function useCashierMenu() {
    return [
        { name: 'dashboard',     label: 'Dashboard',    href: route('cashier.dashboard'),     route: 'cashier.dashboard' },
        { name: 'pos',           label: 'POS',          href: route('cashier.pos'),           route: 'cashier.pos' },
        { name: 'orders',        label: 'Orders',       href: route('cashier.orders'),        route: 'cashier.orders' },
        { name: 'cash-session',  label: 'Sesi Kas',     href: route('cashier.cash-session'),  route: 'cashier.cash-session' },
    ];
}

export function useKitchenMenu() {
    return [
        { name: 'dashboard', label: 'Dashboard',    href: route('kitchen.dashboard'), route: 'kitchen.dashboard' },
        { name: 'display',   label: 'Display',      href: route('kitchen.display'),   route: 'kitchen.display' },
        { name: 'orders',    label: 'Active Orders', href: route('kitchen.orders'),    route: 'kitchen.orders' },
        { name: 'history',   label: 'Riwayat',      href: route('kitchen.history'),   route: 'kitchen.history' },
    ];
}

export function useWarehouseMenu() {
    return [
        { name: 'dashboard', label: 'Dashboard',  href: route('warehouse.dashboard'), route: 'warehouse.dashboard' },
        { name: 'inventory', label: 'Inventory',  href: route('warehouse.inventory'), route: 'warehouse.inventory' },
        { name: 'history',   label: 'Riwayat',    href: route('warehouse.history'),   route: 'warehouse.history' },
    ];
}
