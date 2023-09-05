<?php

namespace App\Http\Controllers\Deal;

use App\Http\Controllers\Controller;
use App\Models\Deals;
use App\Models\DealPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class DealController extends Controller
{
    
    /************************************* Deal List  **************************************************/

    public function dealTableRecord( $status = '' ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');

        $dealList = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
            ->leftJoin('campaigns', 'campaigns.deal_id', '=', 'deals.id')
            ->join('status', 'deals.status', '=', 'status.id')
            ->when($status, function ($query) use ($status) {
                return $query->where('deals.status','=', $status);
            })
            ->where('deals.advertiser_id', '=', $advertiserId)
            ->where('deals.client_id','=', $clientId)
            ->where('deals.media_id','=', $mediasId)
            ->join('day_parts', 'deals.daypart_id', '=', 'day_parts.id')->where('day_parts.status','=', 1)
            ->join('brands', 'deals.brand_id', '=', 'brands.id')->where('brands.status','=', 1)
            ->orderBy('deals.id', 'asc');

        $dealTableData =  $dealList->get([
            'deals.id as deal_id',
            'campaigns.id as campaign_number', 
            'deal_payloads.name as deal_name', 
            'day_parts.name as day_time', 
            'brands.product_name as brand_name',
            'deal_payloads.inventory_type as inventory_type', 
            'deal_payloads.inventory_length as inventory_length', 
            'deal_payloads.rate as rate', 
            'deal_payloads.rc_rate as rc_rate', 
            'deal_payloads.rc_rate_percentage as rc_rate_percentage', 
            'deal_payloads.total_avil as total_avil', 
            'deal_payloads.total_unit as total_unit', 
            'deal_payloads.hh_rating as hh_rating', 
            'deal_payloads.hh_ss as hh_ss', 
            'deal_payloads.hh_cpm as hh_cpm', 
            'deal_payloads.hh_univ as hh_univ', 
            'deal_payloads.a25_49_rating as a25_49_rating', 
            'deal_payloads.a25_49_ss as a25_49_ss', 
            'deal_payloads.a25_49_cpm as a25_49_cpm', 
            'deal_payloads.a25_49_univ as a25_49_univ',
            'status.name as status',
            ])->toArray();

        $dealDayTableData =  $dealList->get([
            'deal_payloads.sunday as sunday', 
            'deal_payloads.monday as monday', 
            'deal_payloads.tuesday as tuesday', 
            'deal_payloads.wednesday as wednesday', 
            'deal_payloads.thursday as thursday', 
            'deal_payloads.friday as friday',
            'deal_payloads.saturday as saturday'
            ])->toArray();
        $dayOfArray = array( 'sunday' => 'S', 'monday' => 'M','tuesday' => 'T','wednesday' => 'W', 'thursday' => 'T', 'friday' => 'F', 'saturday' => 'S' );
        if( count ( $dealDayTableData ) > 0 ){
            foreach( $dealDayTableData as $dealDayTableKey => $dealDayTableVal ){
                foreach( $dealDayTableVal as $dealSingleDayKey => $dealSingleDayVal ){
                    if( ( $dealSingleDayVal == 1 ) && ( array_key_exists( $dealSingleDayKey, $dayOfArray ) ) ){
                        $dealDayTableVal[$dealSingleDayKey] =  $dayOfArray[$dealSingleDayKey];
                    }
                }
                $dealDayTableData[$dealDayTableKey] = implode(" ", $dealDayTableVal);
            }
        }
        $dealTableArray = array(  'tableData' => $dealTableData, 'dayTableData' => $dealDayTableData );
        return $dealTableArray;
    }
    public function index(){
        $dealTableTitle = Helper::dealViewTableName();
        $dealStatusArray = Helper::dealStatusArray();
        $dealViewArray = Helper::dealViewArray();
        $dealResult = array( 
                'title' => 'Deal',
                'tableTitle' => $dealTableTitle,
                'dealStatus' => $dealStatusArray,
                'dealView' => $dealViewArray,
            );
        $dealResult = array_merge( $dealResult, DealController::dealTableRecord() );
        return view( 'pages.deal.index', $dealResult );
    }

    public function postStatus( Request $request ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $dealView = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
        ->where('deals.advertiser_id', '=', $advertiserId)
        ->where('deals.client_id','=', $clientId)
        ->where('deals.media_id','=', $mediasId)
        ->when($request['data'], function ($query) use ($request) {
            return $query->where('deals.status','=', $request['data']);
        })
        ->first( array(
            DB::raw('SUM(deal_payloads.rate) as rate'),
            DB::raw('SUM(deal_payloads.cpm) as cpm'),
            DB::raw('SUM(deal_payloads.impressions) as impressions'),
            DB::raw('SUM(deal_payloads.grp) as grp'),
            DB::raw('SUM(deal_payloads.deal_unit) as deal_unit'),
        ))->toArray();

        $dealViewTable = DealController::dealTableRecord( $request['data'] );
        $dealViewTableHtml = '';
        foreach( $dealViewTable['tableData'] as $key => $tableDetailRowVal ){
            $dealViewTableHtml .= '<tr class="tr-shadow">';
                foreach( $tableDetailRowVal as $tableRowDetailKey => $tableRowDetail ){
                    if(  $tableRowDetailKey == 'day_time' ){
                        $dealViewTableHtml .='<td class="'.  $tableRowDetailKey .'">'. $dealViewTable['dayTableData'][$key] . $tableRowDetail .'</td>';
                    } else if(  $tableRowDetailKey == 'campaign_number' ){
                        if( $tableRowDetail != ''){
                            $dealViewTableHtml .='<td class="'. $tableRowDetailKey .'"><a href="'. URL::to('/campaign/edit/'.base64_encode($tableRowDetail)) .'">'. $tableRowDetail .'</a></td>';
                        } else {
                            $dealViewTableHtml .='<td class="'. $tableRowDetailKey .'">-</td>';
                        }
                    } else {
                        $dealViewTableHtml .='<td class="'. $tableRowDetailKey .'">'. $tableRowDetail .'</td>';
                    }
                }    
                $dealViewTableHtml .='</tr>';
        }
        return response()->json(array( 'deal_view_data' => $dealView, 'deal_table_html' => $dealViewTableHtml ));  
    }
}
