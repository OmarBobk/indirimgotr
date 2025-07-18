<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
      lang="{{ strtolower(str_replace('_', '-', app()->getLocale())) }}"
      class="light" style="border:none">
    <head>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZHW5S051EH"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-ZHW5S051EH');
        </script>

        <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.svg') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine Cart Store -->
        <script>
            document.addEventListener('alpine:init', () => {
            });
        </script>
    </head>
    <body
        class="min-h-screen bg-gray-50"
        data-user="{{auth()->check() ? auth()->id() : ''}}"
        data-role="{{auth()->user()?->roles()->first()->name ?? ''}}"
    >
        <!-- Notification Component -->
        <div
        x-data="{
                notifications: [],
                add(message) {
                    if (!message[0] || !message[0].type || !message[0].message) {
                        console.error('Invalid notification format:', message);
                        console.error('Invalid notification format:', message[0].type);
                        console.error('Invalid notification format:', message[0].message);
                        return;
                    }

                    const notification = {
                        id: Date.now(),
                        type: message[0].type,
                        message: message[0].message,
                        sec: message[0].sec || 3000
                    };

                    this.notifications.push(notification);

                    // Auto-remove notification after 3 seconds
                    setTimeout(() => {
                        this.remove(notification.id);
                    }, notification.sec);
                },
                remove(id) {
                    this.notifications = this.notifications.filter(notification => notification.id !== id);
                }
            }"
        @notify.window="add($event.detail)"
        class="fixed top-4 right-4 z-[99999] space-y-2 w-full max-w-sm"
    >
        <template x-for="notification in notifications" :key="notification.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="transform translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="{
                        'alert-success': notification.type === 'success',
                        'alert-danger': notification.type === 'error',
                        'alert-info': notification.type === 'info',
                        'alert-warning': notification.type === 'warning'
                    }"
                class="w-full shadow-lg rounded-lg pointer-events-auto alert"
            >
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-3">
                            <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                        </div>
                        <div class="flex flex-shrink-0">
                            <button
                                @click="remove(notification.id)"
                                class="inline-flex text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white rounded-md"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

        <header class="bg-white shadow-sm">
            <livewire:frontend.partials.header-component />
        </header>
        <main>
            {{ $slot }}
        </main>
        <footer>
            <livewire:frontend.partials.footer-component />
        </footer>

        <!-- Notification Component -->
        <x-notification />

        @stack('scripts')

        <script>
            window.addEventListener('languageChanged', () => {
                location.reload();
            });
        </script>
    </body>
</html>


