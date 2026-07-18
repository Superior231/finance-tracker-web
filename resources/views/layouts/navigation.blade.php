<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 position-sticky sticky-top">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 md:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.index')">
                        {{ __('Categories') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border-0 border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                            
                            <div class="flex items-center justify-center w-8 h-8 overflow-hidden bg-gray-200 rounded-full profile-image">
                                @if (!empty(Auth::user()->avatar))
                                    <img class="object-cover w-full h-full img" src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}">
                                @elseif (!empty(Auth::user()->avatar_google))
                                    <img class="object-cover w-full h-full img" src="{{ Auth::user()->avatar_google }}">
                                @else
                                    <img class="object-cover w-full h-full img" src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}">
                                @endif
                            </div>
                            
                            <div class="hidden md:block nav-username">
                                <span>{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-0 md:block">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('logout')"
                            id="logout-confirmaton"
                            onclick="event.preventDefault(); logout();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                        
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>

<script>
    function logout() {
        Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'Are you sure you want to logout?',
            showCancelButton: true,
            confirmButtonText: 'Logout',
            customClass: {
                popup: 'sw-popup',
                title: 'sw-title',
                htmlContainer: 'sw-text',
                icon: 'border-success text-success',
                closeButton: 'bg-secondary border-0 shadow-none',
                confirmButton: 'bg-danger border-0 shadow-none',
            },
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    try {
        const navbar = document.querySelector("nav");
        const classList = ["shadow-sm"];

        if (navbar) {
            const handleScroll = () => {
                const action = window.pageYOffset > 0 ? 'add' : 'remove';
                if (navbar) navbar.classList[action](...classList);
            };

            window.addEventListener("scroll", handleScroll);
        }
    } catch (error) {
        console.log("Navbar not found!");
    }
</script>