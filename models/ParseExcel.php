<?php

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider; 

class ParseExcel extends Model
{
    public $array; //Массив данных для провайдера данных

    public $spreadsheet; //Рабочий документ
    
    public $line; //Путь к загруженному документу
    
    public $selected; //Текущая рабочая страница
    
    public $fileName; //Название текующего, загруженного документа

    public function __construct($line) {
        
        if (!empty($line)) {
            
            //Путь к документу, что был загружен
            $this->line = $line;
            
            //Название документа, который загружен
            $fileName = pathinfo($line);
            $this->fileName = $fileName;
            
            //Объект для чтение данных из документа
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); 

            //Значение чтения
            $reader->setReadDataOnly(true); 

            //Рабчий документ
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
    
    public function getArray($name)
    {
        //Возвращаем активный по-умолчанию лист
        if (empty($name) && isset($this->line)) {

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
            
           //Создание ассоциативного массива для провайдера данных 
            for ($i =1;$i <=count($array);$i++){
                $z = 0;
                foreach ($array[0] as $test) {
                    
                    if (isset($array[$i][$z])) {
                        $massiv[$i][$test] = $array[$i][$z];
                    } else {
                        $massiv[$i][$test] = 'Не существует';
                    }
                   
                $z++; 
                
                }  
            }

             return $this->array = $massiv;
             
        } elseif (isset($this->line)) {
            
            //Возвращаем выбранный активный 
            $this->spreadsheet->setActiveSheetIndex($name);
            //Возвращает все ячейки в виде двумерного массива
            $dataArray =  $this->spreadsheet->getActiveSheet()
                ->toArray(
                    // 'A1:A2',     // The worksheet range that we want to retrieve
                    NULL,           // Value that should be returned for empty cells
                    FALSE,          // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                    FALSE,          // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                    FALSE            // Should the array be indexed by cell row and cell column
                );

           //Удаление пустых ячеек
            for ($i = 0;$i<count($dataArray);$i++) {

                for ($y = 0;$y<count($dataArray[$i]);$y++) {

                    if ($dataArray[$i][$y] !=  null) {
                        
                        $array[$i][$y] = $dataArray[$i][$y];
                       
                    }
                      
                }
            }
            
           //Создание ассоциативного массива для провайдера данных 
            for ($i =1;$i <=count($array);$i++){
                $z = 0;
                foreach ($array[0] as $test) {
                    
                    if (isset($array[$i][$z])) {
                        $massiv[$i][$test] = $array[$i][$z];
                    } else {
                        $massiv[$i][$test] = 'Не существует';
                    }
                   
                $z++; 
                
                }  
            }
            
            return $this->array = $massiv;
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

        if (empty($this->array)) {
            
        $this->array = [['Документ не загружен']];
       
        $provider = new ArrayDataProvider([
            'allModels' => $this->array,
            'pagination' => [
                'pageSize' => 20,
            ],

        ]);
        return $provider;
        
        } 
        
        $provider = new ArrayDataProvider([
            'allModels' => $this->array,
            'sort' => [
                'attributes' => $this->settingGrid() 
            ],
            'pagination' => [
                'pageSize' => 20,
            ],

        ]);
        return $provider;
    }
}