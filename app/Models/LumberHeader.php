<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LumberHeader extends Model
{
    use HasFactory;
    protected $connection = 'wknsql1';
    protected $table = 'ScannedLumberHeaders';
    protected $casts = [
        'ScannedLumberID' => 'integer'
    ];
    public $timestamps = false;
}
