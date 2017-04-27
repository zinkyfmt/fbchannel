<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="">
        <div class="form-group" style="padding: 30px; width: 500px">
            <?= Html::beginForm(['index'], 'post', ['enctype' => 'multipart/form-data']) ?>
            <label class="control-label" for="app_id">Facebook App ID</label>
            <input type="text" id="app_id" class="form-control" name="app_id" autofocus="" aria-required="true" aria-invalid="true" style="margin-bottom: 10px" value="1626747924005321">

            <label class="control-label" for="app_secret">Facebook App Secret</label>
            <input type="pass" id="app_secret" class="form-control" name="app_secret" autofocus="" aria-required="true" aria-invalid="true" style="margin-bottom: 10px" value="db5ef8e6f89798decdb1847af0efc2f1">

            <label class="control-label" for="page_id">Facebook Page ID</label>
            <input type="pass" id="page_id" class="form-control" name="page_id" autofocus="" aria-required="true" aria-invalid="true" style="margin-bottom: 10px" value="368669079928720">
            <?= Html::submitButton('Get File CSV', ['class' => 'btn btn-lg btn-success']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
