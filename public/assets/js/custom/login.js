$(document).ready(function(){
    /*************************************************** Login Page Start  ***************************************************/

    function errorMessage(name, message){
        var validator = $( "#login_form" ).validate();
        validator.resetForm();
        if( name == 'user_name' ){
            validator.showErrors({
                "user_name": message
            });
        } else if( name == 'password' ){
            validator.showErrors({
                "password": message
            });
        } else if( name == 'media' ){
            validator.showErrors({
                "media": message
            });
        } else {
            error( message, name );
        }
    }
    var CSRF_TOKEN = $("meta[name='csrf-token']").attr('content');
    var URL = $("meta[name='web-url']").attr('content');
    $("#login_form input[name='user_name']").on("keyup change", function(e){
        var userName = $(this).val();
        var data = {username:userName, flag: 1};
        $.ajax({
            url: URL+"/post-login-user",
            type: 'POST',
            data: {_token: CSRF_TOKEN, data: data },
            success: function(response){
                if( response != 1 ){
                    errorMessage( response.class, response.message );
                    return false;
                }
            }
        });
    });
    
    $("#login_form input[name='password']").on("keyup change", function(e){
        var userNameCheck = $("#login_form input[name='user_name']").val();
        if( userNameCheck != '' ){
            var password = $(this).val();
            var data = {username:userNameCheck, password: btoa(password), flag: 2};
            $.ajax({
                url: URL+"/post-login-user",
                type: 'POST',
                data: {_token: CSRF_TOKEN, data: data },
                success: function(response){
                    if( response != 1 ){
                        errorMessage( response.class, response.message );
                        return false;
                    }
                }
            });
        }else{
            $("#user_name-error").text('Please Insert User Name.');
            $("#user_name").removeClass("is-valid").addClass("is-invalid");
            return false;
        }
    });
    
    
    $("#login_form select#media").change(function(e){
        var userNameCheck = $("#login_form input[name='user_name']").val();
        var mediaId = $(this).val();
        var password = $("#login_form input[name='password']").val();
        if( userNameCheck != '' ){
            var data = {username:userNameCheck,  password: btoa(password), media:mediaId, flag: 3};
            $.ajax({
                url: URL+"/post-login-user",
                type: 'POST',
                data: {_token: CSRF_TOKEN, data: data },
                success: function(response){
                    if( response != 1 ){
                        errorMessage( response.class, response.message );
                    return false;
                    }
                }
            });
        }else{
            $("#user_name-error").text('Please Insert User Name.');
            $("#user_name").removeClass("is-valid").addClass("is-invalid");
            return false;
        }
    }); 

    $("#login_form").validate({
        rules: {
            user_name: { required: true },
            password: { required: true },
            media: { required: true },
        },
        messages: {
            user_name: { required: "Please enter User Name." },
            password: { required: "Please enter Password." },
            media: { required: "Please select Media Line" },
        },
        errorClass: 'error invalid-feedback',
        highlight: function (element) {
           // $(element).closest('.control-group').removeClass('success').addClass('text-danger');
           $(element).addClass('is-invalid');
           $(element).parent().find('.error').addClass('invalid-feedback');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parent().find('.error').addClass('invalid-feedback');
        },
        success: function (element) {
            $(element).parent().find('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        submitHandler: function(form) {
            $('#login_form .submit-sec .spinner').show();
            $('#login_form input[type="submit"]').prop('disabled', true);
            $('#login_form #user_name').prop('disabled', true);
            $('#login_form #password').prop('disabled', true);
            $('#login_form #media').prop('disabled', true);
            var userName = $("#user_name").val();
            var password = $("#password").val();
            var media = $("#media").val();
            $.ajax({
                url: URL+"/post-login",
                type: "POST",
                data: {_token: CSRF_TOKEN, user_name: userName, password: btoa(password), media:media },
                success: function(response) {
                    $('#login_form input[type="submit"]').prop('disabled', false);
                    $('#login_form #user_name').prop('disabled', false);
                    $('#login_form #password').prop('disabled', false);
                    $('#login_form #media').prop('disabled', false);
                    if( response.status == 0 ){
                        $('#login_form .submit-sec .spinner').hide();
                        errorMessage( response.class, response.message );
                        return false;
                    }else{
                        $('#login_form .submit-sec .spinner').hide();
                        success( response.message );
                        setTimeout(function(){
                            $('.login .login-form').hide();
                            $('.login .verify-account-form').show();
                            $('.login .alert-notification').hide();
                            countdown('05:00');
                            $('#email_send_otp').hide();
                        },2000);
                    }
                }            
            });
        }
    });

    /******************************************************** Login Page End  *********************************************************/

    /*************************************************** Verify Account Page Start  ***************************************************/

    $('#verify_account_form').find('input').each(function() {
        $(this).attr('maxlength', 1);
        $(this).on('keyup', function(e) {
            var parent = $($(this).parent());
            
            if(e.keyCode === 8 || e.keyCode === 37) {
                var prev = parent.find('input#' + $(this).data('previous'));
                
                if(prev.length) {
                    $(prev).select();
                }
            } else if((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
                var next = parent.find('input#' + $(this).data('next'));
                
                if(next.length) {
                    $(next).select();
                } else {
                    if(parent.data('autosubmit')) {
                        parent.submit();
                    }
                }
            }
        });
    });
    $( '#verify_account_form input' ).on( "copy cut paste drop", function() {
        return false;
    });

    $('#email_send_otp').click(function(){
        countdown('05:00');
        $('#email_send_otp').css({'pointer-events': 'all','cursor': 'not-allowed','opacity': '0.7'});
        $('.email-otp-number input[type="number"]').each(function(){
            $(this).prop('disabled', true);
            $(this).val('');
        });
        var password = $(this).val();
        var data = {resend:1};
        $.ajax({
            url: URL+"/post-resend-otp",
            type: 'POST',
            data: {_token: CSRF_TOKEN, data: data },
            success: function(response){
                if( response.status == 0 ){
                    error( response.message, response.class );
                }else{
                    $('.email-otp-number input[type="number"]').each(function(){
                        $(this).prop('disabled', false);
                    });
                    success( response.message );
                }
                return false;
            }
        });
        return false;
    });

    $('.email-otp-number input[type="number"]').keypress(function(e){
        var checkValue = this.value;
        if ( checkValue.length >= 1 )
        {
            var changeValue = checkValue.substring(0,checkValue.length);
            $(this).val(changeValue);
            return false;
        }
    });

    $("#verify_account_form").validate({
        submitHandler: function(form) {
            var flag = true;
            var veryFicationCode = [];
            $('.email-otp-number input[type="number"]').each(function(){
                var getCheckboxVal = $(this).val();
                if( getCheckboxVal == ''){
                    error( 'Please Insert Verification Code Send On Your Register Email Address.', '' );
                    flag = false;
                    return false;
                } else {
                    veryFicationCode.push(getCheckboxVal);
                    if( veryFicationCode.length == 6 ){
                        $('.alert-notification').hide();
                    }
                }
            });

            if( veryFicationCode.length == 6 ){
                $('#verify_account_form .submit-sec .spinner').show();
                $('.email-otp-number input[type="number"]').each(function(){
                    $(this).prop('disabled', true);
                });
                var otpCode = veryFicationCode.join('');
                var data = { otp: btoa(otpCode) };
                $.ajax({
                    url: URL+"/post-verify-otp",
                    type: 'POST',
                    data: {_token: CSRF_TOKEN, data: data },
                    success: function(response){
                        $('#verify_account_form .submit-sec .spinner').hide();
                        if( response.status == 0 ){
                            $('.email-otp-number input[type="number"]').each(function(){
                                $(this).prop('disabled', false);
                            });
                            error( response.message, response.class );
                            return false;
                        }else{
                            success( response.message );
                            setTimeout(function(){
                                window.location.href = URL;
                            },500);
                            return true;
                        }
                    }
                });
            }
            return false;
        }
    });

    /*************************************************** Verify Account Page End  ***************************************************/
});