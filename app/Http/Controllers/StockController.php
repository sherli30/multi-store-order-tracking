<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StockRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(private readonly StockService $stockService) {}

    /**
     * Display the stock movement history for a product.
     */
    public function index(Request $request, Product $product): View
    {
        // Untuk sementara kita gunakan variant pertama (Default)
        $variant = $product->variants()->first();

        if (!$variant) {
            abort(404, 'Product has no variants.');
        }

        $movements = StockMovement::where('product_variant_id', $variant->id)
            ->latest()
            ->paginate(20);

        $stats = [
            'total_in'  => StockMovement::where('product_variant_id', $variant->id)->where('type', 'in')->sum('quantity'),
            'total_out' => StockMovement::where('product_variant_id', $variant->id)->where('type', 'out')->sum('quantity'),
            'current'   => $variant->stock,
        ];

        return view('stock.index', compact('product', 'variant', 'movements', 'stats'));
    }

    /**
     * Add stock to a product variant (type: in).
     */
    public function add(StockRequest $request, Product $product): RedirectResponse
    {
        $variant = $product->variants()->first();

        if (!$variant) {
            return back()->with('error', [
                'title' => 'Produk Tidak Valid',
                'message' => 'Produk ini tidak memiliki variasi stok.'
            ]);
        }

        $qty = (int) $request->validated('qty');

        try {
            $this->stockService->addStock(
                variant:     $variant,
                qty:         $qty,
                source:      'manual_adjustment',
                referenceId: null,
            );
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Stok Gagal Ditambahkan',
                'message' => 'Terjadi kesalahan saat menambahkan stok. Silakan coba lagi.'
            ]);
        }

        // ── MULTI NOTIFICATION (TOAST) — add stock ────────────────────────
        $messages = [];
        $messages[] = "Jumlah sebanyak <strong>{$qty} unit</strong> berhasil ditambahkan ke varian <strong>{$variant->name}</strong>.";
        $messages[] = "Total stok saat ini: <strong>{$variant->fresh()->stock} unit</strong>.";

        return back()->with('success', [
            'title' => "Stok \"{$product->name}\" Berhasil Ditambahkan",
            'list' => $messages
        ]);
    }

    /**
     * Deduct stock from a product variant (type: out).
     */
    public function deduct(StockRequest $request, Product $product): RedirectResponse
    {
        $variant = $product->variants()->first();

        if (!$variant) {
            return back()->with('error', [
                'title' => 'Produk Tidak Valid',
                'message' => 'Produk ini tidak memiliki variasi stok.'
            ]);
        }

        $qty = (int) $request->validated('qty');

        try {
            $this->stockService->deductStock(
                variant:     $variant,
                qty:         $qty,
                source:      'manual_adjustment',
                referenceId: null,
            );
        } catch (InsufficientStockException $e) {
            return back()->with('error', [
                'title'   => 'Stok Gagal Dikurangi',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title'   => 'Stok Gagal Dikurangi',
                'message' => 'Terjadi kesalahan saat mengurangi stok. Silakan coba lagi.'
            ]);
        }

        // ── MULTI NOTIFICATION (TOAST) — deduct stock ─────────────────────
        $messages = [];
        $messages[] = "Jumlah sebanyak <strong>{$qty} unit</strong> berhasil dikurangi dari varian <strong>{$variant->name}</strong>.";
        $messages[] = "Total stok saat ini: <strong>{$variant->fresh()->stock} unit</strong>.";

        return back()->with('success', [
            'title' => "Stok \"{$product->name}\" Berhasil Dikurangi",
            'list' => $messages
        ]);
    }
}
