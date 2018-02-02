<?php
use yii\helpers\Html;
use \yii\helpers\Url;
?>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= Url::to(['/css/chat.css'], true);?>">

<div class="page-wrapper">
  <div class="page-container panel panel-default">  
    <section>
      <div id="messages"></div>
      <input id="chat-input" type="text" placeholder="say anything" autofocus/>
    </section>

    <script src="https://media.twiliocdn.com/sdk/js/chat/v1.0/twilio-chat.js"></script>
  </div>  
</div>



<script>
    
    $(function() {
        // Get handle to the chat div 
        var $chatWindow = $('#messages');

        // Our interface to the Chat service
        var chatClient;

        // A handle to the "general" chat channel - the one and only channel we
        // will have in this sample app
        var generalChannel;

        // The server will assign the client a random username - store that value
        // here
        var username;

        // Helper function to print info messages to the chat window
        function print(infoMessage, asHtml) {
            var $msg = $('<div class="info">');
            if (asHtml) {
                $msg.html(infoMessage);
            } else {
                $msg.text(infoMessage);
            }
            $chatWindow.append($msg);
        }

        // Helper function to print chat message to the chat window
        function printMessage(fromUser, message) {
            var $user = $('<span class="username">').text(fromUser + ':');
            if (fromUser === username) {
                $user.addClass('me');
            }
            var $message = $('<span class="message">').text(message);
            var $container = $('<div class="message-container">');
            $container.append($user).append($message);
            $chatWindow.append($container);
            $chatWindow.scrollTop($chatWindow[0].scrollHeight);
        }

        // Alert the user they have been assigned a random username
        print('Logging in...');

        // Get an access token for the current user, passing a username (identity)
        // and a device ID - for browser-based apps, we'll always just use the 
        // value "browser"
        $.getJSON('<?= Url::to(['/twilio/chat-token'], true);?>', {
            device: 'user #'+Math.floor(Math.random() * 1000)
        }, function(data) {
            // Alert the user they have been assigned a random username
            username = $("<div>").text(data.identity).html();

            print('Success. Username: ' 
                + '<span class="me">' + username + '</span>', true);

            // Initialize the Chat client
            chatClient = new Twilio.Chat.Client(data.token);
            chatClient.getSubscribedChannels().then(createOrJoinGeneralChannel);        
        });

        function createOrJoinGeneralChannel() {
            // Get the general chat channel, which is where all the messages are
            // sent in this simple application
            print('Attempting to join "general" chat channel...');
            var promise = chatClient.getChannelByUniqueName('general');
            promise.then(function(channel) {
                generalChannel = channel;
                console.log('Found general channel:');
                console.log(generalChannel);
                setupChannel();
            }).catch(function() {
                // If it doesn't exist, let's create it
                console.log('Creating general channel');
                chatClient.createChannel({
                    uniqueName: 'general',
                    friendlyName: 'General Chat Channel'
                }).then(function(channel) {
                    console.log('Created general channel:');
                    console.log(channel);
                    generalChannel = channel;
                    setupChannel();
                });
            });
        }

        // Set up channel after it has been found
        function setupChannel() {
            // Join the general channel
            generalChannel.join().then(function(channel) {
                print('Joined channel as ' 
                    + '<span class="me">' + username + '</span>.', true);
            });

            // Listen for new messages sent to the channel
            generalChannel.on('messageAdded', function(message) {
                printMessage(message.author, message.body);
            });
        }

        // Send a new message to the general channel
        var $input = $('#chat-input');
        $input.on('keydown', function(e) {
            if (e.keyCode == 13) {
                generalChannel.sendMessage($input.val())
                $input.val('');
            }
        });
    });
    
</script>