<div class="sidebar sidebar-dark sidebar-fixed " id="sidebar">
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="select__row">
                <div class="select_col-md-4">
                    <a href="#" onclick="changeCrop('1')">
                        <img class="selectCropImage" src="/resources/assets/images/paxta_logo.png">
                        <span class="selectCropText">{{ trans('message.Paxta tolasi') }}
                            <br>{{ trans('message.Muvofiqlik sertifikati') }}</span>
                    </a>
                </div>
                <div class="select_col-md-4">
                    <a href="#" onclick="changeCrop('3')">
                        <img class="selectCropImage" src="/resources/assets/images/paxta_image.jpg">
                        <span class="selectCropText">{{ trans('message.Paxta tolasi') }}
                            <br>{{ trans('message.Sifat sertifikati') }}</span>
                    </a>
                </div>
                <div class="select_col-md-4">
                    <a href="#" onclick="changeCrop('2')">
                        <img class="selectCropImage" src="/resources/assets/images/chigit_logo.png">
                        <span class="selectCropText">{{ trans('message.Texnik chigit') }}
                            <br>{{ trans('message.Sifat sertifikati') }}</span>
                    </a>
                </div>
                <div class="select_col-md-4">
                    <a href="#" onclick="changeCrop('4')">
                        <img class="selectCropImage" src="/resources/assets/images/momig.jpg">
                        <span class="selectCropText">{{ trans('message.Paxta momig\'i') }}
                            <br>{{ trans('message.Sifat sertifikati') }}</span>
                    </a>
                </div>
                @if (auth()->user()->id == 1)
                    <div class="select_col-md-4">
                        <a href="#" onclick="changeCrop('5')">
                            <img class="selectCropImage" src="/resources/assets/images/product.png">
                            <span class="selectCropText">{{ trans('message.Paxta tolasidan') }}
                                <br>{{ trans('message.Olinadigan mahsulotlar') }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="sidebar-brand d-none d-md-flex justify-content-around">

        <button id="myBtn" style="border: none; background: transparent; padding: 0;">
            @php
                $crop = getApplicationType();
                $cropImages = [
                    \App\Models\CropsName::CROP_TYPE_1 => '/resources/assets/images/paxta_logo.png',
                    \App\Models\CropsName::CROP_TYPE_2 => '/resources/assets/images/chigit_logo.png',
                    \App\Models\CropsName::CROP_TYPE_3 => '/resources/assets/images/paxta_image.png',
                    \App\Models\CropsName::CROP_TYPE_4 => '/resources/assets/images/momig.jpg',
                    \App\Models\CropsName::CROP_TYPE_5 => '/resources/assets/images/product.png',
                ];
                $imageSrc = $cropImages[$crop];
            @endphp
            <img class="sidebarLogo" src="{{ $imageSrc }}" alt="Crop Logo">
        </button>



        <h2 style="font-size: 20px; color: white; margin: 6px 6px 5px -11px; !important;">
            @if (auth()->user()->id != 1)
                {{ trans('message.AGROINSPEKSIYA') }}
            @endif
        </h2>
    </div>

    @inject('menuService', 'App\Services\MenuService')

    <x-sidebar-menu :menu-service="$menuService" />

    
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
