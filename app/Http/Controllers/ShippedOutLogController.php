<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippedOutLog;

class ShippedOutLogController extends Controller
{
    public function store(Request $request) {
        $logs = new ShippedOutLog;
        $logs->BundleNo = $request->BundleNo;
        $logs->ActionTaken = 'Out';
        $logs->Remarks = $request->Remarks;
        $logs->updated_by = $request->Username;
        $logs->save();
        return response()->json(['message' => 'Data has been created.', 201]);
    }
}
