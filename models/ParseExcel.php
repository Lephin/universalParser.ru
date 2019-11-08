<?php

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider; 

class ParseExcel extends Model
{
    public $array; //Массив данных для провайдера данных

    public $spreadsheet; //Рабочий документ
    
    public $line; //Путь к загруженному документу
    
    public $validatesArray; //Данные валидации
    
    public $massivName;
    
    public $validatesInt;
    
    public $notValidate;
    
    public $validatesEmpty;

    public function __construct($line = null,$validate = null) {
        
        if (!empty($line)) {
            
            if (isset($line) && !empty($line)) {
                $this->line = $line; //Путь к файлу
                $this->spreadsheet = $this->loadExcel(); //Объект данных документа Excel
                $this->array = $this->getArray();
            } else {
                return 'Путь к файлу не указан или не существует';    
            }
            
            if (isset($validate) && !empty($validate)) {
                $this->validatesArray = $validate;
            }
            
        } else {
        
            return null;
            
        }
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
     * @param int $name
     * @return array
     * Возвращает массив данных с рабочих листов
     */
    
    public function getArray()
    {
        //Возвращаем активный по-умолчанию лист
        if (isset($this->line)) {

            //Возвращает все ячейки в виде двумерного массива
            $dataArray =  $this->spreadsheet->getActiveSheet()
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
     * Настрйоки для компонента GridView
     * НЕ ГОТОВО
     */
    public function settingGrid()
    {
        if (isset($this->line)) {
            $array = $this->array;

            foreach ($array[1] as $name => $element) {

                $nameAttribute[] = $name;
            }

            $test1[] = ['class' => 'yii\grid\SerialColumn'];

            foreach ($nameAttribute as $my) {

                $columns[] = ['attribute' => $my];
            }
            $columns1 = $columns;
            $result = array_merge($columns1,$test1);

            return $nameAttribute;
        }
       
    }

    /**
     * 
     * @return type
     * Преобразование данных из Excel в Провайдер данных для компонентов yii2
     */
    public function dataParse()
    {
       $array = $this->array; //Массив готовых данных для провайдера данных yii2
       $settingGrid = $this->settingGrid();
       
        if (!isset($array)) {
            $array = [['Документ не загружен']];
            $settingGrid = [null];
        } 
        
        $provider = new ArrayDataProvider([
            'allModels' => $array,
            'sort' => [
                'attributes' => $settingGrid 
            ],
            'pagination' => [
                'pageSize' => 20,
            ],

        ]);

        return $provider;
            
    }
    
    //Выгрузка 
    public function validatesArray()
    {
        $validatesArrayName = $this->validatesArrayName();
        $validatesArrayEmpty = $this->validatesArrayEmpty();
        $validatesArrayInt = $this->validatesArrayInt();
        
        if (isset($validatesArrayName) && !empty($validatesArrayName)) {
           $resultArray['name'] = $validatesArrayName;
        }
        
        if (isset($validatesArrayEmpty) && !empty($validatesArrayEmpty)) {
            $resultArray['empty'] = $validatesArrayEmpty; 
        }
        
        if (isset($validatesArrayInt) && !empty($validatesArrayInt)) {
                $resultArray['int'] = $validatesArrayInt; 
        }
            
            if (!empty($resultArray)) {
                return $resultArray;
            }
    }

    /**
     * 
     * @param type $name
     * @return string
     * Создание обязательных полей для документа Excel. Если таких полей в документе
     * не будет, то валидация будет не успешна
     */
    public function validatesArrayName()
    {
        $array = $this->array;//Готовый массив данных для прохождения валидации
        $validate = $this->validatesArray;
        
        if (isset($validate) && is_array($validate)) {
            for ($i = 0; $i < count($validate); $i++) { 
                if (isset($validate[$i][0])) {
                    //Проверка название колонок на соответствие
                    if (!array_key_exists($validate[$i][0],$array[1])) { 
                        $notValidate[] = $validate[$i][0];
                    }
                } else {
                    return null;
                }
            }
  
        } 
        
        if (!empty($notValidate)) {
            return $notValidate;
        }
        
    }
    
    /**
     * 
     * @return type
     * //Проверка целых чисел в массиве
     */
    public function validatesArrayInt()
    {
                
    //    if (!empty($this->validatesArrayName())) {
    //        return $this->validatesArrayName();
    //    }
        
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
     * 
     * @return type
     * //Проверка пустых ячеек
     */
    public function validatesArrayEmpty() 
    {
    //    if (!empty($this->validatesArrayName())) {
    //        return $this->validatesArrayName();
    //    }
        
        $validate = $this->validatesArray;
        $array = $this->array;
        
        if (is_array($validate) && isset($validate)) {
            for ($i = 0; $i < count($validate); $i++) {
                //Проверка содержимое колонок на соответствие типа данных
                if (isset($validate[$i][2])) {
                    if (!empty($validate[$i][2] && $validate[$i][2] == 'empty')) {
                        $validatesResult[]= $validate[$i];
                    }
                }
            }
             if (!empty($validatesResult) && isset($validatesResult)) {
                for($i = 0;$i<count($validatesResult);$i++) {
                    for ($y =1;$y<count($array);$y++) {
                        if ($array[$y][$validatesResult[$i][0]] == '') {
                            $notValidate[$validatesResult[$i][0]][$y + 1] = $array[$y][$validatesResult[$i][0]];
                        }
                    }
                }
            
            return $notValidate;
            }
            
        } 
    }

    /**
     * 
     * @param string $arrayMerge
     * @return type
     */
    public function dropArray($arrayMerge)
    {
        if (!empty($array) && is_array($arrayMerge)) {
        $array = $this->array;
        
        $arrayMerge = [
            'Новый элемент массива',
            'Новый элемент массива',
        ];
        
        $newArray = array_keys($array[1]);
        $result = array_merge($arrayMerge,$newArray);
         
        return $result;

        } else {
            return $this->dataParse();    
        }
    }
}