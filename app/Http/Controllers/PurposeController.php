<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use Illuminate\Http\Request;

class PurposeController extends Controller
{
    public function index() {
        return Purpose::where('DisUse', 0)->get();
    }
}
