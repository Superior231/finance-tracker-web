<x-app-layout :title="$title">
    <div x-data="createTransaction({
        typeInitial: '{{ old('type', $defaultType ?? 'expense') }}',
        categories: @js($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->type])),
        oldCategoryId: '{{ old('category_id') }}'
    })">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold leading-tight text-gray-800">
                    {{ $navTitle }}
                </h2>
            </div>
        </x-slot>

        <div class="max-w-5xl px-6 py-2 mx-auto lg:px-8">
            <div class="mb-5 shadow-sm card">
                <div class="p-3 card-body p-lg-4">
                    <!-- TYPE TOGGLE -->
                    <div class="flex p-1 bg-gray-100 rounded-xl w-100">
                        <button type="button" @click="type='expense'"
                            :class="type === 'expense' ? 'bg-green-100 text-gray-900 border-green-300' :
                                'text-gray-600 hover:text-gray-700 border-transparent'"
                            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium border rounded-xl w-100">
                            <i class='py-0 my-0 bx bx-trending-down fs-5'></i>
                            <span>Expense</span>
                        </button>
                        <button type="button" @click="type='income'"
                            :class="type === 'income' ? 'bg-green-100 text-gray-900 border-green-300' :
                                'text-gray-600 hover:text-gray-700 border-transparent'"
                            class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium border rounded-xl w-100">
                            <i class='py-0 my-0 bx bx-trending-up fs-5'></i>
                            <span>Income</span>
                        </button>
                    </div>

                    <div class="mt-6">
                        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data"
                            class="space-y-6" id="form" @receipt-selected="autoParseReceipt($event.detail.file)"
                            @receipt-cleared="clearOcrData()">
                            @csrf
                            <input type="hidden" name="type" :value="type">
                            <input type="hidden" name="ocr_data" :value="ocrDataRaw">

                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Receipt Image</label>

                                <div x-data="receiptUploader()" class="mt-2">
                                    <!-- input asli (disembunyikan) -->
                                    <input x-ref="file" type="file" name="receipt"
                                        accept=".jpg, .jpeg, .png, .webp, .heic, .heif" class="sr-only"
                                        @change="fileChosen">

                                    <!-- Placeholder / Dropzone -->
                                    <div x-show="!previewUrl" x-cloak @click="$refs.file.click()"
                                        @dragover.prevent="drag=true" @dragleave.prevent="drag=false"
                                        @drop.prevent="drop($event)"
                                        :class="drag ? 'border-green-500 bg-green-50' :
                                            'border-green-400 hover:border-green-500'"
                                        class="relative transition-colors border-2 border-dashed cursor-pointer rounded-2xl">
                                        <div class="absolute inset-0 grid p-8 text-center place-items-center">
                                            <div class="flex flex-col items-center">
                                                <i class='bx bx-folder fs-1 text-success'></i>

                                                <p class="mt-3 text-sm text-green-700">
                                                    <span class="text-indigo-600 underline underline-offset-4">
                                                        Choose image or drag and drop here
                                                    </span>
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500">Format: JPG/PNG/JPEG/WEBP · Maks
                                                    10MB
                                                </p>

                                                <span
                                                    class="inline-flex items-center gap-1 px-3 py-2 mt-4 text-sm font-medium text-white bg-green-700 rounded-lg shadow align-items-center hover:bg-green">
                                                    <i class='bx bx-image-alt'></i>
                                                    Upload Image
                                                </span>
                                            </div>
                                        </div>
                                        <!-- spacer agar tinggi enak dilihat -->
                                        <div class="invisible">
                                            <img class="object-cover w-full h-64" alt="">
                                        </div>
                                    </div>

                                    <!-- Preview -->
                                    <div x-show="previewUrl" x-cloak
                                        class="relative overflow-hidden rounded-2xl ring-1 ring-gray-200">
                                        <img :src="previewUrl" alt="Preview struk"
                                            class="object-cover w-full h-72" />
                                        <div
                                            class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/40 via-transparent">
                                        </div>

                                        <!-- info & actions -->
                                        <div
                                            class="absolute bottom-0 left-0 right-0 flex items-center justify-between gap-3 p-3">
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium truncate text-white/90"
                                                    x-text="fileName"></div>
                                                <div class="text-xs truncate text-white/70" x-text="prettySize()">
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="$refs.file.click()"
                                                    class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-medium text-gray-900 shadow hover:bg-white">
                                                    Change
                                                </button>
                                                <button type="button" @click="remove()"
                                                    class="rounded-lg bg-red-600/90 px-3 py-1.5 text-xs font-medium text-white shadow hover:bg-red-600">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- status -->
                                    <p id="strukStatus" class="mt-2 text-sm"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <x-input-label for="category_id" value="Category" />
                                    <x-select-input id="category_id" name="category_id" class="block w-full mt-1"
                                        x-model="category_id" required>
                                        <option value="">Select type</option>
                                        <template x-for="c in filteredCats" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </x-select-input>

                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date" value="Date" />
                                    <x-text-input id="date" type="date" name="date" class="block w-full mt-1"
                                        :value="old('date', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="amount_display" value="Amount" />
                                    <input type="hidden" name="amount" :value="parseRawAmount(amountFormatted)">
                                    <x-text-input id="amount_display" type="text" class="block w-full mt-1"
                                        placeholder="Rp 0" x-model="amountFormatted"
                                        @input="handleAmountInput($event)" />
                                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="title" value="Title" />
                                <x-text-input id="title" type="text" name="title" class="block w-full mt-1"
                                    placeholder="Enter transaction title" required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="note" value="Detail (optional)" />
                                <x-textarea id="note" type="text" name="note"
                                    class="block w-full mt-1"></x-textarea>
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-green-800">Item</h3>
                                    <button x-show="items.length === 0" type="button" @click="addItem()"
                                        class="rounded-10 btn btn-sm btn-outline-success">
                                        + Add Item
                                    </button>
                                </div>

                                <template x-for="(it, idx) in items" :key="idx">
                                    <div class="">
                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                                            <div class="md:col-span-6">
                                                <label class="block text-xs font-medium text-gray-600">Item
                                                    Name</label>
                                                <input :name="`items[${idx}][item_name]`" x-model="it.item_name"
                                                    placeholder="Enter item name"
                                                    class="block w-full px-3 py-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-600">Qty</label>
                                                <input type="number" min="1" :name="`items[${idx}][qty]`"
                                                    x-model.number="it.qty" placeholder="Qty"
                                                    class="block w-full px-3 py-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-medium text-gray-600">Price</label>
                                                <input type="hidden" :name="`items[${idx}][price]`" :value="it.price">
                                                <input type="text" x-model="it.priceFormatted"
                                                    @input="formatItemPrice(idx, $event.target.value)"
                                                    placeholder="Rp 0"
                                                    class="block w-full px-3 py-2 mt-1 border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-xl">
                                            </div>
                                            <div class="flex items-end md:col-span-1">
                                                <button type="button" @click="removeItem(idx)"
                                                    class="flex items-center justify-center w-full px-3 py-2 text-red-700 border border-red-300 rounded-xl md:w-auto hover:bg-red-50 h-[42px] mt-1">
                                                    <i class='py-0 my-0 bx bx-x fs-5'></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div x-show="idx !== items.length - 1"
                                            class="w-full my-3 border-t border-gray-200">
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length > 0" x-cloak
                                    class="flex items-center justify-between w-full mt-3">
                                    <span class="text-xs fw-bold">Total: <span x-text="itemsTotal"></span></span>
                                    <button type="button" @click="addItem()"
                                        class="rounded-10 btn btn-sm btn-outline-success">
                                        + Add Item
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-center">
                                <x-primary-button class="flex items-center justify-center w-full text-center"
                                    type="submit" id="submitBtn">
                                    <span id="spinner" class="hidden">
                                        <i class='bx bx-loader-alt bx-spin bx-rotate-90'></i>
                                    </span>
                                    <span class="text-center" id="textBtn">Save</span>
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                const API_URL = @json(config('services.parser.url'));
                let receiptParsing = false; // cegah submit form saat parser AI masih berjalan

                function createTransaction(init) {
                    return {
                        type: init.typeInitial || 'expense',
                        categories: init.categories || [],
                        category_id: init.oldCategoryId || '',
                        items: [],
                        previewUrl: null,
                        ocrDataRaw: '',

                        get filteredCats() {
                            return this.categories.filter(c => c.type === this.type);
                        },

                        // IDR Currency
                        amountFormatted: '',

                        formatRupiah(num) {
                            if (num === null || num === undefined || num === '') return '';
                            const numeric = String(num).replace(/[^0-9]/g, '');
                            return numeric ? 'Rp ' + new Intl.NumberFormat('id-ID').format(numeric) : '';
                        },

                        formatItemPrice(idx, value) {
                            const raw = this.parseRawAmount(value);
                            this.items[idx].price = raw;
                            this.items[idx].priceFormatted = raw ? this.formatRupiah(raw) : '';
                        },

                        parseRawAmount(val) {
                            if (!val) return 0;
                            return Number(String(val).replace(/[^0-9]/g, '')) || 0;
                        },

                        get itemsTotalRaw() {
                            return this.items.reduce((sum, it) =>
                                sum + ((Number(it.qty) || 0) * (Number(it.price) || 0)), 0);
                        },

                        get itemsTotal() {
                            return this.formatRupiah(this.itemsTotalRaw);
                        },

                        handleAmountInput(e) {
                            const raw = this.parseRawAmount(e.target.value);
                            this.amountFormatted = raw ? this.formatRupiah(raw) : '';
                        },

                        init() {
                            // Auto-set category pertama saat type berubah jika category_id tidak valid
                            this.$watch('type', () => {
                                if (!this.filteredCats.find(c => String(c.id) === String(this.category_id))) {
                                    this.category_id = this.filteredCats[0]?.id ?? '';
                                }
                            });
                            this.$watch('itemsTotalRaw', (newVal) => {
                                if (this.items.length > 0) {
                                    this.amountFormatted = this.formatRupiah(newVal);
                                }
                            });
                            // Inisialisasi awal
                            if (!this.filteredCats.find(c => String(c.id) === String(this.category_id))) {
                                this.category_id = this.filteredCats[0]?.id ?? '';
                            }
                        },

                        addItem() {
                            this.items.push({
                                item_name: '',
                                qty: 1,
                                price: null,
                                priceFormatted: ''
                            });
                        },
                        removeItem(i) {
                            this.items.splice(i, 1);
                        },
                        clearOcrData() {
                            this.items = [];
                            this.ocrDataRaw = '';
                            this.amountFormatted = '';

                            const amountInput = document.getElementById('amount');
                            if (amountInput) {
                                amountInput.value = '';
                            }

                            const statusEl = document.getElementById('strukStatus');
                            if (statusEl) {
                                statusEl.textContent = '';
                                statusEl.className = 'mt-2 text-sm';
                            }
                        },
                        async autoParseReceipt(file) {
                            if (!file) return;

                            const btn = document.getElementById('submitBtn');
                            const spinner = document.getElementById('spinner');
                            const textBtn = document.getElementById('textBtn');
                            const statusEl = document.getElementById('strukStatus');

                            const setStatus = (msg, ok) => {
                                if (!statusEl) return;
                                statusEl.textContent = msg;
                                statusEl.className = 'mt-2 text-sm ' + (ok ? 'text-green-600' : 'text-red-600');
                            };

                            setStatus('Processing receipt images...', true);
                            textBtn.innerText = 'Processing...';
                            spinner.classList.remove('hidden');
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                            receiptParsing = true;

                            const formData = new FormData();
                            formData.append('image', file);

                            try {
                                const res = await fetch(API_URL, {
                                    method: 'POST',
                                    body: formData
                                });
                                const json = await res.json();

                                if (!res.ok || !json.success) {
                                    throw new Error(json.error || 'Failed to parse receipt.');
                                }

                                this.ocrDataRaw = JSON.stringify(json.data);

                                const parsed = (json.data.items || []).map(it => {
                                    const rawPrice = it.price ?? 0;
                                    return {
                                        item_name: it.item_name ?? '',
                                        qty: it.qty ?? 1,
                                        price: rawPrice,
                                        priceFormatted: rawPrice ? this.formatRupiah(rawPrice) : ''
                                    };
                                });

                                this.items = parsed.length ? parsed : [{
                                    item_name: '',
                                    qty: 1,
                                    price: null
                                }];

                                setStatus('Data successfully parsed!', true);
                            } catch (err) {
                                setStatus('Error: ' + err.message, false);
                            } finally {
                                textBtn.innerText = 'Save';
                                spinner.classList.add('hidden');
                                btn.disabled = false;
                                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                                receiptParsing = false;
                            }
                        }
                    }
                }

                function receiptUploader() {
                    return {
                        previewUrl: null,
                        fileName: '',
                        fileSize: 0,
                        drag: false,
                        error: '',
                        maxSize: 10 * 1024 * 1024, // 10MB

                        fileChosen(e) {
                            const file = e.target.files?.[0];
                            this.handleFile(file);
                        },
                        drop(e) {
                            this.drag = false;
                            const file = e.dataTransfer.files?.[0];
                            this.handleFile(file);
                        },
                        handleFile(file) {
                            if (!file) return;
                            this.error = '';

                            if (!file.type.startsWith('image/')) {
                                this.error = 'File must be an image (JPG/PNG/JPEG/WEBP).';
                                return this.clear();
                            }
                            if (file.size > this.maxSize) {
                                this.error = 'File size must be less than 10MB.';
                                return this.clear();
                            }

                            this.fileName = file.name;
                            this.fileSize = file.size;
                            this.previewUrl = URL.createObjectURL(file);

                            // beri tahu form induk supaya parser AI langsung jalan otomatis
                            this.$dispatch('receipt-selected', {
                                file
                            });
                        },
                        prettySize() {
                            if (!this.fileSize) return '';
                            const mb = this.fileSize / (1024 * 1024);
                            return mb.toFixed(2) + ' MB';
                        },
                        remove() {
                            this.clear();
                        },
                        clear() {
                            this.previewUrl = null;
                            this.fileName = '';
                            this.fileSize = 0;
                            if (this.$refs.file) this.$refs.file.value = '';
                            this.$dispatch('receipt-cleared');
                        }
                    }
                }

                document.getElementById('form').addEventListener('submit', function(e) {
                    if (receiptParsing) {
                        e.preventDefault(); // parser AI masih berjalan, jangan submit dulu
                        return;
                    }

                    const spinner = document.getElementById('spinner');
                    const textBtn = document.getElementById('textBtn');

                    textBtn.innerText = 'Saving...';
                    spinner.classList.remove('hidden');
                });
            </script>
        @endpush
    </div>
</x-app-layout>
