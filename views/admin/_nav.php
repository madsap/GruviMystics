<?php
use yii\helpers\Html;
use \yii\helpers\Url;
?>
<ul class="admin-nav">
<li><?= Html::a('Users', Url::toRoute(['admin/user/index'])) ?></li>
<li><?= Html::a('Readers', Url::toRoute(['admin/reader/index'])) ?></li>
</ul>

