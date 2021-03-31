<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LumberLocation extends Model
{
    use HasFactory;
    protected $connection = 'wknsql1';
    protected $table = 'ScannedLumberLocationMasterlists';
}
