<div style="border:3px solid pink;padding:3px;margin-top:5px">
    <input type='radio' name='pay_via' value='<?= $card->id; ?>' id="credit_card_radio_<?= $card->id; ?>" onclick="changePayViaRadio(this.value)"> **** **** **** <?= $card->last4; ?>
</div>