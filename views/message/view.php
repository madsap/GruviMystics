<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-wrapper bg-default col-xs-12">

    <h3 class="text-pink" style="margin:20px 30px 0px 30px;"><?= Html::encode($this->title) ?></h3>

    <div class="page-container">
       <p>
           <?= Html::a('List', ['index'], ['class' => 'btn btn-primary']) ?>
           <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
           <?= Html::a('Delete', ['delete', 'id' => $model->id], [
               'class' => 'btn btn-danger',
               'data' => [
                   'confirm' => 'Are you sure you want to delete this item?',
                   'method' => 'post',
               ],
           ]) ?>
       </p>

       <?= DetailView::widget([
           'model' => $model,
           'attributes' => [
               'id',
               'customerId',
                'customer.firstName',
                'customer.lastName',
                'customer.email',
               'readerId',
                'reader.firstName',
                'reader.lastName',
                'reader.email',
               'message:ntext',
               'status',
               'createAt',
           ],
       ]) ?>
    </div>

</div>
