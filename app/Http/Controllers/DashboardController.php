<?php

namespace App\Http\Controllers;

use App\Models\BundlesLog;
use App\Models\LumberDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function bundles() {
        return LumberDetail::all()->count();
    }
    public function scanned() {
        return LumberDetail::where('ScannedFlag', 'S')->count();
    }
    public function encoded() {
        return LumberDetail::where('ScannedFlag', 'E')->count();
    }
    public function out() {
        return DB::connection('wkn-appserver')->select(DB::raw("SELECT count(Main.BundleNo) AS 'Count'
        FROM BundlesView AS Main
        INNER JOIN BundlesReceivedView AS Received ON Received.BundleNo = Main.BundleNo
        LEFT JOIN BundlesShippedView AS Shipped ON Shipped.BundleNo = Main.BundleNo
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberDetails 
        AS Details ON Details.BundleCode = Main.BundleNo
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberHeaders 
        AS Headers ON Headers.ScannedLumberID = Details.ScannedLumberID
        INNER JOIN [wknsql1].WareHouseManagement.dbo.ScannedLumberLineMasterlists 
        AS Line ON ((Line.LocationCode = Headers.LocationCode) AND (Line.LineCode = Headers.LineCode))
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberLocationMasterlists 
        AS Location ON Location.LocationCode = Headers.LocationCode
        WHERE Shipped.ShippedDate IS NOT NULL"));
    }
    public function outBundles() {
        return DB::connection('wkn-appserver')->select(DB::raw("SELECT Main.BundleNo, Location.LocationName, Line.LineName, Received.ItemId, Main.InvoiceNo, Headers.CreatedDate
        FROM BundlesView AS Main
        INNER JOIN BundlesReceivedView AS Received ON Received.BundleNo = Main.BundleNo
        LEFT JOIN BundlesShippedView AS Shipped ON Shipped.BundleNo = Main.BundleNo
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberDetails 
        AS Details ON Details.BundleCode = Main.BundleNo
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberHeaders 
        AS Headers ON Headers.ScannedLumberID = Details.ScannedLumberID
        INNER JOIN [wknsql1].WareHouseManagement.dbo.ScannedLumberLineMasterlists 
        AS Line ON ((Line.LocationCode = Headers.LocationCode) AND (Line.LineCode = Headers.LineCode))
        INNER JOIN [wknsql1].WarehouseManagement.dbo.ScannedLumberLocationMasterlists 
        AS Location ON Location.LocationCode = Headers.LocationCode
        WHERE Shipped.ShippedDate IS NOT NULL"));
    }
    public function activityLogs() {
        return BundlesLog::orderBy('id', 'desc')->take(5)->get();
    }
}
