{{--<div x-data="{--}}
{{--    notifications: [],--}}
{{--    add(notification) {--}}
{{--        this.notifications.push({--}}
{{--            id: Date.now(),--}}
{{--            type: notification[0].type,--}}
{{--            message: notification[0].message--}}
{{--        });--}}
{{--        setTimeout(() => this.remove(this.notifications[0].id), 3000);--}}
{{--    },--}}
{{--    remove(id) {--}}
{{--        this.notifications = this.notifications.filter(notification => notification.id !== id);--}}
{{--    }--}}
{{--}"--}}
{{--    @notify.window="add($event.detail)"--}}
{{--    class="fixed top-4 right-4 z-50 space-y-2">--}}
{{--    <template x-for="notification in notifications" :key="notification.id">--}}
{{--        <div x-show="notification"--}}
{{--             x-transition:enter="transform ease-out duration-300 transition"--}}
{{--             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"--}}
{{--             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"--}}
{{--             x-transition:leave="transition ease-in duration-100"--}}
{{--             x-transition:leave-start="opacity-100"--}}
{{--             x-transition:leave-end="opacity-0"--}}
{{--             class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">--}}
{{--            <div class="p-4">--}}
{{--                <div class="flex items-start">--}}
{{--                    <div class="flex-shrink-0">--}}
{{--                        <!-- Success Icon -->--}}
{{--                        <template x-if="notification.type === 'success'">--}}
{{--                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />--}}
{{--                            </svg>--}}
{{--                        </template>--}}
{{--                        <!-- Error Icon -->--}}
{{--                        <template x-if="notification.type === 'error'">--}}
{{--                            <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">--}}
{{--                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />--}}
{{--                            </svg>--}}
{{--                        </template>--}}
{{--                    </div>--}}
{{--                    <div class="ml-3 flex-1 pt-0.5">--}}
{{--                        <p x-text="notification.message" class="text-sm font-medium text-gray-900"></p>--}}
{{--                    </div>--}}
{{--                    <div class="ml-4 flex-shrink-0 flex">--}}
{{--                        <button @click="remove(notification.id)" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">--}}
{{--                            <span class="sr-only">Close</span>--}}
{{--                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">--}}
{{--                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />--}}
{{--                            </svg>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </template>--}}
{{--</div>--}}
