<?php

namespace App\Services;

use App\Models\AdminAction;
use Illuminate\Support\Facades\Log;

/**
 * AuditService — Centralized audit logging for all admin actions
 * Tracks changes to products, stores, couriers, categories, and system config
 */
class AuditService
{
    /**
     * Log admin action with before/after values
     */
    public static function logAction(
        ?int $adminId,
        string $entityType,
        ?int $entityId,
        string $actionType,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $reason = null
    ): ?AdminAction {
        try {
            $action = AdminAction::log(
                $adminId,
                $entityType,
                $entityId,
                $actionType,
                self::sanitizeValues($oldValues),
                self::sanitizeValues($newValues),
                $reason
            );

            Log::info("[Audit] Admin action", [
                'admin_id' => $adminId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'action_type' => $actionType,
                'reason' => $reason,
            ]);

            return $action;
        } catch (\Exception $e) {
            Log::error("[AuditService] Failed to log action: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Log product change
     */
    public static function logProductChange(
        ?int $adminId,
        int $productId,
        string $action,
        ?array $old = null,
        ?array $new = null,
        ?string $reason = null
    ): ?AdminAction {
        return self::logAction($adminId, 'Product', $productId, $action, $old, $new, $reason);
    }

    /**
     * Log store change
     */
    public static function logStoreChange(
        ?int $adminId,
        int $storeId,
        string $action,
        ?array $old = null,
        ?array $new = null,
        ?string $reason = null
    ): ?AdminAction {
        return self::logAction($adminId, 'Store', $storeId, $action, $old, $new, $reason);
    }

    /**
     * Log courier change
     */
    public static function logCourierChange(
        ?int $adminId,
        int $courierId,
        string $action,
        ?array $old = null,
        ?array $new = null,
        ?string $reason = null
    ): ?AdminAction {
        return self::logAction($adminId, 'Courier', $courierId, $action, $old, $new, $reason);
    }

    /**
     * Log category change
     */
    public static function logCategoryChange(
        ?int $adminId,
        int $categoryId,
        string $action,
        ?array $old = null,
        ?array $new = null,
        ?string $reason = null
    ): ?AdminAction {
        return self::logAction($adminId, 'Category', $categoryId, $action, $old, $new, $reason);
    }

    /**
     * Log stock adjustment
     */
    public static function logStockAdjustment(
        ?int $adminId,
        int $productId,
        int $oldStock,
        int $newStock,
        string $reason
    ): ?AdminAction {
        return self::logAction(
            $adminId,
            'Product',
            $productId,
            'stock_adjustment',
            ['stock' => $oldStock],
            ['stock' => $newStock],
            $reason
        );
    }

    /**
     * Log shipping configuration change
     */
    public static function logShippingChange(
        ?int $adminId,
        string $entityType, // 'ShippingService' or 'ShippingRate'
        int $entityId,
        string $action,
        ?array $old = null,
        ?array $new = null
    ): ?AdminAction {
        return self::logAction($adminId, $entityType, $entityId, $action, $old, $new);
    }

    /**
     * Log order refund action (admin-triggered)
     */
    public static function logOrderRefund(
        int $adminId,
        int $orderId,
        string $refundMethod,
        ?string $reason = null
    ): ?AdminAction {
        return self::logAction(
            $adminId,
            'Order',
            $orderId,
            'refund_triggered',
            null,
            ['refund_method' => $refundMethod],
            $reason
        );
    }

    /**
     * Remove sensitive data from audit values
     */
    private static function sanitizeValues(?array $values): ?array
    {
        if (!$values) return null;

        $sensitive = ['password', 'secret', 'token', 'card', 'cvv', 'pin'];
        $sanitized = [];

        foreach ($values as $key => $value) {
            if (in_array(strtolower($key), $sensitive)) {
                $sanitized[$key] = '***REDACTED***';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Get audit history for an entity
     */
    public static function getHistory(string $entityType, int $entityId, int $limit = 50)
    {
        return AdminAction::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->with('admin')
            ->get();
    }
}
