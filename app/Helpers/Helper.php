<?php 

namespace App\Helpers;

use App\Models\Advertiser;
use App\Models\Agencys;
use App\Models\Brands;
use App\Models\UserActivitiesLog;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\UserHistories;
use App\Models\Statu;
use App\Models\Deals;
use App\Models\DealPayload;
use App\Models\Campaigns;
use App\Models\CampaignPayload;
use App\Models\DayParts;
use App\Models\Demographic;
use App\Models\Locations;
use App\Models\Medias;
use App\Models\Outlets;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Helper{
    public static function activityLog( $message = null ){
        $logActivity = array(
            'message' => $message,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'agent' => request()->header('user-agent'),
            'user_id' => auth()->check() ? auth()->user()->id : 1
        );
        $userActivitiesCreate = UserActivitiesLog::create($logActivity);
        if( $userActivitiesCreate ){
            return true;
        }else{
            $message = 'Data was not Inserted.';
            $data = array( 'status' => 0 , 'class' => '', 'message' => $message );
            return response()->json($data);
        }
    }
    public static function userHistory(){
        $currentDate = date('Y-m-d H:i:s');
        $userLog = array(
            'user_id' =>  auth()->check() ? auth()->user()->id : 1,              
            'user_logged_in_timestamp' => $currentDate,
            'ip_address' => request()->ip(),
            'created_by' => auth()->check() ? auth()->user()->id : 1,
        );
        $userHistory = UserHistories::create($userLog);
        if( $userHistory ){
            return true;
        }else{
            $message = 'Data was not Inserted.';
            $data = array( 'status' => 0 , 'class' => '', 'message' => $message );
            return response()->json($data);
        }
    }

    public static function dealStatusArray(){
        $dealStatus = Statu::orderBy('id', 'asc')->get()->toArray();
        /*$dealStatus = array(
            array(
                'id' => 1,
                'name' => 'Inflight',
                'slug' => 'inflight',
                'icon' => 'fa fa-plane',
                'background' => 'overview-item--c1'
            ),
            array(
                'id' => 2,
                'name' => 'Proposal',
                'slug' => 'proposal',
                'icon' => 'fa fa-file',
                'background' => 'overview-item--c2'
            ),
            array(
                'id' => 3,
                'name' => 'Ended',
                'slug' => 'ended',
                'icon' => 'fa fa-clock-o',
                 'background' => 'overview-item--c3'
            ),
            array(
                'id' => 4,
                'name' => 'Approved',
                'slug' => 'approved',
                'icon' => 'fa fa-thumbs-up',
                 'background' => 'overview-item--c4'
            ),
            array(
                'id' => 5,
                'name' => 'Ordered',
                'slug' => 'order',
                'icon' => 'fa fa-shopping-cart',
                 'background' => 'overview-item--c1'
            ),
            array(
                'id' => 6,
                'name' => 'Planning',
                'slug' => 'planning',
                'icon' => 'fa fa-list-alt',
                 'background' => 'overview-item--c2'
            ),
            array(
                'id' => 7,
                'name' => 'Expired',
                'slug' => 'expired',
                'icon' => 'fa fa-clock-o',
                 'background' => 'overview-item--c3'
            ),
        );*/
        return $dealStatus;
    }

    public static function dealViewArray(){
        $dealView = array(
            array(
                'id' => 1,
                'name' => 'Dollars',
                'slug' => 'dollars',
                'value' => '$0',
                'background' => 'bg-blue',
                'image' => 'doller-icon.png'
            ),
            array(
                'id' => 2,
                'name' => 'CPM',
                'slug' => 'cpm',
                'value' => '$0',
                'background' => 'bg-green',
                'image' => 'cpm-icon.png'
            ),
            array(
                'id' => 3,
                'name' => 'Impressions',
                'slug' => 'impressions',
                'value' => '$0',
                'background' => 'bg-yellow',
                'image' => 'impressions-icon.png'
            ),
            array(
                'id' => 4,
                'name' => 'GRP',
                'slug' => 'grp',
                'value' => '$0',
                'background' => 'bg-red',
                'image' => 'grp-icon.png'
            ),
            array(
                'id' => 5,
                'name' => 'Deal Unit',
                'slug' => 'deal_unit',
                'value' => '$0',
                'background' => 'bg-orange',
                'image' => 'deal-icon.png'
            ),
        );
        return $dealView;
    }
    public static function dealViewTableName(){
        $dealTableArray = array(
                        "Deal ID", 
                        "Campaign Number", 
                        "Title", 
                        "Day/ Time", 
                        "Brand", 
                        "Inv Type", 
                        "Inv Length", 
                        "$ Rate", 
                        "$ Rate", 
                        "% Rate", 
                        "Total Avails", 
                        "Total Unit", 
                        "HH Rating", 
                        "HH (000)", 
                        "HH CPM", 
                        "HH Univ", 
                        "A25-49 Rating", 
                        "A25-49 (000)", 
                        "A25-49 CPM", 
                        "A25-49 Univ",
                        "Status"
                    );
        return  $dealTableArray;
    }
    public static function campaignViewTableName(){
        $campaignTableArray = array(
                        "Deal ID", 
                        "Campaign Number", 
                        "Campaign Name",
                        "Title", 
                        "Day/ Time", 
                        "Brand",
                        "Start Flight",
                        "End Flight", 
                        "Media Line",
                        "Inv Type", 
                        "Inv Length", 
                        "$ Rate", 
                        "$ Rate", 
                        "% Rate", 
                        "Total Avails", 
                        "Total Unit", 
                        "Status",
                        "Action",
                    );
        return  $campaignTableArray;
    }

    public static function dashboardInterconnectDropdownHtml( $dashboardData, $field, $defaultName, $selecValue, $addName = 0 ){
        if( !empty( $field ) ){
            $fieldName = $field.'_name';
            $fieldId = $field.'_id';
            $dealDropdown = [];
            $dealDropdown[] = '<option value="">'. $defaultName .'</option>';
            if( !empty( $dashboardData ) && ( count( $dashboardData ) > 0 ) ){
                foreach( $dashboardData as $dashboardDataKey => $dashboardDataVal ){
                    if( $dashboardDataVal[$fieldId] ){
                        $customName = ( $addName == 1 )? $dashboardDataVal[$fieldName].' ('. $dashboardDataVal[$fieldId] .')': $dashboardDataVal[$fieldName] ;
                        $selected = ( $selecValue == $dashboardDataVal[$fieldId] )? "selected":"";
                        $dealDropdown[] = '<option value="'. $dashboardDataVal[$fieldId] .'" '.  $selected .'>'. $customName .'</option>';
                    }
                }
            }
            return $dealDropdown;
        }
    }
    
    public static function daySmallArray(){
        return array( 'sunday' => 'S', 'monday' => 'M','tuesday' => 'T','wednesday' => 'W', 'thursday' => 'T', 'friday' => 'F', 'saturday' => 'S' );
    }

    public static function tableAddDaysAndTime( $campainTableArray, $campainArray, $flag ){
        $dayOfArray = Helper::daySmallArray();
        $newCampaingTableData = [];
        if( count( $campainTableArray ) > 0 ){
            if( count ( $campainArray ) > 0 ){
                foreach( $campainArray as $campaignDayTableKey => $campaignDayTableVal ){
                    foreach( $campaignDayTableVal as $campaignSingleDayKey => $campaignSingleDayVal ){
                        if( ( $campaignSingleDayVal == 1 ) && ( array_key_exists( $campaignSingleDayKey, $dayOfArray ) ) ){
                            $campaignDayTableVal[$campaignSingleDayKey] =  $dayOfArray[$campaignSingleDayKey];
                        }
                    }
                    $campainArray[$campaignDayTableKey] = implode(" ", $campaignDayTableVal);
                }
            }
            if( $flag == 1 ){
                foreach( $campainTableArray as $campainSingleTableKey => $campainSingleTableValue ){
                    foreach( $campainSingleTableValue as $campainTableKey => $campainTableValue ){
                        if( $campainTableKey == 'day_time' ){
                            //echo $campainArray[$campainSingleTableKey].' '.$campainTableValue;
                            $campainSingleTableValue[$campainTableKey] = $campainArray[$campainSingleTableKey].' '.$campainTableValue;
                        }
                    }
                    $newCampaingTableData[] = $campainSingleTableValue;
                }
            }else{
                foreach( $campainTableArray as $campainTableKey => $campainTableValue ){
                    if( $campainTableKey == 'day_time' ){
                        $campainTableArray[$campainTableKey] = $campainArray[0].' '.$campainTableValue;
                    }
                }
                $newCampaingTableData = $campainTableArray;
            }
        }
        return $newCampaingTableData;
    }

    public static function campaignDayTime( $daytime, $tableName ){
        return  $daytime->get([
            $tableName.'.sunday as sunday', 
            $tableName.'.monday as monday', 
            $tableName.'.tuesday as tuesday', 
            $tableName.'.wednesday as wednesday', 
            $tableName.'.thursday as thursday', 
            $tableName.'.friday as friday',
            $tableName.'.saturday as saturday'
        ])->toArray();
    }

    public static function changeDateFormate( $dataArray, $fieldName = array() , $flag ){
        $newDataArray = [];
        if( $flag == 1 ){
            if( count( $dataArray ) > 0){
                foreach( $dataArray as $dataSingleArrayKey => $dataSingleArrayVal){
                    foreach( $dataSingleArrayVal as $dataArrayKey => $dataArrayValue){
                       
                        if( in_array( $dataArrayKey, $fieldName ) ){
                            $dataSingleArrayVal[$dataArrayKey] = date('m/d/Y', strtotime($dataArrayValue)); 
                        }
                    }
                    $newDataArray[] = $dataSingleArrayVal;
                }
                
            }
        }else{
            if( count( $dataArray ) > 0){
                foreach( $dataArray as $dataArrayKey => $dataArrayValue){
                    if( in_array( $dataArrayKey, $fieldName ) ){
                        $dataArray[$dataArrayKey] = date('m/d/Y', strtotime($dataArrayValue)); 
                    }
                }
                $newDataArray = $dataArray;
            }
        }
        return $newDataArray;
    }

    public static function removeUnderscore($value){
        $replaceValue = str_replace("_"," ",$value);
        return ucwords($replaceValue);
    }

    public static function addUnderscore($value){
        $replaceValue = str_replace(" ","_",$value);
        return $replaceValue;
    }

    public static function getInput($tableName,$fieldId,$fieldsVal,$class){
        $input = '';
        switch ($tableName) {
            case "bigint":
                $input = '<input type="text" id="db_field_name_'.$fieldId.'" attr-key="bigint" class="au-input au-input--full json-data-field form-control '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "string":
                $input = '<input type="text" id="db_field_name_'.$fieldId.'" attr-key="string" class="au-input au-input--full json-data-field form-control '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "integer":
                $input = '<input type="number" id="db_field_name_'.$fieldId.'" attr-key="integer" class="au-input au-input--full json-data-field form-control '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "datetime":
                $input = '<input type="text" id="db_field_name_'.$fieldId.'" attr-key="datetime" class="au-input au-input--full json-data-field form-control json-datetime '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "time":
                $input = '<input type="text" id="db_field_name_'.$fieldId.'" attr-key="time" class="au-input au-input--full json-data-field form-control json-time '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "date":
                $input = '<input type="text" id="db_field_name_'.$fieldId.'" attr-key="date" class="au-input au-input--full json-data-field form-control json-year '.$class.'" name="db_field_name[]" value="'.$fieldsVal.'" autofocus="">';
                break;
            case "boolean":
                $selectFirst = ( $fieldsVal == 0 )?'selected="selected"':""; 
                $selectSecond = ( $fieldsVal == 1 )?'selected="selected"':""; 
                $input = '<select name="db_field_name[]" id="db_field_name_'.$fieldId.'" attr-key="boolean" class="au-input au-input--full valid json-data-field form-control" aria-invalid="false">
                <option value=""'.$selectFirst.'>0</option><option value="1" '.$selectSecond.'>1</option></select>';
                break;
        }
        return  $input;
    }

    public static function removeId( $mainArrayData ){
        if( count( $mainArrayData ) > 0 ){
            $newData = [];
            foreach( $mainArrayData as $mainArrayDataKey => $mainArrayDataVal ){
                $columnName = $mainArrayDataVal[0];
                $columnType = $mainArrayDataVal[1];
                    switch ($columnName) {
                        case str_contains($columnName, '_id'):
                            $removeId = str_replace('_id','_name',$columnName);
                            $newData[$removeId] = $columnType;
                            break;
                        case 'id':
                            break;
                        case "created_by":
                            break;
                        case "updated_by":
                            break;
                        case "created_at":
                            break;
                        case "updated_at":
                            break;
                        case "delete":
                            break;
                        case "status":
                            break;
                        default:
                        $newData[$columnName] = $columnType;
                    }
              //  }
            }
        }
        return $newData;
    }

    public static function getTableName($tablename){
        $tableData = '';
        switch ($tablename) {
            case "deal":
                $tableData = new Deals;
            break;
            case "deal_payloads":
                $tableData = new DealPayload;
            break;
            case "campaign":
                $tableData = new Campaigns;
            break;
            case "campaign_payloads":
                $tableData = new CampaignPayload;
            break;
        }
        return $tableData;
    }

    public static function createTableFieldDropdown($fieldName,$dropdownArray,$fieldValue,$flag){
        $dropdownHTML = "";
        if( count( $dropdownArray ) > 0 ){
            $dropdownHTML .= '<select name="'.$fieldName.'" class="au-input au-input--full form-control json-mapping-option">';
                $dropdownHTML .= '<option value="">Select Option</option>';
                foreach( $dropdownArray as $dropdownArrayKey => $dropdownArrayValue ){
                    if( $flag == 1 ){
                        $selected = ( $fieldValue == $dropdownArrayValue['id'] ) ? 'selected="selected"' : ""; 
                        $dropdownHTML .= '<option value="'.$dropdownArrayValue['id'].'" '.$selected.'>'.$dropdownArrayValue['name'].'</option>';
                    } else {
                        $selected = ( $fieldValue == $dropdownArrayValue['name'] ) ? 'selected="selected"' : ""; 
                        $dropdownHTML .= '<option value="'.$dropdownArrayValue['name'].'" '.$selected.'>'.$dropdownArrayValue['name'].'</option>';
                    }
                }
            $dropdownHTML .= '</select>';
        }
        return $dropdownHTML;
    }
    public static function getValidationContent($tablename,$selectOptionName){
        $advertiserId = Session::get('advertiser_id');
        $clientsId = Session::get('clients_id');
        $mediasId = Session::get('medias_id');
        $tableData = "";
        switch ($tablename) {
            case "deal_name":
                $dealView = Deals::join('deal_payloads', 'deals.deal_payload_id', '=', 'deal_payloads.id')
                ->where('deals.advertiser_id', '=', $advertiserId)
                ->where('deals.media_id', '=', $mediasId)
                ->where('deals.client_id', '=', $clientsId)
                ->get(['deals.id as id','deal_payloads.name as name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "demographic_name":
                $dealView = Demographic::where('client_id', '=', $clientsId)->where('status', '=', 1)->get(['id','name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "brand_name":
                $dealView = Brands::where('client_id', '=', $clientsId)->where('advertiser_id', '=', $advertiserId)->where('status', '=', 1)->get(['id','product_name as name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "outlet_name":
                $dealView = Outlets::where('client_id', '=', $clientsId)->where('status', '=', 1)->get(['id','outlet_type as name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "agency_name":
                $dealView = Agencys::where('client_id', '=', $clientsId)->where('status', '=', 1)->get(['id','name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "daypart_name":
                $dealView = DayParts::where('client_id', '=', $clientsId)->where('status', '=', 1)->get(['id','name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "location_name":
                $dealView = Locations::where('client_id', '=', $clientsId)->where('status', '=', 1)->get(['id','name'])->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,0);
            break;
            case "campaign_payload_name":
                if(is_numeric($selectOptionName)){
                $dealView = Campaigns::join('campaign_payloads', 'campaigns.campaign_payload_id', '=', 'campaign_payloads.id')
                    ->where('campaigns.advertiser_id', '=', $advertiserId)
                    ->where('campaigns.client_id','=',$clientsId)
                    ->where('campaigns.media_id', '=', $mediasId)
                    ->get(['campaign_payloads.name as name','campaigns.id as id','campaign_payloads.id as cam_pay_id'])
                    ->toArray();
                $tableData = Helper::createTableFieldDropdown($tablename,$dealView,$selectOptionName,1);
                }
            break;
        }
        return $tableData;
    }

    public static function getTableColumnType( $dealPayloads, $arrayData ){
        $tableName = Helper::getTableName($dealPayloads);
        $tableNameTypeArr = [];
        foreach( $arrayData as $arrayDataKey => $arrayDataVal ){
            $tableNameTypeArr[] = array(  $arrayDataVal,  $tableName->getTableTypes($arrayDataVal) );
        }
       return $tableNameTypeArr;
    }

    public static function jsonDataTableList(){
        $dealColumnsList = Helper::getTableName('deal')->getTableColumns();
        $dealColumnsTypeList = Helper::getTableColumnType('deal',$dealColumnsList);
        $dealremoveArray = Helper::removeId($dealColumnsTypeList);

        $dealpaylpadsColumnsList = Helper::getTableName('deal_payloads')->getTableColumns();
        $dealpaylpadsColumnsTypeList = Helper::getTableColumnType('deal_payloads',$dealpaylpadsColumnsList);
        $dealpaylpadsremoveArray = Helper::removeId($dealpaylpadsColumnsTypeList);

        $dealTableFields = array_merge($dealremoveArray,$dealpaylpadsremoveArray);

        $campaignColumnsList =  Helper::getTableName('campaign')->getTableColumns();
        $campaignColumnsTypeList = Helper::getTableColumnType('campaign',$campaignColumnsList);
        $campaignremoveArray = Helper::removeId($campaignColumnsTypeList);

        $campaignpaylpadsColumnsList = Helper::getTableName('campaign_payloads')->getTableColumns();
        $campaignpaylpadsColumnsTypeList = Helper::getTableColumnType('campaign_payloads',$campaignpaylpadsColumnsList);
        $campaignpaylpadsremoveArray = Helper::removeId($campaignpaylpadsColumnsTypeList);

        $campaignTableFields = array_merge($campaignremoveArray,$campaignpaylpadsremoveArray);
        
        $tableFieldsData = array(  
             'deal' => $dealTableFields,
             'campaign' => $campaignTableFields,
            );
        return $tableFieldsData;
    }

    public static function jsonDataGetSpecificTableList($tableName){
        $columnsList = Helper::getTableName($tableName)->getTableColumns();
        $columnsTypeList = Helper::getTableColumnType($tableName,$columnsList);
        $removeArray = Helper::removeId($columnsTypeList);
        $columnArray = [];
        foreach($columnsTypeList as $columnsTypeOfVal){
            switch ($columnsTypeOfVal[0]) {
                case "id":
                    break;
                default:
                    $columnArray[] = $columnsTypeOfVal[0];
            }
        }
        return $columnArray;
    }
    public static function tableOfFields($data){
        $tableFields = [];
        foreach( $data as $mappingKey => $mappingValue ){
            $mappingKey = $mappingKey - 1;
            if( ( $mappingValue['name'] == 'select_db_field[]') && ( $mappingValue['value'] != '' ) ){
                if( array_key_exists($mappingValue['value'],$tableFields) ){
                    $data = array( 'status' => 0 , 'message' => 'Please check Field Option already selected');
                } else{
                    $tableFields[$mappingValue['value']] =   $data[$mappingKey]['value'];
                }
            }
        }
        return $tableFields;
    }
    public static function changeDateFormateYMD($fieldArray){
        return  date('Y-m-d', strtotime($fieldArray));
    }

    public static function addfieldsValue($fieldArray){
        $userId = Session::get('login_user_id');
        $currentDate = date('Y-m-d H:i:s');
        $newFieldArray = [];
        foreach($fieldArray as $fieldArrayKey => $fieldArrayVal) 
        {
            switch ($fieldArrayKey) {
                case "delete":
                    $newFieldArray['delete'] = 0;
                break;
                case "created_by":
                    $newFieldArray['created_by'] = $userId;
                break;
                case "updated_by":
                    $newFieldArray['updated_by'] = 0;
                break;
                case "updated_at":
                    $newFieldArray['updated_at'] = $currentDate;
                break;
                case "created_at":
                    $newFieldArray['created_at'] = $currentDate;
                break;
                case "status":
                    $newFieldArray['status'] = 3;
                break;
                case "valid_from":
                    $newFieldArray['valid_from'] =  date('Y-m-d h:m:s', strtotime($fieldArray['valid_from']));
                break;
                case "valid_to":
                    $newFieldArray['valid_to'] =  date('Y-m-d h:m:s', strtotime($fieldArray['valid_to']));
                break;
                case "flight_start_date":
                    $newFieldArray['flight_start_date'] =  date('Y-m-d h:m:s', strtotime($fieldArray['flight_start_date']));
                break;
                case "flight_end_date":
                    $newFieldArray['flight_end_date'] =  date('Y-m-d h:m:s', strtotime($fieldArray['flight_end_date']));
                break;
                default:
                    $newFieldArray[$fieldArrayKey] = $fieldArrayVal;
            }
        }
        return $newFieldArray;
    }
    public static function checkStringOrNumber($fieldArray){
        if(is_string($fieldArray)){
            return true;
        } else{
            return false;
        }
    }
    public static function getTableIdOrInsertedId($tableOfArray, $tableName){
        if( count( $tableOfArray ) > 0 ){
            if( $tableName == 'brands' ){
                $getMediaData = DB::table($tableName)->where('client_id',$tableOfArray['client_id'])->where('product_name',$tableOfArray['product_name'])->first(['id']);
            } else if( $tableName == 'outlets' ){
                $getMediaData = DB::table($tableName)->where('client_id',$tableOfArray['client_id'])->where('outlet_type',$tableOfArray['outlet_type'])->first(['id']);
            } else if( $tableName == 'deals' ){
                $getMediaData = DB::table($tableName)->join('deal_payloads', 'deal_payloads.id', '=', 'deals.deal_payload_id')->where('deal_payloads.name',$tableOfArray['name'])->where('deals.client_id',$tableOfArray['client_id'])->where('deals.advertiser_id',$tableOfArray['advertiser_id'])->first(['deals.id']);
            } else {
                $getMediaData = DB::table($tableName)->where('client_id',$tableOfArray['client_id'])->where('name',$tableOfArray['name'])->first(['id']);
            }
            
            if( $getMediaData == '' ){
                $userId = Session::get('user_id');
                $currentDate = date('Y-m-d H:i:s');
                $arrayData = array(
                    'status' => 1,
                    'created_by' => $userId,
                    'updated_by' => 0,
                    'created_at' => $currentDate
                );
                $array = array_merge($tableOfArray,$arrayData);
                $createMedia = DB::table($tableName)->insertGetId($array);
                return $createMedia;
            } else{
              return $getMediaData->id;
            }
        }
    }

    public static function insertData($fieldArray){
        $newData = [];
        foreach($fieldArray as $fieldArrayKey => $fieldArrayVal) 
        {
            $checkStatus = Helper::checkStringOrNumber($fieldArrayVal);
            if( $checkStatus ){
                $tableFields = array('client_id' => $fieldArray['client_id'], 'name' => $fieldArrayVal);
                switch ($fieldArrayKey) {
                    case "media_id":
                        $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableFields,'medias');
                    break;
                    case "brand_id":
                        $advertiserId = Session::get('advertiser_id');
                        $tableBrandFields = array('client_id' => $fieldArray['client_id'], 'product_name' => $fieldArrayVal, 'advertiser_id'=>$advertiserId);
                        $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableBrandFields,'brands');
                    break;
                    case "outlet_id":
                        $tableBrandFields = array('client_id' => $fieldArray['client_id'], 'outlet_type' => $fieldArrayVal);
                        $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableBrandFields,'outlets');
                    break;
                    case "location_id":
                        $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableFields,'locations');
                    break;
                    case "daypart_id":
                        $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableFields,'day_parts');
                    break;
                    default:
                        $fieldArray[$fieldArrayKey] = $fieldArrayVal;
                }
            } else{
                $fieldArray[$fieldArrayKey] = $fieldArrayVal;
            }
        }

        foreach($fieldArray as $fieldArrayKey => $fieldArrayVal) 
        {
            switch($fieldArrayKey){
                case "demographic_id":
                    $tableDemoGraphicField = array('client_id' => $fieldArray['client_id'], 'name' => $fieldArrayVal,'location_id' => $fieldArray['location_id'],'outlet_id' => $fieldArray['outlet_id'], 'primory' => 0 );
                    $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableDemoGraphicField,'demographics');
                break;
                case "agency_id":
                    $tableAgencyField = array('client_id' => $fieldArray['client_id'], 'name' => $fieldArrayVal, 'media_id' => $fieldArray['media_id'],'demographic_id' => $fieldArray['demographic_id'],'brand_id' => $fieldArray['brand_id'], 'outlet_id' =>$fieldArray['outlet_id']);
                    $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableAgencyField,'agencys');
                break;
                case "deal_id":
                    $tableAgencyField = array('client_id' => $fieldArray['client_id'], 'name' => $fieldArrayVal, 'media_id' => $fieldArray['media_id'],'advertiser_id' => $fieldArray['advertiser_id']);
                    $fieldArray[$fieldArrayKey] = Helper::getTableIdOrInsertedId($tableAgencyField,'deals');
                break;
            }
        }
        return $fieldArray;
    }
}
?>