function getMessages(readerId, maxMessageId){

    $("#load-more-btn-"+maxMessageId).hide();

    $.ajax({
        url: getAbsoluteUrl('message/list-ajax'),
        type: 'POST',
        dataType : 'json',
         data: { readerId : readerId, maxMessageId : maxMessageId},
         success: function(data) {
            if(data.status == "ok"){
               $("#load-more-btn-"+maxMessageId).remove();
               $("#messages-list-cnt").prepend( data.data.chat.html );
               if(maxMessageId == 0)scrollToBottom("messages-list-cnt", 2000);
            }else if(data.message != ""){
                alert(data.message);
                $("#load-more-btn-"+maxMessageId).show();
            }else{
                alert("unhandled exception");
                $("#load-more-btn-"+maxMessageId).show();
            }
         },
         error: function(data) {
            $("#load-more-btn-"+maxMessageId).show();
            alert("connection error");
            return false;
         },
     });

}

function sendMessage(readerId, customerId, message){

    $.ajax({
        url: getAbsoluteUrl('message/create-ajax'),
        type: 'POST',
        dataType : 'json',
         data: { readerId : readerId, customerId : customerId, message: message},
         success: function(data) {

            if(data.status == "ok"){
               $("#message-content").val('');
               //window.minMessageId = 0;
               window.scrollAnyway = true;
               Gruvi.ping();
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
         },
         error: function(data) {
            alert("connection error");
            return false;
         },
     });

}

function deleteMessage(id){
    
    if(!confirm("Are you sure you want to delete this message?"))return false;
    
    $.ajax({
        url: getAbsoluteUrl('message/delete-ajax'),
        type: 'POST',
        dataType : 'json',
         data: { id : id},
         success: function(data) {

            if(data.status == "ok"){
                $("#chat-message-row-"+id).hide('slow', function(){ $("#chat-message-row-"+id).remove(); });
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
         },
         error: function(data) {
            alert("connection error");
            return false;
         },
     });
}

    
function endCall(){
    
    if(typeof window.checkAmountOfGruviBucksTimeout !== 'undefined')clearTimeout(window.checkAmountOfGruviBucksTimeout);
    
    Twilio.Device.disconnectAll();
    $("#start_call_cnt").show();
    $("#end_call_cnt").hide();
    
    $.ajax({
        url: getAbsoluteUrl('call/end'),
        type: 'GET',
        dataType : 'json',
         data: { },
         success: function(data) {
            if(data.status == "ok"){
               $('#call-details-modal').modal('hide');
            }
         },
         error: function(data) {
            alert("connection error");
            return false;
         },
     });
}
//both
function refreshReadersTeaser(activity, page){
    
    if(typeof window.RTTimout !== 'undefined'){
        clearTimeout(window.RTTimout);
        delete window.RTTimout;
    }
    
    
    $.ajax({
        url: getAbsoluteUrl('user/get-readers-teaser'),
        type: 'POST',
        dataType : 'json',
         data: { activity: activity, page: page, md5: $("#readers_teaser_current_md5").val(), keyword: $("#reader_keyword_input").val()},
         success: function(data) {
            $('#reader_keyword_input :input').prop('disabled', false);
            if(data.status == "ok"){
                if(data.data.html != ""){
                    $("#readers_teaser_cnt").html(data.data.html);
                }else{
                    if(typeof window.RTTimout === 'undefined'){
                        //window.RTTimout = setTimeout("refreshReadersTeaser('"+activity+"', '"+page+"');", 4096); // %PSG: disable to fix pagination jump
                    }
                }
                return false;
            }
         },
         error: function(data) {
            $('#reader_keyword_input :input').prop('disabled', false)
            //alert("connection error");
            return false;
         },
     });
}
//both
function showCallDetails(readerId){
    
    $('#call-details-modal-placeholder').html("Loading...");
    $('#call-details-modal').modal('show');
    
    $.ajax({
        url: getAbsoluteUrl('call/details'),
        type: 'GET',
        dataType : 'json',
         data: { readerId: readerId, popup: 1 },
         success: function(data) {

            if(data.status == "ok"){
                $("#call-details-modal-placeholder").html(data.data.html);
                return true;
            }
            $("#call-details-modal-placeholder").html("no data");
         },
         error: function(data) {
            //alert("connection error");
            $('#call-details-modal-placeholder').html("Connection error");
            return false;
         },
     });
}

function showSessionExpiredMessage(so){
    if((so && !$('#expired_session_modal_alert').hasClass('in'))){
        setTimeout(instantSessionExpiredAlert, 3000);
    }
    
    if(!so && $('#expired_session_modal_alert').hasClass('in')){
      $("#expired_session_modal_alert").modal("hide");  
    }
}

function instantSessionExpiredAlert(){
   $("#expired_session_modal_alert").modal("show");
}

function getAbsoluteUrl(url){
   return ((typeof window.baseUrl !== 'undefined')?window.baseUrl:"/")+url;
}

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight)+"px";
}

function isScrolledToBottom(id) {
    el = document.getElementById(id);
    if(el == null)return false;
    var $el = $(el);
    return el.scrollHeight - $el.scrollTop() - $el.outerHeight() < 10;
}

function scrollToBottom(id, ms) {
    $("#"+id).animate({ scrollTop: $('#'+id).prop("scrollHeight")}, ms);
}

function confirmBlockUser(userId, messageId)
{
    $("#block_user_modal_name_placeholder").html( $("#chat_profile_user_lnk_"+messageId).html() );
    $("#block_user_modal_submit_button").attr('onclick', 'blockUser('+userId+', '+messageId+')');
    $("#block_user_alert_modal").modal("toggle");
}

function blockUser(userId, messageId)
{
    $.ajax({
        url: getAbsoluteUrl('user/block-ajax'),
        type: 'POST',
         data: {userId: userId, messageId:messageId},
         success: function(data) {
             
            if(data.status == "ok"){
               window.minMessageId = 0;
               Gruvi.ping();
               $("#block_user_alert_modal").modal("hide");
               location.reload();
            }else if(data.message != ""){
                alert(data.message);
            }else{
                alert("unhandled exception");
            }
            
         },
         error: function(data) {
            alert("request failed");
            return false;
         },
     });
}

function unblockUser(rowId)
{
    
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

// ===========================

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

    $(document).on('submit', 'form.form-report_user', function (e) {

        e.preventDefault();
        // Called from modal to perform action and clean-up

        var context = $(this);
        var crate = context.closest(".crate-modal");

        //reportUser(userId, meesageId, report_reason);
        $.ajax({
            url: getAbsoluteUrl('user/report-ajax'),
            type: 'POST',
            data: context.serialize(),
            success: function(data) {
                
                var globalModal = context.closest('#global_modal');
                globalModal.modal('hide');
                $("#supercontainer-modal_placeholder").html(''); // empty contents!

                if (data.status == "ok") {
                    window.minMessageId = 0;
                    Gruvi.ping();
                } else if(data.message != ""){
                    alert(data.message);
                } else{
                    alert("unhandled exception");
                }
                
                //location.reload();
            },
            error: function(data) {
                alert("request failed");
                return false;
            },
        });


        return false;
    });


});
