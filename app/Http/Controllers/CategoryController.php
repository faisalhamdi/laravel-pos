<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::orderBy('created_at', 'DESC')->paginate(10);

        return view('categories.index', compact('categories'));
    }
    
    public function store(Request $r) {
        $this->validate($r, [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        try {
            $category = Category::firstOrCreate([
                'name' => $r->name
            ], [
                'description' => $r->description
            ]);
            return redirect()->back()->with(['success' => 'Category : ' . $category->name . ' Added']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);

        $category->delete();

        return redirect()->back()->with(['success' => 'Category : ' . $category->name . ' Deleted']); 
    }

    public function edit($id) {
        $category = Category::findOrFail($id);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $r, $id) {
        $this->validate($r, [
            'name' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        try {
            $category = Category::findOrFail($id);

            $category->update([
                'name' => $r->name,
                'description' => $r->description
            ]);

            return redirect(route('categories.index'))->with(['success' => 'Category : ' . $category->name . ' Updated']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }
}
