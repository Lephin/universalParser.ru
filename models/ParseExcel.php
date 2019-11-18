<?php

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

class ParseExcel extends Model
{
    /**
     *
     * @var type
     * Массив данных для провайдера данных
     */
    public $array;

    /**
     *
     * @var type 
     * Рабочий документ
     */
    public $spreadsheet;
    
    /**
     *
     * @var type 
     * Путь к загруженному документу
     */
    public $line;
    
    /**
     *
     * @var type 
     * Данные валидации
     */
    public $validatesArray;
    
    /**
     *
     * @var type 
     * GET запрос от АЯКС
     */
    public $get;
    
    /**
     *
     * @var type 
     */
    public $validatesArrayAjax;

    public $nowSelectedList;


    /**
     * 
     * @param string $line
     * @param array $validate
     */
    public function __construct($line = null,$validate = null, $get = null, $nowSelectedList = null)
    {
        $this->get = $get;
        $this->nowSelectedList = $nowSelectedList;
        
        if (isset($line)) {
            $this->line = $line; //Путь к файлу
            $this->spreadsheet = $this->loadExcel(); //Объект данных документа Excel
            $this->array = $this->getArray();

        }
        
        $this->validatesArray = json_decode($this->ajaxGet(),true);

    //    if (isset($validate) && is_array($validate)) {
    //        $this->validatesArray = $validate;
    //    } else {
    //        $this->validatesArray = $this->ajaxGet();
    //    }
    }

    /**
     * 
     * @return object
     * Получение данных из загруженного документа
     */
    public function loadExcel()
    {
        $inputFileType =  \PhpOffice\PhpSpreadsheet\IOFactory::identify($this->line); //Объект для чтение данных из документа
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
        $spreadsheet = $reader->load($this->line); // Загрузка документа

        return $spreadsheet;
    }
    
    /**
     * 
     * @return array
     * @return null
     * Возвращает название рабочих листов документа
     */
    public function getSheetNames()
    {
       if (isset($this->line)) {
            return $this->spreadsheet->getSheetNames();
       } else {
           return [null];    
       }
    }
    
    /**
     * 
     * @return array
     * Возвращает массив данных с рабочих листов
     */
    public function getArray()
    {

        $nowSelectedList =  $this->nowSelectedList;
        
    //    if (!isset($nowSelectedList) || empty($nowSelectedLis)) {
    //        $nowSelectedList = 0;
    //    }
        //Возвращает первый активный лист this->spreadsheet->getActiveSheet()
        //Возвращает выбранный активный лист $this->spreadsheet->getSheet($nowSelectedList);
        
        //Возвращаем активный по-умолчанию лист
        if (isset($this->line)) {

            //Возвращает все ячейки в виде двумерного массива
            $dataArray =  $this->spreadsheet->getSheet($nowSelectedList)
                ->toArray(
                    // 'A1:A2',     // The worksheet range that we want to retrieve
                    NULL,           // Value that should be returned for empty cells
                    FALSE,          // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                    FALSE,          // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                    FALSE          // Should the array be indexed by cell row and cell column
                );

            //Удаление пустых ячеек
            for ($i = 0;$i<count($dataArray);$i++) {
                for ($y = 0;$y<count($dataArray[$i]);$y++) {
                    if ($dataArray[$i][$y] !=  null) {
                        $array[$i][$y] = $dataArray[$i][$y];
                    } 
                }
            }
            
            //Определяем правильно расположение записей в таблице Excel
            $array_flip = array_flip($array[0]);
            $result = array_shift($array_flip);
            
           //Создание ассоциативного массива для провайдера данных 
            for ($i =1;$i <= count($array);$i++){
                $z = $result;
                foreach ($array[0] as $test) {
                    if (isset($array[$i][$z])) {
                        $massiv[$i][$test] = $array[$i][$z];
                    } else {
                        $massiv[$i][$test] = null;
                    }
                $z++; 
                }  
            }

            return $massiv;
             
        } else {
            return null;    
        }
    }
    
    /**
     * 
     * @return array
     *
     */
    public function sortArrayDataProvider()
    {
        if (isset($this->line)) {
            $array = $this->array;

            foreach ($array[1] as $name => $element) {

                $nameAttribute[] = $name;
            }
            return $nameAttribute;
        }
    }
    
    /**
     * 
     * @return array
     * Найстроки для компонента
     */
    public function settingGrid() {
        
        if (isset($this->line)) {
            $array = $this->array;

            $header = $this->dropArray(false, null, null, null);
            foreach ($array[1] as $name => $element) {

                $nameAttribute[] = $name;
            }

        //    $test1[] = ['class' => 'yii\grid\SerialColumn'];
            $i = 0;
            foreach ($nameAttribute as $my) {

                $columns[] = [
                    'header'=> Html::dropDownList($my,$my,$header,['id' => 'addDrop','class' => 'form-control']),
                    'attribute' => $my,
                    ];
            }
            
        //    for ($i = 0;$i<count($columns);$i++) {
        //    $result =  array_merge($columns[$i],['header' => 'test']);
                
       //     }
         //   return $result;
            
           //'filter' => Html::dropDownList('changeSheet',null, $nameAttribute, ['class' => 'btn btn-primary'])
        
        //    $result = array_merge($columns,$test1);

            return $columns;
        }
    }

    /**
     * 
     * @return array
     * Преобразование данных из Excel в Провайдер данных для компонентов yii2
     */
    public function dataParse($array = [])
    {
       
       $settingGrid = $this->sortArrayDataProvider();
       
        if (!isset($array)) {
            $array = [['Нет данных для вывода']];
            $settingGrid = [null];
        } 
        
        $provider = new ArrayDataProvider([
            'allModels' => $array,
            'sort' => [
                'attributes' =>  $settingGrid 
            ],
            'pagination' => [
                'pageSize' => 20,
            ],

        ]);

        return $provider;
            
    }
   
    /**
     * 
     * @param bool $boolean
     * @return array
     * Выгружаем либо готовый массив данных, который прошел проверки, либо выгружаем ошибки, которые были найдены при валидации
     */
    public function validatesArray($boolean = null)
    {    
        $validatesArrayEmpty = $this->validatesArrayEmpty(true);
        $validatesArrayName = $this->validatesArrayName();
        
        if (isset($validatesArrayName)){
            return $validatesArrayName;
        } else {
            $this->getArray();
        }
        
        if (isset($validatesArrayEmpty)) {
            return $validatesArrayEmpty; 
        } else {
            return $this->getArray();
        }
        
    }

    /**
     * 
     * @return array
     * @rerurn string
     * Создание обязательных полей для документа Excel. Если таких полей в документе
     * не будет, то валидация будет не успешна
     */
    public function validatesArrayName()
    {
        $array = $this->array;//Готовый массив данных для прохождения валидации
        
        if (!empty($array)) {
            $validate = $this->validatesArray;

            if (isset($validate) && is_array($validate)) {
                for ($i = 0; $i < count($validate); $i++) { 
                    if (!empty($validate[$i][0])) {
                        //Проверка название колонок на соответствие
                        if (!array_key_exists($validate[$i][0],$array[1])) { 
                            $notValidate[0][$validate[$i][0]] = 'Колонка '.$validate[$i][0].' отсутствует в документе';
                        }
                    } else {
                        return [0 => ['error' => 'Не указаны колонки, которые нужно проверить']];
                    }
                }
            } 

            if (!empty($notValidate)) {
                return $notValidate;
            }
        }
    }
    
    /**
     * 
     * @return array
     * @return null
     * Проверка целых чисел в массиве
     * МЕТОД ВРЕМЕННО ЗАБРОШЕН
     */
    public function validatesArrayInt()
    {
                

        $validate = $this->validatesArray;
        $array = $this->array;

        if (is_array($validate) && isset($validate)) {
            for ($i = 0; $i < count($validate); $i++) {
                //Проверка содержимое колонок на соответствие типа данных
                if (isset($validate[$i][1])) {
                    if (!empty($validate[$i][1] && $validate[$i][1] == 'int')) {
                        $validatesResult[]= $validate[$i];
                    }
                }
            }
            
            if (!empty($validatesResult) && isset($validatesResult)) {
                for($i = 0;$i<count($validatesResult);$i++) {
                    for ($y =1;$y<count($array);$y++) {
                        if (!is_numeric($array[$y][$validatesResult[$i][0]])) {
                            $notValidate[$validatesResult[$i][0]][$y] = $array[$y][$validatesResult[$i][0]];
                        }
                    }
                }
                
                 return $notValidate;
            } else {
                return null;
            }
        }        
        
    }
    
    /**
     * @param bool $boolean
     * @return array
     * @return null
     * Проверка пустых ячеек
     */
    public function validatesArrayEmpty($bolean = null) 
    {
        $array = $this->array;
        
        if (!empty($array)) {
            
            $validate = $this->validatesArray;

            if (!empty($this->validatesArrayName())) {
                return $this->validatesArrayName();
            }

            if (is_array($validate) && isset($validate)) {
                for ($i = 0; $i < count($validate); $i++) {
                    //Проверка содержимое колонок на соответствие типа данных
                    if (isset($validate[$i][2])) {
                        if (!empty($validate[$i][2] && $validate[$i][2] == 'empty')) {
                            $validatesResult[]= $validate[$i];
                        }
                    }
                }


                if (isset($validatesResult)) {
                    for($i = 0;$i<count($validatesResult);$i++) {
                        for ($y =1;$y<count($array);$y++) {
                            if ($array[$y][$validatesResult[$i][0]] == '') {
                                    $notValidate[$validatesResult[$i][0]][$y + 1] = $y + 1;
                            }
                        }
                    }
                }

                if (empty($notValidate)) {
                    return null;
                }

                if ($bolean === true && !empty($notValidate)) {
                    foreach ($notValidate as $key => $element) {
                        $results[0][$key] =  'На строках: '.implode(', ', $element).' найдены не соответствие данных';
                    }

                    return $results;
                }    

                return $notValidate;

            }
        }
    }
    /**
     * 
     * @param bool $bolean
     * @param type $style
     * @param int $int
     * @param array $arrayMerge
     * @return string
     */
    public function dropArray(bool $bolean = null, $style = [],int $int = null, $arrayMerge = [])
    {
        if ($bolean === true || $bolean === false) {
            
            $array = $this->array;

            if (!empty($array)) {

                $newArray = array_keys($array[1]);
                
                if (!empty($arrayMerge)) {
                    foreach ($arrayMerge as $key) {
                        array_push($newArray,$key);
                    }
                }
                
                foreach ($newArray as $key) {
                    $resultArray[$key] = $key;
                }
                
                if ($bolean === false) {    
                    return $resultArray;
                }
                
                if ($bolean === true && !is_int($int)) { 
                    for ($i = 0;$i <count($array[1]);$i++) {
                        echo Html::dropDownList($newArray[$i], $newArray[$i], $resultArray , $style);
                    }
                    
                }
                
                if ($bolean === true && is_int($int)) {
                    if (isset($newArray[$int])) {
                        echo Html::dropDownList($newArray[$int], $newArray[$int], $resultArray , $style);
                    } else {
                        echo 'Элемента номер '.$int.' в массиве не найденно ';
                    }
                }
                    
            }
        } else {

            return 'Для вывода данных значение должно быть true или false';
        }

    }
    
    public function ajaxGet()
    {
        $get = $this->get;

        if (isset($get['value'])) { 
            file_put_contents('test.json', $get);
            $_SESSION['myTest'] = $get;
        }
    
        return  file_get_contents('test.json');  
    }
    
    public function ajaxValidateResult(bool $boolean = null)
    {
        //'<li id="liColumns" style="list-style-type: none;"><div id="' + y + '" class="textColumns">' + addColumns + '</div><div class="deleteColumns" onClick="deleteColumns(this)">Удалить</div></li>';
        
        $array = json_decode(file_get_contents('test.json'),true);
        if (isset($array)) {
            if ($boolean === true) {
                echo '<ul id="validateColumnsView">';
                for ($i = 0;$i < count($array);$i++){
                    echo '<li style="list-style-type:none">';
                    for ($y = 0;$y < count($array[$i]);$y++){
                        if (!empty($array[$i][$y])) {
                            echo ' '.$array[$i][$y];
                        }
                    }
                    echo '</li>';
                }
                echo '</ul>';
            } elseif($boolean === false) {

            return json_decode(file_get_contents('test.json'),true);

            }
        } 
    }
    
    public function nowSelectedList()
    {
        
    }
        
}