<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index() {
        return Section::where('Disuse', 0)->get();
    }
}
