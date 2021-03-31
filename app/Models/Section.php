<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $connection = 'wkn-appserver';
    protected $table = 'ItemShippingSections';
    protected $casts = [
        'ItemShippingSectionId' => 'integer',
        'Disuse' => 'integer'
    ];
}
