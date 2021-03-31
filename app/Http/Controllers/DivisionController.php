<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index() {
        return Division::where('Disuse', 0)->get();
    }
}
