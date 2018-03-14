<?php

use yii\helpers\Html;
use \yii\helpers\Url;

$this->title = 'Gruvi Bucks';
$this->params['breadcrumbs'][] = ['label' => 'Gruvi Bucks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper gruvi-bucks-create col-xs-12">
    <div class="page-title text-default h3">Add <span class="text-bold text-orange"><?= Html::encode($this->title) ?></span> to your account</div>
    <div class="page-container">

    
    
        <div class="row">
            <div class="col-sm-4">
                <div class="add_title">
                    <div class="pull-left btn_gruvi add_number_container">1</div>
                    <div class="text-default h4" style="margin-top:0px;">choose an amount of <span class="h2 text-violet text-bold">GruviBucks</span></div>
                </div>
                <div class="form-group" style="padding: 0px 20px 0px 66px;">
                    <button onclick='$("#add_amount_input").val("20.00")' class="btn btn-green"><span class="text-default">ADD</span> $20</button>
                </div>
                <div class="form-group" style="padding: 0px 20px 0px 66px;">
                    <button onclick='$("#add_amount_input").val("40.00")' class="btn btn-green"><span class="text-default">ADD</span> $40</button>
                </div>
                <div class="form-group" style="padding: 0px 20px 0px 66px;">
                    <button onclick='$("#add_amount_input").val("100.00")' class="btn btn-green"><span class="text-default">ADD</span> $100</button>
                </div>

                
                <div class="panel panel-default enter_ammount">
                    <div class="panel-body">
                        <h5 class="text-center text-uppercase text-pink" style="margin-top:0px;">or enter <span class="text-violet">amount</span></h5>
                        <input type="text" id="add_amount_input" class="input_alt bg-light-grey">
                    </div>
                </div>

            </div>
            <div class="col-sm-5">
                <div class="add_title">
                    <div class="pull-left btn_gruvi add_number_container">2</div>
                    <div class="text-default h4">choose a <br/><span class="text-violet text-bold">payment type</span></div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="radio" style="margin-top:0px;">
                            <label>
                                <input type='radio' name='pay_via' value='paypal' onclick="changePayViaRadio(this.value)"> 
                                <img src="../images/paypal-logo-preview.png" alt="" style="vertical-align: bottom;"/>
                                <span class="text-pink">pay with</span> PayPal
                            </label>
                        </div>
                    </div>
                </div>
                
                <div id="current_credit_cards_cnt">
        <?php       
                    if(!empty($creditCards)){
                        foreach($creditCards as $card){ 
                            echo $this->render('credit-card-row', array('card'=>$card)); 
                        } 
                    } 
        ?>
                </div>
                <br>
                
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="radio" style="margin-top:0px;">
                            <label>
                                <input type='radio' name='pay_via' value='credit_card' onclick="changePayViaRadio(this.value)"> 
                                <span class="text-pink">or pay with</span> CreditCard
                            </label>
                        </div>
                        <div id='credit_card_cnt' style="display:none">
                            <form action="/" method="POST" id="add-card-form">
                              <span class="payment-errors" style="color:red;font-size:18px;font-weight:bold;"></span>
                                
                                <h4 class="text-bold text-pink">Payment Information</h4>
                                <div class="form-container">
                                    <div class="form-group">
                                      <input type="text" size="20" data-stripe="name" class="form-control" placeholder="Name on card">
                                    </div>

                                    <div class="form-group">
                                      <input type="text" size="20" data-stripe="number" class="form-control" required placeholder="Card Number">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="2" data-stripe="exp_month" class="form-control" required placeholder="Exp Month(MM)" style="width:50%; float:left;" />
                                        <input type="text" size="2" data-stripe="exp_year" class="form-control" required placeholder="Exp Year(YY)" style="width:50%;"/>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="4" data-stripe="cvc" class="form-control" required placeholder="CVC">
                                    </div>
                                </div>
                                
                                <h4 class="text-bold text-pink">Billing Address</h4>
                                <div class="form-container">
                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_country" class="form-control" placeholder="country">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_zip" class="form-control" placeholder="ZIP/Postal">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_state" class="form-control" placeholder="State/Province">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_city" class="form-control" placeholder="City">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_line1" class="form-control" placeholder="Address 1">
                                    </div>

                                    <div class="form-group">
                                        <input type="text" size="6" data-stripe="address_line2" class="form-control" placeholder="Address 2">
                                    </div>
                                </div>

                              <input type="checkbox" id="terms_of_use" required> I agree <a href="">Terms of Use</a>
                              <input type="submit" class="submit" value="Add Card" style="visibility: hidden" id="submit-card-form-button">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <?php 
                        if(!empty(Yii::$app->request->referrer)){
                            $back_url = Yii::$app->request->referrer;
                            $back_caption = "back";
                        }else{
                            $back_url = Url::to(['/'], true);
                            $back_caption = "cancel";
                        }
                    ?>
                    <a href="<?= $back_url; ?>"><input type="button" value="<?= $back_caption; ?>" class="btn"></a>
                    <div id="paypal-button-container" style="vertical-align:middle;display:none;"></div>
                    <input type="button" value="complete order" id="complete_order_button" onclick="completeOrder();" class="btn text-default btn_gruvi">
                </div>
            </div>
        </div>
    
    </div>
</div>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script>
    
    Stripe.setPublishableKey('<?= Yii::$app->params['stripe']['publishableKey']; ?>');

    $(function() {
      var $form = $('#add-card-form');
      $form.submit(function(event) {
          
          if(!$('#terms_of_use').is(':checked')){
              alert("Please accept the terms of use");
              return false;
          }
          
        // Disable the submit button to prevent repeated clicks:
        $form.find('.submit').prop('disabled', true);

        // Request a token from Stripe:
        Stripe.card.createToken($form, stripeResponseHandler);

        // Prevent the form from being submitted:
        return false;
      });
    });
    
    function completeOrder(){
        
        var creditCardId = $("input[name='pay_via']:checked").val();
        var amount = parseFloat($("#add_amount_input").val());
        
        if(isNaN(amount) || amount == "" || amount < 1){
            alert("amount is empty or wrong");
            return false;
        }
        
        if(creditCardId == 'paypal'){
            //can't click iframe
            return true;
        }
        if(creditCardId == 'credit_card'){
            //alert("Add a card first");
            $("#submit-card-form-button").click();
            return false;
        }
        
        $('#complete_order_button').prop('disabled', true);
        
        $.ajax({
            type: "GET",
            url: getAbsoluteUrl("gruvi-bucks/add"),
            data: { 'creditCardId' : creditCardId, 'amount' : amount},
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {
               if(data.status === "ok"){
                   paymentComplete('');
               }else{
                   $('#complete_order_button').prop('disabled', false);
                   alert("error: "+data.message);
               }
            },
            error: function (errormessage) {
                $('#complete_order_button').prop('disabled', false);
                alert(errormessage);
            }
        });
        
    }
    
    function paymentComplete(via){
        
        if(via === 'paypal'){//tmp block
            window.location.replace("<?= $back_url; ?>");
            return true;
        }
        
        var paypal_note = (via === 'paypal')?" Your Gruvi Bucks will be refined as soon as paypal confirms the transaction":"";
        window.alert('Payment Complete!'+paypal_note);
        window.location.replace("<?= $back_url; ?>");
    }
    
    function addCreditCard(token, last4, expiration) {
       $('#complete_order_button').prop('disabled', true);
        $.ajax({
            type: "GET",
            url: getAbsoluteUrl("user/add-credit-card"),
            data: { 'token' : token, 'last4' : last4, 'expiration' : expiration},
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data) {
               if(data.data.html){
                    $( "#current_credit_cards_cnt" ).append( data.data.html );
                    $('#add-card-form').find("input[type=text], textarea").val("");
                    $('#terms_of_use').attr('checked', false);
                    $("#credit_card_radio_"+data.data.card.id).click();
                    completeOrder();
               }else{
                   $form.find('.submit').prop('disabled', false); // Re-enable submission
                   alert("error: "+data.message);
               }
            },
            error: function (errormessage) {
                $form.find('.submit').prop('disabled', false); // Re-enable submission
                alert(errormessage);
            }
        });
        
    }

    function stripeResponseHandler(status, response) {
      // Grab the form:
      var $form = $('#add-card-form');

      if (response.error) { 
          
        $form.find('.payment-errors').text(response.error.message);
        $form.find('.submit').prop('disabled', false); // Re-enable submission

      } else { // Token was created!
        //console.log(response);
        addCreditCard(response.id, response.card.last4, response.card.exp_month+'/'+response.card.exp_year);
      }
    }
    
    function changePayViaRadio(item){
        if(item == 'credit_card'){
            $("#credit_card_cnt").show();
        }else{
            $("#credit_card_cnt").hide();
        }

        if(item == 'paypal'){
            $("#paypal-button-container").css('display', 'inline-block');
            $("#complete_order_button").hide();
            completeOrder();
        }else{
            $("#paypal-button-container").hide();
            $("#complete_order_button").show();
        }
    }
    
</script>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
        paypal.Button.render({

            env: '<?= Yii::$app->params['paypal']['environment']; ?>', // sandbox | production

            // PayPal Client IDs - replace with your own
            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
            client: {
                sandbox:    '<?= Yii::$app->params['paypal']['sandbox']['ClientID']; ?>',
                production: '<?= Yii::$app->params['paypal']['production']['ClientID']; ?>'
            },

            // Show the buyer a 'Pay Now' button in the checkout flow
            commit: true,

            // payment() is called when the button is clicked
            payment: function(data, actions) {

                // Make a call to the REST api to create the payment
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                amount: { total: $("#add_amount_input").val(), currency: 'USD' },
                                description: 'gruvi bucks',
                                custom: '<?= Yii::$app->user->identity->id; ?>',
                                invoice_number: '<?= Yii::$app->user->identity->id.'_'.time(); ?>'/*,
                                item_list: {
                                    items: <?= Yii::$app->user->identity->id; ?>,
                                }*/
                            }
                        ]
                    },
                    experience: {
                        input_fields: {
                            no_shipping: 1
                        }
                    }
                });
            },

            // onAuthorize() is called when the buyer approves the payment
            onAuthorize: function(data, actions) {

                // Make a call to the REST api to execute the payment
                return actions.payment.execute().then(function() {
                    paymentComplete('paypal');
                });
            },
            
            onError: function(err) {
                //alert(err);
                alert("payment declined");
            }

        }, '#paypal-button-container');
</script>