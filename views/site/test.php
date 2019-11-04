<?php

use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

 echo '<pre>';
// $massiv = (array)$test->dataParse();
 //var_dump();
 echo '</pre>';
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div style="display: inline-block;vertical-align: top"><?php echo $form->field($uploadForm, 'imageFile')->fileInput([]) ?></div>

    <div style="display: inline-block;vertical-align: top;"><button style="margin-top: 24px;">Подтвердить</button></div>

    <div>
        <?php // echo Html::dropDownList('changeSheet',$test->selected, $test->getSheetNames(), ['class' => 'btn btn-primary']);?>
        
        
    </div>
    
        <?php 

        ?>
    
    <?php ActiveForm::end();?>
    
    <?php // echo GridView::widget([
       // 'dataProvider' => $test->dataParse(),
       // 'columns' => $test->settingGrid() 
   // ]) ?>
</div>
