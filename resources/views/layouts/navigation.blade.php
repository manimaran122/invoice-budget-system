<aside class="w-full border-b border-gray-800 bg-app-sidebar lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:border-b-0 lg:border-r">
    <div class="flex h-full flex-col">
        <div class="flex h-16 items-center gap-3 border-b border-gray-800 px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="block h-9 w-auto fill-current text-primary" />
                <span class="text-base font-semibold text-white">
                    Invoice Budget
                </span>
            </a>
        </div>

        <nav class="flex-1 space-y-1 px-4 py-5">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Dashboard
            </a>

            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Admin Dashboard
            </a>

            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Customers
            </a>

            <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Suppliers
            </a>

            <a href="{{ route('admin.product-services.index') }}" class="{{ request()->routeIs('admin.product-services.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Products & Services
            </a>

            <a href="{{ route('admin.budgets.index') }}" class="{{ request()->routeIs('admin.budgets.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Budgets
            </a>

            <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Expenses
            </a>

            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Reports
            </a>

            <a href="{{ route('admin.purchase-invoices.index') }}" class="{{ request()->routeIs('admin.purchase-invoices.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Purchase Invoices
            </a>

            <a href="{{ route('admin.sales-invoices.index') }}" class="{{ request()->routeIs('admin.sales-invoices.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Sales Invoices
            </a>

            <a href="{{ route('admin.currency.index') }}" class="{{ request()->routeIs('admin.currency.*') ? 'bg-primary text-white border-primary' : 'border-transparent text-gray-300 hover:bg-gray-800 hover:text-white' }} flex items-center rounded-md border-l-4 px-4 py-2 text-sm font-medium">
                Currency Rates
            </a>
        </nav>

        <div class="border-t border-gray-800 p-4">
            <div class="mb-3">
                <div class="text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }} block rounded-md px-4 py-2 text-sm font-medium">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-md px-4 py-2 text-left text-sm font-medium text-red-300 hover:bg-gray-800 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
