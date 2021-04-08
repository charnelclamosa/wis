<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWKN extends Model
{
    use HasFactory;
    protected $connection = 'companyinformation_wkn';
    protected $table = 'Employees';
}
