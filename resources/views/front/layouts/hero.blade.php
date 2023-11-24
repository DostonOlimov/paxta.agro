<!-- Hero Section Begin -->
<section class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="hero__categories">
                    <div class="hero__categories__all">
                        <i class="fa fa-bars"></i>
                        <span>Barcha urug'lar</span>
                    </div>
                    <div class="scroll_categories">
                        <ul>
                            @foreach($crops as $crop)
                                <li><a href="{{ url('/about/'.$crop->id) }}">{{$crop->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="hero__search">
                    <div class="hero__search__form">
                        <form action="#">
                            <div class="hero__search__categories">
                                Kategoriyalar
                                <span class="arrow_carrot-down"></span>
                            </div>
                            <input type="text" placeholder="...">
                            <button type="submit" class="site-btn">Qidirish</button>
                        </form>
                    </div>
                </div>
                @php
                    if($crop_name === 'all')
                        { $img = 'urug12.jpg';}
                else  {
                    $img = ( $crop_name ? $crop_name->pre_name.'.jpg' : 'urug.jpg');
                    }
                @endphp
                <div class="hero__item set-bg" data-setbg="{{ url('/img/crops/'.$img) }}">
                    <div class="hero__text">
                        @if($crop_name and $crop_name !== 'all')
                            <h2 style="color:green">{{$crop_name->name}}</h2>
                            <p>urug'lik mahsuloti</p>
                        @else
                            <span>Agroinspeksiya</span>
                            <h2>Urug'lar <br /></h2>
                            <p>va ularning sertifikatlari</p>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->
