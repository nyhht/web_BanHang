<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function showFormAddCate()
    {
        return view('admin.pages.categories-add');
    }

    public function addCategory(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile("image"))
        {
            $imagePath = $request->file("image");
            $fileName = now()->timestamp . '_' .uniqid() . '.' . $imagePath->getClientOriginalExtension();
            $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');
        }

        try {
            Category::create([
                'name' => $request->input('name'),
                // Thêm uniqid() vào sau slug để đảm bảo tính duy nhất, tránh lỗi trùng lặp (Duplicate Entry) trong Database
                'slug' => Str::slug($request->input('name')) . '-' . uniqid(),
                'description' => $request->input('description'),
                'image' => $imagePath,
            ]);

            return redirect()->route('admin.categories.add')->with('success', 'Danh mục đã được thêm thành công!');
        } catch (\Throwable $e) {
            Log::error('Failed creating category', ['error' => $e->getMessage()]);
            return redirect()->route('admin.categories.add')->with('error', 'Lỗi thêm danh mục: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $categories = Category::all();
        Log::info('Admin categories index loaded', ['count' => $categories->count()]);
        return view('admin.pages.categories', compact('categories'));
    }

    public function updateCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->category_id);
            if(!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục không tồn tại!'
                ],  404);
            }

            $category->name = $request->name;
            $category->description = $request->description;

            if($request->hasFile("image")){
                if($category->image) {
                    // Delete old image
                    Storage::disk('public')->delete($category->image);
                }

                $imagePath = $request->file("image");
                $fileName = now()->timestamp . '_' .uniqid() . '.' . $imagePath->getClientOriginalExtension();
                $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');

                $category->image = $imagePath;
            }

            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image ? asset('storage/' . $category->image) : null
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau!'
            ], 500);
        }
    }

    public function deleteCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->category_id);
            if(!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục không tồn tại!'
                ],  404);
            }

            // Delete image if exists
            if($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa danh mục thành công!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau!'
            ], 500);
        }
    }
}
