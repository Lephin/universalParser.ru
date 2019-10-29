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

    <?= $form->field($model, 'imageFile')->fileInput([]) ?>
    
    <div class=""><?php echo $test->line?></div>
    
    <?php 

        echo Html::dropDownList('changeSheet',$test->selected, $test->getSheetNames(), ['class' => 'btn btn-primary']);

    ?>

    <button>Загрузить документ</button>

<?php ActiveForm::end();?>
    
    <?php  echo GridView::widget([
        'dataProvider' => $test->dataParse(),
       // 'columns' => $test->settingGrid() 
    ]) ?>
</div>
