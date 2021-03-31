<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    use HasFactory;
    protected $connection = 'wkn-appserver';
    protected $table = 'Bundles';
    protected $casts = [
        'Qty' => 'integer'
    ];
}
