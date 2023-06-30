<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deals;
use App\Models\DealPayload;
use Illuminate\Support\Facades\Session;
use App\Helpers\Helper;
use App\Models\Campaigns;
use App\Models\DayParts;
use App\Models\Demographic;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function campaignTableRecord( $status = '' ){
        $advertiserId = Session::get('advertiser_id');
        $campaignTableTitle = Helper::campaignViewTableName();
        $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
            ->where('campaigns.advertiser_id', '=', $advertiserId)
            ->when($status, function ($query) use ($status) {
                return $query->where('campaigns.status','=', $status);
            })
            ->join('day_parts', 'campaigns.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
            ->join('brands', 'campaigns.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
            ->join('medias', 'campaigns.media_id', '=', 'medias.id')->where('medias.status','=', 1)
            ->join('deals', 'campaigns.deal_id', '=', 'deals.id')
            ->join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
            ->orderBy('campaigns.id', 'asc');

        $campaignTableData =  $campaignList->get([
            'deal_payloads.id as deal_auto_id',
            'campaigns.deal_id as deal_id', 
            'campaigns.id as campaign_id',
            'campaign_payloads.name as campaign_payloads_name', 
            'deal_payloads.name as deal_payloads_name',
            'day_parts.name as day_time', 
            'brands.product_name as brand_name',
            'campaign_payloads.flight_start_date as campaign_payloads_flight_start_date', 
            'campaign_payloads.flight_end_date as campaign_payloads_flight_end_date', 
            'medias.name as media_name', 
            'deal_payloads.inventory_type as inventory_type', 
            'deal_payloads.inventory_length as inventory_length', 
            'deal_payloads.rate as rate', 
            'deal_payloads.rc_rate as rc_rate', 
            'deal_payloads.rc_rate_percentage as rc_rate_percentage', 
            'deal_payloads.total_avil as total_avil', 
            'deal_payloads.total_unit as total_unit', 
        ])->toArray();
        
        $campaignDayTableData = Helper::campaignDayTime( $campaignList, 'campaign_payloads' );
        $campaignTableData = Helper::tableAddDaysAndTime( $campaignTableData, $campaignDayTableData, 1 ); 
        $cahngeDateFormateFlightStart = Helper::changeDateFormate( $campaignTableData, 'campaign_payloads_flight_start_date', 1);
        $finalAllCampaignData = Helper::changeDateFormate( $cahngeDateFormateFlightStart, 'campaign_payloads_flight_end_date', 1);
        return $finalAllCampaignData;
    }
    public function index(){
        $advertiserId = Session::get('advertiser_id');
        $campaignTableTitle = Helper::campaignViewTableName();
        $dealStatusArray = Helper::dealStatusArray();
        $dealViewArray = Helper::dealViewArray();
        $data = array( 
            'title' => 'Campaign',
            'tableTitle' => $campaignTableTitle,
            'dayTableData' => '',
            'tableData' => CampaignController::campaignTableRecord(), 
            'dealStatus' => $dealStatusArray,
            'dealView' => $dealViewArray,
        );
        return view( 'pages.campaign.index', $data );
    }

    public function postStatus( Request $request ){
        $advertiserId = Session::get('advertiser_id');

        $dealView = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
        ->where('campaigns.advertiser_id', '=', $advertiserId)
        ->when($request['data'], function ($query) use ($request) {
            return $query->where('campaigns.status','=', $request['data']);
        })
        ->first( array(
            DB::raw('SUM(campaign_payloads.rate) as rate'),
            DB::raw('SUM(campaign_payloads.cpm) as cpm'),
            DB::raw('SUM(campaign_payloads.impressions) as impressions'),
            DB::raw('SUM(campaign_payloads.grp) as grp'),
            DB::raw('SUM(campaign_payloads.deal_unit) as deal_unit'),
        ))->toArray();

        $campaignViewTable = CampaignController::campaignTableRecord( $request['data'] );
        $campaignViewTableHtml = '';
        if( count( $campaignViewTable ) > 0 ){
            foreach( $campaignViewTable as $key => $tableDetailRowVal ){
                $campaignViewTableHtml .= '<tr class="tr-shadow">';
                    foreach( $tableDetailRowVal as $tableRowDetailKey => $tableRowDetail ){
                        if( $tableRowDetailKey == 'deal_auto_id' ) {
                            $campaignViewTableHtml .='<td>
                            <label class="form-check au-radio deal-number">
                                <input class="form-check-input" type="radio" value="'.$tableDetailRowVal['campaign_id'].'" name="deal_number" id="deal_number" autoid="" >
                            </label>
                        </td>';
                        }else {
                            $campaignViewTableHtml .='<td class="'. $tableRowDetailKey .'">'. $tableRowDetail .'</td>';
                        }
                    }    
                    $campaignViewTableHtml .='</tr>';
            }
        }
        return response()->json(array( 'deal_view_data' => $dealView, 'deal_table_html' => $campaignViewTableHtml ));  
    }

    public function getEditCampaignDetail(Request $request){
        if( $request['campaignId'] != '' ){
            $campaignID = $request['campaignId'];
            $advertiserId = Session::get('advertiser_id');
            $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                ->join('brands', 'campaigns.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
                ->join('medias', 'campaigns.media_id', '=', 'medias.id')->where('medias.status','=', 1)
                ->join('agencys', 'campaigns.agency_id', '=', 'agencys.id')->where('agencys.status','=', 1)
                ->join('outlets', 'campaigns.outlet_id', '=', 'outlets.id')->where('outlets.status','=', 1)
                ->join('demographics', 'campaigns.demographic_id', '=', 'demographics.id')->where('demographics.status','=', 1)
                ->join('day_parts', 'campaigns.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
                ->join('deals', 'campaigns.deal_id', '=', 'deals.id')
                ->join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->where('campaigns.advertiser_id', '=', $advertiserId)->where('campaigns.id','=',$campaignID);

            $campaignListArray = $campaignList ->first([
                'campaigns.id as campaign_id',
                'campaigns.*',
                'day_parts.name as time_day_part',
                'deal_payloads.name as deal_payloads_name',
                'brands.product_name as brand_name',
                'demographics.name as demographics_name',
                'outlets.outlet_type as outlets_name',
                'day_parts.name as day_time',
                'medias.name as media_name', 
                'agencys.name as agency_name',
                'agencys.agency_commission as agency_commission',
                'campaign_payloads.*'
            ])->toArray();
        
            $campaignDayTableData = Helper::campaignDayTime( $campaignList, 'campaign_payloads' );
            $campaignTableData = Helper::tableAddDaysAndTime( $campaignListArray, $campaignDayTableData, 0 ); 
            $cahngeDateFormateFlightStart = Helper::changeDateFormate( $campaignTableData, 'flight_start_date', 0);
            $finalCampaignData = Helper::changeDateFormate( $cahngeDateFormateFlightStart, 'flight_end_date', 0);

            $response = array(
                'status' => 1,
                'message' => 'Data',
                'data' => array(
                    'campaign_data' => $finalCampaignData 
                )
            );
            return response()->json($response);  
        }else{
            $response = array(
                'status' => 0,
                'message' => 'Please check Campaign Id is Incorrect.',
                'data' => ''
            );
            return response()->json($response);  
        }
    }

    public function getEditCampaignInfo(Request $request, $campaignID){
        $advertiserId = Session::get('advertiser_id');
        $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
            ->where('campaigns.advertiser_id', '=', $advertiserId)->where('campaigns.id','=',$campaignID)
            ->first([
                'campaigns.id as campaign_payloads_id',
                'campaign_payloads.name as campaign_payloads_name',
                'campaigns.valid_from as campaigns_valid_from', 
                'campaigns.deal_year as campaigns_year',
            ]);
            $demographicList = Demographic::where('status','=',1)->get(['id','name'])->toArray();
            $daypartsList = DayParts::where('status','=',1)->get(['id','name'])->toArray();
            if( !empty( $campaignList ) ){
            $data = array(
                'title' => 'Edit Campaign',
                'campaign' => $campaignList->toArray(),
                'demographicList' => $demographicList,
                'dayPartList' => $daypartsList,
            );
            return view( 'pages.campaign.edit', $data );
        }else{
            $data = array(
                'title' => 'Edit Campaign',
                'campaign' => '',
                'demographicList' => $demographicList,
                'dayPartList' => $daypartsList,
            );
            return view( 'pages.campaign.edit', $data );
        }
    }

    public function postEditCampaign( Request $request ){
        $advertiserId = Session::get('advertiser_id');
        $userId = Session::get('user_id');
        if( $request->data != null ){
            $newCampaignArray = [];
            $newDayArray = [];
            foreach( $request->data as $value ){
                if( $value['name'] == 'days[]'){
                    $newDayArray[] = $value['value'];
                }else{
                    $newCampaignArray[$value['name']] = $value['value'];
                }
            }
            $newCampaignArray['day_parts'] = $newDayArray;
            if( count( $newCampaignArray ) > 0 ){
                $campaignFlightStartDate = '';
                if( $newCampaignArray['flight_start_date'] != ''){
                    $campaignFlightStartDate = date('Y-m-d', strtotime($newCampaignArray['flight_start_date']));
                }else{
                    $campaignFlightStartDate = date('Y-m-d', strtotime($newCampaignArray['campaign_flight_start_date']));
                }

                $campaignFlightEndDate = '';
                if( $newCampaignArray['flight_end_date'] != ''){
                    $campaignFlightEndDate = date('Y-m-d', strtotime($newCampaignArray['flight_end_date']));
                }else{
                    $campaignFlightEndDate = date('Y-m-d', strtotime($newCampaignArray['campaign_flight_end_date']));
                }

                $dayOfArray = Helper::daySmallArray();
                $dayOfData = [];
                $dayOfDbData = [];
                foreach( $dayOfArray as $dayPartsKey => $dayPartsValue ){
                    if(in_array( $dayPartsKey, $newCampaignArray['day_parts'] )){
                        $dayOfDbData[$dayPartsKey] = 1;
                    }else{
                        $dayOfDbData[$dayPartsKey] = null;
                    }
                }
                
                foreach( $newCampaignArray['day_parts'] as $dayPartsKey => $dayPartsValue ){
                    if( array_key_exists( $dayPartsValue, $dayOfArray ) ){
                        $dayOfData[] = $dayOfArray[$dayPartsValue];
                    }
                }
                $currentDate = date('Y-m-d H:i:s');
                
                $updateCampaign = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                    ->where('campaigns.advertiser_id', '=', $advertiserId)
                    ->where('campaigns.delete', '=', 0)
                    ->where('campaign_payloads.delete', '=', 0)
                    ->where('campaign_payloads.id','=',$newCampaignArray['campaign_payload_id'])
                    ->where('campaigns.id','=',$newCampaignArray['campaign_id'])
                    ->where('campaigns.deal_id','=',$newCampaignArray['campaign_deal_id'])
                    ->update([
                        'campaign_payloads.flight_start_date' => $campaignFlightStartDate,
                        'campaign_payloads.flight_end_date' => $campaignFlightEndDate,
                        'campaign_payloads.sunday' => $dayOfDbData['sunday'],
                        'campaign_payloads.monday' => $dayOfDbData['monday'],
                        'campaign_payloads.tuesday' => $dayOfDbData['tuesday'],
                        'campaign_payloads.wednesday' => $dayOfDbData['wednesday'],
                        'campaign_payloads.thursday' => $dayOfDbData['thursday'],
                        'campaign_payloads.friday' => $dayOfDbData['friday'],
                        'campaign_payloads.saturday' => $dayOfDbData['saturday'],
                        'campaign_payloads.sunday_split' => $newCampaignArray['sunday_split'],
                        'campaign_payloads.monday_split' => $newCampaignArray['monday_split'],
                        'campaign_payloads.tuesday_split' => $newCampaignArray['tuesday_split'],
                        'campaign_payloads.wednesday_split' => $newCampaignArray['wednesday_split'],
                        'campaign_payloads.thursday_split' => $newCampaignArray['thursday_split'],
                        'campaign_payloads.friday_split' => $newCampaignArray['friday_split'],
                        'campaign_payloads.saturday_split' => $newCampaignArray['saturday_split'],
                        'campaigns.daypart_id' => $newCampaignArray['day_parts_id'],
                        'campaigns.updated_by' => $userId,
                        'campaign_payloads.updated_by' => $userId,
                        'campaigns.updated_at' => $currentDate,
                        'campaign_payloads.updated_at' =>$currentDate,
                    ]);


                $json_array = array(
                    'campaign_id' => $newCampaignArray['campaign_id'],
                    'deal_id' => $newCampaignArray['campaign_deal_id'],
                    'campaign_name' => $newCampaignArray['campaign_name'],
                    'deal_title' => $newCampaignArray['deal_payloads_name'],
                    'day_time' => implode(" ", $dayOfData).' '.$newCampaignArray['campaign_day_parts'],
                    'brand_name' => $newCampaignArray['brand_name'],
                    'flight_start_date' => $campaignFlightStartDate,
                    'flight_end_date' => $campaignFlightEndDate,
                    'media_line' => $newCampaignArray['media_line_name'],
                    'inventory_type' => $newCampaignArray['inv_type'],
                    'inventory_length' => $newCampaignArray['inv_length'],
                    'dollar_rate' => $newCampaignArray['dollar_rate'],
                    'dollar_rates' => $newCampaignArray['dollar_rates'],
                    'percentage_rate' => $newCampaignArray['per_rate'],
                    'total_avails' => $newCampaignArray['total_avails'],
                    'total_unit' => $newCampaignArray['total_unit'],
                );
                $fileName = $newCampaignArray['campaign_id'];
                Storage::put('/public/campaign/'.$fileName.'.json', json_encode($json_array));

                if( $updateCampaign == 0 ){
                    $data = array( 'status' => 0 , 'message' => 'Record was not Updated.');
                }else{
                    $data = array( 'status' => 1 , 'message' => 'Success');
                }
                
                return response()->json($data);  
            }
        }
    }
}
