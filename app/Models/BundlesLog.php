<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BundlesLog extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'BundleNo',
        'ActionTaken',
        'Remarks',
        'updated_by'
    ];
}
