<?php
/**
 * @author: Mattakorn Limkool
 *
 */

namespace obbz\yii2\utils\xpdf;


use yii\base\Model;

class PdfInfo extends Model
{
    public $creator;

    public $producer;

    public $creatingDate;

    public $modifyDate;

    public $tagged;

    public $form;

    public $totalPages;

    public $encrypted;

    public $pageSize;

    /** @var  int  amount bytes for file size*/
    public $fileSize;

    public $optimized;

    public $pdfVersion;


    protected $regMatchArray = [
        'creator' => "/Creator:\s*(.*)/i",
        'producer' => "/Producer:\s*(.*)/i",
        'creatingDate' => "/CreationDate:\s*(.*)/i",
        'modifyDate' => "/ModDate:\s*(.*)/i",
        'tagged' => "/Tagged:\s*(.*)/i",
        'form' => "/Form:\s*(.*)/i",
        'totalPages' => "/Pages:\s*(\d+)/i",
        'encrypted' => "/Encrypted:\s*(.*)/i",
        'pageSize' => "/Page size:\s*(.*)/i",
        'fileSize' => "/File size:\s*(\d+)/i",
        'optimized' => "/Optimized:\s*(.*)/i",
        'pdfVersion' => "/PDF version:\s*(.*)/i",
    ];

    public function setAttributesByOutput($outputInfo){
        $value = null;
        foreach($outputInfo as $op)
        {
            foreach($this->regMatchArray as $attribute => $regexp){
                // Extract the number
                if(preg_match($regexp, $op, $matches) === 1)
                {
                    $this->$attribute = $matches[1];
                    break;
                }
            }

        }
    }
}