var URL = $("meta[name='web-url']").attr('content');
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
var STORAGE = $('meta[name="storage-path"]').attr('content');

function nullNumber(number){
    if( number === null ){
        return 0;
    }else{
        return number;
    }
}
function success( message ){
    $(".alert-notification").show().removeClass('alert-danger').addClass('alert-success');
    $(".alert-notification").show().empty().text(message);
}

function error( message, className ){
    $(".alert-notification").show().removeClass('alert-success').addClass('alert-danger');
    $(".alert-notification").show().empty().text(message);
    if( className !== '' ){
      $('#'+className).removeClass('is-valid').addClass('is-invalid');
    }
}
function checkedCheckbox( fieldName, fieldValue ){
  if( fieldValue == 1 ){
      $(fieldName).attr('checked',true);
  }else{
      $(fieldName).attr('checked',false);
  }
}

function getCurrentDate(time = false){
  var timeData = "";
  var today = new Date();
  if( time != false){  
    var twoDigitMinites = ((today.getMinutes().length+1) === 1)? (today.getMinutes()) : '0' + (today.getMinutes());
    timeData = today.getHours() + ":" + twoDigitMinites + ":" + today.getSeconds();
  }
  var twoDigitMonth = ((today.getMonth().length+1) === 1)? (today.getMonth()+1) : '0' + (today.getMonth()+1);
  var date = twoDigitMonth+'/'+today.getDate()+'/'+today.getFullYear();
  return date+timeData;
}

function dataAppend( fieldClass, fieldValue, flag = 0, fieldName = '' ){
  $(fieldClass).empty().append(fieldValue);
  if( flag == 1 ){
    var currentDate = getCurrentDate();
    var getAdvertiserName = $('#advertiser_name').val();
    $('.send-to-approval').prop("disabled", false);
    $(fieldClass).addClass('bg-active');
    $('#summary .date_change').addClass('bg-active').text(currentDate);
    $('#summary .chnage_by').addClass('bg-active').text(getAdvertiserName);
  }
  if( ( fieldName != '' ) && ( fieldValue ==  fieldName ) ){
    $(fieldClass).removeClass('bg-active');
  }
}

function dataValue( fieldName, fieldValue ){
  $(fieldName).val(fieldValue);
}
function sucessNotification( message ){
  $(".alert-notification-success .success-message").empty().text(message);
  $(".alert-notification-success").show('medium');
  setTimeout(function(){
    $(".alert-notification-success").hide('medium');
  }, 5000);
  $(".sendButton .close").click(function(){
      $(".alert-notification-success").hide('medium');
  });
}
function errorNotification( message ){
  $(".alert-notification-error .error-message").empty().text(message);
  $(".alert-notification-error").show('medium');
  setTimeout(function(){
    $(".alert-notification-error").hide('medium');
  }, 5000);
  $(".sendButton .close").click(function(){
      $(".alert-notification-error").hide('medium');
  });
}

function addErrorMessage(name, message){
    var errorMessageDiv = '<label class="error invalid-feedback" for="'+name+'">'+message+'</label>';
    $('#'+name).parent().find('label.error').empty();
    if( message != '' ){
        $('#'+name).parent().append(errorMessageDiv);
    }
}
/*
var timer2 = "02:00";
var interval = setInterval(function() {
  var timer = timer2.split(':');
  //by parsing integer, I avoid all extra string processing
  var minutes = parseInt(timer[0], 10);
  var seconds = parseInt(timer[1], 10); --seconds;
  minutes = (seconds < 0) ? --minutes : minutes;
  if (minutes < 0) clearInterval(interval);
  seconds = (seconds < 0) ? 59 : seconds;
  seconds = (seconds < 10) ? '0' + seconds : seconds;
  //minutes = (minutes < 10) ?  minutes : minutes;
  console.log(minutes + ':' + seconds);
  timer2 = minutes + ':' + seconds;
}, 1000);
*/
function countdown( timer2 = null) {
  var interval = setInterval(function() {
    var timer = timer2.split(':');
    //by parsing integer, I avoid all extra string processing
    var minutes = parseInt(timer[0], 10);
    var seconds = parseInt(timer[1], 10); --seconds;
    minutes = (seconds < 0) ? --minutes : minutes;
    if (minutes < 0) clearInterval(interval);
    seconds = (seconds < 0) ? 59 : seconds;
    seconds = (seconds < 10) ? '0' + seconds : seconds; 
    var timerText = minutes + ':' + seconds;
    if( timerText != '-1:59' ){
      $('.email-time .counter').empty().append(timerText);
    }
    if( ( seconds == '00' ) && ( minutes == '0' ) ){
      $('#email_send_otp').show();
      $('#email_send_otp').css({'pointer-events': 'auto','cursor': 'pointer','opacity': '1'});
    }
    timer2 = minutes + ':' + seconds;
  }, 1000);
}

function uniqueArray(list) {
  var result = [];
  $.each(list, function(i, e) {
      if ($.inArray(e, result) == -1) result.push(e);
  });
  return result;
}
$(document).ready(function(){
    var menu = $('.js-item-menu');
    var sub_menu_is_showed = -1;

    for (var i = 0; i < menu.length; i++) {
      $(menu[i]).on('click', function (e) {
        e.preventDefault();
        $('.js-right-sidebar').removeClass("show-sidebar");        
        if (jQuery.inArray(this, menu) == sub_menu_is_showed) {
          $(this).toggleClass('show-dropdown');
          sub_menu_is_showed = -1;
        }
        else {
          for (var i = 0; i < menu.length; i++) {
            $(menu[i]).removeClass("show-dropdown");
          }
          $(this).toggleClass('show-dropdown');
          sub_menu_is_showed = jQuery.inArray(this, menu);
        }
      });
    }
    $(".js-item-menu, .js-dropdown").click(function (event) {
      event.stopPropagation();
    });

    $("body,html").on("click", function () {
      for (var i = 0; i < menu.length; i++) {
        menu[i].classList.remove("show-dropdown");
      }
      sub_menu_is_showed = -1;
    });
    
    $('.bars-icon').click(function() {
      $('body').toggleClass('header-resize');
      $('body').toggleClass('body-resize');
    });
    $('.navbar-sidebar ul li').mouseover(function(){
        $('body').addClass('hover-resize');
    });
    $('.navbar-sidebar ul li').mouseout(function(){
        $('body').removeClass('hover-resize');
    });
    $(".dropdown-close").click(function(){
      $(".account-item").removeClass("show-dropdown");
   });


   var validNavigation = false;

        // Attach the event keypress to exclude the F5 refresh (includes normal refresh)
        $(document).bind('keypress', function(e) {
            if (e.keyCode == 116){
                validNavigation = true;
            }
        });

        // Attach the event click for all links in the page
        $("a").bind("click", function() {
            validNavigation = true;
        });

        // Attach the event submit for all forms in the page
        $("form").bind("submit", function() {
          validNavigation = true;
        });

        // Attach the event click for all inputs in the page
        $("input[type=submit]").bind("click", function() {
          validNavigation = true;
        }); 

        /*$(window).on("unload", function() {
          if( window.performance.navigation.type ==  performance.navigation.TYPE_RELOAD ) {
            validNavigation = true;
          } else{
            if (!validNavigation) {     
              //window.close();
              $.cookie("advertiser_session",null,{domain:'localhost'},{path:'/'});
              $.cookie("laravel_session",null,{domain:'localhost'},{path:'/'});
              $.cookie("XSRF-TOKEN",null,{domain:'localhost'},{path:'/'},{expire:'2023-07-23T14:38:44.957Z'});
              location.reload();
          }
          }
        })
        */

});
