<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Campaigns;
use App\Models\DealPayload;
use App\Models\Deals;
use App\Models\Demographic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Helpers\Helper;
use App\Models\Agencys;
use App\Models\Brands;
use App\Models\Locations;
use App\Models\Outlets;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Medias;
use Jenssegers\Agent\Facades\Agent;

class DashboardController extends Controller
{

    public function removeDubplicateRow( $duplicateArrayOfData ){
        if( $duplicateArrayOfData ){
            $dataOfCollection = collect($duplicateArrayOfData);
            $dataFilter = $dataOfCollection->unique('id');
            return $dataFilter->all();
        }else{
            return false;
        }
    }

    public function filterAllDropdownData(){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $dealData = Deals::join('deal_payloads', 'deal_payloads.id', '=', 'deals.deal_payload_id')->where('deals.advertiser_id', '=' , $advertiserId)->where('deals.client_id', '=' , $clientId )->where('deals.media_id', '=' , $mediasId )
                    ->get(['deal_payloads.name as deal_payloads_name','deals.id as deal_payloads_id'])->toArray();
        $campaignData = Campaigns::join('campaign_payloads', 'campaign_payloads.id', '=', 'campaigns.campaign_payload_id')->where('campaigns.advertiser_id', '=' , $advertiserId)->where('campaigns.client_id', '=' , $clientId )->where('campaigns.media_id', '=' , $mediasId )
                ->get(['campaigns.id as campaign_id','campaign_payloads.name as campaign_name'])->toArray();
        $demographicsList = Demographic::where('status', '=' , 1)->where('client_id', '=' , $clientId )->get(['demographics.id as demographics_id','demographics.name as demographics_name'])->toArray();
        $outletList = Outlets::where('status', '=' , 1)->where('client_id', '=' , $clientId )->get(['outlets.id as outlets_id','outlets.outlet_type as outlets_name'])->toArray();
        $agencyList = Agencys::where('status', '=' , 1)->where('client_id', '=' , $clientId )->get(['agencys.id as agencys_id','agencys.name as agencys_name'])->toArray();
        $locationList = Locations::where('status', '=' , 1)->where('client_id', '=' , $clientId )->get(['locations.id as locations_id','locations.name as locations_name'])->toArray();
        $brandList = Brands::where('status', '=' , 1)->where('advertiser_id', '=', $advertiserId)->where('client_id', '=' , $clientId )->get(['brands.id as brands_id','brands.product_name as brands_name'])->toArray();
       
        $dealDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $dealData , 'deal_payloads', 'Deal', '', 0);
        $campaignDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $campaignData , 'campaign', 'Campaign', '', 1);
        $demographicsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $demographicsList , 'demographics', 'Demographics', '', 0);
        $outletsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $outletList , 'outlets', 'Out Let', '', 0);
        $agencysDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $agencyList , 'agencys', 'Agency', '', 0);
        $locationsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $locationList , 'locations', 'Location', '', 0);
        $brandsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $brandList , 'brands', 'Brand', '', 0);
        $filterDropDownData = array(
            'deal'  =>  $dealDropdownOptions,
            'campaign'  =>  $campaignDropdownOptions,
            'demographic'   =>  $demographicsDropdownOptions,
            'outlet'    =>  $outletsDropdownOptions,
            'agency'    =>  $agencysDropdownOptions,
            'location'  =>  $locationsDropdownOptions,
            'brand' =>  $brandsDropdownOptions,
        );
        return $filterDropDownData;
    }
    public function index( Request $request ){
        
       // $medias = Campaigns::with('medias')->with('brands')->get()->toArray();
        $dealStatusArray = Helper::dealStatusArray();
        $dealViewArray = Helper::dealViewArray();
        $dashboardData = array( 
            'title' => 'Dashboard',
            'dealStatus' => $dealStatusArray,
            'dealView' => $dealViewArray
        );     
        if (Auth::user()) {                   
            return view('pages.dashboard.index', $dashboardData);
        } else{
            $mediaList = Medias::where('status','=','1')->get(['id','name'])->toArray();
        $mediaData = array( 
        'data' => array( 'mediaList' => $mediaList ),
        'title' => 'Login' );
            return view('pages.login.index', $dashboardData);
        }
    }

    public function dashboardDealFilter( Request $request){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        if( $request->data !== null ){
            $data = $request->data;
            $results = Deals::join('deal_payloads','deal_payloads.id', '=', 'deals.deal_payload_id' )
                ->join('campaigns', 'campaigns.deal_id', '=', 'deals.id')->where( 'campaigns.delete','=',0)
                ->where('deals.advertiser_id','=', $advertiserId)
                ->where('deals.client_id','=', $clientId)
                ->where('deals.media_id','=', $mediasId)
                ->when($data['deal_no'], function ($query) use ($data) {
                    return $query->where('deals.id','=', $data['deal_no']);
                })
                ->when($data['campaign'], function ($query) use ($data) {
                    return $query->where('campaigns.id','=', $data['campaign']);
                })
                ->when($data['demographics'], function ($query) use ($data) {
                    return $query->where('deals.demographic_id','=', $data['demographics']);
                })
                ->when($data['outlet'], function ($query) use ($data) {
                    return $query->where('deals.outlet_id','=', $data['outlet']);
                })
                ->when($data['agency'], function ($query) use ($data) {
                    return $query->where('deals.agency_id','=', $data['agency']);
                })
                ->when($data['location'], function ($query) use ($data) {
                    return $query->where('deals.location_id','=', $data['location']);
                })
                ->when($data['brand'], function ($query) use ($data) {
                    return $query->where('deals.brand_id','=', $data['brand']);
                })
                ->when($data['daterange'], function ($query) use ($data) {
                    return $query->whereDate('deal_payloads.flight_start_date', '>=', $data['start_daterange'])->whereDate('deal_payloads.flight_end_date', '<=', $data['end_daterange']);
                })->first( array(
                    DB::raw('SUM(deal_payloads.rate) as rate'),
                    DB::raw('SUM(deal_payloads.cpm) as cpm'),
                    DB::raw('SUM(deal_payloads.impressions) as impressions'),
                    DB::raw('SUM(deal_payloads.grp) as grp'),
                    DB::raw('SUM(deal_payloads.deal_unit) as deal_unit'),
                  ));
             
            $filterData = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->join('campaigns', 'campaigns.deal_id', '=', 'deals.id')->where( 'campaigns.delete','=',0)
                ->join('campaign_payloads', 'campaign_payloads.id', '=', 'campaigns.campaign_payload_id')->where( 'campaigns.delete','=',0)
                ->join('demographics', 'demographics.id', '=', 'deals.demographic_id')->where( 'demographics.status','=',1)
                ->join('outlets', 'outlets.id', '=', 'deals.outlet_id')->where( 'outlets.status','=',1)
                ->join('agencys', 'agencys.id', '=', 'deals.agency_id')->where( 'agencys.status','=',1)
                ->join('locations', 'locations.id', '=', 'deals.location_id')->where( 'locations.status','=',1)
                ->join('brands', 'brands.id', '=', 'deals.brand_id')->where( 'brands.status','=',1)
                ->where('deals.advertiser_id','=', $advertiserId)
                ->where('deals.client_id','=', $clientId)
                ->where('deals.media_id','=', $mediasId)
                ->when($data['deal_no'], function ($query) use ($data) {
                    return $query->where('deals.id','=', $data['deal_no']);
                })->when($data['campaign'], function ($query) use ($data) {
                    return $query->where('campaigns.id','=', $data['campaign']);
                })->when($data['demographics'], function ($query) use ($data) {
                    return $query->where('deals.demographic_id','=', $data['demographics']);
                })->when($data['outlet'], function ($query) use ($data) {
                    return $query->where('deals.outlet_id','=', $data['outlet']);
                })->when($data['agency'], function ($query) use ($data) {
                    return $query->where('deals.agency_id','=', $data['agency']);
                })->when($data['location'], function ($query) use ($data) {
                    return $query->where('deals.location_id','=', $data['location']);
                })->when($data['brand'], function ($query) use ($data) {
                    return $query->where('deals.brand_id','=', $data['brand']);
                })->when($data['daterange'], function ($query) use ($data) {
                    return $query->whereDate('deal_payloads.flight_start_date', '>=', $data['start_daterange'])->whereDate('deal_payloads.flight_end_date', '<=', $data['end_daterange']);
                })->get([
                    'deal_payloads.name as deal_payloads_name',
                    'deals.id as deal_payloads_id',
                    'campaigns.id as campaign_id',
                    'campaign_payloads.name as campaign_name',
                    'demographics.id as demographics_id',
                    'demographics.name as demographics_name',
                    'outlets.id as outlets_id',
                    'outlets.outlet_type as outlets_name',
                    'agencys.id as agencys_id',
                    'agencys.name as agencys_name',
                    'locations.id as locations_id',
                    'locations.name as locations_name',
                    'brands.id as brands_id',
                    'brands.product_name as brands_name',
                    
                ])->toArray();
            $filterDropDownData = '';
            if( count( $filterData ) !== 0 ){
                $dealDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'deal_payloads', 'Deal', $data['deal_no'], 0);
                $campaignDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'campaign', 'Campaign', $data['campaign'], 1);
                $demographicsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'demographics', 'Demographics', $data['demographics'], 0);
                $outletsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'outlets', 'Out Let', $data['outlet'], 0);
                $agencysDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'agencys', 'Agency', $data['agency'], 0);
                $locationsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'locations', 'Location', $data['deal_no'], 0);
                $brandsDropdownOptions = Helper::dashboardInterconnectDropdownHtml( $filterData , 'brands', 'Brand', $data['brand'], 0);
                $filterDropDownData = array(
                    'deal'  =>  $dealDropdownOptions,
                    'campaign'  =>  $campaignDropdownOptions,
                    'demographic'   =>  $demographicsDropdownOptions,
                    'outlet'    =>  $outletsDropdownOptions,
                    'agency'    =>  $agencysDropdownOptions,
                    'location'  =>  $locationsDropdownOptions,
                    'brand' =>  $brandsDropdownOptions,
                );
            }
            
            $response = array(
                    'result' => $results, 
                    'dropdown' => ''
            );
            return response()->json($response);  

        }else{
            $results = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->join('campaigns', 'campaigns.deal_id', '=', 'deals.id')->where( 'campaigns.delete','=',0)
                ->where('deals.advertiser_id','=', $advertiserId)
                ->where('deals.client_id','=', $clientId)
                ->where('deals.media_id','=', $mediasId)->first( array(
                    DB::raw('SUM(deal_payloads.rate) as rate'),
                    DB::raw('SUM(deal_payloads.cpm) as cpm'),
                    DB::raw('SUM(deal_payloads.impressions) as impressions'),
                    DB::raw('SUM(deal_payloads.grp) as grp'),
                    DB::raw('SUM(deal_payloads.deal_unit) as deal_unit'),
                  ));
            $response = array(
                'result' => $results, 
                'dropdown' => DashboardController::filterAllDropdownData()
            );
            return response()->json($response);        
        }
    }

    public function dashboardAdvertiserFilter( Request $request){
        $allStatusArray = Helper::dealStatusArray();
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $advertiserDashboardArray = [];
        if( $request->data !== null ){
            $data = $request->data;
            if( count( $allStatusArray ) > 0 ){
                foreach( $allStatusArray as $allStatusArrayKey => $allStatusArrayVal  ){
                    $statusName = strtolower($allStatusArrayVal['name']);
                    $data = $request->data;
                    $advertiserData = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                                    ->whereDate('deal_payloads.flight_start_date', '>=', $data['start_date'])
                                    ->whereDate('deal_payloads.flight_end_date', '<=', $data['end_date'])
                                    ->where('deals.advertiser_id','=', $advertiserId)
                                    ->where('deals.client_id','=', $clientId)
                                    ->where('deals.media_id','=', $mediasId)
                                    ->where('deals.status',$allStatusArrayVal['id'])->count();
                    $advertiserDashboardArray[$statusName] = $advertiserData;
                }
            }
        }else{
            if( count( $allStatusArray ) > 0 ){
                foreach( $allStatusArray as $allStatusArrayKey => $allStatusArrayVal  ){
                    $statusName = strtolower($allStatusArrayVal['name']);
                    $data = $request->data;
                    $advertiserData = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                                        ->where('deals.advertiser_id','=', $advertiserId)
                                        ->where('deals.client_id','=', $clientId)
                                        ->where('deals.media_id','=', $mediasId)
                                        ->where('deals.status',$allStatusArrayVal['id'])->count();
                    $advertiserDashboardArray[$statusName] = $advertiserData;
                }
            }
        }
        $statusArray = $advertiserDashboardArray;
        return response()->json($statusArray);  
    }
}
