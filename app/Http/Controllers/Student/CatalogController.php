<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Book;
use App\Models\Category;
use App\Models\Course;
use App\Models\Payment;
use App\Services\BookDownloadService;
use App\Services\SiteSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function __construct(protected BookDownloadService $bookDownloads) {}

    /**
     * Explorar Publicaciones
     */
    public function explorePublications(Request $request)
    {
        $search = $request->input('search');

        $booksQuery = Book::latest();
        if ($search) {
            $booksQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $books = $booksQuery->paginate(6, ['*'], 'books_page')->withQueryString();
        $user = Auth::user();

        $bookPayments = Payment::query()
            ->where('user_id', $user->id)
            ->whereNotNull('book_id')
            ->whereIn('status', ['pendiente', 'en_revision', 'aprobado'])
            ->with('bookOrder')
            ->get()
            ->keyBy('book_id');

        $books->getCollection()->transform(function ($b) use ($bookPayments) {
            $isPaid = (float) $b->price > 0;
            $payment = $bookPayments->get($b->id);
            $order = $payment?->bookOrder;

            return [
                'id' => $b->id,
                'title' => $b->title,
                'price' => $b->price,
                'sale_price' => $b->sale_price,
                'stock' => $b->stock,
                'author' => $b->author ?? 'Instituto IEE',
                'description' => $b->description,
                'cover_image' => $b->cover_image,
                'download_url' => $b->download_url,
                'is_available' => (bool) $b->is_available,
                'payment_status' => $payment?->status,
                'needs_comprobante' => $payment && $payment->status === 'pendiente',
                'has_pending_payment' => $payment && in_array($payment->status, ['pendiente', 'en_revision'], true),
                'has_approved_purchase' => $payment && $payment->status === 'aprobado',
                'can_purchase' => $isPaid && $b->canAcceptNewPurchase() && ! $payment,
                'is_out_of_stock' => $isPaid && ! $b->hasUnlimitedStock() && $b->stock <= 0,
                'shipping' => $order ? [
                    'status' => $order->shipping_status,
                    'label' => $order->label(),
                    'tracking_url' => $order->tracking_url,
                    'delivery_mode' => $order->delivery_mode,
                    'pickup_location' => $order->pickup_location,
                    'student_note' => $order->student_note,
                    'carrier' => $order->carrier,
                ] : null,
            ];
        });

        $articlesQuery = Article::latest();
        if ($search) {
            $articlesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('media', 'like', "%{$search}%");
            });
        }

        $articles = $articlesQuery->paginate(6, ['*'], 'articles_page')->withQueryString();
        $articles->getCollection()->transform(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'media' => $a->media ?? 'Análisis',
            'published_at' => $a->published_at ? $a->published_at->format('Y-m-d') : now()->format('Y-m-d'),
            'thumbnail' => $a->thumbnail,
            'download_url' => $a->download_url,
        ]);

        $banner = Banner::where('section', 'publicaciones')->orderBy('order')->first();

        return Inertia::render('Publications', [
            'books' => $books,
            'articles' => $articles,
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Explorar Masterclasses
     */
    public function exploreMasterclasses(Request $request)
    {
        $banner = Banner::where('section', 'masterclass')->first();

        $categories = Category::whereHas('courses', function ($q) {
            $q->where('type', 'evento');
        })->orderBy('name')->get();

        $query = Course::published()
            ->where('type', 'evento')
            ->with(['category']);

        if ($request->filled('category') && $request->category !== 'Todas') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(6)->withQueryString();

        return Inertia::render('Masterclasses', [
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $request->only(['category']),
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Explorar Consultoría
     */
    public function exploreConsultoria()
    {
        $banner = Banner::where('section', 'consultoria')->orderBy('order')->first();

        return Inertia::render('Consultoria', [
            'banner' => $banner,
            'isDashboard' => true,
        ]);
    }

    /**
     * Descargar Libro (archivo local o enlace externo — siempre contabiliza).
     */
    public function downloadBook(Book $book)
    {
        if (! $book->is_available) {
            abort(403, 'El libro no está disponible para descarga.');
        }

        if ($book->isPaid()) {
            abort(403, 'Los libros de pago se entregan de forma física. Revisa el estado de tu pedido en Mis Pagos.');
        }

        $userId = Auth::id();

        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            $this->bookDownloads->record($book, $userId, 'file');

            return Storage::disk('public')->download($book->file_path);
        }

        if ($book->download_url && str_starts_with($book->download_url, 'http')) {
            $this->bookDownloads->record($book, $userId, 'external');

            return redirect()->away($book->download_url);
        }

        abort(404, 'El archivo del libro no se encuentra disponible.');
    }

    /**
     * Interés de compra vía WhatsApp (libros de pago).
     */
    public function bookPurchaseInterest(Book $book)
    {
        if (! $book->is_available || (float) $book->price <= 0) {
            abort(404);
        }

        $this->bookDownloads->record($book, Auth::id(), 'whatsapp');

        $number = SiteSettingsService::whatsappSales();
        $message = sprintf(
            'Hola, estoy interesado en adquirir el material: *%s* que tiene un costo de S/ %s. ¿Me podrían brindar más información?',
            $book->title,
            number_format((float) $book->price, 2)
        );

        return redirect()->away('https://wa.me/'.$number.'?text='.urlencode($message));
    }

    /**
     * Descargar Artículo
     */
    public function downloadArticle(Article $article)
    {
        if (! $article->file_path || ! Storage::disk('public')->exists($article->file_path)) {
            abort(404, 'El archivo del artículo no se encuentra disponible.');
        }

        return Storage::disk('public')->download($article->file_path);
    }
}
