
$(document).ready(function() {

});


function setReaderOpt(option, status)
{

    status = (status)?"1":"0";
    $.ajax({
        url: getAbsoluteUrl('user/set-reader-opt'),
        type: 'POST',
         data: {option: option, status:status},
         success: function(data) {
            if(data.status != "ok"){
                alert("Can't change option. Please try again");
                return false;
            }
            if(option == "voice")location.reload();
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
    
}

function changeReaderStatus(obj)
{
    
    // var url = (obj.options[obj.selectedIndex].value == 'available')?'user/set-active':'user/set-inactive';
    var url = (obj === "available")?"user/set-active":"user/set-inactive";
    $.ajax({
        url: getAbsoluteUrl(url),
        type: 'POST',
         data: {ajax: 1},
         success: function(data) {
            if(data.status !== "ok"){
                alert("Can't change status. Please try again");
                return false;
            }
            
            location.reload();
            
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
     
}

function answerReaderCall()
{
    window.twilioConnection.accept();
    showCallDetails();
}

function rejectReaderCall()
{
    window.twilioConnection.reject();
    if($('#call-details-modal').hasClass('in'))$('#call-details-modal').modal('toggle');
}

/*
function getReaderCalls(){
   
    if(typeof window.activityStatusTimeout !== 'undefined')clearTimeout(window.activityStatusTimeout);
    
    $.ajax({
        url: getAbsoluteUrl('call/details'),
        type: 'POST',
         data: { test: '15' },
         success: function(data) {
            if(data.status != "ok"){
                alert("Session has expired. Please log in again");
                return false;
            }
            
            if(typeof data.data.show_popup !== 'undefined' && data.data.show_popup == '1'){
                
                if(typeof window.twilioConnection !== 'undefined'){
                    $("#call-details-modal-placeholder").html(data.data.html);
                    if(!$('#call-details-modal').hasClass('in'))$('#call-details-modal').modal('show');
                    if(typeof window.activityStatusTimeout === 'undefined')window.activityStatusTimeout = setTimeout(getCalls, 1024);
                }else{//connection is lost somehow
                    $("#call-details-modal-placeholder").html("Client's web page doesn't respond. <a href='#' onclick='endCall(); return false;'>abort</a>");
                    if(typeof window.activityStatusTimeout === 'undefined')window.activityStatusTimeout = setTimeout(getCalls, 4096);
                    if($('#call-details-modal').hasClass('in'))$('#call-details-modal').modal('toggle');
                }
                
            }else{
                    if(typeof window.activityStatusTimeout === 'undefined')window.activityStatusTimeout = setTimeout(getCalls, 8192);
                    if($('#call-details-modal').hasClass('in'))$('#call-details-modal').modal('toggle');
                
            }
            
            
         },
         error: function(data) {
            alert("Session has expired. Please log in again");
            return false;
         },
     });
}*/
