@extends('layouts.default')
@section('title') {{'Campaign'}} @endsection
@section('content')
    <div class="main-content campaign-view-sec">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 card-main">
                        <div class="deal-view-box card">
                            <div class="head d-flex justify-content-center align-items-center mb-4">
                                <h2 class="mb-0">Campaign View</h2>
                                <div class="d-flex align-items-center flight-range">
                                    <div class="daterange d-flex align-items-center">
                                        <select name="deal_status" id="deal_status" class="au-input" >
                                            <option value="">Status</option>
                                            @if( count( $dealStatus ) > 0 )
                                                @foreach( $dealStatus as $dealStatusKey => $dealStatusVal )
                                                    <option value="{{ $dealStatusVal['slug'] }}">{{ $dealStatusVal['name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row deal-view">
                                @if( count( $dealView ) > 0 )
                                    @foreach( $dealView as $dealViewKey => $dealViewVal )
                                        @php
                                            $imageUrl = "public/images/dashboard/".$dealViewVal['image'];
                                        @endphp
                                        <div class="col form-group">
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
                </div>
                <div class="row">
                    <div class="col-md-12 card-main">
                        <div class="deal-view-box card">
                            <div class="head d-flex justify-content-center align-items-center mb-4">
                                <div class="d-flex align-items-center flight-range">
                                    <div class="daterange d-flex align-items-center">
                                        <button class="btn btn-lg btn-secondary" >Create Campaign</button>    
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-responsive-data2">
                                <table id="campaign_table" class="table custom-table table-borderless table-striped dataTable no-footer" style="width:100%;" >
                                    <thead>
                                        <tr>
                                            @foreach( $tableTitle as $tableTitleKey => $tableTitleRow )
                                                @if( $tableTitleKey == 1 )
                                                    <th class="reorder">{{ $tableTitleRow }}</th>
                                                @else
                                                    <th>{{ $tableTitleRow }}</th>
                                                @endif
                                            @endforeach    
                                        </tr>
                                    </thead>
                                    <tbody id="campaign_view_body">
                                        @foreach( $tableData as $tableDetailRowKey => $tableDetailRowVal )
                                            <tr class="tr-shadow">
                                                @foreach( $tableDetailRowVal as $tableRowDetailKey => $tableRowDetailVal )
                                                    @if( $tableRowDetailKey == 'deal_auto_id' )
                                                    @php 
                                                        $campaignId = base64_encode( $tableDetailRowVal['campaign_id'] );
                                                        $url = url('/campaign/edit/'. $campaignId);
                                                    @endphp
                                                    <td>
                                                        <a href="{{ $url }}"><i class="fa fa-pencil-alt fa-lg"></i></a>
                                                    </td>
                                                    @else
                                                        <td key="{{ $tableRowDetailKey }}">{{ $tableRowDetailVal }}</td>    
                                                    @endif
                                                @endforeach    
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- <div class="head d-flex justify-content-center align-items-center mb-4">
                                <button class="btn btn-lg btn-secondary" id="edit_campaign_id" dealid="" autoincrementid="" disabled>Edit Campaign</button>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </div> 
@stop