<?php
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div style="display: inline-block;vertical-align: top"><?= $form->field($model, 'imageFile')->fileInput([]) ?></div>

    <div style="display: inline-block;vertical-align: top;"><button style="margin-top: 24px;">Подтвердить</button></div>

    <div>
        <?php // echo Html::dropDownList('changeSheet',$test->selected, $test->getSheetNames(), ['class' => 'btn btn-primary']);?>
        
        
    </div>
    
        <?php 

        ?>
    
    <?php ActiveForm::end();
    $array = $test->validatesArray();
  //  $arrayName = $test->validatesArrayName();
  //  $arrayInt = $test->validatesArrayInt();
  //  $arrayEmpty = $test->validatesArrayEmpty();
  
   echo '<pre>';
   var_dump($array);
  // var_dump($arrayEmpty);
  // var_dump($arrayInt);
  // var_dump($arrayName);
   echo '</pre>';
   
  
    ?>
     
        
        <?php  echo GridView::widget([
        'dataProvider' => $test->dataParse(),
       // 'columns' => $test->settingGrid() 
    ]) ?>
</div>
