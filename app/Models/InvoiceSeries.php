<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSeries extends Model
{
    use HasFactory;

    protected $table = 'invoice_series';

    protected $guarded = [];

    /**
     * Standard registered document series definitions
     */
    public static function standardDefinitions(): array
    {
        return [
            'INV'  => ['name' => 'Sale Invoice', 'padding' => 4, 'is_default' => 1],
            'SO'   => ['name' => 'Sales Order', 'padding' => 4, 'is_default' => 0],
            'QUO'  => ['name' => 'Sale Quotation', 'padding' => 4, 'is_default' => 0],
            'PO'   => ['name' => 'Purchase Order', 'padding' => 4, 'is_default' => 0],
            'PINV' => ['name' => 'Purchase Invoice / Bill', 'padding' => 4, 'is_default' => 0],
            'DC'   => ['name' => 'Delivery Challan (Sale)', 'padding' => 4, 'is_default' => 0],
            'DDC'  => ['name' => 'Direct Delivery Challan', 'padding' => 4, 'is_default' => 0],
            'DGRN' => ['name' => 'Direct Goods Receiving Note', 'padding' => 4, 'is_default' => 0],
            'GRN'  => ['name' => 'PO Goods Receiving Note', 'padding' => 4, 'is_default' => 0],
            'SR'   => ['name' => 'Sale Return', 'padding' => 4, 'is_default' => 0],
            'PRET' => ['name' => 'Purchase Return', 'padding' => 4, 'is_default' => 0],
            'TAX'  => ['name' => 'Tax Invoice', 'padding' => 3, 'is_default' => 0],
            'CO'   => ['name' => 'Company Invoice', 'padding' => 3, 'is_default' => 0],
        ];
    }

    /**
     * Ensure all standard series exist in the database.
     */
    public static function ensureStandardSeries(): void
    {
        $defs = self::standardDefinitions();
        foreach ($defs as $pref => $def) {
            self::firstOrCreate(
                ['prefix' => $pref],
                [
                    'padding' => $def['padding'],
                    'next_number' => 1,
                    'is_default' => $def['is_default']
                ]
            );
        }
    }

    /**
     * Generate the next formatted document number for a given prefix.
     */
    public static function generateNextNo($prefix = null)
    {
        $series = null;

        if ($prefix) {
            $series = self::where('prefix', strtoupper(trim($prefix)))->first();
        }

        $pref = $series ? strtoupper($series->prefix) : ($prefix ? strtoupper(trim($prefix)) : null);

        if (!$pref) {
            $defaultSeries = self::where('is_default', 1)->first() ?: self::first();
            $pref = $defaultSeries ? strtoupper($defaultSeries->prefix) : 'INV';
            $padding = $defaultSeries->padding ?? 4;
            $nextNumSeries = $defaultSeries->next_number ?? 1;
        } else {
            $defaultPad = in_array($pref, ['TAX', 'CO', 'PTAX', 'PCO']) ? 3 : 4;
            $padding = $series ? ($series->padding ?? $defaultPad) : $defaultPad;
            $nextNumSeries = $series->next_number ?? 1;
        }

        $numFromRecords = 0;

        // 1. Check Sales table (INV, SO, QUO, TAX, CO, etc.)
        $lastSale = Sale::where('invoice_no', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastSale && $lastSale->invoice_no) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastSale->invoice_no, $matches)) {
                $numFromRecords = max($numFromRecords, (int) $matches[1]);
            }
        }

        // 2. Check Purchases table (PO, PINV, PUR, etc.)
        $lastPurchase = Purchase::where('invoice_no', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastPurchase && $lastPurchase->invoice_no) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastPurchase->invoice_no, $matches)) {
                $numFromRecords = max($numFromRecords, (int) $matches[1]);
            }
        }

        // 3. Check Goods Receiving Notes table (GRN, DGRN, etc.)
        $lastGrn = GoodsReceivingNote::where('grn_number', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastGrn && $lastGrn->grn_number) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastGrn->grn_number, $matches)) {
                $numFromRecords = max($numFromRecords, (int) $matches[1]);
            }
        }

        // 4. Check Delivery Challans table (DC, DDC, etc.)
        if (class_exists(\App\Models\DeliveryChallan::class)) {
            $lastDc = \App\Models\DeliveryChallan::where('dc_number', 'LIKE', $pref . '-%')
                ->orderBy('id', 'desc')
                ->first();
            if ($lastDc && $lastDc->dc_number) {
                if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastDc->dc_number, $matches)) {
                    $numFromRecords = max($numFromRecords, (int) $matches[1]);
                }
            }
        }

        // 5. Check Sale Returns table (SR, SRET, etc.)
        if (class_exists(\App\Models\SaleReturn::class)) {
            try {
                $lastSr = \App\Models\SaleReturn::where('return_invoice', 'LIKE', $pref . '-%')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($lastSr && $lastSr->return_invoice) {
                    if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastSr->return_invoice, $matches)) {
                        $numFromRecords = max($numFromRecords, (int) $matches[1]);
                    }
                }
            } catch (\Throwable $e) {}
        }

        // 6. Check Purchase Returns table (PRET, etc.)
        if (class_exists(\App\Models\PurchaseReturn::class)) {
            try {
                $lastPr = \App\Models\PurchaseReturn::where('return_invoice', 'LIKE', $pref . '-%')
                    ->orderBy('id', 'desc')
                    ->first();
                if ($lastPr && $lastPr->return_invoice) {
                    if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastPr->return_invoice, $matches)) {
                        $numFromRecords = max($numFromRecords, (int) $matches[1]);
                    }
                }
            } catch (\Throwable $e) {}
        }

        $nextNum = max((int) $nextNumSeries, $numFromRecords + 1);

        return $pref . '-' . str_pad($nextNum, $padding, '0', STR_PAD_LEFT);
    }

    /**
     * Increment and update series counter after document creation
     */
    public static function incrementCounterForInvoice($invoiceNo)
    {
        if (empty($invoiceNo)) return;

        $invoiceNo = trim($invoiceNo);

        if (preg_match('/^([A-Z0-9]+)-(\d+)$/i', $invoiceNo, $matches)) {
            $prefix = strtoupper($matches[1]);
            $number = (int) $matches[2];
            $padLen = strlen($matches[2]);

            $series = self::where('prefix', $prefix)->first();
            if ($series) {
                if ($number >= $series->next_number) {
                    $series->next_number = $number + 1;
                    $series->save();
                }
            } else {
                self::create([
                    'prefix' => $prefix,
                    'next_number' => $number + 1,
                    'padding' => max(3, $padLen),
                    'is_default' => 0
                ]);
            }
        }
    }

    /**
     * Normalize user input (e.g. '200' -> 'SO-0200', 'so-200' -> 'SO-200')
     */
    public static function normalizeNumber(?string $input, string $defaultPrefix = 'INV'): string
    {
        if (empty($input)) {
            return self::generateNextNo($defaultPrefix);
        }

        $trimmed = trim($input);

        // If user typed only a number (e.g. '200' or '0200')
        if (is_numeric($trimmed)) {
            $num = (int) $trimmed;
            $series = self::where('prefix', strtoupper($defaultPrefix))->first();
            $pad = $series->padding ?? 4;
            return strtoupper($defaultPrefix) . '-' . str_pad($num, $pad, '0', STR_PAD_LEFT);
        }

        // If user typed 'PREFIX-NUMBER' or 'PREFIX NUMBER' or 'PREFIX#NUMBER'
        if (preg_match('/^([A-Za-z0-9]+)[\s\-#_]+(\d+)$/i', $trimmed, $matches)) {
            $pref = strtoupper($matches[1]);
            $num = (int) $matches[2];
            $series = self::where('prefix', $pref)->first();
            $pad = $series->padding ?? strlen($matches[2]);
            return $pref . '-' . str_pad($num, max(strlen($matches[2]), $pad), '0', STR_PAD_LEFT);
        }

        return strtoupper($trimmed);
    }
}
