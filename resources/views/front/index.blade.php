@extends('layouts.front')
@section('content')

@include('front.layouts.hero')

<!-- Categories Section Begin -->
<section class="categories">
    <div class="container">
        <div class="row">
            <div class="categories__slider owl-carousel">
                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="img/crops/paxta2.jpg">
                        <h5><a href="{{ url('/about/21') }}">Paxta</a></h5>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="img/crops/bugdoy.jpg">
                        <h5><a href="{{ url('/about/13') }}">Bug'doy</a></h5>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="img/crops/kartoshka.jpg">
                        <h5><a href="{{ url('/about/14') }}">Kartoshka</a></h5>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="img/crops/sholi.jpg">
                        <h5><a href="{{ url('/about/22') }}">Sholi</a></h5>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="categories__item set-bg" data-setbg="img/crops/makkajoxori.jpg">
                        <h5><a href="{{ url('/about/16') }}">Makkajo'xori</a></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Categories Section End -->

<!-- Featured Section Begin -->
<section class="featured spad">
    <div class="container">
        <div class="mycontainer">
        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img style="height: 300px;" src="/img/seeds/don1.jpg" alt="people">
                        <div class="weather-info">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img style="height: 300px;" src="/img/seeds/don3.jpg" alt="people">
                        <div class="weather-info">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img style="height: 300px;" src="/img/seeds/don4.jpg" alt="people">
                        <div class="weather-info">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card tale-bg">
                    <div class="card-people mt-auto">
                        <img style="height: 300px;" src="/img/seeds/don2.jpg" alt="people">
                        <div class="weather-info">

                        </div>
                    </div>
                </div>
            </div>
    </div>
        </div>
    </div>
</section>
<!-- Featured Section End -->

<!-- Banner Begin -->
<div class="banner">
    <div class="container">
        <div class="row  tranparent_row" >
            <div class="col-md-6 mb-6 stretch-card transparent">
                <div class="card card1">
                    <div class="card-body ">
                        <p class="mb-6" style="font-size: 22px">Kelib tushgan arizalar soni</p>
                        <p class="fs-30 mb-2">Jami : {{$all_app_count}}ta</p>
                        <p>{{$month_app_count}} ta (oxirgi 30 kunda)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-6 stretch-card transparent">
                <div class="card card2" >
                    <div class="card-body">
                        <p class="mb-6" style="font-size: 22px">Mahaliy ishlab chiqazuvchilardan kelib tushgan arizalar</p>
                        <p class="fs-30 mb-2">Jami : {{$local_app}}ta</p>
                        <p style="font-size:18px">@php echo round(100 * ($all_app_count > 0 ? $local_app / $all_app_count : 0),2);@endphp %</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row tranparent_row">
            <div class="col-md-6 mb-6 mb-lg-0 stretch-card transparent">
                <div class="card card3">
                    <div class="card-body">
                        <p class="mb-6 " style="font-size: 22px">Import qilingan urug'lar uchun kelib tushgan arizalar</p>
                        <p class="fs-20 mb-2">Jami : {{$global_app}} ta</p>
                        <p style="font-size:18px">@php echo round(100 * ($all_app_count > 0 ? $global_app / $all_app_count : 0),2);@endphp %</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 stretch-card transparent">
                <div class="card card4">
                    <div class="card-body">
                        <p class="mb-6" style="font-size: 22px">Taqdim etilgan sertificatlar soni</p>
                        <p class="fs-30 mb-2">Jami : {{$all_cer_count}}ta</p>
                        <p>{{$month_cer_count}} ta (oxirgi 30 kunda)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Banner End -->

@endsection


