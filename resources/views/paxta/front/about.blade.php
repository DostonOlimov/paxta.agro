@extends('layouts.front')
@section('content')

@include('front.layouts.hero')

<section class="hero">
    <div class="container">
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="p-4 pr-5 border-bottom bg-light d-sm-flex justify-content-between">
                <h4 class="card-title mb-0"></h4>
                <div id="chartdiv-chart-legend" class="mr-4"></div>

            </div>
            <div class="card-body d-flex flex-column">
                <div id="chartdiv"></div>
            </div>
        </div>
    </div>
</div>
{{--        <h1 style="text-align: center;font-size:35px;font-weight:900;">Xodimning oylik ko'rsatkichlari</h1>--}}
{{--        <div style = "width:70%">--}}
{{--            <div id="chartdiv"></div>--}}
{{--        </div>--}}
    </div>
</section>
<!-- pie chart component js start-->
<x-pie_chart :data1="$chart_data"  />
<!-- pie chart component js start-->

<!-- bar chart component js start-->
{{--<x-bar_chart :data1="$balls" />--}}
<!-- bar chart component js start-->

@endsection
