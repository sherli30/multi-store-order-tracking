<?php

namespace App\Http\Controllers;

use App\Models\AdminAction;
use App\Models\TrackingHistory;
use Illuminate\Http\Request;

/**
 * AuditLogController — Display audit trails and admin actions
 * Provides endpoints for admins to view what changes were made to entities
 */
class AuditLogController extends Controller
{
    /**
     * Display admin actions (all non-order entity changes)
     */
    public function adminActions(Request $request)
    {
        $query = AdminAction::with('admin')->latest();

        // Filter by entity type
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $actions = $query->paginate(50);

        // Get unique entity types and action types for filters
        $entityTypes = AdminAction::distinct()->pluck('entity_type')->sort();
        $actionTypes = AdminAction::distinct()->pluck('action_type')->sort();
        $admins = \App\Models\User::where('role', 'administrator')->orderBy('name')->get(['id', 'name']);

        return view('audit.admin_actions', compact('actions', 'entityTypes', 'actionTypes', 'admins'));
    }

    /**
     * Display order tracking history with audit details
     */
    public function orderTracking(Request $request, $orderId)
    {
        $order = \App\Models\Order::with(['customer', 'store'])->findOrFail($orderId);
        $trackingHistory = TrackingHistory::where('order_id', $orderId)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('audit.order_tracking', compact('order', 'trackingHistory'));
    }

    /**
     * Display entity history (e.g., all changes to a product)
     */
    public function entityHistory(Request $request, string $entityType, int $entityId)
    {
        $actions = AdminAction::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Fetch entity name based on type
        $entityName = $this->getEntityName($entityType, $entityId);

        return view('audit.entity_history', compact('entityType', 'entityId', 'entityName', 'actions'));
    }

    /**
     * Export audit log to CSV
     */
    public function exportAdminActions(Request $request)
    {
        $query = AdminAction::with('admin')->latest();

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $actions = $query->get();

        $csv = "Waktu,Admin,Entity Type,Entity ID,Action,Alasan,IP Address\n";
        foreach ($actions as $action) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",%d,\"%s\",\"%s\",\"%s\"\n",
                $action->created_at,
                $action->admin?->name ?? 'System',
                $action->entity_type,
                $action->entity_id,
                $action->action_type,
                $action->reason ?? '',
                $action->ip_address ?? ''
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="admin-audit-log-' . now()->format('Y-m-d-His') . '.csv"',
        ]);
    }

    /**
     * Get entity name for display
     */
    private function getEntityName(string $entityType, int $entityId): ?string
    {
        return match ($entityType) {
            'Product' => \App\Models\Product::find($entityId)?->name,
            'Store' => \App\Models\Store::find($entityId)?->name,
            'Courier' => \App\Models\Courier::find($entityId)?->name,
            'Category' => \App\Models\ProductCategory::find($entityId)?->name,
            'Order' => \App\Models\Order::find($entityId)?->midtrans_order_id,
            default => null,
        };
    }
}
