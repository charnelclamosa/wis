<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\LumberDetail;
use App\Models\LumberHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BundleShippingDetail;
use App\Models\BundleShippingHeader;

class BundleController extends Controller
{
    public function index(Request $request, $line, $location) {
        $bundles = DB::connection('wkn-appserver')->select(DB::raw("SELECT Shipped.BundleItemShippingId, Headers.ScannedLumberID, Headers.LineCode, Line.LineName, Headers.LocationCode, Location.LocationName, 
        Main.BundleNo,Main.SupplierId, Main.DeliveryId, Received.ItemId, Received.Invoiceno, Received.ReceivedDate, Main.Description, 
        CAST(Main.Qty AS INT) AS Qty, Main.QtyUnit, Main.SawmillId, 
        Details.CheckFlag, Details.ScannedFlag, Shipped.ShippedDate, Shipped.BundleItemShippingId, Headers.CreatedDate
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
        WHERE Headers.LineCode = '$line' AND Headers.LocationCode = '$location' AND Received.ItemId = '$request->ItemId' AND Received.Invoiceno = '$request->InvoiceNo' AND Headers.CreatedDate BETWEEN '$request->Date 00:00:00' AND '$request->Date 23:59:59'"));
        return response($bundles);
    }
    public function idGenerator() {
        $lastId = BundleShippingHeader::orderBy('BundleItemShippingId', 'desc')->first();
        $latestId = $lastId->BundleItemShippingId + 1;
        return response($latestId);
    }
    public function shippedOutHeader(Request $request) {
        $header = new BundleShippingHeader;
        $header->BundleItemShippingId = $request->BundleItemShippingId;
        $header->ShippedDate = $request->ShippedDate;
        $header->DivisionId = $request->DivisionId;
        $header->ItemShippingPurposeId = $request->PurposeId;
        $header->ItemShippingSectionId = $request->SectionId;
        $header->save();
        return response()->json(['message' => 'Data has been created.'], 201);
    }
    public function shippedOutDetail(Request $request) {
        $bundles = $request->Bundles;
        foreach($bundles as $key => $bundle) {
            $detail = new BundleShippingDetail;
            $detail->BundleItemShippingId = $request->BundleItemShippingId;
            $detail->BundleNo = $bundle["BundleNo"];
            $detail->save();
        }
        return response()->json(['message' => 'Data has been created.'], 201);
    }
    public function inHeader($id) {
        $bundles = BundleShippingHeader::where('BundleItemShippingId', $id)->get();
        foreach($bundles as $bundle) {
            $bundle->delete();
        }
        return response()->json(['message' => 'Data has been deleted.'], 200);
    }
    public function inDetails($id) {
        $bundles = BundleShippingDetail::where('BundleItemShippingId', $id)->get();
        foreach($bundles as $bundle) {
            $bundle->delete();
        }
        return response()->json(['message' => 'Data has been deleted.'], 200);
    }
    public function forPrinting($line, $location) {
        $bundles = DB::connection('wkn-appserver')->select(DB::raw("SELECT Headers.ScannedLumberID, Headers.LineCode, Line.LineName, Headers.LocationCode, Location.LocationName, 
        Main.BundleNo, Main.SupplierId, Main.DeliveryId, Received.ItemId, Main.Description, 
        CAST(Main.Qty AS INT) AS Qty, Main.QtyUnit, Main.SawmillId
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
        WHERE Headers.LineCode = '$line' AND Headers.LocationCode = '$location'"));
        return response($bundles);
    }
    public function isExistReceived($BundleNo) {
        $bundle = DB::connection('wkn-appserver')->select(DB::raw("SELECT Main.BundleNo,Main.SupplierId, Main.DeliveryId, Received.ItemId, 
        Received.Invoiceno, Received.ReceivedDate, Main.Description, 
        CAST(Main.Qty AS INT) AS Qty, Main.QtyUnit, Main.SawmillId
        FROM BundlesView AS Main
        INNER JOIN BundlesReceivedView AS Received ON Received.BundleNo = Main.BundleNo
        WHERE Received.BundleNo = '$BundleNo'"));
        return response($bundle);
    }
    public function isExistScanned($BundleNo) {
        $bundle = LumberDetail::where('BundleCode', $BundleNo)->get();
        return response($bundle);
    }
    public function idGeneratorLumber() {
        $lastRecord = LumberHeader::orderBy('ScannedLumberID', 'desc')->first();
        $latestId = $lastRecord->ScannedLumberID + 1;
        return response($latestId);
    }
    public function encodeHeader(Request $request, $id) {
        $header = new LumberHeader;
        $header->ScannedLumberID = $id;
        $header->LocationCode = $request->LocationCode;
        $header->LineCode = $request->LineCode;
        $header->Encoder = $request->Username;
        $header->CreatedDate = now();
        $header->UpdatedDate = now();
        $header->UpdatedByCode = $request->Username;
        $header->save();
        return response()->json(['message' => 'Data has been created'], 201);
    }
    public function encodeDetails(Request $request, $BundleNo) {
        $detail = new LumberDetail;
        $detail->ScannedLumberID = $request->ScannedLumberID;
        $detail->BundleCode = $BundleNo;
        $detail->CheckFlag = 0;
        $detail->ScannedFlag = 'E';
        $detail->CreatedDate = now();
        $detail->UpdatedDate = now();
        $detail->UpdatedByCode = $request->Username;
        $detail->save();
        return response()->json(['message' => 'Data has been created.'], 201);
    }
}
