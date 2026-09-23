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
     * Generate the next formatted invoice number for a given prefix.
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

        // Check Sales table
        $lastSale = Sale::where('invoice_no', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastSale && $lastSale->invoice_no) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastSale->invoice_no, $matches)) {
                $numFromRecords = max($numFromRecords, (int) $matches[1]);
            }
        }

        // Check Purchases table
        $lastPurchase = Purchase::where('invoice_no', 'LIKE', $pref . '-%')
            ->orderBy('id', 'desc')
            ->first();
        if ($lastPurchase && $lastPurchase->invoice_no) {
            if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastPurchase->invoice_no, $matches)) {
                $numFromRecords = max($numFromRecords, (int) $matches[1]);
            }
        }

        // Check Goods Receiving Notes table
        if ($pref === 'GRN' || str_starts_with($pref, 'GRN')) {
            $lastGrn = GoodsReceivingNote::where('grn_number', 'LIKE', $pref . '-%')
                ->orderBy('id', 'desc')
                ->first();
            if ($lastGrn && $lastGrn->grn_number) {
                if (preg_match('/' . preg_quote($pref, '/') . '-(\d+)/i', $lastGrn->grn_number, $matches)) {
                    $numFromRecords = max($numFromRecords, (int) $matches[1]);
                }
            }
        }

        $nextNum = max((int) $nextNumSeries, $numFromRecords + 1);

        return $pref . '-' . str_pad($nextNum, $padding, '0', STR_PAD_LEFT);
    }

    /**
     * Increment series counter after sale/purchase creation if applicable
     */
    public static function incrementCounterForInvoice($invoiceNo)
    {
        if (empty($invoiceNo)) return;

        if (preg_match('/^([A-Z0-9]+)-(\d+)$/i', trim($invoiceNo), $matches)) {
            $prefix = strtoupper($matches[1]);
            $number = (int) $matches[2];

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
                    'padding' => strlen($matches[2]),
                    'is_default' => 0
                ]);
            }
        }
    }
}
