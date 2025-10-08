<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" href="{{ config('settings.site_favicon') ?? asset('favicon.ico') }}" type="image/x-icon">

    @include('backend.layouts.partials.theme-colors')
    @yield('before_vite_build')

    @viteReactRefresh
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    @stack('styles')
    @yield('before_head')

    @if (!empty(config('settings.global_custom_css')))
        <style>
            {!! config('settings.global_custom_css') !!}
        </style>
    @endif

    <!-- @include('backend.layouts.partials.integration-scripts') -->

    @php echo ld_apply_filters('admin_head', ''); @endphp

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body x-data="{ 
    page: 'ecommerce', 
    loaded: true, 
    darkMode: false, 
    stickyMenu: false, 
    sidebarToggle: $persist(false), 
    scrollTop: false 
}" x-init="
    darkMode = JSON.parse(localStorage.getItem('darkMode'));
    $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));
    $watch('sidebarToggle', value => localStorage.setItem('sidebarToggle', JSON.stringify(value)))
" :class="{ 'dark bg-gray-900': darkMode === true }">

    <!-- Preloader -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>
    <!-- End Preloader -->
    <!-- Page Wrapper -->
    <div class="flex h-screen overflow-hidden">
        @include('backend.layouts.partials.sidebar-logo')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay -->
            <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
            <!-- End Small Device Overlay -->

            @include('backend.layouts.partials.header')

            <!-- Main Content -->
            <main>
                @yield('admin-content')
            </main>
            <!-- End Main Content -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html = document.documentElement;
            const darkModeToggle = document.getElementById('darkModeToggle');
            const header = document.getElementById('appHeader');


            // Update header background based on current mode
            function updateHeaderBg() {
                if (!header) return;
                const isDark = html.classList.contains('dark');
            }

            // Initialize dark mode
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'true') {
                html.classList.add('dark');
            } else if (savedDarkMode === 'false') {
                html.classList.remove('dark');
            } else {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    html.classList.add('dark');
                }
            }

            updateHeaderBg();

            const observer = new MutationObserver(updateHeaderBg);
            observer.observe(html, {
                attributes: true,
                attributeFilter: ['class']
            });

            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('darkMode', isDark);
                    updateHeaderBg();
                });
            }

            // Initialize sidebar state from localStorage if it exists
            if (window.Alpine) {
                const sidebarState = localStorage.getItem('sidebarToggle');
                if (sidebarState !== null) {
                    document.addEventListener('alpine:initialized', () => {
                        // Ensure the Alpine.js instance is ready
                        setTimeout(() => {
                            const alpineData = document.querySelector('body').__x;
                            if (alpineData && typeof alpineData.$data !== 'undefined') {
                                alpineData.$data.sidebarToggle = JSON.parse(sidebarState);
                            }
                        }, 0);
                    });
                }
            }
        });
    </script>

    @if (!empty(config('settings.global_custom_js')))
        <script>
            {!! config('settings.global_custom_js') !!}
        </script>
    @endif

    <script>
        window.sendMessage = function (customerId) {
            let input = document.getElementById('chat-message');
            const inputValue = input.value;
            input.value = "";
            let text = inputValue.trim();
            if (!text) return;

            fetch("/admin/customers/" + customerId + "/send-message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message: text })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log("Message sent");
                    }
                    else {
                        input.value = inputValue; // Restore original input value on failure
                        alert("Failed to send message: " + (data.error || 'Unknown error'));
                    }
                })
                .catch(err => console.error(err));
        }

        window.handleMediaClick = function (mediaId, mimeType) {
            const container = document.getElementById("media-" + mediaId);
            if (!container) return;

            // Show temporary "Downloading..." message while fetch is running
            container.innerHTML = `<span class="text-gray-500 text-sm">Downloading...</span>`;

            fetch("{{ route('admin.whatsapp.chatbox.media', ':id') }}".replace(':id', mediaId))
                .then(res => {
                    if (!res.ok) throw new Error("Failed to fetch media");
                    return res.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    let html = "";

                    if (mimeType.startsWith("image/")) {
                        html = `<img src="${url}" alt="image" class="rounded-lg max-w-full" style="width: 250px" />`;
                    } else if (mimeType.startsWith("video/")) {
                        html = `<video style="width:200px" controls class="rounded-lg max-w-full">
                          <source src="${url}" type="${mimeType}">
                        </video>`;
                    } else if (mimeType.startsWith("audio/")) {
                        html = `<audio controls class="w-full" style="width:300px">
                          <source src="${url}" type="${mimeType}">
                        </audio>`;
                    } else {
                        html = `<a href="${url}" download="media" class="px-3 py-1 rounded bg-blue-600 text-white text-sm">Download File</a>`;
                    }

                    container.innerHTML = html;
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML = `<span class="text-red-500 text-sm">Failed to load media</span>`;
                });
        };


    </script>

    @stack('scripts')
</body>

</html>