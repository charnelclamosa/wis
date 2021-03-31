<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stickers</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&display=swap" rel="stylesheet"> 
    <style type="text/css" media="all">
    * {
        margin: 0;
        padding: 0;
        font-family: 'Roboto', sans-serif;
    }
    body {
        padding: 2rem 0;
        margin-top: 2rem;
    }
    .header {
        position: fixed;
        padding: 2rem;
    }
    .content {
        padding: 2rem;
        margin-left: 1rem;
    }
    .location {
        position: absolute;
        text-align: left;
    }
    .line {
        position: absolute;
        text-align: right;
    }
    
    .box {
        border: 1px solid black;
        padding: 0.5rem;
        margin: 0.6rem;
        width: 160px;
        height: 85px;
        display: inline-block;
    }
    .regular {
        font-size: 10px;
    }
    .small {
        font-size: 8px;
    }
    .page-break {
    page-break-after: always;
}
    </style>
</head>
<body>
    <div class="header">
            <div class="location"><h1>Location: {{$location}}</h1></div>
            <div class="line"><h1>Line: {{$line}}</h1></div>
    </div>
    <div class="content">
            @php $xAxis = 0 @endphp
            @foreach ($bundles as $bundle)
            @php $xAxis++ @endphp
                <div class="box">
                <p class="regular">{{$bundle['BundleNo']}}</p>
                <p class="regular">{{$bundle['DeliveryId']}}</p>
                <p class="small">{{$bundle['Description']}}</p>
                <p class="small">Quantity: {{$bundle['Qty']}} {{$bundle['QtyUnit']}}(s)</p>
                <p>{!! DNS1D::getBarcodeHTML($bundle['BundleNo'], 'C39', 1, 11) !!}</p>
                <p>{!! DNS1D::getBarcodeHTML($bundle['BundleNo'], 'C39', 1, 11) !!}</p>
                </div>
                @if($xAxis % 5 == 0)
                    @php echo '<div></div><br>'; @endphp
                @endif
             @endforeach
    </div>

        
</body>
</html>