<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingStatus extends Model
{
    use HasFactory;

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
        'vdatu',
        'zterm',
        'fkstk',
        'kunr', // Diperbarui
    ];
}
