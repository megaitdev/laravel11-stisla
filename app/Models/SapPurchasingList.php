<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapPurchasingList extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mak_sd';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sap_purchasing_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ifnr',   // Vendor ID
        'bsart',  // Document Type
        'name1',  // Vendor Name
        'bedat',  // PO Date
        'eindt',  // Delivery Date
        'tim',    // Time/Counter
        'tima',   // Time/Counter Alt
        'verkf',  // Salesperson
        'frgke',  // Release Indicator
        'ktlog',  // Catalog Type
        'ebeln',  // PO Number (Primary Index)
        'ebelp',  // PO Item Number
        'banfn',  // Purchase Requisition
        'bednr',  // Tracking Number
        'matnr',  // Material Number
        'txz01',  // Short Text/Description
        'menge',  // Quantity
        'menga',  // Quantity Alt
        'netwr',  // Net Value
        'mblnr',  // Material Doc Number
        'budat',  // Posting Date
        'is_open', // Open Status
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bedat' => 'date',
        'eindt' => 'date',
        'budat' => 'date',
        'tim'   => 'integer',
        'tima'  => 'integer',
        'menge' => 'integer',
        'menga' => 'integer',
        'netwr' => 'decimal:2',
        'is_open' => 'boolean',
    ];

    /**
     * Scope a query to filter by vendor ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $vendorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVendor($query, string $vendorId)
    {
        return $query->where('ifnr', $vendorId);
    }

    /**
     * Scope a query to filter by PO Number.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $poNumber
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPoNumber($query, string $poNumber)
    {
        return $query->where('ebeln', $poNumber);
    }

    /**
     * Scope a query to filter by date range (PO Date).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenPoDate($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('bedat', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by delivery date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDeliveryDate($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('eindt', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by release indicator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $indicator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByReleaseIndicator($query, string $indicator)
    {
        return $query->where('frgke', $indicator);
    }

    /**
     * Get formatted net value with currency.
     *
     * @return string
     */
    public function getFormattedNetValueAttribute(): string
    {
        return number_format($this->netwr, 2, ',', '.');
    }

    /**
     * Get formatted quantity.
     *
     * @return string
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->menge, 0, ',', '.');
    }
}

