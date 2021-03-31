<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleShippingHeader extends Model
{
    use HasFactory;
    protected $connection = 'wkn-appserver';
    protected $table = 'BundleItemShippingHeaders';
    protected $casts = [
        'BundleItemShippingId' => 'integer',
        'DivisionId' => 'integer',
        'ItemShippingPurposeId' => 'integer',
        'ItemShippingSectionId' => 'integer'
    ];
    protected $primaryKey = 'BundleItemShippingId';
    public $timestamps = false;
}
