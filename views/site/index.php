<?php
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

// echo '<pre>';
// var_dump($test->getSheetNames());
// var_dump($test->nowSelectedList);
// var_dump($test->ajaxValidateResult(true));
// var_dump($test->validatesArray);
// echo '</pre>';
?>
<div class="site-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <div style="display: block;vertical-align: top"><?= $form->field($model, 'imageFile')->fileInput(['value' => 'Документ'])->label('Загрузить документ') ?></div>

    <div  style="display: block;vertical-align: top;"><button style="margin-bottom: 40px;">Загрузить</button></div>
    

    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <td>
                    <h3>Выберите лист</h3>
                    <?php echo Html::dropDownList('changeNameList',$test->nowSelectedList, $test->getSheetNames(), ['class' => 'form-control form-control-sm'])?>
                    <?php echo Html::submitButton('Подтвердить',['class' => 'btn btn-success','style' => ['margin-top' => '10px']]) ?>
                </td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>
                    
                    <h3>Панель создания валидации</h3>
                    <table>
                        <tr>
                            <td>    
                            <?php echo Html::textInput('validateColumns', null, ['id' => 'validateAddColumns','class' => 'form-control'])?>
                            <?php 
                            ?>
                            </td>
                            <td style="vertical-align: top">
                                <h5 style="margin-left: 20px;">Введите наименования колонки</h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Html::dropDownList('validateRules', null, ["null" => 'Правил нет', 'empty' => 'Проверить на пустоту',],['id' => 'validateRules','class' => 'form-control form-control-sm']);?>
                                <?php // echo Html::button('Подтвердить правило');?> 
                                <?php
                                echo Html::button('Добавить правило ++',[
                                           'onClick' => 'createValidateRule()',
                                           'class' => 'btn btn-success',
                                           'style' => ['display' => 'block','margin-top' => '10px']
                                       ]);     
                               ?>
                                <?php echo Html::submitButton('Создать проверку',[
                                    'class' => 'btn btn-success',
                                    'style' => ['margin-top' => '10px']
                                ]); ?>
                             </td>
                             <td style="vertical-align: top">
                             <h5 style="margin-left: 20px;">Выбирите правило проверки для данной колонки</h5>   
                             
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <h5>Добавить новую колонку</h5>
                    <?php echo Html::textInput('newColumns', null, ['id' => 'addColumns'])?>
                    <?php echo Html::button('Добавить',[
                        'onClick' => 'add()'
                    ])?>
                    
                </td>
            </tr>
            <tr>
                <td>
                    <ul id="validateRulesView">
                    </ul>
                    
                    <h5>Действующие правила</h5>
                    <?php $test->ajaxValidateResult(true)?>
                </td>
                <td>
                    <ul id="getColumns">
                    </ul>
                    
                </td>
                
            </tr>
        </tbody>
    </table>
    
  <?php echo GridView::widget([
            //    'filterModel' => ["ID"],
            //    'tableOptions' => ['id' => 'addDrop'],
                'dataProvider' => $test->dataParse($test->validatesArray()),
                'columns' => $test->settingGrid(),
            ]) ;
        
    echo Html::submitButton();
  
  ActiveForm::end();
        ?>
</div>
