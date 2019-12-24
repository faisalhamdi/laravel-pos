<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Exception;
// use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use Intervention\Image\Facades\Image as Image;
use File;
use Image;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->orderBy('created_at', 'DESC')->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create() {
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $r) {
        // validasi data
        $this->validate($r, [
            'code' => 'required|string|max:10|unique:products',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        try {
            $photo = null;
            if ($r->hasFile('photo')) {
                $photo = $this->saveFile($r->name, $r->file('photo'));
            }

            $product = Product::create([
                'code' => $r->code,
                'name' => $r->name,
                'description' => $r->description,
                'stock' => $r->stock,
                'price' => $r->price,
                'category_id' => $r->category_id,
                'photo' => $photo
            ]);

            return redirect(route('products.index'))
                    ->with(['success' => '<strong>' . $product->name . '</strong> Added']);

        } catch (Exception $e) {
            return redirect()->back()
            ->with(['error' => $e->getMessage()]);
        }
    }

    private function saveFile($name, $photo)
    {
        $images = str_slug($name) . time() . '.' . $photo->getClientOriginalExtension();
        $path = public_path('uploads/product');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        } 
        Image::make($photo)->save($path . '/' . $images);
        return $images;
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);

        if (!empty($product->photo)) {
            File::delete(public_path('uploads/product', $product->photo));
        }

        $product->delete();

        return redirect()->back()->with(['success' => '<strong>' . $product->name . '</strong> Deleted.!']);
    }

    public function edit($id) {
        $product = Product::findOrFail($id);

        $categories = Category::orderBy('name', 'ASC')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $r, $id) {
        // validasi data
        $this->validate($r, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        try {
            $product = Product::findOrFail($id);
            $photo = $product->photo;
            
            if ($r->hasFile('photo')) {

                !empty($photo) ? File::delete(public_path('uploads/product/' . $photo)) : null;

                $photo = $this->saveFile($r->name, $r->file('photo'));
            }
            
            $product->update([
                'name' => $r->name,
                'description' => $r->description,
                'stock' => $r->stock,
                'price' => $r->price,
                'category_id' => $r->category_id,
                'photo' => $photo
            ]);

            return redirect(route('products.index'))
                    ->with(['success' => '<strong>' . $product->name . '</strong> Updated!']);

        } catch (Exception $e) {
            return redirect()->back()
            ->with(['error' => $e->getMessage()]);
        }
    }

    
}
