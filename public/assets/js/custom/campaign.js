$(document).ready(function(){
    /******************************************************** Campaign page Start  ************************************************/
    function campaignGetId(){
        $('#campaign_table input[name="deal_number"]').click(function(){
            var editCampaignDealId = $(this).val();
            var editCampaignId = $(this).attr('autoid');
            $('#edit_campaign_id').attr('dealid',editCampaignDealId).attr('autoincrementid',editCampaignId).removeAttr("disabled");
        });
        $('.campaign-view-sec #edit_campaign_id').click(function(){
            var dealId = $(this).attr('dealid');
            var autoIncrementId = $(this).attr('autoincrementid');
            window.location.href = URL+'/campaign/edit/'+dealId;
        });
    }
    function getCampaginViewData( dealStatus = null){
        var url = URL+'/post-campaign-status';
        $.ajax({
            url: url,
            type: 'POST',
            data: {_token: CSRF_TOKEN, data: dealStatus},
            success: function(response){
                
                $('#campaign_table').DataTable().destroy();
                $('#campaign_view_body').empty().append(response.deal_table_html);
                $('#campaign_table').DataTable({
                    pageLength: 5,
                    rowReorder: true,
                    responsive: true,
                    columnDefs: [
                        { orderable: true, className: 'reorder', targets: 0 },
                        { orderable: false, targets: '_all' }
                    ]
                });
                if( response.deal_view_data ){
                    var dealViewData = response.deal_view_data;
                    $('#deal_dollars').empty().append('$'+Number(nullNumber(dealViewData.rate)).toLocaleString('en'));
                    $('#deal_cpm').empty().append('$'+nullNumber(dealViewData.cpm));
                    $('#deal_deal_unit').empty().append(Number(nullNumber(dealViewData.deal_unit)).toLocaleString('en'));
                    $('#deal_grp').empty().append(nullNumber(dealViewData.grp));
                    $('#deal_impressions').empty().append(Number(nullNumber(dealViewData.impressions)).toLocaleString('en'));
                    campaignGetId();
                    return true;
                }else{
                    return fasle;
                }
            }
        });
    }
    function getFlightSplitCheck(current){
        var getClass = $(current).parent().parent().parent().parent().parent().find('.active').attr('id');
        var response = true;
        if( $('#'+getClass).hasClass('flighting-tab') ){
            $('#edit_flight tr.day-checkbox-list .form-check-input').each(function(){
                var day = $(this).val();
                var checkboxChecked = $(this).is(":checked");
                var splitVal = $('#edit_flight tr.day-split-checkbox-list .number-field input[name="'+day+'_split"]').val();
                if( ( splitVal != '' && splitVal != 0 && checkboxChecked == false ) || ( splitVal == '' && checkboxChecked == true ) || ( splitVal == 0 && checkboxChecked == true ) ) {
                    //var checkedVal = day+'_split';
                    //var splitVal = $('#edit_flight tr.day-split-checkbox-list input[name="'+checkedVal+'"]').val();
                    if( splitVal ==  ''){
                        var uppercashDay = day.charAt(0).toUpperCase() + day.slice(1);
                        errorNotification('Please Enter '+ uppercashDay +' Split Number.');
                        response = false;
                    }
                    if( splitVal ==  0 ){
                        var uppercashDay = day.charAt(0).toUpperCase() + day.slice(1);
                        errorNotification('Please Enter '+ uppercashDay +' Number Greater then 0.');
                        response = false;
                    }
                    if( checkboxChecked ==  false){
                        var uppercashDay = day.charAt(0).toUpperCase() + day.slice(1);
                        errorNotification('Please Select '+ uppercashDay +' Checkbox.');
                        response = false;
                    }
                }
            });
        } 
        return response;
    }

    function changeDetailNewTable(dayTimeVal,flag){
        var dayOfList = [];
        $('#edit_flight tr.day-checkbox-list .form-check-input').each(function(){
            if( $(this).is(":checked") ) {
                dayOfList.push($(this).attr('day'));
            }
        });
        var checkDayVal = dayOfList.join(" ");
        dataAppend('#new_campaign_table .new-campaign_day-time', checkDayVal+' '+dayTimeVal,flag);
    }

    getCampaginViewData();
    $("#deal_status").change(function(){
        var dealStatus = $(this).val();
        getCampaginViewData( dealStatus );
    });
    $('#campaign_table').DataTable({
        pageLength: 5,
        responsive: true,
        rowReorder: true,
        columnDefs: [
            { orderable: true, className: 'reorder', targets: 0 },
            { orderable: false, targets: '_all' }
        ]
    });
    campaignGetId();

    /*************************************************** Campaign page End  ***************************************************/

    /***********************************************  Edit Campaign page Start ***********************************************/

    function getCampaignDetail(){
        var url = URL+'/get-campaign-detail';
        var currentUrl = window.location.href;
        var splitArray = currentUrl.split('/');
        var thirdLastVal = splitArray[splitArray.length - 3];
        var secondLastVal = splitArray[splitArray.length - 2];
        var firstLastVal = splitArray[splitArray.length - 1];
        if( thirdLastVal == 'campaign' && secondLastVal == 'edit' ){
            $.ajax({
                url: url,
                type: 'POST',
                data: {_token: CSRF_TOKEN, campaignId: firstLastVal },
                success: function(response){
                    if( response.status == 1 ){
                        var campaignArrayData = response.data.campaign_data;
                        console.log( campaignArrayData )
                        /* Step 1 General */
                        dataValue('input[name="campaign_number"]', campaignArrayData.campaign_id );
                        dataValue('input[name="campaign_name"]', campaignArrayData.name );
                        dataValue('input[name="brand_name"]', campaignArrayData.brand_name );
                        dataValue('input[name="media_line_name"]', campaignArrayData.media_name );
                        dataValue('input[name="dollar_rate"]', campaignArrayData.rate );
                        dataValue('input[name="agency_name"]', campaignArrayData.agency_name );
                        dataValue('input[name="demo_name"]', campaignArrayData.demo );
                        dataValue('input[name="ae_name"]', campaignArrayData.ae );
                        dataValue('input[name="outlet_name"]', campaignArrayData.outlets_name );
                        dataValue('input[name="market_place"]', campaignArrayData.market_place );
                        dataValue('input[name="realistic"]', campaignArrayData.realistic );
                        dataValue('input[name="agency_commision"]', campaignArrayData.agency_commission );
                        dataValue('input[name="revenue_risk"]', campaignArrayData.revenue );
                        dataValue('input[name="campaign_day_time"]', campaignArrayData.time_day_part );
                        dataValue('input[name="campaign_id"]', campaignArrayData.campaign_id );
                        dataValue('input[name="campaign_deal_id"]', campaignArrayData.deal_id );
                        dataValue('input[name="campaign_payload_id"]', campaignArrayData.id );
                        dataValue('input[name="inv_type"]', campaignArrayData.inventory_type );
                        dataValue('input[name="inv_length"]', campaignArrayData.inventory_length );
                        dataValue('input[name="dollar_rates"]', campaignArrayData.rc_rate );
                        dataValue('input[name="per_rate"]', campaignArrayData.rc_rate_percentage );
                        dataValue('input[name="total_avails"]', campaignArrayData.total_avil );
                        dataValue('input[name="total_unit"]', campaignArrayData.total_unit );
                        dataValue('input[name="deal_payloads_name"]', campaignArrayData.deal_payloads_name );
                        dataValue('input[name="demo_name"]', campaignArrayData.demographics_name)
                        dataValue('input[name="ad_length"]', campaignArrayData.inventory_length)
                        dataValue('input[name="ad_length_old"]', campaignArrayData.inventory_length)
                        dataValue('input[name="created_by_old"]', campaignArrayData.created_by)
                        dataValue('input[name="created_date_old"]', campaignArrayData.created_at)
                        dataValue('input[name="change_by_old"]', campaignArrayData.updated_by)
                        dataValue('input[name="change_date_old"]', campaignArrayData.updated_at)
                        $('input[name="ad_length"]').daterangepicker({
                            timePicker : true,
                            singleDatePicker:true,
                            timePicker24Hour : true,
                            timePickerIncrement : 1,
                            timePickerSeconds : true,
                            locale : {
                                format : 'HH:mm:ss'
                        }}, (from_date) => {
                            $('input[name="ad_length"]').val(campaignArrayData.inventory_length);
                        }).on('show.daterangepicker', function (ev, picker) {
                            picker.container.find(".calendar-table").hide();
                        });
                        $('input[name="flight_start_date"]').daterangepicker({
                            autoUpdateInput: false,
                            minDate:campaignArrayData.valid_from,
                            maxDate:campaignArrayData.valid_to,
                            locale: {
                                format: 'MM/DD/YYYY',
                            },
                            singleDatePicker: true
                        }, (from_date) => {
                            $('input[name="flight_start_date"]').val(from_date.format('MM/DD/YYYY'));
                        });
                        $('input[name="flight_end_date"]').daterangepicker({
                            autoUpdateInput: false,
                            minDate:campaignArrayData.valid_from,
                            maxDate:campaignArrayData.valid_to,
                            locale: {
                                format: 'MM/DD/YYYY',
                            },
                            singleDatePicker: true
                        }, (from_date) => {
                            $('input[name="flight_end_date"]').val(from_date.format('MM/DD/YYYY'));
                        });
                        
                        dataValue('input[name="cpm_ipm_demo_population"]', campaignArrayData.demo_population)
                        dataValue('input[name="cpm_ipm_grp"]', campaignArrayData.grp)
                        dataValue('input[name="cpm_ipm_cpm"]', campaignArrayData.cpm)
                        dataValue('input[name="cpm_ipm_impressions"]', campaignArrayData.impressions)

                        /* Step 1 End */

                        /* Step 2 Demographic */
                        //$('#edit_campaign select#demographic_name').prop("disabled", true);
                        $('#edit_campaign select#demographic_name option').each(function(){
                            var value = $(this).val();
                            if( campaignArrayData.demographic_id == value ){
                                $(this).prop('selected', true);
                            }else{
                                $(this).prop('selected', false);
                            }
                        })
                        $('.cpm-imp-tab #demographic_name').change(function(){
                            var demoName =  $(this).find('option:selected').text()
                            dataAppend('#summary .demo_name', demoName, 1, campaignArrayData.demographics_name);
                        });
                        $('.cpm-imp-tab input[name="cpm_ipm_impressions"]').change(function(){
                            dataAppend('#summary .impressions', $(this).val(), 1, campaignArrayData.impressions);
                        });
                        $('.cpm-imp-tab input[name="cpm_ipm_cpm"]').change(function(){
                            dataAppend('#summary .cpm', $(this).val(), 1, campaignArrayData.cpm);
                        });
                        $('.cpm-imp-tab input[name="cpm_ipm_demo_population"]').change(function(){
                            dataAppend('#summary .demo_population', $(this).val(), 1, campaignArrayData.demo_population);
                        });
                        $('.cpm-imp-tab input[name="cpm_ipm_grp"]').change(function(){ 
                            dataAppend('#summary .grp', $(this).val(), 1, campaignArrayData.grp);
                        });
                        /* Step 2 End */

                        /* Step 3 Flighting */
                        var campaignFlightStartDate = moment(campaignArrayData.flight_start_date).format('MM/DD/YYYY');
                        var campaignFlightEndDate = moment(campaignArrayData.flight_end_date).format('MM/DD/YYYY');

                        /* Flight Time Day & Flight fields */

                        dataValue('input[name="campaign_flight_start_date"]', campaignFlightStartDate );
                        dataValue('input[name="campaign_flight_end_date"]', campaignFlightEndDate );
                        dataValue('input[name="campaign_day_parts"]', campaignArrayData.time_day_part );
                        $('select#day_parts_id option').each(function(){
                            var value = $(this).text();
                            if( value == campaignArrayData.time_day_part ){
                                $(this).prop('selected', true);
                            }else{
                                $(this).prop('selected', false);
                            }
                        })
                        
                        /* Flight Table Section */
                        checkedCheckbox('#edit_flight #sunday', campaignArrayData.sunday);
                        checkedCheckbox('#edit_flight #monday', campaignArrayData.monday);
                        checkedCheckbox('#edit_flight #tuesday', campaignArrayData.tuesday);
                        checkedCheckbox('#edit_flight #wednesday', campaignArrayData.wednesday);
                        checkedCheckbox('#edit_flight #thursday', campaignArrayData.thursday);
                        checkedCheckbox('#edit_flight #friday', campaignArrayData.friday);
                        checkedCheckbox('#edit_flight #saturday', campaignArrayData.saturday);

                        dataValue('#edit_flight #sunday_split', campaignArrayData.sunday_split );
                        dataValue('#edit_flight #monday_split', campaignArrayData.monday_split );
                        dataValue('#edit_flight #tuesday_split', campaignArrayData.tuesday_split );
                        dataValue('#edit_flight #wednesday_split', campaignArrayData.wednesday_split );
                        dataValue('#edit_flight #thursday_split', campaignArrayData.thursday_split );
                        dataValue('#edit_flight #friday_split', campaignArrayData.friday_split );
                        dataValue('#edit_flight #saturday_split', campaignArrayData.saturday_split );

                        /* Step 3 End */
                        
                        /* Step 4 Summary */
                        dataAppend('#summary .demo_name', campaignArrayData.demographics_name);
                        dataAppend('#summary .ae_name', campaignArrayData.ae);
                        dataAppend('#summary .outlet_name', campaignArrayData.outlets_name);
                        dataAppend('#summary .new-campaign-id', campaignArrayData.campaign_id);
                        dataAppend('#summary .change_by', campaignArrayData.change_by);
                        dataAppend('#summary .market_place', campaignArrayData.market_place);
                        dataAppend('#summary .demo_population', campaignArrayData.demo_population);
                        dataAppend('#summary .impressions', campaignArrayData.impressions);
                        dataAppend('#summary .grp', campaignArrayData.grp);
                        dataAppend('#summary .cpm', campaignArrayData.cpm);
                        dataAppend('#summary .campaign_number, #new_campaign_table .new-campaign-id, #old_campaign_table .old-campaign-id', campaignArrayData.campaign_id );
                        dataValue('#summary .campaign_name', campaignArrayData.name );
                        dataAppend('#new_campaign_table .new-campaign-name, #old_campaign_table .old-campaign-name', campaignArrayData.name);
                        dataAppend('#new_campaign_table .new-campaign-deal-name, #old_campaign_table .old-campaign-deal-name', campaignArrayData.deal_payloads_name);
                        dataAppend('#new_campaign_table .new-campaign_day-time, #old_campaign_table .old-campaign_day-time', campaignArrayData.day_time );
                        dataAppend('#summary .brand_name, #new_campaign_table .new-campaign-brand, #old_campaign_table .old-campaign-brand', campaignArrayData.brand_name);
                        dataAppend('.new-campaign-table .new-flight-stat-date-text, #new_campaign_table .new-campaign-start-flight-date, #old_campaign_table .old-campaign-start-flight-date, .old-campaign-table .flight-stat-date-text', campaignArrayData.flight_start_date);
                        dataAppend('.new-campaign-table .new-flight-end-date-text, #new_campaign_table .new-campaign-end-flight-date, #old_campaign_table .old-campaign-end-flight-date, .old-campaign-table .flight-end-date-text', campaignArrayData.flight_end_date);
                        dataAppend('#summary .media_line_name, #new_campaign_table .new-campaign-media-line, #old_campaign_table .old-campaign-media-line', campaignArrayData.media_name);
                        dataAppend('#new_campaign_table .new-campaign-inv-type, #old_campaign_table .old-campaign-inv-type', campaignArrayData.inventory_type );
                        dataAppend('#new_campaign_table .new-campaign-inv-type, #old_campaign_table .old-campaign-inv-type', campaignArrayData.inventory_type );
                        dataAppend('#new_campaign_table .new-campaign-inv-length, #old_campaign_table .old-campaign-inv-length', campaignArrayData.inventory_length );
                        dataAppend('#new_campaign_table .new-campaign-rate, #old_campaign_table .old-campaign-rate', campaignArrayData.rate );
                        dataAppend('#new_campaign_table .new-campaign-do-rate, #old_campaign_table .old-campaign-do-rate', campaignArrayData.rc_rate );
                        dataAppend('#new_campaign_table .new-campaign-per-rate, #old_campaign_table .old-campaign-per-rate', campaignArrayData.rc_rate_percentage );
                        dataAppend('#new_campaign_table .new-campaign-total-avail, #old_campaign_table .old-campaign-total-avail', campaignArrayData.total_avil );
                        dataAppend('#new_campaign_table .new-campaign-total-unit, #old_campaign_table .old-campaign-total-unit', campaignArrayData.total_unit );                        
                    }
                }
            });
        }
    }

    getCampaignDetail();

    /* All Tab add Active Class */


    /* Flight & Ad Length Section Start */ 
    
  /*  $('input[name="ad_length"]').daterangepicker({
        timePicker : true,
        singleDatePicker:true,
        timePicker24Hour : true,
        timePickerIncrement : 1,
        timePickerSeconds : true,
        locale : {
            format : 'HH:mm:ss'
        },
    }).on('show.daterangepicker', function (ev, picker) {
        picker.container.find(".calendar-table").hide();
    });
*/
    $('input[name="ad_length"]').on('apply.daterangepicker', function(){
        var startDate = $(this).val(); 
        var adLengthOld = $('#ad_length_old').val(); 
        dataAppend('#new_campaign_table .new-campaign-inv-length', startDate,1, adLengthOld);
    });
    /** Flight Change Event  */
    $('#flight_start_date').on('apply.daterangepicker', function(){
        var startDate = $(this).val(); 
        $('#summary .new-flight-stat-date-text').empty().text(startDate);
        var campaignFlightStartDate = $('#campaign_flight_start_date').val();
        dataAppend('#new_campaign_table .new-campaign-start-flight-date', startDate,1, campaignFlightStartDate);
    });

    $('#flight_end_date').on('apply.daterangepicker', function(){
        var endDate = $(this).val();
        $('#summary .new-flight-end-date-text').empty().text(endDate);
        var campaignFlightEndDate = $('#campaign_flight_end_date').val();
        dataAppend('#new_campaign_table .new-campaign-end-flight-date', endDate,1,campaignFlightEndDate);
    });
    $('#edit_flight .day-split-checkbox-list .number-field input[type="number"]').change(function(){
        var getDayOfSplit = $(this).val(); 
        var onlyDay = $(this).attr('id').split("_split");
        if( ( getDayOfSplit == 0 ) || ( getDayOfSplit == '' ) ){
            $('#edit_flight .day-checkbox-list .form-check input[id="'+onlyDay[0]+'"]').attr('checked',false);
        }else{
            $('#edit_flight .day-checkbox-list .form-check input[id="'+onlyDay[0]+'"]').attr('checked',true);
        }
        var dayTime = $('#day_parts_id option:selected').text();
        changeDetailNewTable(dayTime,1)
    });

    /* Flight Section End */

    $('input[name="sunday_split"], input[name="monday_split"], input[name="tuesday_split"], input[name="wednesday_split"], input[name="thursday_split"], input[name="friday_split"], input[name="saturday_split"]').keypress(function (e) {    
        var currentVal = $(this).val();
        if( currentVal >= 100 ){
            var changeValue = currentVal.substring(0,currentVal.length - 1);
            $(this).val(changeValue);
            return false;
        }
    });
    $('input[name="days[]"]').change(function(){
        $('.send-to-approval').prop("disabled", false);
        var dayTimeVal = $('#campaign_day_time').val();
        changeDetailNewTable(dayTimeVal,1)
    });
    
    $('select:input[name="day_parts_id"]').change(function(){
        $('.send-to-approval').prop("disabled", false);
        var dayPartVal = $(this).find('option:selected').text();
        changeDetailNewTable(dayPartVal,1)
    });

    $("#edit_campaign").validate({
        submitHandler: function(form) {
           // var disabled = form.find(':input:disabled').removeAttr('disabled');
           var fileName = $('#campaign_id').val();
           $(form).find(':input:disabled').each(function(){
                $(this).prop('disabled',false)
           });
           //console.log( $(form).find(':input:disabled').attr('name') )
            var getFormAllData = $(form).serializeArray();
            console.log( getFormAllData )
            var url = URL+'/campaign/post-campaign-edit';
            $.ajax({
                url: url,
                type: 'POST',
                data: {_token: CSRF_TOKEN, data: getFormAllData},
                success: function(response){
                    sucessNotification('Sucessfully Data Update.');
                    $(form).find(':input:disabled').each(function(){
                         $(this).prop('disabled',true)
                    });
                    const data = response;
                    const link = document.createElement('a');
                    link.setAttribute('href', URL+'/storage/app/public/campaign/'+fileName+'.json');
                    link.setAttribute('download', fileName+'.json'); // Need to modify filename ...
                    link.click();
                    setTimeout(function(){
                        window.location.href = URL+'';
                    },3000)
                    return true;
                }
            });
        }
    });

    $('#summary .keep-original').click(function(){
        window.location.href = URL+'/campaign';
    })
    /* Edit Campaign page End  */
    $('.campaign-edit .tab-btn').click(function(e){
        e.preventDefault();

        var tabClass = $(this).attr('attr-active'); 

        if( getFlightSplitCheck( this ) ){
            $('ul.nav-tabs li a').each(function(){
                $(this).removeClass('active').attr('aria-selected', false);
            });
            $('#'+tabClass).addClass('active').attr('aria-selected', true);;
      
            $('#content .tab-pane').each(function(){
                $(this).removeClass('show').removeClass('active');
            });
            $('.'+tabClass+'-tab').addClass('show').addClass('active');
        }
       // getCampaignDetail();
    });
      /***********************************************  Edit Campaign page End ***********************************************/
});