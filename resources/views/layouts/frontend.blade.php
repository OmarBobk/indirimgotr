<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
      lang="{{ strtolower(str_replace('_', '-', app()->getLocale())) }}"
      class="light" style="border:none">
    <head>
        <script type="text/javascript">
            var _iub = _iub || [];
            _iub.csConfiguration = {"siteId":4147810,"cookiePolicyId":69197729,"lang":"en","storage":{"useSiteId":true}};
        </script>
        <script type="text/javascript" src="https://cs.iubenda.com/autoblocking/4147810.js"></script>
        <script type="text/javascript" src="//cdn.iubenda.com/cs/gpp/stub.js"></script>
        <script type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8" async></script>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-K2NP60HHJL"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-K2NP60HHJL');
        </script>

        <link rel="icon" type="image/png" href="{{ asset('assets/images/indirimgo_logo.png') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Add user info to layout -->
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-role" content="{{ auth()->check() ? (auth()->user()->roles->first()?->name ?? 'user') : 'user' }}">
        <title>{{ $title ?? config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine Cart Store -->
        <script>
            setInterval(() => {
                fetch('/keep-alive');
            }, 5 * 60 * 1000);
            document.addEventListener('alpine:init', () => {
                Alpine.store('cart', {
                    items: Alpine.$persist([]).as('cart_items'),
                    itemsCount: 0,
                    subtotal: 0,
                    syncTimeout: null,

                    init() {
                        this.updateTotals();

                        // Listen for currency changes
                        window.addEventListener('currency-switched', () => {
                            this.syncWithServer();
                        });

                        // Listen for the checkout event from CheckoutComponent to clear the cart.
                        window.addEventListener('checkout', () => {
                            this.clear();
                        });

                        // Listen for cart items from server
                        window.addEventListener('cart-items-from-server', (event) => {
                            this.loadItemsFromServer(event.detail[0]);
                        });

                        // Initial sync with server after a short delay to ensure Livewire is loaded
                        setTimeout(() => {
                            this.syncWithServer();
                        }, 500);
                    },

                    loadItemsFromServer(items) {
                        console.log('Loading items from server:', items);
                        this.items = items;
                        this.updateTotals();
                    },

                    updateTotals() {
                        this.itemsCount = this.items.reduce((sum, item) => sum + item.quantity ,0);
                        this.subtotal = this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    },

                    addItem(product, quantity = 1) {
                        const existingItem = this.items.find(item => item.product_id === product.id);

                        if (existingItem) {
                            existingItem.quantity += quantity;
                        } else {
                            this.items.push({
                                price: product.prices[0].base_price,
                                quantity: quantity,
                                subtotal: this.subtotal,
                                product_id: product.id,

                                name: product.name,
                                description: product.description,
                                image: product.images[0].image_url
                            });
                        }
                        window.Livewire.dispatch('notify', [{
                            type: 'success',
                            message: `Added ${product.name} to cart`,
                            sec: 1000
                        }]);

                        this.updateTotals();
                        this.scheduleSync();
                    },

                    updateQuantity(productId, quantity) {
                        const item = this.items.find(item => item.product_id === productId);
                        if (item) {
                            if (quantity < 1) {
                                this.removeItem(productId);
                            } else {
                                item.quantity = quantity;
                                this.updateTotals();
                                this.scheduleSync();
                            }
                        }
                    },

                    removeItem(productId) {
                        this.items = this.items.filter(item => item.product_id !== productId);
                        this.updateTotals();

                        console.log('remove item', productId);

                        let id = productId
                        window.Livewire.dispatch('remove-item', {id});
                    },

                    clear() {
                        this.items = [];
                        this.updateTotals();

                        // this.syncWithServer();

                        window.Livewire.dispatch('clear-cart')
                    },

                    scheduleSync() {
                        if (this.syncTimeout) {
                            clearTimeout(this.syncTimeout)
                        }

                        this.syncTimeout = setTimeout(() => {
                            this.syncWithServer();
                        }, 1000)
                    },

                    async syncWithServer() {
                        if(this.syncTimeout) {
                            clearTimeout(this.syncTimeout);
                        }

                        try {
                            const items = this.items.map(item => ({
                                product_id: item.product_id,
                                quantity: item.quantity
                            }));

                            await window.Livewire.dispatch('sync-cart', { items })
                        } catch (error) {
                            console.error('Error syncing cart:', error);
                        }

                    }
                })
            });
        </script>
    </head>
    <body
        class="min-h-screen bg-gray-50"
        data-user="{{auth()->check() ? auth()->id() : ''}}"
        data-role="{{auth()->user()?->roles()->first()->name ?? ''}}"
    >
                <!-- Modern Notification Component -->
        <div
        x-data="{
                notifications: [],
                add(message) {
                    if (!message[0] || !message[0].type || !message[0].message) {
                        console.error('Invalid notification format:', message);
                        return;
                    }

                    const notification = {
                        id: Date.now(),
                        type: message[0].type,
                        message: message[0].message,
                        sec: message[0].sec || 5000
                    };

                    this.notifications.push(notification);

                    // Auto-remove notification after 5 seconds
                    setTimeout(() => {
                        this.remove(notification.id);
                    }, notification.sec);
                },
                remove(id) {
                    this.notifications = this.notifications.filter(notification => notification.id !== id);
                },
                getIcon(type) {
                    switch(type) {
                        case 'success':
                            return '<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z\' clip-rule=\'evenodd\'></path></svg>';
                        case 'error':
                            return '<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z\' clip-rule=\'evenodd\'></path></svg>';
                        case 'warning':
                            return '<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z\' clip-rule=\'evenodd\'></path></svg>';
                        case 'info':
                            return '<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\' clip-rule=\'evenodd\'></path></svg>';
                        default:
                            return '<svg class=\'w-6 h-6\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path fill-rule=\'evenodd\' d=\'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\' clip-rule=\'evenodd\'></path></svg>';
                    }
                }
            }"
        @notify.window="add($event.detail)"
        class="notification-container"
    >
        <template x-for="notification in notifications" :key="notification.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform translate-y-2 opacity-0 scale-95"
                x-transition:enter-end="transform translate-y-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                :class="{
                        'notification-success': notification.type === 'success',
                        'notification-error': notification.type === 'error',
                        'notification-info': notification.type === 'info',
                        'notification-warning': notification.type === 'warning'
                    }"
                class="notification pointer-events-auto"
            >
                <!-- Progress bar -->
                <div class="notification-progress"></div>

                <!-- Content -->
                <div class="notification-content">
                    <!-- Icon -->
                    <div class="notification-icon" x-html="getIcon(notification.type)"></div>

                    <!-- Message -->
                    <div class="notification-text">
                        <p class="notification-message" x-text="notification.message"></p>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="remove(notification.id)"
                        class="notification-close"
                        type="button"
                    >
                        <span class="sr-only">Close notification</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

        <header
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            x-data="{ isScrolled: false }"
            x-init="window.addEventListener('scroll', () => {
        isScrolled = window.scrollY > 10;
    })"
            :class="isScrolled
        ? 'fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-lg border-b border-gray-200'
        : 'fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white'"
        >
            <livewire:frontend.partials.header-component />
        </header>

        <main class="pt-28">
            {{ $slot }}
        </main>


        <footer>
            <livewire:frontend.partials.footer-component />
        </footer>

        <!-- Notification Component -->
        <x-notification />

        <!-- Product Modal Component - Global -->
        <livewire:frontend.product-modal-component />

        @stack('scripts')

        <script>
            window.addEventListener('languageChanged', () => {
                location.reload();
            });
        </script>
    </body>
</html>


