<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Books', [
            'books' => Book::latest()->paginate(12),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|in:Libro,Libro en camino,Guía',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'author' => 'nullable|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'required|image|max:2048',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'download_url' => 'nullable|url|max:255',
            'is_available' => 'required|boolean',
        ]);

        if ($data['category'] === 'Libro en camino') {
            $data['price'] = 0;
            $data['author'] = $data['author'] ?? 'IEE';
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
        }

        if ($request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('book_files', 'public');
        }

        Book::create($data);

        return redirect()->back()->with('success', 'Libro creado correctamente.');
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'category' => 'required|in:Libro,Libro en camino,Guía',
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'author' => 'nullable|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'download_url' => 'nullable|url|max:255',
            'is_available' => 'required|boolean',
        ]);

        if ($data['category'] === 'Libro en camino') {
            $data['price'] = 0;
        }

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
        } else {
            unset($data['cover_image']);
        }

        if ($request->hasFile('file_path')) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('book_files', 'public');
        } else {
            unset($data['file_path']);
        }

        $book->update($data);

        return redirect()->back()->with('success', 'Libro actualizado correctamente.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }
        $book->delete();

        return redirect()->back()->with('success', 'Libro eliminado correctamente.');
    }
}
