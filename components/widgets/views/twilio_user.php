    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.3/twilio.min.js"></script>
    <script type="text/javascript">

      Twilio.Device.setup("<?= $token; ?>");

      Twilio.Device.ready(function (device) {
          $("#user-call-log").text("");
        //$("#log").text("Client '<?= $clientName; ?>' is ready");
      });

      Twilio.Device.error(function (error) {
        //$("#log").text("Error: " + error.message);
        $("#user-call-log").text("Error: " + error.message);
        Twilio.Device.setup("<?= $token; ?>");
        endCall();
      });

      Twilio.Device.connect(function (conn) {
        $("#user-call-log").text("Call in progress");
      });

      Twilio.Device.disconnect(function (conn) {
        $("#user-call-log").text("Call ended");
        endCall();
      });

    </script>