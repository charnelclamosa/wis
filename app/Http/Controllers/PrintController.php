<?php

namespace App\Http\Controllers;

use PDF;
use Milon\Barcode\DNS1D;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function index(Request $request) {
        $bundles = $request->Items;
        $location = $request->Location;
        $line = $request->Line;
        $pdf = PDF::loadView('Bundles', compact('bundles', 'location', 'line'));
        $pdf->setPaper('a3', 'portrait');
        return $pdf->download();
    }
}
