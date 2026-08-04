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
    public function index()
    {
        $transactions = Transaction::with('category', 'items')
            ->where('user_id', Auth::user()->id)
            ->latest()->paginate(12);

        $totalMonthly = Transaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('pages.transaction.index', compact('transactions', 'totalMonthly'));
    }

    public function create()
    {
        $transactions = Transaction::all();
        $categories = Category::select(['id', 'name', 'type'])
                            ->where('user_id', Auth::id())
                            ->orderBy('name')->get();
        $defaultType = 'expense';

        return view('pages.transaction.create', [
            'categories' => $categories,
            'defaultType' => $defaultType,
            'title' => 'Create Transaction - Finance Tracker',
            'navTitle' => 'Create Transaction',
            'transactions' => $transactions
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
                            'subtotal'       => ($it['qty'] ?? 1) * ($it['price'] ?? 0),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.create')->with('success', 'Transaction created successfully!');
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
}
