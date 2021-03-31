<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LumberLine;

class LumberLineController extends Controller
{
    public function index() {
        return LumberLine::select(
            'LocationCode',
            'LineCode',
            'LineName',
        )->get();
    }
}
