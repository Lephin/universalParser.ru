<?php

namespace app\models;

use Yii;
use yii\base\Model;
//use yii\web\UploadedFile;
//use PhpOffice\PhpSpreadsheet\Spreadsheet; //Для работы с Excel
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;//Для работы с Excel
//use PhpOffice\PhpSpreadsheet\Reader\IReader; // Для чтения документа Excel

class UploadForm extends Model
{
    public $imageFile;
    public $lineFile;


    public function rules() {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => ['xlsx', 'xls'], 'maxSize' => 10024 * 10024, 'checkExtensionByMimeType' => false],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
                $sesion = Yii::$app->session;
                $this->imageFile->saveAs( $_SERVER['DOCUMENT_ROOT'].'/uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension); // Полный путь файла куда он загружен
                $this->lineFile = $_SERVER['DOCUMENT_ROOT'].'/uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
                $sesion->set('line', $this->lineFile);
            return true;
        } else {
            return false;
        }
    
    }
    
}

