
function fieldMapping(){
    var mappingFlag = true; 
    $('tbody#db_fields_mapping tr').each(function(){
        var valiDationFlag = true;
        var mappingId =  $(this).find('td.mapping select option:selected');
        var mappingVal =  $(this).find('td.text-right').attr('attr-name');
        var fieldInput = $(this).find('td.mapping select[name="select_db_field[]"]');
        fieldInput.parent().parent().find('.error-message').empty();
        if( mappingId.val() != mappingVal ){
            mappingId.parent().parent().parent().find('.error-message').append('<label class="error invalid-feedback" for="">Please check table field</label>');
            mappingFlag = false; 
            valiDationFlag = false; 
        }
        var fieldVal = $(this).find('td.validation select option:selected');
        if( fieldVal.val() == '' ){
            fieldVal.parent().parent().parent().find('.error-message').append('<label class="error invalid-feedback" for="">Please select option</label>');
            mappingFlag = false; 
            valiDationFlag = false; 
        }

        if( ( $(this).find('td.json-mapping-field .form-control').attr('attr-key') == 'datetime' ) || ( $(this).find('td.json-mapping-field .form-control').attr('attr-key') == 'bigint' ) ){
            
            var fieldVal = fieldInput.val();
            var fieldData = fieldInput.parent().parent().find('.json-data-field').val();

            if( fieldVal == '' ){
                var inputId = fieldInput.attr('id');
                fieldInput.parent().parent().find('.error-message').append('<label class="error invalid-feedback" for="'+inputId+'">Please select table field</label>');
                mappingFlag = false; 
                valiDationFlag = false; 
            } 

            if( fieldData == '' ){
                var inputId = fieldInput.attr('id');
                fieldInput.parent().parent().find('.error-message').append('<label class="error invalid-feedback" for="'+inputId+'">Please enter value.</label>');
                mappingFlag = false; 
                valiDationFlag = false; 
            } 
        }
        if( valiDationFlag == true ){
            fieldInput.parent().parent().find('.error-message').append('<i class="far fa-check-circle" style="color:green"></i>');
            mappingFlag = true; 
        }
    });
    return mappingFlag;
}
$(document).ready(function () {
    $('.nav-tabs > li a[title]').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target);
        if (target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {
        var parentId = $(this).parent().parent().parent().parent().attr('id');
        if( parentId == 'upload'){
            var flag = 1;
            var tableListLength = $('#table_list').find(":selected").val();
            var jsonUploadfile = $('#json_file').prop('files').length;

            if( jsonUploadfile == 0 ){
                flag = 0;
                addErrorMessage('json_file','Please Upload JSON file.')
            } else if(jsonUploadfile != 0 ){
                if( $('#json_file').prop('files')[0]['type'] != 'application/json' ){
                    flag = 0;
                    addErrorMessage('json_file','Please Upload only JSON file format.');
                } else{
                    addErrorMessage('json_file','');
                    if( tableListLength == '' ){
                        flag = 0;
                        addErrorMessage('table_list','Please select Any one Option.');
                    } else {
                        flag = 1;
                    } 
                }
            }   
            if( flag == 1 ){
                addErrorMessage('table_list','');
                var data = new FormData();
                var form_data = $('#json_add_data_form').serializeArray();
                $.each(form_data, function (key, input) {
                    data.append(input.name, input.value);
                });

                var file_data = $('input[name="json_file"]')[0].files;
                for (var i = 0; i < file_data.length; i++) {
                    data.append("json_file[]", file_data[i]);
                }
                data.append('_token',CSRF_TOKEN);
                data.append('key', 'value');
                var parentData = $(this).parent();
                parentData.find('span.spinner').show();
                $(this).prop('disabled',true);
                $.ajax({
                    url: URL+"/get-json-data",
                    method: "post",
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    data: data,
                    cache: false,
                    success: function (response) {
                        parentData.find('span.spinner').hide();
                        parentData.find('.next-step').prop('disabled',false);
                        if( response.status == 0 ){
                            errorNotification( '<p>'+response.message+'</p>' )
                            return false;
                        } else {
                            $('#db_fields_mapping').empty().append(response);
                            parentData.find('span.spinner').hide();
                            $(this).prop('disabled',false);
                            setTimeout(function(){
                                $('#db_fields_mapping tr').each(function(){
                                    if( $(this).find('td .form-control').hasClass('json-datetime') ){
                                        var dateId = $(this).find('td .json-datetime').attr('id');
                                        $('#'+dateId).daterangepicker({
                                            autoUpdateInput: false,
                                            timePicker : true,
                                            singleDatePicker:true,
                                            timePicker24Hour : true,
                                            timePickerIncrement : 1,
                                            timePickerSeconds : true,
                                            locale: {
                                                format: 'MM-DD-YYYY HH:mm:ss',
                                            },
                                        }, (from_date) => {
                                            $('#'+dateId).val(from_date.format('MM-DD-YYYY HH:mm:ss'));
                                        });
                                    }
                                    if( $(this).find('td .form-control').hasClass('json-year')){
                                        var dateId = $(this).find('td .json-year').attr('id');
                                        $('#'+dateId).datepicker({
                                            format: "yyyy",
                                            viewMode: "years",
                                            minViewMode: "years"
                                        });
                                    }

                                    if($(this).find('td .form-control').hasClass('json-time')){
                                        var dateId = $(this).find('td .json-time').attr('id');
                                        $('#'+dateId).daterangepicker({
                                            timePicker : true,
                                            singleDatePicker:true,
                                            timePicker24Hour : true,
                                            timePickerIncrement : 1,
                                            timePickerSeconds : true,
                                            locale : {
                                                format : 'HH:mm:ss'
                                        }}, (from_date) => {
                                            $('#'+dateId).val(from_date.format('HH:mm:ss'));
                                        });
                                    }
                                });
                                fieldMapping();
                                $('#db_fields_mapping .json-mapping-option').change(function(){
                                    var selectVal = $(this).find(':selected').val();
                                    $(this).parent().parent().find('.json-mapping-field input[type="text"]').val(selectVal);
                                });
                                var active = $('.wizard .nav-tabs li.active');
                                active.next().removeClass('disabled');
                                nextTab(active);
                            }, 1000);
                            return true;
                        }
                    }
                });
            } else {
                return false;
            }
        } else if( parentId == 'fieldmapping') {
            var flagResponse = fieldMapping();
            if( flagResponse == true ){
                var parentData = $(this).parent();
                parentData.find('span.spinner').show();
                var formData = $('form').serializeArray();
                var url = URL+'/json-mapping-data';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, data: formData },
                    success: function(response){
                        parentData.find('span.spinner').hide();
                        if( response.status == 0 ){
                            jsonErrorMessage( response.class, response.message )
                            return false;
                        }else{
                            $('#preview .table-field-list').empty();
                            $('#preview .table-field-list').append(response);
                            setTimeout(function(){
                                var active = $('.wizard .nav-tabs li.active');
                                active.next().removeClass('disabled');
                                nextTab(active);
                            },1000);
                        }
                    }
                });
            } else {
                return false;
            }
        } else if( parentId == 'preview') {
            var parentData = $(this).parent();
            parentData.find('span.spinner').show();
            var formData = $('form').serializeArray();
                var url = URL+'/insert-json-data';
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, data: formData },
                    success: function(response){
                        parentData.find('span.spinner').hide();
                        if( response.status == 1 ){
                            sucessNotification(response.message)
                            setTimeout( function() {window.location.href = URL  ; },1000 );
                            return true;
                        } else {
                            errorNotification('<p>'+response.message+'</p>');
                            return false;
                        }
                    }
                });
            return false;
        } else {
            var active = $('.wizard .nav-tabs li.active');
            active.next().removeClass('disabled');
            nextTab(active);
        }
    });
    $(".prev-step").click(function (e) {
        var active = $('.wizard .nav-tabs li.active');
        prevTab(active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}


$('.nav-tabs').on('click', 'li', function() {
    $('.nav-tabs li.active').removeClass('active');
    $(this).addClass('active');
});
