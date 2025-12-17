<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatrikTopEkspor extends Model
{
    use HasFactory;
    protected $connection = 'mak_sd'; // Specify the database connection if needed
    protected $table = 'matrix_top_ekspor'; // Specify the table name if it
    protected $fillable = [
        'id_customer',
        'nama_customer',
        'zterm',
        'top',
        'produksi',
        'ekspor',
    ];
}
