<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHRD extends Model
{
    use HasFactory;
    protected $connection = 'companyinformation_hrd';
    protected $table = 'Employees';
}
