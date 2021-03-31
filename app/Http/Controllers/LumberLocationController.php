<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LumberLocation;

class LumberLocationController extends Controller
{
    public function index() {
        return LumberLocation::select(
            'LocationCode',
            'LocationName'
        )->get();
    }
}
