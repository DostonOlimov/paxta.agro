<div class="hero" id="hero">
    <div class="container">
        <div class="hero__title animated animatedFadeInUp fadeInUp">
            <h1>{{trans('app.Qishloq xo\'jalik ekinlari urug\'larini sertifikatlashtirish tizimi')}}</h1>
        </div>
        <div class="search-bar-hero animated animatedFadeInUp fadeInUp" style="animation-delay: 0.6s">
            <div class='form-container'>
                <div class='form-tab'>
                    <div class='search-field'>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <p class='search-placeholder'>{{trans('app.O\'zingizga kerakli ma\'lumotni izlang...')}}</p>
                        <form action="#" id="myForm">
                            <input autocomplete="off" type='text' pattern='\S+.*' name='input' id='input' class='text-field'>
                        </form>
                    </div>
                    <button class="search-btn" id="submitBtn">{{trans('app.qidirish')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
