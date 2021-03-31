<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleShippingDetail extends Model
{
    use HasFactory;
    protected $connection = 'wkn-appserver';
    protected $table = 'BundleItemShippingDetails';
    protected $casts = [
        'BundleItemShippingId' => 'integer'
    ];
    protected $primaryKey = 'BundleItemShippingId';
    public $timestamps = false;
}
