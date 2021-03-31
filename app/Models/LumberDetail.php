<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LumberDetail extends Model
{
    use HasFactory;
    protected $connection = 'wknsql1';
    protected $table = 'ScannedLumberDetails';
    protected $casts = [
        'ScannedLumberID' => 'integer',
        'BundleCode' => 'string',
        'CheckFlag' => 'integer',
    ];
    public $timestamps = false;
}
