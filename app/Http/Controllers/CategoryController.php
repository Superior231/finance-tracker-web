<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = Category::withCount('transactions')->where('user_id', $user->id)->get();
        $types = Category::select('type')
                     ->distinct()
                     ->whereNotNull('type')
                     ->pluck('type');

        return view('pages.category.index', [
            'title' => 'Categories - Finance Tracker',
            'navTitle' => 'Categories',
            'active' => 'categories',
            'categories' => $categories,
            'types' => $types
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'unique:categories|required|string|max:255',
        ], [
            'type.required' => 'The category type is required.',
            'type.in'       => 'The category type is invalid.',
            'name.required' => 'The category name is required.',
            'name.unique'   => 'The category name has already been taken.',
        ]);

        if ($request->has('user_id') && (int) $request->user_id !== Auth::id()) {
            return redirect()->route('categories.index')->with('error', 'Oops... Something went wrong!');
        }

        $data = $request->only('type', 'name');
        $data['user_id'] = Auth::id();

        $category = Category::create($data);

        if ($category) {
            return redirect()->route('categories.index')->with('success', 'Category created successfully!');
        } else {
            return redirect()->route('categories.index')->with('error', 'Category failed to create!');
        }
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id() || ($request->has('user_id') && (int)$request->user_id !== Auth::id())) {
            return redirect()->route('categories.index')->with('error', 'Oops... Something went wrong!');
        }

        $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'unique:categories|required|string|max:255',
        ], [
            'type.required' => 'The category type is required.',
            'type.in'       => 'The category type is invalid.',
            'name.required' => 'The category name is required.',
            'name.unique'   => 'The category name has already been taken.',
        ]);

        // Cek apakah pengguna yang terautentikasi adalah pemilik dari data yang ingin diperbarui
        if (Auth::id() !== (int) Auth::user()->id) {
            return redirect()->route('profile.index')->with('error', 'Oops... Something went wrong!');
        }

        $category->update($request->only('type', 'name'));

        if ($category) {
            return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
        } else {
            return redirect()->route('categories.index')->with('error', 'Category failed to update!');
        }
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return redirect()->route('categories.index')->with('error', 'Oops... Something went wrong!');
        }

        if ($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Category cannot be deleted because it has transaction!.');
        }

        $category->delete();

        if ($category) {
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
        } else {
            return redirect()->route('categories.index')->with('error', 'Category failed to delete!');
        }
    }
}
