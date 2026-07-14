<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookDownload;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Books', [
            'books' => Book::withCount([
                'downloads',
                'payments as approved_sales_count' => fn ($q) => $q->where('status', 'aprobado'),
            ])
                ->withSum(['payments as total_earned' => fn ($q) => $q->where('status', 'aprobado')], 'amount')
                ->latest()
                ->paginate(8),
            'stats' => [
                'total' => Book::count(),
                'available' => Book::where('is_available', true)->count(),
                'downloads' => BookDownload::count(),
                'downloads_month' => BookDownload::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'book_income' => (float) Payment::where('status', 'aprobado')->whereNotNull('book_id')->sum('amount'),
                'book_sales' => Payment::where('status', 'aprobado')->whereNotNull('book_id')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'author' => 'nullable|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'required|image|max:2048',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'download_url' => 'nullable|url|max:255',
            'is_available' => 'required|boolean',
        ]);

        if ((float) $data['price'] > 0) {
            $request->validate(['stock' => 'required|integer|min:0']);
            $data['stock'] = (int) $request->input('stock');
            $data['file_path'] = null;
            $data['download_url'] = null;
        } else {
            $data['stock'] = null;
            $data['sale_price'] = null;
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
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'author' => 'nullable|string|max:255',
            'description' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'download_url' => 'nullable|url|max:255',
            'is_available' => 'required|boolean',
        ]);

        if ((float) $data['price'] > 0) {
            $request->validate(['stock' => 'required|integer|min:0']);
            $data['stock'] = (int) $request->input('stock');
            $data['download_url'] = null;
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
        } else {
            $data['stock'] = null;
            $data['sale_price'] = null;
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
