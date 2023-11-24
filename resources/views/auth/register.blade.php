
@extends('layouts.front')
@section('content')

<div class="container " >
    <div class="container-fluid page-body-wrapper full-page-wrapper" >
        <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one"  >
            <div class="row w-100">
                <div class="col-lg-6 mx-auto">
                    <div class="row">
                        <div class="col-lg-6 pr-0">
                            <a href="{!! url('login') !!}"><button type="button" class="btn btn-primary rounded-top p-3 w-100" ><b>Kirish</b></button></a>
                        </div>
                        <div class="col-lg-6 pl-0">
                            <a href="{!! url('register') !!}"><button type="button" class="btn btn-light rounded-top p-3 w-100"><b>Ro'yxatdan o'tish</b></button></a>
                        </div>
                    </div>
                    <div class="auto-form-wrapper">

                        <form action="{{ route('register.store') }}" method="POST">
                            {{ csrf_field() }}

                            <div class=" form-group has-feedback {{ $errors->has('firstname') ? ' has-error' : '' }}">
                                <b><label class="label" for="first-name">
                                    Ismingizni kiriting: <label class="text-danger">*</label>
                                </label></b>
                                <div class="">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"  placeholder="{{
								  		trans('app.Enter First Name')}}" class="form-control" maxlength="25" required>
                                    @error('name')
                                        <span class="help-block">
									 			<strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group has-feedback {{ $errors->has('lastname') ? ' has-error' : '' }}">
                                <b><label class="label" for="last-name">
                                    Familyangizni kiriting <label class="text-danger">*</label>
                                </label></b>
                                <div class="">
                                    <input type="text" id="lastname" name="lastname"  value="{{ old('lastname') }}" placeholder="{{ trans('app.Enter Last Name')}}" class="form-control" maxlength="25" required>
                                    @error('lastname')
                                        <span class="help-block">
											 <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <b> <label class="label">Email</label></b>
                                <div class="">
                                    <input name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}"  required>

                                </div>
                            </div>
                            @error('email')
                                <span class="help-block">
                                <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                                <b><label class="form-label" for="Password">{{ trans('app.Password')}} <label class="text-danger">*</label></label></b>
                                <div class="">
                                    <input type="password" id="password" placeholder="Parolni kiriting" name="password"  class="form-control" maxlength="20" autocomplete="new-password" required>
                                    @error('password')
                                        <span class="help-block">
											<strong>{{$message}}</strong>
										</span>
                                    @enderror
                                </div>
                            </div>
                            <div class=" form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <b><label class="form-label currency" style="padding-right: 0px;"for="Password">{{ trans('app.Confirm Password') }} <label class="text-danger">*</label></label></b>
                                <div class="">
                                    <input type="password"  name="password_confirmation" placeholder="{{ trans('app.Enter Confirm Password')}}" class="form-control" maxlength="20" title='' required>
                                    @error('password_confirmation')
                                        <span class="help-block">
											<strong>{{$message}}</strong>
										</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary submit-btn btn-block">Ro'yxatdan o'tish</button>
                            </div>
                        </form>
                    </div>
                    <hr>
                    <b><p style="color:red;" class="footer-text text-center">Copyright © {{ date('Y') }} O'ZAGROINSPEKSIYA. Barcha huquqlar himoyalangan.</p></b>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
@endsection
@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ URL::asset('vendors/moment/min/moment.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        $('input[name="password_confirmation"]').change(function () {
            if ($('input[name="password_confirmation"]').val() !== $('input[name="password"]').val()) {
                swal({
                    title: "Parollar bir xil emas",
                    type: "warning",
                    text: "Tasdiqlovchi parol kiritilgan parolga mos kelmadi",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Qaytadan tasdiqlash",
                    closeOnConfirm: true
                }).then((isConfirm) => {
                    $('input[name="password_confirmation"]').val('').focus();
                    // Remove 'title' from the line below if it's not needed
                    $('input[name="password_confirmation"]').attr('title', '');
                });
            }
        });
    });
    </script>
@endsection
{{--@include('front.layouts.footer')--}}
