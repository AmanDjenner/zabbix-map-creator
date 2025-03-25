<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="flex min-h-screen">
            <flux:sidebar sticky stashable class="w-64 border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
                <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
                    <x-app-logo />
                </a>
                <flux:navlist variant="outline">
                    <flux:navlist.group heading="Platform" class="grid">
                        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    </flux:navlist.group>
                    <flux:navlist.group heading="Administrare" class="grid">
                        <flux:navlist.item icon="calendar" :href="route('admin.manager')" :current="request()->routeIs('admin.manager')" wire:navigate>{{ __('Admin Manager') }}</flux:navlist.item>
                        <flux:navlist.item icon="calendar" :href="route('admin.events')" :current="request()->routeIs('admin.events')" wire:navigate>{{ __('Evenimente') }}</flux:navlist.item>
                        <flux:navlist.item icon="cube" :href="route('admin.objects')" :current="request()->routeIs('admin.objects')" wire:navigate>{{ __('Obiecte') }}</flux:navlist.item> 
                        <flux:navlist.item icon="user-group" :href="route('admin.detinuti')" :current="request()->routeIs('admin.detinuti')" wire:navigate>{{ __('Deținuți') }}</flux:navlist.item>
                        <flux:navlist.item icon="exclamation-circle" :href="route('admin.injuries')" :current="request()->routeIs('admin.injuries')" wire:navigate>{{ __('Leziuni') }}</flux:navlist.item>
                    </flux:navlist.group>
                    <flux:navlist.group heading="Utilizator" class="grid">
                        <flux:navlist.item icon="chart-bar" :href="route('admin.detinuti-statistics')" :current="request()->routeIs('admin.detinuti-statistics')" wire:navigate>{{ __('Sinteza 24 H') }}</flux:navlist.item>
                        <flux:navlist.item icon="chart-bar" :href="route('user.events-24h')" :current="request()->routeIs('user.events-24h')" wire:navigate>{{ __('Evenimente 24 H') }}</flux:navlist.item>
                        <flux:navlist.item icon="cube" :href="route('user.objects')" :current="request()->routeIs('user.objects')" wire:navigate>{{ __('Obiecte Interzise') }}</flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
                <flux:spacer />
                <flux:navlist variant="outline">
                    <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                        {{ __('Repository') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                        {{ __('Documentation') }}
                    </flux:navlist.item>
                </flux:navlist>
                <flux:dropdown position="bottom" align="start">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevrons-up-down"
                    />
                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden ">
                                        <span class="flex h-full w-full items-center justify-center bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>
                                    <div class="grid flex-1 text-left text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <flux:menu.radio.group>
                            <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>
                        <flux:menu.separator />
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:sidebar>

            <div class="flex-1 p-6 overflow-y-auto">
                {{ $slot }}
            </div>
        </div>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden ">
                                    <span class="flex h-full w-full items-center justify-center bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>
                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog" wire:navigate>Settings</flux:menu.item>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        @livewireScripts
        @stack('scripts')
        @fluxScripts
    </body>
</html>