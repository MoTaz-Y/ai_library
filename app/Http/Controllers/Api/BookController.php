<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // عرض الكتب مع الفلترة والبحث (متاح للجميع)
    public function index(Request $request)
    {
        $query = Book::with('category');

        // فلترة بالبحث (العنوان، المؤلف، أو الـ ISBN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // فلترة بالتصنيف
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // إرجاع النتيجة مع Pagination (10 كتب بالصفحة)
        $books = $query->latest()->paginate(10);

        return response()->json($books);
    }

    // إضافة كتاب جديد (Admin فقط)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'isbn' => 'required|string|unique:books,isbn|max:100',
            'publication_date' => 'nullable|date',
            'available_copies' => 'required|integer|min:0',
            'cover_image' => 'nullable|string', // يمكن استبدالها بـ file upload لاحقاً
        ]);

        $book = Book::create($validated);

        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book->load('category'),
        ], 201);
    }

    // عرض كتاب محدد
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return response()->json($book);
    }

    // تعديل كتاب (Admin فقط)
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'isbn' => 'sometimes|required|string|max:100|unique:books,isbn,' . $book->id,
            'publication_date' => 'nullable|date',
            'available_copies' => 'sometimes|required|integer|min:0',
            'cover_image' => 'nullable|string',
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book->load('category'),
        ]);
    }

    // حذف كتاب (Admin فقط)
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}