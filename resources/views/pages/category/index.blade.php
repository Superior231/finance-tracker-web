<x-app-layout :title="$title">
    @push('styles')
        <!-- Datatables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $navTitle }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="card">
            <div class="p-3 card-body p-lg-4">
                <div class="actions d-flex justify-content-between align-items-center">
                    <h4 class="py-0 my-0 fw-bold">All Categories</h4>
                    <button class="gap-1 px-4 py-2 text-light btn btn-primary d-flex align-items-center rounded-pill" data-bs-toggle="modal" data-bs-target="#tambah-kategori-modal">
                        <i class='bx bx-plus fs-5'></i>
                        Create
                    </button>
                </div>
                <hr class="my-3">
    
                <div class="pb-5 table-responsive">
                    <table class="table table-striped table-hover" id="myDataTable">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-center">Type</th>
                                <th>Updated at</th>
                                <th>Created at</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $item)
                                <tr class="align-middle">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="type-info d-flex align-items-center justify-content-center pe-3">
                                        @if ($item->type == 'income')
                                            <span class="badge rounded-pill bg-success">{{ $item->type }}</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger">{{ $item->type }}</span>
                                        @endif
                                    </div>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($item->updated_at)->translatedFormat('d F Y, H:i') }} WIB</td>
                                    <td>{{ Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y, H:i') }} WIB</td>
                                    <td>
                                        <div class="gap-2 actions d-flex align-items-center justify-content-center pe-3">
                                            <button class="p-2 rounded-05 btn btn-primary d-flex align-items-center justify-content-center" onclick="editCategory('{{ $item->id }}', '{{ $item->type }}', '{{ $item->name }}')" data-bs-toggle="modal" data-bs-target="#edit-kategori-modal">
                                                <i class='p-0 m-0 bx bxs-pencil'></i>
                                            </button>
                                            <form id="delete-category-form-{{ $item->id }}" action="{{ route('categories.destroy', $item->id) }}" method="POST">
                                                @csrf @method('DELETE')
            
                                                <button type="button" class="p-2 rounded btn btn-danger d-flex align-items-center justify-content-center" onclick="confirmDeleteCategory({{ $item->id }})">
                                                    <i class='p-0 m-0 bx bxs-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
        <!-- Modal Tambah Kategori -->
        <div class="modal fade" id="tambah-kategori-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="tambah-kategori-label">Create category</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2">
                                <x-input-label for="type" value="Type" />
                                <x-select-input name="type" id="type" class="block w-full mt-1" x-model="type" required>
                                    <option value="">Select type</option>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </x-select-input>
                            </div>
                            
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" type="text" name="name" class="block w-full mt-1" placeholder="Enter a new category name" required />
                        </div>
                        <div class="border-0 modal-footer">
                            <button type="submit" class="px-4 btn btn-primary rounded-10" id="tambah-kategori-btn">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal Tambah Kategori -->

        <!-- Modal Edit Kategori -->
        <div class="modal fade" id="edit-kategori-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="edit-kategori-label">Edit category</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form id="edit-category-form" method="POST">
                        @csrf @method('PUT')

                        <div class="modal-body">
                            <div class="mb-2">
                                <x-input-label for="edit-type" value="Type" />
                                <x-select-input id="edit-type" name="type" class="block w-full mt-1" x-model="edit-type"
                                    required>
                                    <option value="income">Income</option>
                                    <option value="expense">Expense</option>
                                </x-select-input>
                            </div>

                            <x-input-label for="edit-name" value="Name" />
                            <x-text-input id="edit-name" type="text" name="name" class="block w-full mt-1" placeholder="Enter category name" required />
                        </div>
                        <div class="border-0 modal-footer">
                            <button type="submit" id="edit-kategori-btn" class="px-4 btn btn-primary rounded-10">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>        
        <!-- Modal Edit Kategori End -->
    <!-- Modal End -->
    
    @push('scripts')
        <!-- JQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
        <!-- Datatables Js -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
        <script>
            $(document).ready(function() {
                $('#myDataTable').DataTable();
            });
            $('#myDataTable').DataTable({
                "language": {
                    "searchPlaceholder": "Search..."
                }
            });
    
            function editCategory(category, type, name) {
                $('#edit-type').val(type);
                $('#edit-name').val(name);
    
                let url = "{{ route('categories.update', ':id') }}";
                url = url.replace(':id', category);
    
                $('#edit-category-form').attr('action', url);
                $('#edit-kategori-modal').modal('show');
            }
    
            function confirmDeleteCategory(categoryId) {
                Swal.fire({
                    icon: 'question',
                    title: 'Are You Sure?',
                    text: 'Are you sure you want to delete this category?',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    customClass: {
                        popup: 'sw-popup',
                        title: 'sw-title',
                        htmlContainer: 'sw-text',
                        icon: 'sw-icon border-red-300',
                        closeButton: 'bg-secondary border-0 shadow-none',
                        confirmButton: 'bg-danger border-0 shadow-none',
                    },
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-category-form-' + categoryId).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>