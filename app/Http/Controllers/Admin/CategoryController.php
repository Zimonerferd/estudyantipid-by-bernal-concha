<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', [
            'categories' => Category::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
        ], [
            'name.unique' => 'That category already exists.',
        ], ['name' => 'category name']);

        $category = Category::create([
            'name'         => trim($data['name']),
            'weekly_limit' => config('estudyantipid.default_category_limit'),
            'is_savings'   => false,
        ]);

        ActivityLog::record("Category “{$category->name}” added");

        return redirect()->route('admin.categories');
    }

    public function destroy(Category $category)
    {
        // Past expenses keep their category name; the category just stops being offered.
        $category->delete();

        ActivityLog::record("Category “{$category->name}” removed");

        return redirect()->route('admin.categories');
    }
}
