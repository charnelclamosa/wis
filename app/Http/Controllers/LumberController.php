<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bundle;
use App\Models\LumberDetail;
use App\Models\LumberHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LumberController extends Controller
{
    public function index() {
        $lumbers = DB::connection('wknsql1')->select(DB::raw("SELECT DISTINCT Header.ScannedLumberID, Detail.BundleCode, Header.Encoder, Detail.CheckFlag, Detail.ScannedFlag, Line.LineName, Location.LocationName, Bundle.InvoiceNo  FROM ScannedLumberHeaders AS Header
        INNER JOIN ScannedLumberDetails AS Detail ON Detail.ScannedLumberID = Header.ScannedLumberID
        INNER JOIN ScannedLumberLineMasterlists AS Line ON ((Line.LineCode = Header.LineCode) AND (Line.LocationCode = Header.LocationCode))
        INNER JOIN ScannedLumberLocationMasterlists AS Location ON Location.LocationCode = Header.LocationCode
        INNER JOIN [wkn-appserver].WarehouseManagement.dbo.Bundles AS Bundle 
        ON Bundle.BundleNo = Detail.BundleCode
        WHERE Detail.CheckFlag = 0"));
        return response($lumbers);
    }
    public function updateFlag(Request $request) {
        LumberDetail::where('ScannedLumberID', $request->ScannedLumberID)->update([
            'CheckFlag' => $request->CheckFlag,
            'UpdatedDate' => Carbon::now(),
            'UpdatedByCode' => $request->UpdatedBy
        ]);
        return response()->json(['message' => 'Update successful'], 200);
    }
}
