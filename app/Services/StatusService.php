<?php

namespace App\Services;

/**
 * StatusService — Centralized status label and mapping service
 * Ensures consistent status display across all views, notifications, and reports
 * All user-facing labels are in Indonesian
 */
class StatusService
{
    // ─── Order Status Constants ─────────────────────────────────────────
    const ORDER_PENDING = 'pending';
    const ORDER_WAITING_CONFIRMATION = 'menunggu_konfirmasi_admin';
    const ORDER_PROCESSING_NEEDED = 'perlu_diproses';
    const ORDER_PROCESSING = 'processing';
    const ORDER_READY_TO_SHIP = 'ready_to_ship';
    const ORDER_SHIPPING = 'shipping';
    const ORDER_DELIVERED = 'delivered';
    const ORDER_COMPLETED = 'completed';
    const ORDER_CANCELLED = 'cancelled';
    const ORDER_REFUNDED = 'refunded';

    // ─── Transaction Status Constants ───────────────────────────────────
    const TRANSACTION_PENDING = 'pending';
    const TRANSACTION_PAID = 'paid';
    const TRANSACTION_FAILED = 'failed';
    const TRANSACTION_REFUND = 'refund';

    // ─── Order Status Labels (All Indonesian) ──────────────────────────
    const ORDER_LABELS = [
        self::ORDER_PENDING => 'Belum Bayar',
        self::ORDER_WAITING_CONFIRMATION => 'Menunggu Konfirmasi',
        self::ORDER_PROCESSING_NEEDED => 'Perlu Diproses',
        self::ORDER_PROCESSING => 'Dikemas',
        self::ORDER_READY_TO_SHIP => 'Siap Dikirim',
        self::ORDER_SHIPPING => 'Dikirim',
        self::ORDER_DELIVERED => 'Pesanan Tiba',
        self::ORDER_COMPLETED => 'Selesai',
        self::ORDER_CANCELLED => 'Dibatalkan',
        self::ORDER_REFUNDED => 'Pengembalian',
    ];

    // ─── Transaction Status Labels (All Indonesian) ─────────────────────
    const TRANSACTION_LABELS = [
        self::TRANSACTION_PENDING => 'Menunggu',
        self::TRANSACTION_PAID => 'Lunas',
        self::TRANSACTION_FAILED => 'Gagal',
        self::TRANSACTION_REFUND => 'Dana Dikembalikan',
    ];

    // ─── Midtrans Status to Transaction Status Mapping ──────────────────
    const MIDTRANS_TO_TRANSACTION = [
        'settlement' => self::TRANSACTION_PAID,
        'capture' => self::TRANSACTION_PAID,
        'pending' => self::TRANSACTION_PENDING,
        'deny' => self::TRANSACTION_FAILED,
        'cancel' => self::TRANSACTION_FAILED,
        'expire' => self::TRANSACTION_FAILED,
        'failure' => self::TRANSACTION_FAILED,
        'refund' => self::TRANSACTION_REFUND,
    ];

    // ─── Badge CSS Classes (for styling) ────────────────────────────────
    const ORDER_BADGE_CLASSES = [
        self::ORDER_PENDING => 'badge-pending',
        self::ORDER_WAITING_CONFIRMATION => 'badge-info',
        self::ORDER_PROCESSING_NEEDED => 'badge-perlu_diproses',
        self::ORDER_PROCESSING => 'badge-processing',
        self::ORDER_READY_TO_SHIP => 'badge-primary',
        self::ORDER_SHIPPING => 'badge-shipping',
        self::ORDER_DELIVERED => 'badge-info',
        self::ORDER_COMPLETED => 'badge-completed',
        self::ORDER_CANCELLED => 'badge-cancelled',
        self::ORDER_REFUNDED => 'badge-refunded',
    ];

    const TRANSACTION_BADGE_CLASSES = [
        self::TRANSACTION_PENDING => 'badge-pending',
        self::TRANSACTION_PAID => 'badge-paid',
        self::TRANSACTION_FAILED => 'badge-failed',
        self::TRANSACTION_REFUND => 'badge-refund',
    ];

    /**
     * Get Indonesian label for order status
     */
    public static function getOrderLabel(string $status): string
    {
        return self::ORDER_LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Get Indonesian label for transaction status
     */
    public static function getTransactionLabel(string $status): string
    {
        return self::TRANSACTION_LABELS[$status] ?? ucfirst($status);
    }

    /**
     * Get badge CSS class for order status
     */
    public static function getOrderBadgeClass(string $status): string
    {
        return self::ORDER_BADGE_CLASSES[$status] ?? 'badge-secondary';
    }

    /**
     * Get badge CSS class for transaction status
     */
    public static function getTransactionBadgeClass(string $status): string
    {
        return self::TRANSACTION_BADGE_CLASSES[$status] ?? 'badge-secondary';
    }

    /**
     * Convert Midtrans webhook status to normalized transaction status
     */
    public static function midtransToTransactionStatus(string $midtransStatus): string
    {
        return self::MIDTRANS_TO_TRANSACTION[$midtransStatus] ?? self::TRANSACTION_PENDING;
    }

    /**
     * Get all order statuses
     */
    public static function getOrderStatuses(): array
    {
        return [
            self::ORDER_PENDING,
            self::ORDER_WAITING_CONFIRMATION,
            self::ORDER_PROCESSING_NEEDED,
            self::ORDER_PROCESSING,
            self::ORDER_READY_TO_SHIP,
            self::ORDER_SHIPPING,
            self::ORDER_DELIVERED,
            self::ORDER_COMPLETED,
            self::ORDER_CANCELLED,
            self::ORDER_REFUNDED,
        ];
    }

    /**
     * Get all transaction statuses
     */
    public static function getTransactionStatuses(): array
    {
        return [
            self::TRANSACTION_PENDING,
            self::TRANSACTION_PAID,
            self::TRANSACTION_FAILED,
            self::TRANSACTION_REFUND,
        ];
    }

    /**
     * Get order statuses with their labels (for dropdowns, tabs)
     */
    public static function getOrderStatusesWithLabels(): array
    {
        return array_map(
            fn($status) => ['value' => $status, 'label' => self::getOrderLabel($status)],
            self::getOrderStatuses()
        );
    }

    /**
     * Get transaction statuses with their labels (for dropdowns, tabs)
     */
    public static function getTransactionStatusesWithLabels(): array
    {
        return array_map(
            fn($status) => ['value' => $status, 'label' => self::getTransactionLabel($status)],
            self::getTransactionStatuses()
        );
    }
}
