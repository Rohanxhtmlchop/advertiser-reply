@extends('layouts.default')
@section('title') {{'Edit Campaign'}} @endsection
@section('content')
    <div class="main-content campaign-edit">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 card-main">
                        @if( !empty( $campaign ) )
                        <div class="deal-view-box">
                            <div class="campaign-number-time-sec mb-3">
                                <div class="campaign-number text-center">
                                    <h4>Editing Campaign : {{ $campaign['campaign_payloads_id'] }} {{ $campaign['campaign_payloads_name'] }}</h4>
                                </div>
                                <div class="campaign-date text-center">
                                    <span>Add Deal Number between Deal {{ $campaign['campaigns_deal_id'] }} Valid From date {{ date('m-d-Y', strtotime($campaign['campaigns_valid_from'])) }} to Till date {{ date('m-d-Y', strtotime($campaign['campaigns_valid_to'])) }}  Deal Year {{ $campaign['campaigns_year'] }}</span>
                                </div>
                            </div>
                            <form method="post" id="edit_campaign">
                            @csrf
                                <input type="hidden" name="campaign_id" id="campaign_id" value="" />
                                <input type="hidden" name="campaign_deal_id" id="campaign_deal_id" value="" />
                                <input type="hidden" name="deal_payloads_name" id="deal_payloads_name" value="" />
                                <input type="hidden" name="campaign_payload_id" id="campaign_payload_id" value="" />
                                <input type="hidden" name="campaign_day_time" id="campaign_day_time" value="" />
                                <input type="hidden" name="inv_type" id="inv_type" value="" />
                                <input type="hidden" name="inv_length" id="inv_length" value="" />
                                <input type="hidden" name="dollar_rates" id="dollar_rates" value="" />
                                <input type="hidden" name="per_rate" id="per_rate" value="" />
                                <input type="hidden" name="total_avails" id="total_avails" value="" />
                                <input type="hidden" name="total_unit" id="total_unit" value="" />
                                <div class="responsive-tabs">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a id="general" href="#pane-A" class="nav-link active show" data-toggle="tab" role="tab" aria-controls="general" aria-selected="true" ><i class="fa fa-wrench" aria-hidden="true"></i>General</a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="cpm-imp" href="#pane-B" class="nav-link" data-toggle="tab" role="tab" aria-controls="cpm-imp" aria-selected="false"><i class="fa fa-eye" aria-hidden="true"></i>CPM/IPM</a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="flighting" href="#pane-C" class="nav-link" data-toggle="tab" role="tab" aria-controls="flighting" aria-selected="false" ><i class="fa fa-calendar-o" aria-hidden="true"></i>Flight Start & End Date</a>
                                        </li>
                                        <li class="nav-item">
                                            <a id="summary" href="#pane-D" class="nav-link" data-toggle="tab" role="tab" aria-controls="summary" aria-selected="false" ><i class="fa fa-file-text" aria-hidden="true"></i>Summary</a>
                                        </li>
                                    </ul>
                                    <div id="content" class="tab-content" role="tablist">
                                        <div id="pane-A" class="general-tab card tab-pane fade show active" role="tabpanel"
                                            aria-labelledby="general">
                                            <div class="card-header" role="tab" id="heading-A">
                                                <h5 class="mb-0">
                                                    <a data-bs-toggle="collapse" href="#collapse-A"
                                                        aria-expanded="true" aria-controls="collapse-A">
                                                        General
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapse-A" class="collapse show" data-bs-parent="#content" role="tabpanel" aria-labelledby="heading-A">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <input type="hidden" id="advertiser_name" name="advertiser_name" value="{{ Session::get('advertiser_name')}}">
                                                        <div class="col-md-6 col-xxl-4 form-group form-group-inline">
                                                            <label for="campaign_number">Campaign Number</label>
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
                                                        <div class="btn-row mt-3 text-center">
                                                            <a href="javascript:void(0);" class="btn btn-lg btn-secondary tab-btn" attr-active="cpm-imp" >Go To CPM/IPM</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pane-B" class="cpm-imp-tab card tab-pane fade" role="tabpanel" aria-labelledby="cpm-imp">
                                            <div class="card-header" role="tab" id="heading-B">
                                                <h5 class="mb-0">
                                                    <a class="collapsed" data-bs-toggle="collapse" href="#collapse-B" aria-expanded="false" aria-controls="collapse-B">
                                                        CPM/IPM
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapse-B" class="collapse" data-bs-parent="#content" role="tabpanel" aria-labelledby="heading-B">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row  align-items-center">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="realistic">Demographics</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <select name="demographic_name" class="au-input au-input--full" id="demographic_name" >
                                                                    <option value="">Demographic</option>
                                                                    @if( count( $demographicList ) > 0 )
                                                                        @foreach( $demographicList as $demographicListKey => $demographicListVal )
                                                                            <option value="{{ $demographicListVal['id'] }}">{{ $demographicListVal['name'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row  align-items-center">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="demo_population">Demo Population</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="number" name="cpm_ipm_demo_population" class="au-input--full form-control" value="" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row  align-items-center">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="demo_population">Impressions</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="number" name="cpm_ipm_impressions" class="au-input--full form-control" value="" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row  align-items-center">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="demo_population">GRP</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="number" name="cpm_ipm_grp" class="au-input--full form-control" value="" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row  align-items-center">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="demo_population">CPM</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="number" name="cpm_ipm_cpm" class="au-input--full form-control" value="" >
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="btn-row mt-3 text-center">
                                                            <a href="javascript:void(0);" class="btn btn-lg btn-secondary tab-btn" attr-active="general" >Go To General</a>
                                                            <a href="javascript:void(0);" class="btn btn-lg btn-secondary ml-md-3 tab-btn" attr-active="flighting" >Go To Flighting</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pane-C" class="flighting-tab card tab-pane fade" role="tabpanel" aria-labelledby="flighting">
                                            <div class="card-header" role="tab" id="heading-C">
                                                <h5 class="mb-0">
                                                    <a data-bs-toggle="collapse" href="#collapse-C"
                                                        aria-expanded="true" aria-controls="collapse-C">
                                                        Flighting
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapse-C" class="collapse" data-bs-parent="#content" role="tabpanel" aria-labelledby="heading-C">
                                                <div class="card-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-6 form-group date-field">
                                                                    <input type="text" placeholder="Flight Start Date" id="flight_start_date" class="au-input au-input--full form-control" name="flight_start_date">
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="text" id="campaign_flight_start_date" placeholder="MM/DD/YYYY" class="au-input au-input--full form-control" name="campaign_flight_start_date" value=""disabled="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-6 form-group">
                                                                    <label for="revenue_risk">Ad Length</label>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="text" id="ad_length" class="au-input au-input--full form-control" name="ad_length" value="">
                                                                    <input type="hidden" id="ad_length_old" class="au-input au-input--full form-control" name="ad_length_old" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-6 form-group date-field">
                                                                    <input type="text" placeholder="Flight End Date" id="flight_end_date" class="au-input au-input--full form-control" name="flight_end_date" >
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="text" id="campaign_flight_end_date" placeholder="MM/DD/YYYY" class="au-input au-input--full form-control" name="campaign_flight_end_date" value=""disabled="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-6 form-group">
                                                                    <select name="day_parts_id"class="au-input au-input--full" id="day_parts_id">
                                                                    <option value="">Time / Day Part</option>
                                                                                @if( count( $dayPartList ) > 0 )
                                                                                    @foreach( $dayPartList as $dayPartListKey => $dayPartListVal )
                                                                                        <option value="{{ $dayPartListVal['id'] }}">{{ $dayPartListVal['name'] }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 form-group">
                                                                    <input type="text" id="campaign_day_parts"class="au-input au-input--full form-control"name="campaign_day_parts" value=""disabled="">
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="flighting-table-box table-responsive">
                                                        <table class="table custom-table table-borderless table-striped dataTable no-footer" id="edit_flight">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>S - Sunday</th>
                                                                    <th>M - Monday</th>
                                                                    <th>T - Tuesday</th>
                                                                    <th>W - Wednesday</th>
                                                                    <th>T - Thursday</th>
                                                                    <th>F - Friday</th>
                                                                    <th>S - Saturday</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="day-checkbox-list">
                                                                    <td>Days</td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="S" id="sunday" value="sunday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="M" id="monday" value="monday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="T" id="tuesday" value="tuesday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="W" id="wednesday" value="wednesday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="T" id="thursday" value="thursday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="F" id="friday" value="friday"  /></div></td>
                                                                    <td><div class="form-check"><input type="checkbox" class="form-check-input" name="days[]" day="S" id="saturday" value="saturday"  /></div></td>
                                                                </tr>
                                                                <tr class="day-split-checkbox-list">
                                                                    <td>Split</td>
                                                                    <td>
                                                                        <div class="number-field">    
                                                                            <input type="number" class="au-input form-control" name="sunday_split" id="sunday_split" value="" max="100" />
                                                                        </div>
                                                                    </td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="monday_split" id="monday_split" value="" max="100" /></div></td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="tuesday_split" id="tuesday_split" value="" max="100" /></div></td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="wednesday_split" id="wednesday_split" value="" max="100" /></div></td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="thursday_split" id="thursday_split" value="" max="100" /></div></td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="friday_split" id="friday_split" value="" max="100" /></div></td>
                                                                    <td><div class="number-field"><input type="number" class="au-input form-control" name="saturday_split" id="saturday_split" value="" max="100" /></div></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div> 
                                                    <div class="btn-row mt-3 text-center">
                                                        <a href="javascript:void(0);" class="btn btn-lg btn-secondary tab-btn" attr-active="cpm-imp" >Go To CPM/IPM</a>
                                                        <a href="javascript:void(0);" class="btn btn-lg btn-secondary ml-md-3 tab-btn" attr-active="summary" >Go To Summary</a>
                                                    </div>     
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pane-D" class="summary-tab card tab-pane fade " role="tabpanel" aria-labelledby="summary">
                                            <div class="card-header" role="tab" id="heading-D">
                                                <h5 class="mb-0">
                                                    <a data-bs-toggle="collapse" href="#collapse-D"
                                                        aria-expanded="true" aria-controls="collapse-D">
                                                        Summary
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapse-D" class="collapse" data-bs-parent="#content"
                                                role="tabpanel" aria-labelledby="heading-D">
                                                <div class="card-body" id="summary">
                                                    <div class="row mb-2">
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="campaign_number">Campaign Number</label>
                                                            <span class="campaign_number au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="campaign_name">Campaign Name</label>
                                                            <span class="campaign_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="brand_name">Brand</label>
                                                            <span class="brand_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="media_line">Media Line</label>
                                                            <span class="media_line_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="demo_name">Demo</label>
                                                            <span class="demo_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="ae_name">AE</label>
                                                            <span class="ae_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="outlet_name">Outlet</label>
                                                            <span class="outlet_name au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="market_place">Market Place</label>
                                                            <span class="market_place au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="demo_population">Demo Population</label>
                                                            <span class="demo_population au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="impressions">Impressions</label>
                                                            <span class="impressions au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="grp">GRP</label>
                                                            <span class="grp au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="cpm">CPM</label>
                                                            <span class="cpm au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="date_change">Date Change</label>
                                                            <span class="date_change au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                        <div class="col-md-6 form-group-inline form-group col-xxl-4">
                                                            <label for="chnage_by">Change By</label>
                                                            <span class="chnage_by au-input au-input--full form-control disable-bg "></span>
                                                        </div>
                                                    </div>
                                                    <div class="new-campaign-table mb-2">
                                                    <h4>New Detail</h4>
                                                        <div class="flight-date row">
                                                            <div class="col-lg-6 form-group form-group-inline flight-start-date">
                                                                <span class="label">Flight Start Date</span>
                                                                <span class="new-flight-stat-date-text au-input au-input--full form-control ">MM/DD/YYYY</span>
                                                            </div>
                                                            <div class="col-lg-6 form-group form-group-inline flight-end-date">
                                                                <span class="label">Flight End Date</span>
                                                                <span class="new-flight-end-date-text au-input au-input--full form-control">MM/DD/YYYY</span>
                                                            </div>
                                                        </div>
                                                        <div class="new-campaign-table-box table-responsive">
                                                            <table id="new_campaign_table"  class="table custom-table table-borderless table-striped dataTable no-footer"  style="width:100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Campaign Number</th>
                                                                        <th>Campaign Name</th>
                                                                        <th>Title</th>
                                                                        <th>Day/ Time</th>
                                                                        <th>Brand</th>
                                                                        <th>Start Flight</th>
                                                                        <th>End Flight</th>
                                                                        <th>Media Line</th>
                                                                        <th>Inv Type</th>
                                                                        <th>Inv Length</th>
                                                                        <th>$ Rate</th>
                                                                        <th>$ Rate</th>
                                                                        <th>% Rate</th>
                                                                        <th>Total Avails</th>
                                                                        <th>Total Unit</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="tr-shadow">
                                                                        <td class="new-campaign-id"></td>
                                                                        <td class="new-campaign-name" ></td>
                                                                        <td class="new-campaign-deal-name" ></td>
                                                                        <td class="new-campaign_day-time" ></td>
                                                                        <td class="new-campaign-brand" ></td>
                                                                        <td class="new-campaign-start-flight-date" ></td>
                                                                        <td class="new-campaign-end-flight-date" ></td>
                                                                        <td class="new-campaign-media-line" ></td>
                                                                        <td class="new-campaign-inv-type" ></td>
                                                                        <td class="new-campaign-inv-length" ></td>
                                                                        <td class="new-campaign-rate" ></td>
                                                                        <td class="new-campaign-do-rate" ></td>
                                                                        <td class="new-campaign-per-rate" ></td>
                                                                        <td class="new-campaign-total-avail" ></td>
                                                                        <td class="new-campaign-total-unit" ></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="old-campaign-table">
                                                        <h4>Old Detail</h4>
                                                        <div class="flight-date row">
                                                            <div class="flight-start-date form-group form-group-inline col-lg-6">
                                                                <span class="label">Flight Start Date</span>
                                                                <span class="flight-stat-date-text au-input au-input--full form-control">MM/DD/YYYY</span>
                                                            </div>
                                                            <div class="flight-end-date form-group form-group-inline col-lg-6">
                                                                <span class="label">Flight End Date</span>
                                                                <span class="flight-end-date-text au-input au-input--full form-control">MM/DD/YYYY</span>
                                                            </div>
                                                        </div>
                                                        <div class="old-campaign-table-box table-responsive">
                                                            <table id="old_campaign_table" class="table custom-table table-borderless table-striped dataTable no-footer" style="width:100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Campaign Number</th>
                                                                        <th>Campaign Name</th>
                                                                        <th>Title</th>
                                                                        <th>Day/ Time</th>
                                                                        <th>Brand</th>
                                                                        <th>Start Flight</th>
                                                                        <th>End Flight</th>
                                                                        <th>Media Line</th>
                                                                        <th>Inv Type</th>
                                                                        <th>Inv Length</th>
                                                                        <th>$ Rate</th>
                                                                        <th>$ Rate</th>
                                                                        <th>% Rate</th>
                                                                        <th>Total Avails</th>
                                                                        <th>Total Unit</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr class="tr-shadow">
                                                                        <td class="old-campaign-id"></td>
                                                                        <td class="old-campaign-name" ></td>
                                                                        <td class="old-campaign-deal-name" ></td>
                                                                        <td class="old-campaign_day-time" ></td>
                                                                        <td class="old-campaign-brand" ></td>
                                                                        <td class="old-campaign-start-flight-date" ></td>
                                                                        <td class="old-campaign-end-flight-date" ></td>
                                                                        <td class="old-campaign-media-line" ></td>
                                                                        <td class="old-campaign-inv-type" ></td>
                                                                        <td class="old-campaign-inv-length" ></td>
                                                                        <td class="old-campaign-rate" ></td>
                                                                        <td class="old-campaign-do-rate" ></td>
                                                                        <td class="old-campaign-per-rate" ></td>
                                                                        <td class="old-campaign-total-avail" ></td>
                                                                        <td class="old-campaign-total-unit" ></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="btn-row mt-3 text-center">
                                                        <a href="javascript:void(0);" class="btn btn-lg btn-secondary keep-original" attr-active="cpm-imp" >Discard Changes</a>
                                                        <a href="javascript:void(0);" class="btn btn-lg btn-secondary ml-md-3 tab-btn" attr-active="flighting" >Go To Flighting</a>
                                                        <input type="submit" class="btn btn-lg btn-secondary ml-md-3 send-to-approval" name="submit" id="submit" value="Send To Approval" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @else
                        <div class="deal-view-box card">
                            <h4>Error Please check Campaign Id</h4>
                        </div>    
                        @endif
                    </div>
                </div>
            </div>        
        </div>
    </div> 
@stop