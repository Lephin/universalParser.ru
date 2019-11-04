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

    public function __construct($line,$validate) {
        
        if (!empty($line)) {
            
            //Путь к документу, что был загружен
            $this->line = $line;
            
            $this->validatesArray = $validate;
                    
            //Объект для чтение данных из документа
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); 

            //Значение чтения
            $reader->setReadDataOnly(true); 

            //Рабочий документ
            $this->spreadsheet = $reader->load($this->line);
            
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
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); //Объект для чтение данных из документа
        $reader->setReadDataOnly(true); // Не знаю
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
           
           return  [null];
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
            for ($i =1;$i < count($array);$i++){
                $z = $result;
                foreach ($array[0] as $test) {
                    if (isset($array[$i][$z])) {
                        $massiv[$i][$test] = $array[$i][$z];
                    } else {
                        $massiv[$i][$test] = '';
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
     */
    public function settingGrid()
    {
        if (isset($this->line)) {
            $array = $this->getArray();

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
       $array = $this->getArray(); //Массив готовых данных для провайдера данных yii2
        
        if (!isset($array)) {
            $provider = new ArrayDataProvider([
                'allModels' => [['Документ не загружен']],
                'pagination' => [
                    'pageSize' => 20,
                ],

            ]);
                return $provider;
        } 
        
        $provider = new ArrayDataProvider([
            'allModels' => $array,
            'sort' => [
                'attributes' => $this->settingGrid() 
            ],
            'pagination' => [
                'pageSize' => 20,
            ],

        ]);

        return $provider;
            
    }
    
    /**
     * 
     * @param type $name
     * @return string
     * Создание обязательных полей для документа Excel. Если таких полей в документе
     * не будет, то валидация будет не успешна
     */
    public function validatesArray()
    {
        
        $array = $this->getArray();//Готовый массив данных для прохождения валидации
        $validate = $this->validatesArray;
        
        if (isset($validate) && is_array($validate)) {
            for ($i = 0; $i < count($validate); $i++) { //Проверка соответствий ключей. Обязательнх ключей в массиве
                if (!array_key_exists($validate[$i],$array[1])) { 
                    $provider = new ArrayDataProvider([
                        'allModels' => [['Колонка '.$validate[$i]. ' отсутствует']],
                        'pagination' => [
                            'pageSize' => 20,
                        ],

            ]);
                return $provider;
                }
            }
        } 
            
        return $this->dataParse();
        
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