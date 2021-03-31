<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LumberLine extends Model
{
    use HasFactory;
    protected $connection = 'wknsql1';
    protected $table = 'ScannedLumberLineMasterlists';
}
