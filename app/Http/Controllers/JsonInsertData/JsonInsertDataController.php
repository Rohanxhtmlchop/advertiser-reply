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
                $jsonHTML .= '<div class="col-md-6">';
                    $jsonHTML .='<div class="row">';
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Field Name</strong></span>';
                        $jsonHTML .='</div>';  
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Field Value</strong></span>';
                        $jsonHTML .='</div>';
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Mapping</strong></span>';
                        $jsonHTML .='</div>';
                    $jsonHTML .='</div>';
                $jsonHTML .='</div>';
                $jsonHTML .= '<div class="col-md-6">';
                    $jsonHTML .='<div class="row">';
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Field Name</strong></span>';
                        $jsonHTML .='</div>';  
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Field Value</strong></span>';
                        $jsonHTML .='</div>';
                        $jsonHTML .='<div class="col-md-4 form-group text-center">';
                            $jsonHTML .='<span><strong>Mapping</strong></span>';
                        $jsonHTML .='</div>';
                    $jsonHTML .='</div>';
                $jsonHTML .='</div>';
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
                    $jsonHTML .= '<div class="col-md-6" style="display:none;">';
                        $jsonHTML .='<div class="row ">';
                            $jsonHTML .='<div class="col-md-4 form-group">';
                                $jsonHTML .= $jsonInput;
                            $jsonHTML .='</div>';
                            $jsonHTML .='<div class="col-md-4 form-group mapping">';
                                $jsonHTML .='<select name="select_db_field[]" id="select_db_field_'.$fieldId.'" class="au-input au-input--full valid" aria-invalid="false">';
                                    $jsonHTML .='<option value="">Table Fields Option</option>';
                                    foreach($tableFieldsList as $tableFieldsListKey => $tableFieldsListVal ){
                                        $selected  = ( $extraFieldsKey == $tableFieldsListKey )? 'selected="selected"':'';
                                        $jsonHTML .='<option value="'.Helper::addUnderscore($tableFieldsListKey).'" attr-key="" '.$selected.'>'.Helper::removeUnderscore($tableFieldsListKey).'</option>';
                                    }
                                $jsonHTML .= '</select>';
                            $jsonHTML .='</div>';
                        $jsonHTML .='</div>';
                    $jsonHTML .='</div>';
                }
                foreach( $jsonFileFields as $jsonFileFieldsKey => $jsonFileFieldsVal ){
                    if( array_key_exists($jsonFileFieldsKey, $tableFieldsList) ) {
                        $jsonInput = Helper::getInput($tableFieldsList[$jsonFileFieldsKey], $fieldId, $jsonFileFieldsVal, 'field-exists');
                    } else {
                        $jsonInput = Helper::getInput('bigint', $fieldId, $jsonFileFieldsVal, 'field-not-exists');
                    }
                    $jsonFieldNameRemoveUndersocde = Helper::removeUnderscore($jsonFileFieldsKey);
                    $addEvenOddClass = ( $fieldId % 2) ? 'odd':'even';
                    $jsonHTML .= '<div class="col-md-6 '.$addEvenOddClass.' ">';
                        $jsonHTML .='<div class="row align-items-center">';
                            $jsonHTML .='<div class="col-md-4 form-group text-right">';
                                $jsonHTML .='<span><strong>'.$jsonFieldNameRemoveUndersocde.'</strong></span>';
                            $jsonHTML .='</div>';  
                            $jsonHTML .='<div class="col-md-4 form-group json-mapping-field">';
                                $jsonHTML .= $jsonInput;
                            $jsonHTML .='</div>';
                            $jsonHTML .='<div class="col-md-4 form-group mapping">';
                                $jsonHTML .='<select name="select_db_field[]" id="select_db_field_'.$fieldId.'" class="au-input au-input--full valid" aria-invalid="false">';
                                    $jsonHTML .='<option value="">Table Fields Option</option>';
                                    foreach($tableFieldsList as $tableFieldsListKey => $tableFieldsListVal ){
                                        $selected  = ( $jsonFileFieldsKey == $tableFieldsListKey )? 'selected="selected"':'';
                                        $jsonHTML .='<option value="'.Helper::addUnderscore($tableFieldsListKey).'" attr-key="" '.$selected.'>'.Helper::removeUnderscore($tableFieldsListKey).'</option>';
                                    }
                                $jsonHTML .= '</select>';
                            $jsonHTML .='</div>';
                        $jsonHTML .='</div>';
                    $jsonHTML .='</div>';
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
        if( count( $request['data'] ) > 0 ){
            $tableFields = Helper::tableOfFields($request['data']);
            $data = '';
            $tableName = $request['data'][0]['value'];
            if( $tableName == 'deal' ){
                $checkCount = DealPayload::where('name',$tableFields['deal_payload_name'])->count();
                if( $checkCount != 0 ){
                    $data = array( 'status' => 0 , 'message' => 'Deal Name already Exists.');
                }
            } else if( $tableName == 'campaign' ){
                $checkCampaignCount = CampaignPayload::where('name',$tableFields['campaign_payload_name'])->count();
                $checkDealPayloadCount = DealPayload::where('name',$tableFields['deal_name'])->count();
                $checkDealCount = DealPayload::join('deals', 'deals.deal_payload_id', '=', 'deals.id')
                ->where('deals.advertiser_id',$advertiserId)
                ->where('deals.client_id',$clientId)->count();
                if( $checkCampaignCount != 0 ){
                    $data = array( 'status' => 0 , 'message' => 'Campaign Name already Exists.');
                } else if( $checkDealPayloadCount == 0 ){
                    $data = array( 'status' => 0 , 'message' => 'Deal Name was not Exists.');
                } else if ( ( $checkDealCount == 0 ) && ( $checkDealCount > 1 )  ){
                    $data = array( 'status' => 0 , 'message' => 'Please Check Deal was Not added');
                }
            } else {
                $validFrom = date('m-d-Y hh:mm:ss', strtotime($tableFields['valid_from']));
                $validTo = date('m-d-Y hh:mm:ss', strtotime($tableFields['valid_to']));
                $flightStartDate = date('m-d-Y hh:mm:ss', strtotime($tableFields['flight_start_date']));
                $flightEndDate = date('m-d-Y hh:mm:ss', strtotime($tableFields['flight_end_date']));
                if( $validFrom >= $validTo ){ 
                    $data = array( 'status' => 0 , 'message' => 'Please check Valid To & From Date.');
                } else if( $flightStartDate >= $flightEndDate ){ 
                    $data = array( 'status' => 0 , 'message' => 'Please check Flight End & Start Date.');
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
            $data = array( 'status' => 0 , 'message' => 'Please check Data.');
        }
        return response()->json($data);  
    }
}
