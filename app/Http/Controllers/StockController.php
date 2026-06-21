<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StockRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use App\Services\AuditService;
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
        $movements = StockMovement::where('product_id', $product->id)
            ->latest()
            ->paginate(20);

        $rawOut = StockMovement::where('product_id', $product->id)
            ->where('type', 'out')
            ->sum('quantity');

        $reversals = StockMovement::where('product_id', $product->id)
            ->where('type', 'in')
            ->whereNotIn('source', ['initial_stock', 'manual_adjustment'])
            ->sum('quantity');

        $totalOut = max(0, $rawOut - $reversals);

        $totalIn = StockMovement::where('product_id', $product->id)
            ->where('type', 'in')
            ->whereIn('source', ['initial_stock', 'manual_adjustment'])
            ->sum('quantity');

        $stats = [
            'total_in'  => $totalIn,
            'total_out' => $totalOut,
            'current'   => $product->stock,
        ];

        return view('stock.index', compact('product', 'movements', 'stats'));
    }

    /**
     * Add stock to a product (type: in).
     */
    public function add(StockRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $qty = (int) $data['qty'];
        $note = $data['note'];
        $oldStock = $product->stock;

        try {
            $this->stockService->addStock(
                product: $product,
                qty: $qty,
                source: 'manual_adjustment',
                referenceId: null,
                note: $note
            );

            // Log audit trail for stock addition
            AuditService::logStockAdjustment(
                auth()->id(),
                $product->id,
                $oldStock,
                $oldStock + $qty,
                'Manual stock addition: ' . $note
            );
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Menambahkan Stok',
                'list' => [
                    'Gagal menambahkan stok.',
                    'Pastikan data tidak diubah oleh administrator lain di waktu yang sama, lalu coba kembali.'
                ]
            ])->withInput();
        }

        return back()->with('success', [
            'title' => "Stok Berhasil Ditambahkan",
            'list' => [
                "Produk: <strong>{$product->name}</strong>",
                "Jumlah: <strong>+{$qty} unit</strong>",
                "Catatan: {$note}"
            ]
        ]);
    }

    /**
     * Deduct stock from a product (type: out).
     */
    public function deduct(StockRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $qty = (int) $data['qty'];
        $note = $data['note'];
        $oldStock = $product->stock;

        try {
            $this->stockService->deductStock(
                product: $product,
                qty: $qty,
                source: 'manual_adjustment',
                referenceId: null,
                note: $note
            );

            // Log audit trail for stock deduction
            AuditService::logStockAdjustment(
                auth()->id(),
                $product->id,
                $oldStock,
                $oldStock - $qty,
                'Manual stock deduction: ' . $note
            );
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['qty' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal Mengurangi Stok',
                'list' => [
                    'Gagal mengurangi stok.',
                    'Pastikan stok cukup dan tidak sedang terkunci oleh transaksi lain.'
                ]
            ])->withInput();
        }

        return back()->with('success', [
            'title' => "Stok Berhasil Dikurangi",
            'list' => [
                "Produk: <strong>{$product->name}</strong>",
                "Jumlah: <strong>-{$qty} unit</strong>",
                "Catatan: {$note}"
            ]
        ]);
    }
}
