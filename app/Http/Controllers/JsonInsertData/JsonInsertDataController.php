<?php

namespace App\Http\Controllers\JsonInsertData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CampaignPayload;
use App\Models\Campaigns;
use App\Models\DealPayload;
use App\Models\Deals;
use Illuminate\Support\Facades\Session;

class JsonInsertDataController extends Controller
{
    public function index(){
        $removeArray = Helper::jsonDataTableList();
        $tableNameList = [];
        if( count( $removeArray ) > 0  ){
            foreach($removeArray as $removeArrayKey => $removeArrayVal){
                $tableNameList[] = $removeArrayKey;
            }
        }

        $data = array( 
            'title' => 'JsonInsertData',
            'tableList' => $tableNameList,
        );
        return view( 'pages.jsoninsertdata.index', $data );
    }

    public function getJSONData( Request $request ){
        $tableName = $request['table_list'];
        $tempFilePath= $request->file('json_file')[0]->getPathName();
        $data = file_get_contents($tempFilePath);
        $jsonFileFields = (array) json_decode($data);
        $removeArray = Helper::jsonDataTableList();
        $tableFieldsList = $removeArray[$tableName];
        $jsonHTML = '';
        if( count( $jsonFileFields ) > 0 ){
            if( $jsonFileFields['name'] == $tableName ){
                unset($jsonFileFields['name']);
                $fieldId = 1;
                $extraFields = array(
                    'advertiser_name' => Session::get('advertiser_id'),
                    'client_name' => Session::get('clients_id'),
                    'media_name' => Session::get('media_line'),
                );
                foreach( $extraFields as $extraFieldsKey => $extraFieldsValue ){
                    if( array_key_exists($extraFieldsKey, $tableFieldsList) ) {
                        $jsonInput = Helper::getInput($tableFieldsList[$extraFieldsKey], $fieldId, $extraFieldsValue, 'field-exists');
                    } else {
                        $jsonInput = Helper::getInput('bigint', $fieldId, $extraFieldsValue, 'field-not-exists');
                    }
                    $jsonHTML .= '<tr class="tr-shadow" style="display:none;">';
                        //$jsonHTML .='<td class="">';
                            $jsonHTML .='<td class=" form-group">';
                                $jsonHTML .= $jsonInput;
                            $jsonHTML .='</td>';
                            $jsonHTML .='<td class="form-group mapping">';
                                $jsonHTML .='<select name="select_db_field[]" id="select_db_field_'.$fieldId.'" class="au-input au-input--full valid" aria-invalid="false">';
                                    $jsonHTML .='<option value="">Table Fields Option</option>';
                                    foreach($tableFieldsList as $tableFieldsListKey => $tableFieldsListVal ){
                                        $selected  = ( $extraFieldsKey == $tableFieldsListKey )? 'selected="selected"':'';
                                        $jsonHTML .='<option value="'.Helper::addUnderscore($tableFieldsListKey).'" attr-key="" '.$selected.'>'.Helper::removeUnderscore($tableFieldsListKey).'</option>';
                                    }
                                $jsonHTML .= '</select>';
                            $jsonHTML .='</td>';
                        //$jsonHTML .='</div>';
                    $jsonHTML .='</tr>';
                }
                foreach( $jsonFileFields as $jsonFileFieldsKey => $jsonFileFieldsVal ){
                    if( array_key_exists($jsonFileFieldsKey, $tableFieldsList) ) {
                        $jsonInput = Helper::getInput($tableFieldsList[$jsonFileFieldsKey], $fieldId, $jsonFileFieldsVal, 'field-exists');
                    } else {
                        $jsonInput = Helper::getInput('bigint', $fieldId, $jsonFileFieldsVal, 'field-not-exists');
                    }
                    $jsonFieldNameRemoveUndersocde = Helper::removeUnderscore($jsonFileFieldsKey);
                    $addEvenOddClass = ( $fieldId % 2) ? 'odd':'even';
                    $jsonHTML .= '<tr class="tr-shadow '.$addEvenOddClass.' ">';
                       // $jsonHTML .='<div class="row align-items-center">';
                            $jsonHTML .='<td class="form-group text-right" attr-name="'.$jsonFileFieldsKey.'">';
                                $jsonHTML .='<span><strong>'.$jsonFieldNameRemoveUndersocde.'</strong></span>';
                            $jsonHTML .='</td>';  
                            $jsonHTML .='<td class="form-group validation">';
                                $jsonHTML .= Helper::getValidationContent($jsonFileFieldsKey, $jsonFileFieldsVal);
                            $jsonHTML .='</td>';
                            $jsonHTML .='<td class="form-group json-mapping-field">';
                                $jsonHTML .= $jsonInput;
                            $jsonHTML .='</td>';
                            $jsonHTML .='<td class="form-group mapping">';
                                $jsonHTML .='<select name="select_db_field[]" id="select_db_field_'.$fieldId.'" class="au-input au-input--full valid form-control" aria-invalid="false">';
                                    $jsonHTML .='<option value="">Table Fields Option</option>';
                                    foreach($tableFieldsList as $tableFieldsListKey => $tableFieldsListVal ){
                                        $selected  = ( $jsonFileFieldsKey == $tableFieldsListKey )? 'selected="selected"':'';
                                        $jsonHTML .='<option value="'.Helper::addUnderscore($tableFieldsListKey).'" attr-key="" '.$selected.'>'.Helper::removeUnderscore($tableFieldsListKey).'</option>';
                                    }
                                $jsonHTML .= '</select>';
                            $jsonHTML .='</td>';
                            $jsonHTML .='<td class="form-group error-message '.$jsonFileFieldsKey.'">';
                            $jsonHTML .='<i class="far fa-check-circle" style="color:green"></i>';
                            $jsonHTML .='</td>';
                       // $jsonHTML .='</div>';
                    $jsonHTML .='</tr>';
                    $fieldId++;
                }
                return json_encode($jsonHTML);
            } else{
                $data = array( 'status' => 0 , 'message' => 'Please Check JSON field name & Table field name');
                return response()->json($data);    
            }
        } else{
            $data = array( 'status' => 0 , 'message' => 'Please Check JSON file');
            return response()->json($data);  
        }
    }

    public function jsonMappingData( Request $request ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        if( count( $request['data'] ) > 0 ){
            $tableFields = Helper::tableOfFields($request['data']);
            $data = '';
            $tableName = $request['data'][0]['value'];
            if( $tableName == 'deal' ){
                $checkCount = DealPayload::where('name',$tableFields['deal_payload_name'])->count();
                if( $checkCount != 0 ){
                    $data = array( 'status' => 0 , 'class' => 'deal_payload_name', 'message' => 'Deal Name already Exists.');
                }
            } else if( $tableName == 'campaign' ){
                $checkCampaignCount = CampaignPayload::where('name',$tableFields['campaign_payload_name'])->count();
                $checkDealPayloadCount = DealPayload::where('name',$tableFields['deal_name'])->count();
                $checkDealCount = DealPayload::join('deals', 'deals.deal_payload_id', '=', 'deals.id')
                ->where('deals.advertiser_id',$advertiserId)
                ->where('deals.client_id',$clientId)->count();

                $checkCampaignCount = CampaignPayload::join('campaigns', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                    ->where('campaign_payloads.name','=',$tableFields['campaign_payload_name'])
                    ->where('campaigns.advertiser_id','=',$advertiserId)
                    ->where('campaigns.client_id','=',$clientId)
                    ->where('campaigns.media_id','=',$mediasId)
                    ->count();
                $checkDealPayloadCount = DealPayload::join('deals', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                    ->where('deal_payloads.name','=',$tableFields['deal_name'])
                    ->where('deals.advertiser_id','=',$advertiserId)
                    ->where('deals.client_id','=',$clientId)
                    ->where('deals.media_id','=',$mediasId)
                    ->count();
                if( ( !is_numeric($tableFields['campaign_payload_name']) ) && ( $checkCampaignCount != 0 ) ){
                    $data = array( 'status' => 0 , 'class' => 'campaign_payload_name', 'message' => 'Campaign Name already Exists.');
                } else if( $checkDealPayloadCount == 0 ){
                    $data = array( 'status' => 0 ,  'class' => 'deal_name', 'message' => 'Deal Name was not Exists. Please create Deal');
                } 
            } else {
                $validFrom = date('m-d-Y hh:mm:ss', strtotime($tableFields['valid_from']));
                $validTo = date('m-d-Y hh:mm:ss', strtotime($tableFields['valid_to']));
                $flightStartDate = date('m-d-Y hh:mm:ss', strtotime($tableFields['flight_start_date']));
                $flightEndDate = date('m-d-Y hh:mm:ss', strtotime($tableFields['flight_end_date']));
                if( $validFrom >= $validTo ){ 
                    $data = array( 'status' => 0 , 'class' => 'flight_start_date',  'message' => 'Please check Valid To & From Date.');
                } else if( $flightStartDate >= $flightEndDate ){ 
                    $data = array( 'status' => 0 , 'class' => 'flight_end_date',  'message' => 'Please check Flight End & Start Date.');
                }
            }
        
            if( $data != '' ){
                return response()->json($data);  
            } else {
                $tableHTML = '';
                foreach( $tableFields as $tableFieldsKey => $tableFieldsVal ){
                    $tableFieldName = Helper::removeUnderscore($tableFieldsKey);
                    $tableHTML .='<tr class="tr-shadow">';
                        $tableHTML .='<th class="new-campaign-id">'.$tableFieldName.'</th>';
                        $tableHTML .='<td class="new-campaign-name">'.$tableFieldsVal.'</td>';
                    $tableHTML .='</tr>';
                }
                return response()->json($tableHTML);  
            }
        }
    }

    public function InsertjsonData( Request $request ){
        $advertiserId = Session::get('advertiser_id');
        $clientId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $userId = Session::get('user_id');
        if( count( $request['data'] ) > 0 ){
            $tableFields = Helper::tableOfFields($request['data']);
            $removeArray = Helper::jsonDataTableList();
            $tableName = $request['data'][0]['value'];
            if( $tableName == 'deal' ){
                $payloadTableName = 'deal_payloads';
                $payloadFieldName = 'deal_payload';
            } else if( $tableName == 'campaign' ){
                $payloadTableName = 'campaign_payloads';
                $payloadFieldName = 'campaign_payload';
            }
            $dealPayloadTableFieldArray = Helper::jsonDataGetSpecificTableList($payloadTableName);
            $dealPayloadInsertArray = [];
            if( count( $dealPayloadTableFieldArray ) > 0 ){
                foreach( $dealPayloadTableFieldArray as $dealPayloadTableFieldVal){
                    if( $dealPayloadTableFieldVal == 'name'){
                        $dealPayloadInsertArray[$dealPayloadTableFieldVal] = $tableFields[$payloadFieldName.'_name'];
                    } else{ 
                        $fieldValue = ( array_key_exists($dealPayloadTableFieldVal,$tableFields) ) ? $tableFields[$dealPayloadTableFieldVal] : null;
                        $dealPayloadInsertArray[$dealPayloadTableFieldVal] = $fieldValue;
                    }
                }
            }
            $dealPayloadFieldArray = Helper::addfieldsValue($dealPayloadInsertArray);
          
            if( !is_numeric($dealPayloadFieldArray['name']) ){
                if( $tableName == 'deal' ){
                    $dealpayloadInsert = DealPayload::create($dealPayloadFieldArray);
                } else if($tableName == 'campaign'){
                    $checkCount = DealPayload::where('name', $tableFields['deal_name'])->count();
                    if( $checkCount == 0 ){
                        $data = array( 'status' => 0 , 'message' => 'Deal Name not Exists.');
                    } else {
                        $dealpayloadInsert = CampaignPayload::create($dealPayloadFieldArray);
                    }
                }

                if( !empty( $dealpayloadInsert->id ) ){
                    $insertPayloadId = $dealpayloadInsert->id;
                    $dealTableFieldArray = Helper::jsonDataGetSpecificTableList($tableName);
                    $dealInsertArray = [];
                    if( count( $dealTableFieldArray ) > 0 ){
                        foreach( $dealTableFieldArray as $dealTableFieldVal){
                            if( str_contains($dealTableFieldVal, '_id')){
                                $newName = str_replace("_id","_name",$dealTableFieldVal);
                                $dealInsertArray[$dealTableFieldVal] = $tableFields[$newName];
                            } else{ 
                                $fieldValue = ( array_key_exists($dealTableFieldVal,$tableFields) ) ? $tableFields[$dealTableFieldVal] : null;
                                $dealInsertArray[$dealTableFieldVal] = $fieldValue;
                            }
                        }
                        $dealFieldArray = Helper::addfieldsValue($dealInsertArray);
                        $dealFieldArray['advertiser_id'] = $advertiserId;
                        $dealFieldArray['client_id'] = $clientId;
                        $dealFieldArray[$payloadFieldName.'_id'] = $insertPayloadId;
                        $inserData = Helper::insertData($dealFieldArray);
                        
                        if( $tableName == 'deal' ){
                            $dealInsert = Deals::create($inserData);
                        } else{
                            $dealInsert = Campaigns::create($inserData);
                        }
                        if( !empty( $dealInsert->id ) ){
                            $data = array( 'status' => 1 , 'message' => 'Data Successfully Inserted.');
                        } else { 
                            $data = array( 'status' => 0 , 'message' => 'Data Was Not Inserted.');
                        }
                    } else { 
                        $data = array( 'status' => 0 , 'message' => 'Data Was Not Inserted.');
                    }
                } else { 
                    $data = array( 'status' => 0 , 'message' => 'Data Was Not Inserted.');
                }
            } else {
                $dealTableFieldArray = Helper::jsonDataGetSpecificTableList($tableName);
                $dealPayloadFieldArray['updated_by'] = $userId;
                $dealPayloadFieldArray['change_by'] = $userId;
                $dealPayloadFieldArray['date_change'] = date('Y-m-d');
                $dealPayloadFieldName = $dealPayloadFieldArray['name'];
                unset($dealPayloadFieldArray['name']);
                unset($dealPayloadFieldArray['created_at']);
                unset($dealPayloadFieldArray['created_by']);
                $dealInsertArray = [];
                foreach( $dealTableFieldArray as $dealTableFieldVal){
                    if( str_contains($dealTableFieldVal, '_id')){
                        $newName = str_replace("_id","_name",$dealTableFieldVal);
                        $dealInsertArray[$dealTableFieldVal] = $tableFields[$newName];
                    } else{ 
                        $fieldValue = ( array_key_exists($dealTableFieldVal,$tableFields) ) ? $tableFields[$dealTableFieldVal] : null;
                        $dealInsertArray[$dealTableFieldVal] = $fieldValue;
                    }
                }
                $dealFieldArray = Helper::addfieldsValue($dealInsertArray);
                $dealFieldArray['advertiser_id'] = $advertiserId;
                $dealFieldArray['client_id'] = $clientId;
                $inserData = Helper::insertData($dealFieldArray);
                if( $tableName == 'campaign'){
                    $campaignIdArray = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                    ->where('campaigns.advertiser_id', '=', $advertiserId)
                    ->where('campaigns.client_id','=',$clientId)
                    ->where('campaigns.media_id', '=', $mediasId)
                    ->where('campaigns.id', '=', $dealPayloadFieldName)
                    ->first(['campaign_payloads.id as cam_pay_id'])
                    ->toArray();

                    $updateCampaignPayload = CampaignPayload::where('id','=',$campaignIdArray['cam_pay_id'])->update($dealPayloadFieldArray);
                    $updateCampaignId = $inserData['campaign_payload_id'];
                    $inserData['updated_by'] = $userId;
                    unset($inserData['campaign_payload_id']);
                    unset($inserData['created_at']);
                    unset($inserData['created_by']);
                    if( $updateCampaignPayload == 1 ){
                        $updateCampaign = Campaigns::where('id','=',$updateCampaignId)
                        ->where('advertiser_id','=',$advertiserId)
                        ->where('client_id','=',$clientId)
                        ->where('media_id','=',$mediasId)
                        ->update($inserData);
                        if( $updateCampaign == 1 ){
                            $data = array( 'status' => 1 , 'message' => 'Data Successfully Updated.');
                        }
                    }   
                }

                if( $tableName == 'deal'){
                    $campaignIdArray = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                    ->where('deals.advertiser_id', '=', $advertiserId)
                    ->where('deals.client_id','=',$clientId)
                    ->where('deals.media_id', '=', $mediasId)
                    ->where('deals.id', '=', $dealPayloadFieldName)
                    ->first(['deal_payloads.id as cam_pay_id'])
                    ->toArray();
                    $updateCampaignPayload = DealPayload::where('id','=',$campaignIdArray['cam_pay_id'])->update($dealPayloadFieldArray);
                    $updateDealId = $inserData['deal_payload_id'];
                    $inserData['updated_by'] = $userId;
                    unset($inserData['deal_payload_id']);
                    unset($inserData['created_at']);
                    unset($inserData['created_by']);
                    if( $updateCampaignPayload == 1 ){
                        $updateCampaign = Deals::where('id','=',$updateDealId)
                        ->where('advertiser_id','=',$advertiserId)
                        ->where('client_id','=',$clientId)
                        ->where('media_id','=',$mediasId)
                        ->update($inserData);
                        if( $updateCampaign == 1 ){
                            $data = array( 'status' => 1 , 'message' => 'Data Successfully Updated.');
                        }
                    }   
                }
            }         
        } else { 
            $data = array( 'status' => 1 , 'message' => 'Data Successfully Updated.');
        }
        return response()->json($data);  
    }
}
