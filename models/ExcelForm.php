<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use yii\base\Model;
use app\models\UploadForm;
/**
 * Description of ExcelForm
 *
 * @author lephin
 */
class ExcelForm extends Model 
{
    public function rules() {
        parent::rules();
    }
    
    /**
    * 
    * @return object
    * Получение данных из загруженного документа
    */
    public function loadExcel($line)
    {
     //   $uploadFileLine = new UploadForm();
        
        if (isset($line)){
            //Получает путь к загруженному файлу
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx(); //Объект для чтение данных из документа
            $reader->setReadDataOnly(true); // Не знаю
            $spreadsheet = $reader->load($line); // Загрузка документа
            
            return $spreadsheet;
            
        } else {
            return null;
        }
        
    }
}
