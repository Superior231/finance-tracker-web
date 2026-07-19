<x-app-layout :title="$title">
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800">
            {{ $navTitle }}
        </h2>
    </x-slot>

    <section class="px-6 py-2 mx-auto profile max-w-7xl lg:px-8">
       <div class="row row-cols-1 row-cols-md-2 g-3">
            <div class="col-12 col-md-4">
                <div class="shadow-sm card">
                    <div class="card-body user-info">
                        <div class="profile-image">
                            @if (!empty(Auth::user()->avatar))
                                <img class="object-cover w-full h-full img" src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}">
                            @elseif (!empty(Auth::user()->avatar_google))
                                <img class="object-cover w-full h-full img" src="{{ Auth::user()->avatar_google }}">
                            @else
                                <img class="object-cover w-full h-full img" src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}">
                            @endif
                        </div>

                        <div class="gap-0 d-flex flex-column align-items-center justify-content-center">
                            <h2 class="text-lg font-bold text-center">{{ Auth::user()->name }}</h2>
                            <p class="text-sm">{{ Auth::user()->email }}</p> 
                        </div>
                    </div>
                </div>
            </div>

            <div class="gap-2 col-12 col-md-8 d-flex flex-column actions">
                <a href="#" class="p-0 shadow-sm card edit-profile" data-bs-toggle="modal" data-bs-target="#edit-profile">
                    <div class="gap-2 card-body d-flex align-items-center">
                        <i class='bx bx-user fs-4'></i>
                        <span class="font-semibold">Edit profile</sp>
                    </div>
                </a>

                @if (empty(Auth::user()->avatar_google))
                    <a href="#" class="p-0 shadow-sm card change-password" data-bs-toggle="modal" data-bs-target="#change-password">
                        <div class="gap-2 card-body d-flex align-items-center">
                            <i class='bx bx-lock-alt fs-4'></i>
                            <span class="font-semibold">Change password</sp>
                        </div>
                    </a>
                @endif

                <a href="{{ route('logout') }}" onclick="event.preventDefault(); logout();" class="p-0 shadow-sm card logout">
                    <div class="gap-2 card-body d-flex align-items-center text-danger">
                        <i class='bx bx-arrow-from-left fs-3'></i>
                        <span class="font-bold">Logout</sp>
                    </div>
                </a>

                @if (empty(Auth::user()->avatar_google))
                    <a href="#" class="p-0 mb-5 shadow-sm card delete-account" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
                        <div class="gap-2 card-body d-flex align-items-center">
                            <i class='bx bx-trash-alt fs-4'></i>
                            <span class="font-semibold">Delete Account</span>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="edit-profile" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="edit-profile-label">Edit profile</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="delete-avatar-btn">
                    <form id="delete-avatar-form-{{ Auth::user()->id }}" action="{{ route('profile.delete.avatar', Auth::user()->id) }}" method="POST">
                        @csrf @method('DELETE') 
                        <button type="button" onclick="deleteAvatar({{ Auth::user()->id }})">Hapus avatar</button>
                    </form>
                </div>
                
                <form action="{{ route('profile.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="modal-body">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="profile-image">
                                @if (!empty(Auth::user()->avatar))
                                    <img class="object-cover w-full h-full img" src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" id="image-preview">
                                @elseif (!empty(Auth::user()->avatar_google))
                                    <img class="object-cover w-full h-full img" src="{{ Auth::user()->avatar_google }}" id="image-preview">
                                @else
                                    <img class="object-cover w-full h-full img" src="https://ui-avatars.com/api/?background=random&name={{ urlencode(Auth::user()->name) }}" id="image-preview">
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 mb-3">
                            <x-input-label for="image" value="Avatar (jpg, jpeg, png, dan webp)" />
                            <x-file-input name="avatar" class="block w-full mt-1" id="image" accept=".jpg, .jpeg, .png, .webp" />
                            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="edit-name" value="Name" />
                            <x-text-input type="text" class="block w-full mt-1" name="name" id="edit-name" placeholder="Enter name" :value="old('name', Auth::user()->name)" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="edit-email" :value="__('Email')" />
                            <x-text-input id="edit-email" name="email" type="email" class="block w-full mt-1" :value="old('email', Auth::user()->email)" required autocomplete="off" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div class="pt-0 mt-0 border-0 modal-footer">
                        <button type="submit" class="px-4 btn btn-primary rounded-10" id="simpan-edit-profile-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="change-password" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="change-password-label">Change password</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf 
                    @method('PUT')
    
                    <div class="modal-body">
                        {{-- Current Password --}}
                        <div class="mb-3">
                            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                            <x-password-input id="update_password_current_password" name="current_password" type="password" class="block w-full mt-1" autocomplete="current-password" placeholder="Enter current password" required />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>
    
                        {{-- New Password --}}
                        <div class="mb-3">
                            <x-input-label for="update_password_password" :value="__('New Password')" />
                            <x-password-input id="update_password_password" name="password" type="password" class="block w-full mt-1" autocomplete="new-password" placeholder="Enter new password" required />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>
    
                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                            <x-password-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full mt-1" autocomplete="new-password" placeholder="Confirm new password" required />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>
    
                    <div class="pt-0 mt-0 border-0 modal-footer">
                        <button type="submit" class="px-4 btn btn-primary rounded-10" id="simpan-change-password-btn">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy', Auth::user()->id) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-password-input id="password" name="password" type="password" class="block w-full mt-1" placeholder="Enter your password" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-6">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
    
    @push('scripts')
        <script>
            function deleteAvatar(userId) {
                Swal.fire({
                    icon: 'question',
                    title: 'Are You Sure?',
                    text: 'Are you sure you want to delete this avatar?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
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
                        document.getElementById('delete-avatar-form-' + userId).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
