<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UserRelation */

$this->title = 'Create User Relation';
$this->params['breadcrumbs'][] = ['label' => 'User Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
