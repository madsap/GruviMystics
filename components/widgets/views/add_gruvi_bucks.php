<?php
    use \yii\helpers\Url;
    
    yii\bootstrap\Modal::begin([
        'headerOptions' => ['id' => 'add-gruvi-bucks-modal-header'],
        'id' => 'add-gruvi-bucks-modal',
        'size' => 'modal-lg',
        'closeButton' => false,
        //'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
    ]);

?>

<!-- $('#add-gruvi-bucks-modal').modal('toggle'); -->

            <!--
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            -->
    <div class="modal-body h4" id="add-gruvi-bucks-placeholder">
        Your <span class="text-pink text-bold">GruviBucks</span> are about to run out.
        Would you like to add more <span class="text-pink text-bold">GruviBucks</span>
        now so that your call does not terminate?

        <br><br>

        <?php if(!empty($creditCard)){ ?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 text-violet h3">
                Choose an amount of <span class="text-pink text-bold">GruviBucks</span> to add to your account:
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 ">
                <div class="form-group">
                    <button id="fb_add_20_button" onclick='$("#fb_amount_to_add_input").val("20.00");fbAddGruviBucks();' class="btn btn-green h3 btn-block"><span class="text-default">ADD</span> $20</button>
                </div>
                <div class="form-group">
                    <button id="fb_add_40_button" onclick='$("#fb_amount_to_add_input").val("40.00");fbAddGruviBucks();' class="btn btn-green h3 btn-block"><span class="text-default">ADD</span> $40</button>
                </div>
                <br>
                <div class="text-center">
                    <span class="text-grey">Add a</span> <br>
                    <u>different amount</u>
                </div>
                <br><br>
                $ <input type="text" placeholder="0.00" style="width:96px" id="fb_amount_to_add_input"> 
                <input id="fb_complete_order_button" type="button" value="ADD" onclick="fbAddGruviBucks();" class="btn btn-green text-default">

            </div>
        </div>
        <?php }else{ ?>


         Default Credit Card wasn't found. 
        Please add one <a href="<?= Url::to(['gruvi-bucks/add']); ?>" onclick="$('#add-gruvi-bucks-modal').modal('toggle');" target="_blank">here</a>

        <?php } ?>
    </div>

<?php 
yii\bootstrap\Modal::end();
?>


<?php if(!empty($creditCard)){ ?>
    <script>
        function fbAddGruviBucks(){
           $('#fb_complete_order_button').prop('disabled', true);
           $('#fb_add_20_button').prop('disabled', true);
           $('#fb_add_40_button').prop('disabled', true);

            $.ajax({
                type: "GET",
                url: getAbsoluteUrl("gruvi-bucks/add"),
                data: { 'creditCardId' : '<?= $creditCard->id; ?>', 'amount' : $('#fb_amount_to_add_input').val()},
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function (data) {
                   $('#fb_complete_order_button').prop('disabled', false);
                   $('#fb_add_20_button').prop('disabled', false);
                   $('#fb_add_40_button').prop('disabled', false);
                   if(data.status === "ok"){
                       $('#add-gruvi-bucks-modal').modal('hide');
                   }else{
                       alert("error: "+data.message);
                   }
                },
                error: function (errormessage) {
                    $('#fb_complete_order_button').prop('disabled', false);
                    $('#fb_add_20_button').prop('disabled', false);
                    $('#fb_add_40_button').prop('disabled', false);
                    alert(errormessage);
                }
            });
        }
    </script>
<?php } ?>