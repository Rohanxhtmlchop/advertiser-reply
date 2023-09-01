@extends('layouts.default')

@section('content')

    <div class="page-wrapper login">
        <img src="{{ asset('public/images/login/login-bg.png') }}" alt="" class="bg-img">
        <div class="container">
            <div class="login-wrap">
                <div class="login-content">
                    <div class="login-logo">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('public/images/logo/logo.png') }}" alt="CoolAdmin">
                        </a>
                    </div>
                    <div class="login-form">
                        <h3 class="text-center mb-3">Login</h3>
                        <div class="alert alert-notification text-center mb-3" role="alert"></div>
                        <form id="login_form" method="post">   
                            @csrf
                            <div class="user-sec">
                                <div class="row form-group align-items-center">
                                    <div class="col-md-4">
                                        <label for="user_name">User Name</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" id="user_name" class="au-input au-input--full form-control" name="user_name" value="{{ old('user_name') }}"  autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="password-sec">
                                <div class="row form-group align-items-center">
                                    <div class="col-md-4">
                                        <label for="password">Password</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="password" id="password" class="au-input au-input--full form-control" name="password" value="{{ old('password') }}" >
                                    </div>
                                </div>
                            </div>
                            <div class="media-sec">
                                <div class="row form-group align-items-center">
                                    <div class="col-md-4">
                                        <label for="password">Media Line</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="media" id="media" class="au-input au-input--full form-control">
                                            <option value="">Select Option</option>
                                            @if( $data['mediaList'] )
                                                @foreach( $data['mediaList'] as $mediaKey => $mediaValue)
                                                <option value="{{ $mediaValue['id'] }}" 
                                                    @if ( $mediaValue['id'] == old('media') ) selected="selected" @endif>{{ $mediaValue['name'] }}</option>
                                                @endforeach;
                                            @endif;
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center submit-sec">
                                <span class="spinner spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                <input type="submit" class="au-btn au-btn--block au-btn--green advertiser-login-btn" value="AdvertiserReplay Login" />
                            </div>
                        </form>
                    </div>
                    <div class="verify-account-form" style="display: none;">
                        <h3 class="text-center mb-3">Verify Account</h3>
                        <div class="alert alert-notification text-center mb-3" role="alert"></div>
                        <form id="verify_account_form" method="post">   
                            @csrf
                            <div class="email-otp-sec">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="password">Email OTP</label>
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <div class="email-otp-number d-flex">
                                            <input class="form-control rounded mr-2" type="number" id="first" name="first" data-next="second"  maxlength="1" /> 
                                            <input class="form-control rounded mr-2" type="number" id="second"  name="second" data-next="third" data-previous="second" maxlength="1" /> 
                                            <input class="form-control rounded mr-2" type="number" id="third"  name="third" data-next="fourth"  data-previous="third" maxlength="1" /> 
                                            <input class="form-control rounded mr-2" type="number" id="fourth" name="fourth" data-next="fifth"  data-previous="fourth" maxlength="1" /> 
                                            <input class="form-control rounded mr-2" type="number" id="fifth" name="fifth" data-next="sixth" data-previous="fifth" maxlength="1" /> 
                                            <input class="form-control rounded" type="number" id="sixth" name="sixth" data-previous="sixth" maxlength="1" /> 
                                        </div>
                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-md-6 email-time">
                                                    <span>Time: <b class="counter">5:00</b></span>
                                                </div>
                                                <div class="col-md-6 send-email-otp">
                                                    <button id="email_send_otp">Resend OTP</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center submit-sec">
                                <span class="spinner spinner-border spinner-border-sm verify-loader" role="status" aria-hidden="true" style="display: none;"></span>
                                <input type="submit" class="au-btn au-btn--block au-btn--green verify-otp-btn" value="Verify OTP" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('includes.script')
    @stop