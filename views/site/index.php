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

    <div style="display: inline-block;vertical-align: top"><?= $form->field($model, 'imageFile')->fileInput(['value' => 'Документ'])->label('Загрузить документ') ?></div>

    <div style="display: inline-block;vertical-align: top;"><button style="margin-top: 24px;">Подтвердить</button></div>

    <div>
        <?php //  echo Html::dropDownList('changeSheet', null, [$dropSelect], ['class' => 'btn btn-primary']);?>
    </div>

    
    <?php 
    $style = ['class' => 'btn btn-primary'];
    $dropSelect = $test->dropArray(true);
    //$dropSelect2 = $test->dropArray(true);
    $array = $test->validatesArray();
    $dataParse = $test->getArray();
    $sortGrid = $test->sortArrayDataProvider();
    $settingGrid = $test->settingGrid();
    $arrayName = $test->validatesArrayName();
    $arrayInt = $test->validatesArrayInt();
    $arrayEmpty = $test->validatesArrayEmpty();
    $dataDropArray = $test->dataDropArray();
    $dataParse = $test->dataParse();
    
  //  echo $dropSelect;
    
   echo '<pre>';
  // var_dump($dataDropArray);
  // var_dump($dataParse);
  // var_dump($dropSelect);
  // var_dump($sortGrid);
  // var_dump($settingGrid);
  // var_dump($dropSelect);
   var_dump($array);
  // var_dump($arrayEmpty);
  // var_dump($arrayInt);
  // var_dump($arrayName);
   echo '</pre>';
              
?>

  <?php 
  
  echo Html::submitButton();
  
  ActiveForm::end(); ?> 
        <?php  

        echo GridView::widget([
            //    'filterModel' => $dropSelect,
                'dataProvider' => $test->dataParse(),
            //    'columns' => $settingGrid
            ]) ?>
</div>
