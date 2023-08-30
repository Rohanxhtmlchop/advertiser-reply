@extends('layouts.default')
@section('title') {{'Deal'}} @endsection
@section('content')
    <div class="main-content deal-view-sec">
        <div class="section__content section__content--p10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 card-main">
                        <div class="deal-view-box card">
                            <div class="head d-flex justify-content-center align-items-center mb-3">
                                <h2 class="mb-0">Deal View</h2>
                                <div class="d-flex align-items-center flight-range select">
                                    <div class="daterange d-flex align-items-center">
                                        <select name="deal_status" id="deal_status" class="au-input form-control" >
                                            <option value="">Status</option>
                                            @if( count( $dealStatus ) > 0 )
                                                @foreach( $dealStatus as $dealStatusKey => $dealStatusVal )
                                                    <option value="{{ $dealStatusVal['id'] }}">{{ $dealStatusVal['name'] }}</option>
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
                                        <div class="col">
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
                            <div class="table-responsive table-responsive-data2 ">
                                <table id="deals_table" class="table custom-table table-borderless table-striped dataTable no-footer " style="width:100%;">
                                    <thead>
                                        <tr>
                                            @foreach( $tableTitle as $tableTitleKey => $tableTitleRow )
                                                @if( $tableTitleKey == 1 )
                                                    <th class="reorder">{{ $tableTitleRow }}</th>
                                                @else
                                                    <th class="no-sort">{{ $tableTitleRow }}</th>
                                                @endif
                                            @endforeach    
                                        </tr>
                                    </thead>
                                    <tbody id="deal_view_body">
                                        @foreach( $tableData as $key => $tableDetailRowVal )
                                            <tr class="tr-shadow">
                                                @foreach( $tableDetailRowVal as $tableRowDetailKey => $tableRowDetail )
                                                    @if(  $tableRowDetailKey == 'day_time' )
                                                        <td class="{{ $tableRowDetailKey }}">{{ $dayTableData[$key] }} {{ $tableRowDetail }}</td>
                                                    @else 
                                                        <td class="{{ $tableRowDetailKey }}">{{ $tableRowDetail }}</td>
                                                    @endif
                                                @endforeach    
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </div> 
@stop