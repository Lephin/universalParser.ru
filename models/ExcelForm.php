<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use app\models\UploadForm;

/**
 * Description of ExcelForm
 *
 * @author lephin
 */
class ExcelForm extends Model 
{
    
    public $line; //Путь к загруженному документу
    
    public $spreadsheet; //Объект документа 


    public function __construct($line = null) {
        
        if (isset($line) && !empty($line)) {
            $this->line = $line; //Путь к файлу
            $this->spreadsheet = $this->loadExcel(); //Объект данных документа Excel
        } else {
            return 'Путь к файлу не указан или не существует';    
        }
        
    }

    public function rules() {
        parent::rules();
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
     * Преобразование данных из объекта Excel в массив для провайдера данных. Возвращаем готовый массив активного листа 
     */
    public function excelToArray()
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
            return [[null]];    
        }

    }
    
    /**
     * 
     * @return type
     * Создание провайдера данных из подготовленного массива
     */
    public function dataParse()
    {
        $array = $this->excelToArray(); //Массив готовых данных для провайдера данных yii2
        
        if (isset($array)) {
            $array = $array;
        } else {
            $array = [['Документ не загружен']];
        }
        
            $provider = new ArrayDataProvider([
                'allModels' => $array,
                'pagination' => [
                    'pageSize' => 20,
                ],

            ]);
                return $provider;

    }
}
