@extends('layouts.default')
@section('title') {{'Dashboard'}} @endsection
@section('content')
    <div class="main-content">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 card-main">
                        <div class="filter-box card" id="dashboard_deal">
                            <div class="head d-flex justify-content-end align-items-center mb-4">
                                <!--<h2 class="mb-0">Filter</h2> -->
                                <div class="d-flex align-items-center flight-range">
                                    <div class="daterange d-flex">
                                        <div class="daterange-input">
                                            <input type="text" name="daterange" id="daterange" value="" placeholder="mm/dd/yyyy - mm/dd/yyyy" />
                                            <input type="hidden" name="start_daterange" id="start_daterange" value="" />
                                            <input type="hidden" name="end_daterange" id="end_daterange" value="" />
                                        </div>
                                    </div>
                                    <button class="btn btn-lg btn-secondary" id="deal_reset">Reset</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="deal_no" id="deal_no"  class="au-input form-control au-input--full">
                                        <option value="">Deal</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="campaign" id="campaign"  class="au-input form-control au-input--full">
                                        <option value="">Campaign</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="demographics" id="demographics"  class="au-input form-control au-input--full">
                                        <option value="">Demographics</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="outlet" id="outlet"  class="au-input form-control au-input--full">
                                        <option value="">Out Let</option>    
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="agency" id="agency"  class="au-input form-control au-input--full">
                                        <option value="">Agency</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="location" id="location"  class="au-input form-control au-input--full">
                                        <option value="">Location</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 col-xl-3 form-group">
                                    <select name="brand" id="brand"  class="au-input form-control au-input--full">
                                        <option value="">Brand</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 card-main">
                        <div class="deal-view-box card">
                            <div
                            class="head d-flex justify-content-center align-items-center mb-4">
                            <h2 class="mb-0">Deal View</h2>
                            </div>
                            
                            <div class="row deal-view">
                                @if( count( $dealView ) > 0 )
                                    @foreach( $dealView as $dealViewKey => $dealViewVal )
                                        @php
                                            $imageUrl = "public/images/dashboard/".$dealViewVal['image'];
                                        @endphp
                                        <div class="col-sm-6">
                                            <div class="deal-component {{ $dealViewVal['background'] }}">
                                                <h3>{{ $dealViewVal['name'] }}</h3>
                                                <h5 id="deal_{{ $dealViewVal['slug'] }}">{{ $dealViewVal['value'] }}</h5>
                                                <div class="icon-box">
                                                <img src="{{ asset($imageUrl) }}" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 card-main">
                        <div class="advertiser-dashboard-box card">
                            <div class="advertiser-view">
                                <div class="head d-xxl-flex justify-content-between align-items-center mb-4">
                                    <h2 class="mb-0">Advertiser Dashboard</h2>
                                    <div class="d-flex align-items-center justify-content-center flight-range">
                                        <div class="daterange d-flex align-items-center">
                                            <div class="daterange-input">
                                                <input type="text" name="advertiser_daterange" id="advertiser_daterange" value="" placeholder="mm/dd/yyyy - mm/dd/yyyy" />
                                                <input type="hidden" name="advertiser_start_daterange" id="advertiser_start_daterange" value="" />
                                                <input type="hidden" name="advertiser_end_daterange" id="advertiser_end_daterange" value="" />
                                            </div>
                                        </div>
                                        <button class="btn btn-lg btn-secondary" id="advertiser_reset">Reset</button>
                                    </div>
                                </div>
                                <div class="row">
                                    @if( count( $dealStatus ) > 0 )
                                        @foreach( $dealStatus as $dealStatusKey => $dealStatusVal )
                                            <div class="col-sm-6 col-lg-6 col-xl-6">
                                                <div class="advertiser-component d-flex align-items-center">
                                                    <span class="info-icon {{ $dealStatusVal['background'] }}"><i class="{{ $dealStatusVal['icon'] }}"></i></span>
                                                    <h4 id="advertiser_{{ strtolower($dealStatusVal['name']) }}" >0 {{ $dealStatusVal['name'] }}</h4>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="changepasswrod" class="modal fade popup-form" tabindex="-1" style="display:none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-notification text-center mb-3" role="alert"></div>
                    <form id="forgot_password" novalidate="novalidate">   
                        @csrf

                        <div class="form-group row">
                            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" >
                            <input type="hidden" id="user_id" class="form-control" name="user_id" value="{{ Session::get('user_id') }}">
                        </div>
                        <div class="form-group">
                            <label for="current password" class="col-form-label">Current Password</label>
                                <input type="password" id="current_password" class="form-control" name="current_password" value="">
                        </div>
                        <div class="form-group">
                            <label for="new password" class="col-form-label">New Password</label>
                                <input type="password" id="new_password" class="form-control" name="new_password" value="">
                        </div>
                        <div class="form-group">
                            <label for="confirm password" class="col-form-label">Confirm Password</label>
                                <input type="password" id="confirm_password" class="form-control" name="confirm_password">
                        </div>

                        <div class="text-center">
                            <input type="submit" class="au-btn au-btn--block au-btn--green" value="Change Password">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop