//some of variables are in window.*** instead of Gruvi.*** due to weird reset in some cases (e.g. timer call)
var Gruvi = {
    ping: function() {
       
        if(window.gruviPinging)return false;
        window.gruviPinging = true;
        
        if(typeof window.pingInterval !== 'undefined')clearTimeout(window.pingInterval);
        if(typeof window.minMessageId === 'undefined')window.minMessageId = 0;
        
        $.ajax({
            url: getAbsoluteUrl('user/ping'),
            type: 'POST',
             data: { controller: window.controller, action: window.action, readerAjaxUpdate: window.readerAjaxUpdate, minMessageId: window.minMessageId },
             success: function(data) {
                
                //Session check
                window.gruviPinging = false;
                if(data.status != "ok"){
                    showSessionExpiredMessage(true);
                    window.pingInterval = setInterval(function(){Gruvi.ping()}, 4096);
                    return false;
                }
                showSessionExpiredMessage(false);
                window.pingInterval = setInterval(function(){Gruvi.ping()}, ((typeof data.data.call !== 'undefined')?2048:4096));
                
                
                //Gruvi Bucks update
                if(typeof data.data.gruviBucks !== 'undefined'){
                    
                    $("#gruvi_bucks_text_blue").html("$"+data.data.gruviBucks.amount);

                    if(data.data.gruviBucks.toggleGruviBucksModal == "1")$('#add-gruvi-bucks-modal').modal('show');
                    if(data.data.gruviBucks.toggleGruviBucksModal == "0")$("#add-gruvi-bucks-modal").modal("hide");
                    
                }
                
                //Call dialog
                console.log(data.data.call);
                if(typeof data.data.call !== 'undefined'){
                    
                    if(window.userRole != 'reader' || typeof window.twilioConnection !== 'undefined'){
                        var html_to_popup = data.data.call.html;
                    }else{//connection is lost somehow
                        //alert(window.action);
                        var html_to_popup = "Call connection is not active. <a href='#' onclick='endCall(); return false;'>abort</a>";
                    }
                    
                    $("#call-details-modal-placeholder").html(html_to_popup);
                    $('#call-details-modal').modal('show');

                }else{
                    if(document.getElementById('call-details-modal-no-auto-close') == null)$('#call-details-modal').modal('hide');
                }
                //alert(data.data.reader);
                //Update Readers Profile
                if(typeof data.data.reader !== 'undefined'){
                    $("#reader-activity-cnt").html(data.data.reader.activity);
                    //alert((data.data.reader.canCall == "1"));
                    $("#reader-call-btn").prop('disabled', (data.data.reader.canCall != "1"));
                    $("#reader-chat-btn").prop('disabled', (data.data.reader.canChat != "1"));
                }

                if(typeof data.data.chat !== 'undefined'){
                    if(data.data.chat.minMessageId > 0){
                        if(window.minMessageId < 1)$("#messages-list-cnt").html('');
                        window.minMessageId = data.data.chat.minMessageId;

                        if($("#chat-message-row-header-today").length > 0){
                            var $html = $('<div />',{html:data.data.chat.html});
                            $html.find('#chat-message-row-header-today').remove();
                            data.data.chat.html = $html.html();
                        }

                        if(isScrolledToBottom("messages-list-cnt")){
                            window.scrollAnyway = true;
                        }else if(window.scrollAnyway  === 'undefined'){
                            window.scrollAnyway = false;
                        }
                        
                        $("#messages-list-cnt").append( data.data.chat.html );
                        if(window.scrollAnyway){
                            scrollToBottom("messages-list-cnt", 500)
                            window.scrollAnyway = false;
                        }else{
                            $("#chat-new-messages-icon").show();
                        }
                        
                        
                        
                    }
                    if(data.data.chat.messagesClear != null && data.data.chat.messagesClear != ""){
                       // alert(data.data.chat.messagesClear);
                        $.each( data.data.chat.messagesClear.split(","), function( key, value ) {
                           // alert( key + ": " + value );
                           $("#chat-message-row-"+value).hide('slow', function(){ $("#chat-message-row-"+value).remove(); });
                        });
                    }
                }

             },
             error: function(data) {
                window.gruviPinging = false;
                showSessionExpiredMessage(true);
                window.pingInterval = setInterval(function(){Gruvi.ping()}, 4096);
                return false;
             },
         });
       
       
       
	},
    
}