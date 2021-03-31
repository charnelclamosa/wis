<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;
    protected $connection = 'wkn-appserver';
    protected $table = 'Divisions';
    protected $casts = [
        'DivisionId' => 'integer',
        'Disuse' => 'integer'
    ];
}
