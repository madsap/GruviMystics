    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.3/twilio.min.js"></script>
    <script type="text/javascript">

      Twilio.Device.setup("<?= $token; ?>");

      Twilio.Device.ready(function (device) {
        $("#reader-log").html("<h3 style='color:green;font-weight:bold;'>ONLINE</h3>");
      });

      Twilio.Device.error(function (error) {
        $("#reader-log").text("Error: " + error.message);
        $("#reader-call-log").text("Error: " + error.message);
        Twilio.Device.setup("<?= $token; ?>");
      });

      Twilio.Device.connect(function (conn) {
        $("#reader-log").text("Successfully established call");
        //console.log("Successfully established call");
    
        $.ajax({
            url: getAbsoluteUrl('call/answer'),
            type: 'GET',
            dataType : 'json',
             data: { },
             success: function(data) {
                if(data.status == "ok"){
                   //getReaderCalls();
                   Gruvi.ping();
                   return false;
                }
             },
             error: function(data) {
                alert("connection error");
                return false;
             },
         });
     
      });

      Twilio.Device.disconnect(function (conn) {
        $("#reader-log").text("Call ended");
        console.log("Call ended");
      });

      Twilio.Device.incoming(function (conn) {
            window.twilioConnection = conn;
            showCallDetails();
            //console.log(conn);
        /*
        if(confirm("accept the incoming connection from " + conn.parameters.From + " and start two-way audio?")){
            conn.accept();
            console.log("Accepted");
        }else{
            conn.reject();
            console.log("Rejected");
        }
        $("#readder-log").text("Incoming connection from " + conn.parameters.From);
        */
      });

      function call() {
        // get the phone number to connect the call to
        params = {"PhoneNumber": $("#number").val(), "debug": true, "statusCallbackEvent": "initiated ringing answered completed"};
        Twilio.Device.connect(params);
      }

      function hangup() {
        Twilio.Device.disconnectAll();
      }
    </script>