@extends('layouts.default')
@section('title') {{'Advertiser Profile'}} @endsection
@section('content')
    <div class="main-content campaign-edit">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 card-main">
                        <div class="deal-view-box">
                            <form method="post" id="advertiser_profile">
                            @csrf
                                <div class="responsive-tabs">
                                    <div id="content" class="tab-content" role="tablist">
                                        <div id="pane-A" class="general-tab card tab-pane fade show active" role="tabpanel" aria-labelledby="general">
                                            <div id="collapse-A" class="collapse show" data-bs-parent="#content" role="tabpanel" aria-labelledby="heading-A">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <input type="hidden" id="advertiser_id" name="advertiser_id" value="">
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="campaign_number">Advertiser ID</label>
                                                            <input type="text" id="campaign_number" class="au-input au-input--full form-control campaign_number" name="campaign_number" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="campaign_name">Campaign Name</label>
                                                            <input type="text" id="campaign_name"class="au-input au-input--full form-control campaign_name"name="campaign_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="brand_name">Brand</label>
                                                            <input type="text" id="brand_name"class="au-input au-input--full form-control brand_name"name="brand_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="media_line">Media Line</label>
                                                            <input type="text" id="media_line"class="au-input au-input--full form-control media_line_name"name="media_line_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="dollar_rate">$ Rate</label>
                                                            <input type="text" id="dollar_rate"class="au-input au-input--full form-control"name="dollar_rate" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="agency_name">Agency Name</label>
                                                            <input type="text" id="agency_name"class="au-input au-input--full form-control"name="agency_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="demo_name">Demo</label>
                                                            <input type="text" id="demo_name"class="au-input au-input--full form-control"name="demo_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="ae_name">AE</label>
                                                            <input type="text" id="ae_name"class="au-input au-input--full form-control"name="ae_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="outlet_name">Outlet</label>
                                                            <input type="text" id="outlet_name"class="au-input au-input--full form-control"name="outlet_name" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="market_place">Market Place</label>
                                                            <input type="text" id="market_place"class="au-input au-input--full form-control"name="market_place" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="realistic">Realistic</label>
                                                            <div class="number-field">
                                                                <input type="text" id="realistic"class="au-input au-input--full form-control"name="realistic" value="" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="agency_commision">Agency Commision</label>
                                                            <div class="number-field">
                                                                <input type="text" id="agency_commision"class="au-input au-input--full form-control"name="agency_commision" value="" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="revenue_risk">Revenue Risk</label>
                                                            <div class="number-field">
                                                                <input type="text" id="revenue_risk"class="au-input au-input--full form-control"name="revenue_risk" value="" disabled="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="budget">Budget</label>
                                                            <input type="text" id="budget"class="au-input au-input--full form-control"name="budget" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="change_by">Created By</label>
                                                            <input type="text" id="created_by"class="au-input au-input--full form-control"name="created_by_old" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="change_by">Created Date</label>
                                                            <input type="text" id="created_date"class="au-input au-input--full form-control"name="created_date_old" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                        <label for="change_by">Change By</label>
                                                            <input type="text" id="change_by"class="au-input au-input--full form-control"name="change_by_old" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="change_by">Created Date</label>
                                                            <input type="text" id="change_date"class="au-input au-input--full form-control"name="change_date_old" value="" disabled="">
                                                        </div>
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                        </div>
                                                        <div class="btn-row mt-3 text-center">
                                                            <a href="javascript:void(0);" class="btn btn-lg btn-secondary tab-btn" attr-active="cpm-imp" >Go To CPM/IPM</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </div> 
@stop