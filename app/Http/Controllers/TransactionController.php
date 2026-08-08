<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'items'])
            ->where('user_id', Auth::id());

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->latest()->get();

        $totalMonthly = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('pages.transaction.index', [
            'transactions' => $transactions,
            'totalMonthly' => $totalMonthly,
            'title' => 'Transactions - Finance Tracker',
            'navTitle' => 'Transactions'
        ]);
    }

    public function create()
    {
        $categories = Category::select(['id', 'name', 'type'])
                            ->where('user_id', Auth::id())
                            ->orderBy('name')->get();
        $defaultType = 'expense';

        return view('pages.transaction.create', [
            'categories' => $categories,
            'defaultType' => $defaultType,
            'title' => 'Create Transaction - Finance Tracker',
            'navTitle' => 'Create Transaction'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
            ],
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'title' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items.*.item_name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
            'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic,heif|max:10048',
            'ocr_data' => 'nullable|json',
        ], [
            'type.required' => 'The transaction type is required.',
            'type.in' => 'The selected transaction type is invalid.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category was not found.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 100 characters.',
            'note.string' => 'The note must be a string.',
            'items.*.item_name.string' => 'The item name must be a string.',
            'items.*.qty.numeric' => 'The item quantity must be a number.',
            'items.*.price.numeric' => 'The item price must be a number.',
            'receipt.image' => 'The receipt must be an image.',
            'receipt.mimes' => 'The receipt must be a file of type: jpg, jpeg, png, webp, heic, heif.',
            'receipt.max' => 'The receipt may not be greater than 10MB.',
            'ocr_data.json' => 'The OCR data must be a valid JSON.',
        ]);

        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'The selected category does not match the transaction type!']);
        }

        DB::transaction(function () use ($validated, $request) {
            $receiptName = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $receiptName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                
                $image = Image::make($file)->resize(1200, 1200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode('webp', 80);

                Storage::disk('public')->put('receipts/' . $receiptName, (string) $image);
            }

            $ocrData = !empty($validated['ocr_data']) ? json_decode($validated['ocr_data'], true) : null;

            $tx = Transaction::create([
                'user_id'     => Auth::user()->id,
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'type'        => $validated['type'],
                'date'        => $validated['date'],
                'amount'      => $validated['amount'],
                'note'        => $validated['note'] ?? null,
                'receipt'     => $receiptName,
                'ocr_data'    => $ocrData,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $it) {
                    if (!empty($it['item_name'])) {
                        Item::create([
                            'transaction_id' => $tx->id,
                            'item_name'      => $it['item_name'],
                            'qty'            => $it['qty'] ?? 1,
                            'price'          => $it['price'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('category', 'items');

        if ($transaction->user_id !== Auth::user()->id) {
            return redirect()->route('transactions.index')->with('error', 'Oops... Something went wrong!');
        }

        return view('transactions.show', [
            'tx' => $transaction,
        ]);
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::user()->id) {
            return redirect()->route('transactions.index')->with('error', 'Oops... Something went wrong!');
        }

        $categories = Category::select(['id', 'name', 'type'])
                            ->where('user_id', Auth::id())
                            ->orderBy('name')->get();

        return view('pages.transaction.edit', [
            'transaction' => $transaction,
            'categories' => $categories,
            'title' => 'Edit Transaction - Finance Tracker',
            'navTitle' => 'Edit Transaction',
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', Auth::id()),
            ],
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'title' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items.*.item_name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
            'receipt' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic,heif|max:10048',
            'ocr_data' => 'nullable|json',
            'remove_receipt' => 'nullable|boolean',
        ], [
            'type.required' => 'The transaction type is required.',
            'type.in' => 'The selected transaction type is invalid.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category was not found.',
            'date.required' => 'The date is required.',
            'date.date' => 'The date must be a valid date.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 100 characters.',
            'note.string' => 'The note must be a string.',
            'items.*.item_name.string' => 'The item name must be a string.',
            'items.*.qty.numeric' => 'The item quantity must be a number.',
            'items.*.price.numeric' => 'The item price must be a number.',
            'receipt.image' => 'The receipt must be an image.',
            'receipt.mimes' => 'The receipt must be a file of type: jpg, jpeg, png, webp, heic, heif.',
            'receipt.max' => 'The receipt may not be greater than 10MB.',
            'ocr_data.json' => 'The OCR data must be a valid JSON.',
        ]);

        if ($transaction->user_id !== Auth::user()->id) {
            return redirect()->route('transactions.index')->with('error', 'Oops... Something went wrong!');
        }

        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'The selected category does not match the transaction type!']);
        }

        DB::transaction(function () use ($transaction, $validated, $request) {
            $oldReceipt = $transaction->receipt;
            $receiptName = $oldReceipt;
            $hasNewReceipt = false;
            $wantsRemoveReceipt = $request->boolean('remove_receipt');

            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $receiptName = time() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
                $image = Image::make($file)
                    ->resize(1200, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode('webp', 80);
                Storage::disk('public')->put('receipts/' . $receiptName, (string) $image);
                $hasNewReceipt = true;
            }

            $updateData = [
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'type'        => $validated['type'],
                'date'        => $validated['date'],
                'amount'      => $validated['amount'],
                'note'        => $validated['note'] ?? null,
            ];

            // FIX: ocr_data hanya ditimpa kalau memang ada data baru dikirim
            // (mencegah data OCR lama hilang saat edit transaksi tanpa ganti struk)
            if (!empty($validated['ocr_data'])) {
                $updateData['ocr_data'] = json_decode($validated['ocr_data'], true);
            }

            if ($hasNewReceipt) {
                $updateData['receipt'] = $receiptName;
            } elseif ($wantsRemoveReceipt) {
                // FIX: handle penghapusan struk yang sebelumnya tidak diproses sama sekali
                $updateData['receipt'] = null;
                $updateData['ocr_data'] = null;
            }

            $transaction->update($updateData);
            Item::where('transaction_id', $transaction->id)->delete();

            foreach ($validated['items'] ?? [] as $it) {
                if (!empty($it['item_name'])) {
                    Item::create([
                        'transaction_id' => $transaction->id,
                        'item_name'      => $it['item_name'],
                        'qty'            => $it['qty'] ?? 1,
                        'price'          => $it['price'] ?? 0,
                    ]);
                }
            }

            // Hapus file lama dari storage kalau diganti ATAU dihapus
            if (($hasNewReceipt || $wantsRemoveReceipt) && !empty($oldReceipt)) {
                Storage::disk('public')->delete('receipts/' . $oldReceipt);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::user()->id) {
            return redirect()->route('transactions.index')->with('error', 'Oops... Something went wrong!');
        }

        if (!empty($transaction->receipt)) {
            Storage::disk('public')->delete('receipts/' . $transaction->receipt);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully!');
    }
}
