@extends('layouts.front')
@section('content')
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


@endsection


