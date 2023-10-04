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
use Illuminate\Support\Facades\Crypt;

class CampaignController extends Controller
{
    /************************************* Campaign List  **************************************************/
    
    public function campaignTableRecord( $status = '' ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $campaignTableTitle = Helper::campaignViewTableName();
        $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
            ->where('campaigns.advertiser_id', '=', $advertiserId)
            ->where('campaigns.client_id','=', $clientId)
            ->where('campaigns.media_id','=', $mediasId)
            ->when($status, function ($query) use ($status) {
                return $query->where('campaigns.status','=', $status);
            })
            ->join('day_parts', 'campaigns.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
            ->join('brands', 'campaigns.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
            ->join('medias', 'campaigns.media_id', '=', 'medias.id')->where('medias.status','=', 1)
            ->join('deals', 'campaigns.deal_id', '=', 'deals.id')
            ->join('status', 'campaigns.status', '=', 'status.id')
            ->join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
            ->orderBy('campaigns.id', 'asc');

        $campaignTableData =  $campaignList->get([
            'campaigns.deal_id as deal_id', 
            'campaigns.id as campaign_id',
            'campaign_payloads.name as campaign_payloads_name', 
            'deal_payloads.name as deal_payloads_name',
            'day_parts.name as day_time', 
            'brands.product_name as brand_name',
            'campaign_payloads.flight_start_date as campaign_payloads_flight_start_date', 
            'campaign_payloads.flight_end_date as campaign_payloads_flight_end_date', 
            'medias.name as media_name', 
            'campaign_payloads.inventory_type as inventory_type', 
            'campaign_payloads.inventory_length as inventory_length', 
            'campaign_payloads.rate as rate', 
            'campaign_payloads.rc_rate as rc_rate', 
            'campaign_payloads.rc_rate_percentage as rc_rate_percentage', 
            'campaign_payloads.total_avil as total_avil', 
            'campaign_payloads.total_unit as total_unit', 
            'status.name as status',
            'campaign_payloads.id as deal_auto_id',
            'campaigns.show_data as show_data',
        ])->toArray();
        
        $campaignDayTableData = Helper::campaignDayTime( $campaignList, 'campaign_payloads' );
        $campaignTableData = Helper::tableAddDaysAndTime( $campaignTableData, $campaignDayTableData, 1 ); 
        $cahngeDateFormateFlightStart = Helper::changeDateFormate( $campaignTableData, array('campaign_payloads_flight_start_date','campaign_payloads_flight_end_date'), 1);

        return $cahngeDateFormateFlightStart;
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
            'tableData' =>'', 
            'dealStatus' => $dealStatusArray,
            'dealView' => $dealViewArray,
        );
        return view( 'pages.campaign.index', $data );
    }

    public function postStatus( Request $request ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $dealView = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
        ->where('campaigns.advertiser_id', '=', $advertiserId)
        ->where('campaigns.client_id','=', $clientId)
        ->where('campaigns.media_id','=', $mediasId)
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
                            if( ( $tableDetailRowVal['status'] == 'Under Approval' ) || ( $tableDetailRowVal['status'] == 'InflIght' ) ){
                                $campaignViewTableHtml .='<td></td>';
                            } else{
                                $encryptId = base64_encode($tableDetailRowVal['campaign_id']);
                                $path = '/campaign/edit/'.$encryptId;
                                $campaignURL = url($path); 
                                $campaignViewTableHtml .='<td><a href="'.$campaignURL.'"><i class="fa fa-pencil-alt fa-lg"></i></a></td>';
                            }
                        } else if( $tableRowDetailKey != 'show_data'){
                            $campaignViewTableHtml .='<td class="'. $tableRowDetailKey .'">'. $tableRowDetail .'</td>';
                        }
                    }    
                    $campaignViewTableHtml .='</tr>';
            }
        }
        return response()->json(array( 'deal_view_data' => $dealView, 'deal_table_html' => $campaignViewTableHtml ));  
    }

    /************************************* Edit Campaign Detail  **************************************************/

    public function getEditCampaignDetail(Request $request){
        if( $request['campaignId'] != '' ){
            $campaignID = base64_decode( $request['campaignId']);
            $advertiserId = Session::get('advertiser_id');
            $clientId = Session::get('clients_id');
            $mediasId = Session::get('medias_id');
            $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                ->join('brands', 'campaigns.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
                ->join('medias', 'campaigns.media_id', '=', 'medias.id')->where('medias.status','=', 1)
                ->join('agencys', 'campaigns.agency_id', '=', 'agencys.id')->where('agencys.status','=', 1)
                ->join('outlets', 'campaigns.outlet_id', '=', 'outlets.id')->where('outlets.status','=', 1)
                ->join('users', 'campaigns.updated_by', '=', 'users.id')
                ->join('demographics', 'campaigns.demographic_id', '=', 'demographics.id')->where('demographics.status','=', 1)
                ->join('day_parts', 'campaigns.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
                ->join('deals', 'campaigns.deal_id', '=', 'deals.id')
                ->join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->where('campaigns.advertiser_id', '=', $advertiserId)
                ->where('campaigns.id','=',$campaignID)
                ->where('campaigns.client_id','=', $clientId)
                ->where('campaigns.media_id','=', $mediasId);
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
                'campaign_payloads.*',
                'demographics.demo_population as demographics_demo_population',
                'demographics.grp as demographics_grp',
                'demographics.cpm as demographics_cpm',
                'demographics.impression as demographics_impression',
                'users.user_name as users_name',
            ])->toArray();
        
            $campaignDayTableData = Helper::campaignDayTime( $campaignList, 'campaign_payloads' );
            $campaignTableData = Helper::tableAddDaysAndTime( $campaignListArray, $campaignDayTableData, 0 ); 
            $cahngeDateFormateFlightStart = Helper::changeDateFormate( $campaignTableData, array( 'flight_start_date', 'flight_end_date', 'valid_from', 'valid_to','created_at','updated_at' ), 0);
            //$finalCampaignData = Helper::changeDateFormate( $cahngeDateFormateFlightStart, array( 'flight_end_date' ), 0);
            $response = array(
                'status' => 1,
                'message' => 'Data',
                'data' => array(
                    'campaign_data' => $cahngeDateFormateFlightStart 
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
        $campaignID = base64_decode($campaignID);
        $advertiserId = Session::get('advertiser_id');
        $campaignList = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
            ->where('campaigns.advertiser_id', '=', $advertiserId)->where('campaigns.id','=',$campaignID)
            ->first([
                'campaigns.id as campaign_payloads_id',
                'campaign_payloads.name as campaign_payloads_name',
                'campaigns.valid_to as campaigns_valid_to', 
                'campaigns.valid_from as campaigns_valid_from', 
                'campaigns.deal_year as campaigns_year',
                'campaigns.deal_id as campaigns_deal_id',
            ]);
            $demographicList = Demographic::where('status','=',1)->get(['id','name'])->toArray();
            $daypartsList = DayParts::where('status','=',1)->get(['id','name'])->toArray();
            $disabled = ''; 
            if( $request->has('type') ) {
                $type = base64_decode($request->query('type'));
                if( $type == 1 ){
                    $disabled = 'disabled';
                }
            }
        if( !empty( $campaignList ) ){
            $data = array(
                'title' => 'Edit Campaign',
                'campaign' => $campaignList->toArray(),
                'demographicList' => $demographicList,
                'dayPartList' => $daypartsList,
                'disabled' => $disabled,
            );
            return view( 'pages.campaign.edit', $data );
        }else{
            $data = array(
                'title' => 'Edit Campaign',
                'campaign' => '',
                'demographicList' => $demographicList,
                'dayPartList' => $daypartsList,
                'disabled' => $disabled,
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
                    $campaignFlightStartDate = Helper::changeDateFormateYMD($newCampaignArray['flight_start_date']); 
                }else{
                    $campaignFlightStartDate = Helper::changeDateFormateYMD($newCampaignArray['campaign_flight_start_date']); 
                }

                $campaignFlightEndDate = '';
                if( $newCampaignArray['flight_end_date'] != ''){
                    $campaignFlightEndDate = Helper::changeDateFormateYMD($newCampaignArray['flight_end_date']);
                }else{
                    $campaignFlightEndDate = Helper::changeDateFormateYMD($newCampaignArray['campaign_flight_end_date']);
                }

                $dayOfArray = Helper::daySmallArray();
                $dayOfData = [];
                $dayOfDbData = [];
                $jsonDayOfDbData = [];
                foreach( $dayOfArray as $dayPartsKey => $dayPartsValue ){
                    if(in_array( $dayPartsKey, $newCampaignArray['day_parts'] )){
                        $dayOfDbData[$dayPartsKey] = 1;
                        $jsonDayOfDbData[] = $dayPartsValue;
                    }else{
                        $dayOfDbData[$dayPartsKey] = null;
                        $jsonDayOfDbData[] = 'X';
                    }
                }
                
                foreach( $newCampaignArray['day_parts'] as $dayPartsKey => $dayPartsValue ){
                    if( array_key_exists( $dayPartsValue, $dayOfArray ) ){
                        $dayOfData[] = $dayOfArray[$dayPartsValue];
                    }
                }

                $jsonCampaignArray = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                ->join('brands', 'campaigns.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
                ->join('clients', 'campaigns.client_id', '=', 'clients.id')->where('clients.delete','=', 0)
                ->join('medias', 'campaigns.media_id', '=', 'medias.id')->where('medias.status','=', 1)
                ->join('agencys', 'campaigns.agency_id', '=', 'agencys.id')->where('agencys.status','=', 1)
                ->join('outlets', 'campaigns.outlet_id', '=', 'outlets.id')->where('outlets.status','=', 1)
                ->join('demographics', 'campaigns.demographic_id', '=', 'demographics.id')->where('demographics.status','=', 1)
                ->join('day_parts', 'campaigns.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
                ->join('deals', 'campaigns.deal_id', '=', 'deals.id')
                ->join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->where('campaigns.advertiser_id', '=', $advertiserId)
                ->where('campaigns.id','=',$newCampaignArray['campaign_id'])
                ->where('campaigns.deal_id','=',$newCampaignArray['campaign_deal_id'])
                ->first([
                    'campaigns.id as campaign_id', 
                    'campaigns.*',
                    'campaign_payloads.*',
                    'deal_payloads.name as deal_payloads_name',
                    'medias.name as media_name', 
                    'demographics.name as demographics_name', 
                    'outlets.outlet_type as outlet_type', 
                    'day_parts.name as day_parts_name', 
                    'agencys.name as agencys_name', 
                    'clients.name as client_name', 
                    'brands.product_name as brand_name',
                    DB::raw("DATE_FORMAT(campaign_payloads.flight_start_date, '%m-%d-%Y') as flight_start_date"),
                    DB::raw("DATE_FORMAT(campaign_payloads.flight_end_date, '%m-%d-%Y') as flight_end_date"),
                    DB::raw("DATE_FORMAT(campaigns.valid_from, '%m-%d-%Y %H:%m:%s ') as valid_from"),
                    DB::raw("DATE_FORMAT(campaigns.valid_to, '%m-%d-%Y %H:%m:%s') as valid_to")])->toArray();
                
                $removeFields = ['media_id', 'demographic_id','brand_id','outlet_id','agency_id','daypart_id','sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
                $jsonCampaignList = array_diff_key($jsonCampaignArray, array_flip($removeFields));

                $currentDate = date('Y-m-d H:i:s');
                $dateChange = ( $newCampaignArray['date_change'] != '' )? Helper::changeDateFormateYMD($newCampaignArray['date_change']) : '';
                $updateCampaign = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                    ->join('demographics', 'campaigns.demographic_id', '=', 'demographics.id')->where('demographics.status','=', 1)
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
                        'campaigns.demographic_id' => $newCampaignArray['demographic_name'],
                        'campaign_payloads.demo_population' => $newCampaignArray['cpm_ipm_demo_population'],
                        'campaign_payloads.impressions' => $newCampaignArray['cpm_ipm_impressions'],
                        'campaign_payloads.grp' => $newCampaignArray['cpm_ipm_grp'],
                        'campaign_payloads.cpm' => $newCampaignArray['cpm_ipm_cpm'],
                        'campaign_payloads.inventory_length' => $newCampaignArray['ad_length'],
                        'campaigns.status' => 8,
                        'campaign_payloads.date_change' => $dateChange,
                        'campaign_payloads.change_by' => $advertiserId,
                        'demographics.updated_by' => $userId,
                        'demographics.updated_at' => $currentDate,
                        'campaigns.updated_by' => $userId,
                        'campaigns.updated_at' => $currentDate,
                        'campaign_payloads.updated_by' => $userId,
                        'campaign_payloads.updated_at' =>$currentDate,
                    ]);
 
                $jsonData = array_merge($jsonCampaignList, array('time_part' => implode(" ",$jsonDayOfDbData) ) );
                $fileName = $newCampaignArray['campaign_id'];
                Storage::put('/public/campaign/'.$fileName.'.json', json_encode($jsonData));
                Helper::activityLog('Update Campaign');
                if( $updateCampaign == 0 ){
                    $data = array( 'status' => 0 , 'message' => 'Record was not Updated.');
                }else{
                    $data = array( 'status' => 1 , 'message' => 'Campaign '.$newCampaignArray['campaign_id'].' Was Successfully Updated.');
                }
                return response()->json($data);  
            }
        }
    }
}
