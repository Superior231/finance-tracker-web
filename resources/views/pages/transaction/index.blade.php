<x-app-layout :title="$title">
    @push('styles')
        <!-- Datatables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold leading-tight text-gray-800">
                {{ $navTitle }}
            </h2>
        </div>
    </x-slot>

    <div class="px-6 py-2 mx-auto max-w-7xl lg:px-8">
        <div class="mb-5 shadow-sm card">
            <div class="p-3 card-body p-lg-4">
                <div class="actions d-flex justify-content-between align-items-center">
                    <h4 class="py-0 my-0 fw-bold">All Transactions</h4>
                    <a href="{{ route('transactions.create') }}"
                        class="gap-1 px-4 py-2 text-light btn btn-primary d-flex align-items-center rounded-10">
                        <i class='bx bx-plus text-light fs-5'></i>
                        <span class="text-light">Create</span>
                    </a>
                </div>
                <hr class="my-3">
                <form method="GET" action="{{ route('transactions.index') }}" class="mb-1 filter">
                    <div class="gap-2 d-flex flex-column w-100">
                        <div class="gap-2 d-flex flex-column flex-md-row align-items-end">
                            <div class="w-100">
                                <x-input-label for="start_date" value="Start Date" />
                                <x-text-input id="start_date" type="date" name="start_date" class="block w-full mt-1"
                                    :value="request('start_date')" />
                            </div>

                            <div class="w-100">
                                <x-input-label for="end_date" value="End Date" />
                                <x-text-input id="end_date" type="date" name="end_date" class="block w-full mt-1"
                                    :value="request('end_date')" />
                            </div>
                        </div>
                        <div class="gap-2 d-flex justify-content-end align-items-center">
                            <a href="{{ route('transactions.index') }}"
                                class="btn btn-outline-danger hover:!text-green-50 rounded-xl w-full sm:w-auto px-4 py-2">Reset</a>
                            <button type="submit"
                                class="w-full px-4 py-2 btn btn-outline-success rounded-xl sm:w-auto">Filter</button>
                        </div>
                    </div>
                </form>
                <hr class="my-3">

                <div class="table-responsive" style="min-height: 350px;">
                    <table id="myDataTable" class="table table-striped table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center">Receipt</th>
                                <th class="text-center">Type</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th class="text-center">Amount</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr class="align-middle">
                                    <td>
                                        <div class="position-relative d-flex justify-content-center">
                                            <div class="image d-flex justify-content-center" style="cursor: pointer;"
                                                data-bs-toggle="modal" data-bs-target="#showImageModal"
                                                onclick="showImage('{{ $transaction->receipt }}')">
                                                @if (!empty($transaction->receipt))
                                                    <img src="{{ asset('storage/receipts/' . $transaction->receipt) }}"
                                                        alt="receipt image">
                                                @else
                                                    <img src="{{ url('assets/img/logo.png') }}" alt="receipt image">
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="type-info d-flex align-items-center justify-content-center pe-3">
                                            @if ($transaction->type == 'income')
                                                <span
                                                    class="px-2 py-1 text-green-700 capitalize bg-green-100 border !border-green-300 rounded-pill !text-xs">{{ $transaction->type }}</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 text-red-700 capitalize bg-red-100 border !border-red-300 rounded-pill !text-xs">{{ $transaction->type }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <span>{{ $transaction->category->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column max-w-[160px]">
                                            <span class="font-medium truncate">{{ $transaction->title }}</span>
                                            @if ($transaction->items->isNotEmpty())
                                                <a class="text-decoration-underline text-primary"
                                                    style="cursor: pointer;" id="showItem-{{ $transaction->id }}"
                                                    onclick="showItems({{ $transaction->id }})">
                                                    <span>View items</span>
                                                </a>

                                                <div class="show-items-container d-none flex-column"
                                                    id="showItemContainer-{{ $transaction->id }}">
                                                    @foreach ($transaction->items as $item)
                                                        <span class="py-0 my-0 truncate">-
                                                            {{ $item->item_name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-order="{{ $transaction->amount }}">
                                        <div class="justify-content-end d-flex">
                                            <span class="font-medium">{{ $transaction->formatted_amount }}</span>
                                        </div>
                                    </td>
                                    <td data-order="{{ $transaction->date }}">
                                        <span>
                                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions d-flex justify-content-center">
                                            <div class="dropdown">
                                                <i class="bx bx-dots-vertical-rounded fs-4"
                                                    id="action-{{ $transaction->id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" style="cursor: pointer;" title="Actions"></i>

                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="action-{{ $transaction->id }}">
                                                    <li>
                                                        <a class="gap-1 transition duration-150 ease-in-out dropdown-item d-flex align-items-center focus:bg-green-100"
                                                            href="{{ route('transactions.show', $transaction->id) }}">
                                                            <i class='bx bx-show fs-5'></i>
                                                            View details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="gap-1 transition duration-150 ease-in-out dropdown-item d-flex align-items-center focus:bg-green-100"
                                                            href="{{ route('transactions.edit', $transaction->id) }}">
                                                            <i class='bx bx-pencil fs-5'></i>
                                                            Edit data
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form id="deleteTransactionForm-{{ $transaction->id }}"
                                                            action="{{ route('transactions.destroy', $transaction->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <a style="cursor: pointer;"
                                                                class="gap-1 transition duration-150 ease-in-out dropdown-item d-flex align-items-center focus:bg-red-100 active:bg-red-700 active:!text-red-100"
                                                                onclick="confirmDeleteTransaction('{{ $transaction->id }}', '{{ $transaction->title }}')">
                                                                <i class='bx bx-trash fs-5'></i>
                                                                Delete
                                                            </a>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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

    <div class="modal fade" id="showImageModal" tabindex="-1" aria-labelledby="showImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="bg-transparent modal-content">
                <div class="border-0 modal-header">
                    <div class="close d-flex justify-content-end w-100" data-bs-dismiss="modal" aria-label="Close"
                        style="cursor: pointer;">
                        <i class='bx bx-x text-light fs-1'></i>
                    </div>
                </div>
                <div class="modal-body d-flex justify-content-center">
                    <div class="receipt-image" style="max-width: 100vw;">
                        <img src="" alt="image" id="showImage" style="width: 100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- JQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Datatables Js -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#myDataTable').DataTable({
                    "order": [[5, 'desc']],
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 6] },
                    ],
                    "language": {
                        "searchPlaceholder": "Search..."
                    },
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ]
                });
            });

            function showItems(transactionId) {
                var container = document.getElementById('showItemContainer-' + transactionId);

                container.classList.toggle('d-none');
                container.classList.toggle('d-flex');
            }

            function showImage(receipt) {
                let imageUrl = receipt ? '{{ asset('storage/receipts') }}/' + receipt :
                    '{{ url('assets/img/logo.png') }}';
                $('#showImage').attr('src', imageUrl);
            }

            function confirmDeleteTransaction(transactionId, transactionTitle) {
                Swal.fire({
                    icon: 'question',
                    title: 'Are You Sure?',
                    html: `Are you sure you want to delete <b class="text-danger">${transactionTitle}</b>?`,
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
                        document.getElementById('deleteTransactionForm-' + transactionId).submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
