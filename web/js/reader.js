
$(document).ready(function() {

    $(document).on('click', '#global_modal .tag-clickme_to_cancel', function (e) {
        var context = $(this);
        var globalModal = context.closest('#global_modal');
        globalModal.modal('hide');
        $("#supercontainer-modal_placeholder").html(''); // empty contents!
        return false;
    });

    $(document).on('click', '.tag-clickme_to_show_report_modal', function (e) {

        // Shows the modal

        var context = $(this);

        //$("#report_user_modal_name_placeholder").html( $("#chat_profile_user_lnk_"+messageId).html() ); // fill in name
        //$("#report_user_alert_modal").modal("toggle"); // show modal

        var url = context.data('route');
        var payload = {
                        'reporter_id': context.data('reporter_id'),
                        'reported_id': context.data('reported_id'),
                        'message_id': context.data('message_id')
                      };
        $.getJSON( url, payload, function(response) {
            $("#supercontainer-modal_placeholder").html(response.data.html);
            $("#global_modal").modal("toggle"); // show modal
        });

        // %FIXME: need to make an ajax call to (a) populate a modal placeholder (b) show it
        // ...tear down should remove the contents that was added

        //$('div.tag-report_selection  .active').trigger("click");
        //$('.selectpicker').selectpicker('refresh');
    });

    $(document).on('submi', 'form.form-report_user', function (e) {

        e.preventDefault();
        // Called from modal to perform action and clean-up

        var context = $(this);
        var crate = context.closest(".crate-modal");
        var userId = crate.find

        reportUser(userId, meesageId, report_reason);

        $("#report_user_alert_modal").modal("hide");

        return false;
    });
});

function confirmBlockUser(userId, messageId){
    $("#block_user_modal_name_placeholder").html( $("#chat_profile_user_lnk_"+messageId).html() );
    $("#block_user_modal_submit_button").attr('onclick', 'blockUser('+userId+', '+messageId+')');
    $("#block_user_alert_modal").modal("toggle");
}
/*
function confirmReportUser(userId, messageId){
    $("#report_user_modal_name_placeholder").html( $("#chat_profile_user_lnk_"+messageId).html() );
    $("#report_user_modal_submit_button").attr('onclick', 'reportUser('+userId+', '+messageId+', '+report_reason+')');
    $("#report_user_alert_modal").modal("toggle");
}
*/

function reportUser(userId, messageId, report_reason) {
    
    $.ajax({
        url: getAbsoluteUrl('user/report-ajax'),
        type: 'POST',
         data: {
             userId: userId, 
             messageId:messageId,
             report_reason:report_reason
         },
         success: function(data) {
             
            if(data.status == "ok"){
               window.minMessageId = 0;
               Gruvi.ping();
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
            
            //location.reload();
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
}

function blockUser(userId, messageId){
    
    $.ajax({
        url: getAbsoluteUrl('user/block-ajax'),
        type: 'POST',
         data: {userId: userId, messageId:messageId},
         success: function(data) {
             
            if(data.status == "ok"){
               window.minMessageId = 0;
               Gruvi.ping();
               $("#block_user_alert_modal").modal("hide");
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
            
            //location.reload();
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
}

function unblockUser(rowId){
    
    $.ajax({
        url: getAbsoluteUrl('user/unblock-ajax'),
        type: 'POST',
         data: {rowId: rowId},
         success: function(data) {
             
            if(data.status == "ok"){
               location.reload();
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
            
            //location.reload();
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
}


function setReaderOpt(option, status){

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

function changeReaderStatus(obj){
    
    var url = (obj.options[obj.selectedIndex].value == 'available')?'user/set-active':'user/set-inactive';
    $.ajax({
        url: getAbsoluteUrl(url),
        type: 'POST',
         data: {ajax: 1},
         success: function(data) {
            if(data.status != "ok"){
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

function answerReaderCall(){
    window.twilioConnection.accept();
    showCallDetails();
}

function rejectReaderCall(){
    window.twilioConnection.reject();
    if($('#call-details-modal').hasClass('in'))$('#call-details-modal').modal('toggle');
}
