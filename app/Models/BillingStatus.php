<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BillingStatus extends Model
{
    use HasFactory;
    protected $connection = 'mak_sd'; // Specify the database connection if needed
    protected $table = 'billing_statuses'; // Specify the table name if it differs from the

    protected $fillable = [
        'weeks',
        'vtweg',
        'bldat',
        'tglgi',
        'lfart',
        'mblnr', // Diperbarui
        'vbeln',
        'vgbel', // Diperbarui
        'name1',
        'name2',
        'notes',
        'fkdat',
        'vbelm',
        'stb',
        'stbk',
        'hna',
        'tdisc',
        'nett',
        'lfstk',
        'zterm',
        'fkstk',
        'kunr', // Diperbarui
        'vdatu', // Tanggal dokumen
        'erdav', // Tanggal OC
        'erdai', // Tanggal Billing
        'augbl', // Nomor dokumen akuntansi (AUGBL)
        'ihrez', // Referensi eksternal untuk dokumen pembayaran
    ];

    public function matrixTopEkspor()
    {
        return $this->hasOne(MatrikTopEkspor::class, 'zterm', 'zterm')->select([
            'id_customer',
            'nama_customer',
            'zterm',
            'top',
            'produksi',
            'ekspor'
        ]);
    }

    /**
     * Get payment due date by adding TOP days to vdatu
     * @return string|null
     */
    public function getPaymentDueDateAttribute()
    {

        if (!$this->vdatu || !$this->matrixTopEkspor || $this->matrixTopEkspor->top < 0) {
            return null;
        }

        try {
            $vdatu = \Carbon\Carbon::parse($this->vdatu);
            $topDays = (int) $this->matrixTopEkspor->top;
            if ($topDays == 0) {
                return $vdatu->format('Y-m-d');
            }
            return $vdatu->addDays($topDays)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get payment due date as Carbon instance
     * @return \Carbon\Carbon|null
     */
    public function getPaymentDueDateCarbonAttribute()
    {
        if (!$this->vdatu || !$this->matrixTopEkspor || $this->matrixTopEkspor->top < 0) {
            return null;
        }

        try {
            $vdatu = \Carbon\Carbon::parse($this->vdatu);
            $topDays = (int) $this->matrixTopEkspor->top;
            return $vdatu->addDays($topDays);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Scope to get billing with payment due date calculation
     */
    public function scopeWithPaymentDueDate($query)
    {
        return $query->with('matrixTopEkspor');
    }
}
