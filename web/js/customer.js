function customerRequestACall(phone, readerId){

    status = (status)?"1":"0";
    $.ajax({
        url: getAbsoluteUrl('request-a-call/create-ajax'),
        type: 'POST',
         data: {readerId: readerId, phone:phone},
         success: function(data) {
            if(data.status != "ok"){
                console.log(data);
                alert(data.message);
                return false;
            }
            alert("Your request has been sent successfully");
         },
         error: function(data) {
             //console.log(data);
             var show = (typeof data.responseJSON !== 'undefined')?data.responseJSON.message:"request failed";
            alert(show);
            return false;
         },
     });

}

//customer
function customerStartCall(number, rate){
    
    if(Twilio.Device.status() == 'ready'){
        params = {"PhoneNumber": number, "debug": true, statusCallbackEvent: "initiated ringing answered completed"};
        Twilio.Device.connect(params);
        $("#start_call_cnt").hide();
        $("#end_call_cnt").show();
        $("#user-call-log").text("Dialing...");
        /*
        if(typeof window.checkAmountOfGruviBucksTimeout === 'undefined'){
            window.checkAmountOfGruviBucksTimeout = setTimeout('CustomerCheckAmountOfGruviBucks('+rate+')', 8192);
        }*/
    }else{
        alert("Device isn't ready");
    }
}

/*
function CustomerCheckCallStatus(){
    
    if(typeof window.showCallDetailsTimeout !== 'undefined')clearTimeout(window.showCallDetailsTimeout);
    $.ajax({
        url: getAbsoluteUrl('call/details'),
        type: 'GET',
        dataType : 'json',
         data: { readerId: 0, popup: 1 },
         success: function(data) {
            
            if(typeof data.data.show_popup !== 'undefined' && data.data.show_popup == '1'){
                $('#call-details-modal').modal('show');
                $("#call-details-modal-placeholder").html(data.data.html);
                //window.showCallDetailsTimeout = setTimeout(CustomerCheckCallStatus, 2048);
                return true;
            }else{
                $("#call-details-modal-placeholder").html("the conversation is over..");
            }
            
         },
         error: function(data) {
            //alert("connection error");
            return false;
         },
     });
}*/

//customer
/*
function CustomerCheckAmountOfGruviBucks(rate){
    
    if(typeof window.checkAmountOfGruviBucksTimeout !== 'undefined')clearTimeout(window.checkAmountOfGruviBucksTimeout);
    $.ajax({
        url: getAbsoluteUrl('user/get-amount-of-gruvi-bucks'),
        type: 'GET',
        dataType : 'json',
         data: { popup: 1 },
         success: function(data) {
            if(typeof data.data.amount !== 'undefined'){
                if(data.data.amount < rate){
                    $("#add-gruvi-bucks-modal").modal("toggle");
                }else{
                    window.checkAmountOfGruviBucksTimeout = setTimeout('CustomerCheckAmountOfGruviBucks('+rate+')', 8192);
                }
                $("#gruvi_bucks_text_blue").html("$"+data.data.amount);
                return true;
            }
            
         },
         error: function(data) {
            //alert("connection error");
            return false;
         },
     });
}*/