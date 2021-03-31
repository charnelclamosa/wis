<?php

namespace App\Http\Controllers;

use App\Models\BundlesLog;
use Illuminate\Http\Request;

class BundleLogController extends Controller
{
    public function index($id) {
        return BundlesLog::where('BundleNo', $id)->select(
            'BundleNo',
            'ActionTaken',
            'Remarks',
            'created_at',
            'updated_by'
        )->get();
    }

    public function store(Request $request) {
        $logs = $request->Bundles;
        foreach($logs as $item) {
            $log = new BundlesLog;
            $log->BundleNo = $item['BundleNo'];
            $log->ActionTaken = $request->ActionTaken;
            $log->Remarks = $request->Remarks;
            $log->updated_by = $request->Username;
            $log->save();
        }
        return response()->json(['message' => 'Data has been created.', 201]);
    }
}
