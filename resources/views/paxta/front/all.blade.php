@extends('layouts.front')
@section('content')

@include('front.layouts.hero')

<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                 <div class="p-4 pr-5 border-bottom bg-light d-sm-flex justify-content-between">
                     <h4 class="card mb-0">Taqdim etilgan sertifikatlar soni</h4>
                        <div id="chartdiv-chart-legend" class="mr-4"></div>
                 </div>
                <div class="card-body d-flex flex-column">
                    <div id="chartdiv"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<!-- pie chart component js start-->
<x-xbar_chart :data1="$data1"  />
<!-- pie chart component js start-->

@endsection
