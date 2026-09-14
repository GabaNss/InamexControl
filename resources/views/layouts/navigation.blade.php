<nav x-data="{ open: false }" class="bg-white border-b-4 border-brand-600 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <x-application-logo class="block h-9 w-auto" />
                        <span class="font-bold text-lg text-gray-900 leading-tight hidden sm:inline">
                            Inamex<span class="text-brand-600">Control</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @can('viewAny', App\Models\Paciente::class)
                        <x-nav-link :href="route('pacientes.index')" :active="request()->routeIs('pacientes.*')" wire:navigate>
                            {{ __('Internas') }}
                        </x-nav-link>
                    @endcan
                    @can('viewAny', App\Models\Medicamento::class)
                        <x-nav-link :href="route('medicamentos.index')" :active="request()->routeIs('medicamentos.*')" wire:navigate>
                            {{ __('Medicamentos') }}
                        </x-nav-link>
                    @endcan
                    @can('viewAny', App\Models\Prescricao::class)
                        <x-nav-link :href="route('prescricoes.index')" :active="request()->routeIs('prescricoes.*')" wire:navigate>
                            {{ __('Prescrições') }}
                        </x-nav-link>
                    @endcan
                    @can('viewAny', App\Models\RegistroMedicacao::class)
                        <x-nav-link :href="route('registros.index')" :active="request()->routeIs('registros.*')" wire:navigate>
                            {{ __('Registros') }}
                        </x-nav-link>
                    @endcan
                    @can('viewAny', App\Models\DiarioStatus::class)
                        <x-nav-link :href="route('diario.show')" :active="request()->routeIs('diario.*')" wire:navigate>
                            {{ __('Diário') }}
                        </x-nav-link>
                    @endcan
                    @can('gerar-relatorios')
                        <x-nav-link :href="route('relatorios.index')" :active="request()->routeIs('relatorios.*')" wire:navigate>
                            {{ __('Exportação') }}
                        </x-nav-link>
                    @endcan
                    @can('viewAny', App\Models\User::class)
                        <x-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" wire:navigate>
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    @endcan
                    @can('ver-auditoria')
                        <x-nav-link :href="route('auditoria.index')" :active="request()->routeIs('auditoria.*')" wire:navigate>
                            {{ __('Auditoria') }}
                        </x-nav-link>
                    @endcan
                    @can('gerenciar-backup')
                        <x-nav-link :href="route('backup.index')" :active="request()->routeIs('backup.*')" wire:navigate>
                            {{ __('Backup') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @can('viewAny', App\Models\Paciente::class)
                <x-responsive-nav-link :href="route('pacientes.index')" :active="request()->routeIs('pacientes.*')" wire:navigate>
                    {{ __('Internas') }}
                </x-responsive-nav-link>
            @endcan
            @can('viewAny', App\Models\Medicamento::class)
                <x-responsive-nav-link :href="route('medicamentos.index')" :active="request()->routeIs('medicamentos.*')" wire:navigate>
                    {{ __('Medicamentos') }}
                </x-responsive-nav-link>
            @endcan
            @can('viewAny', App\Models\Prescricao::class)
                <x-responsive-nav-link :href="route('prescricoes.index')" :active="request()->routeIs('prescricoes.*')" wire:navigate>
                    {{ __('Prescrições') }}
                </x-responsive-nav-link>
            @endcan
            @can('viewAny', App\Models\RegistroMedicacao::class)
                <x-responsive-nav-link :href="route('registros.index')" :active="request()->routeIs('registros.*')" wire:navigate>
                    {{ __('Registros') }}
                </x-responsive-nav-link>
            @endcan
            @can('viewAny', App\Models\DiarioStatus::class)
                <x-responsive-nav-link :href="route('diario.show')" :active="request()->routeIs('diario.*')" wire:navigate>
                    {{ __('Diário') }}
                </x-responsive-nav-link>
            @endcan
            @can('gerar-relatorios')
                <x-responsive-nav-link :href="route('relatorios.index')" :active="request()->routeIs('relatorios.*')" wire:navigate>
                    {{ __('Exportação') }}
                </x-responsive-nav-link>
            @endcan
            @can('viewAny', App\Models\User::class)
                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')" wire:navigate>
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
            @endcan
            @can('ver-auditoria')
                <x-responsive-nav-link :href="route('auditoria.index')" :active="request()->routeIs('auditoria.*')" wire:navigate>
                    {{ __('Auditoria') }}
                </x-responsive-nav-link>
            @endcan
            @can('gerenciar-backup')
                <x-responsive-nav-link :href="route('backup.index')" :active="request()->routeIs('backup.*')" wire:navigate>
                    {{ __('Backup') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
