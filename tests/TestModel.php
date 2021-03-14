<?php


namespace shumorkiniv\tests;


use shumorkiniv\validators\InnValidator;
use shumorkiniv\validators\OgrnOgrnipValidator;
use yii\base\Model;

class TestModel extends Model
{
    /** @var mixed Test INN attribute */
    public $inn;
    /** @var mixed Test OGRN attribute */
    public $ogrn;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inn'], InnValidator::class],
            [['ogrn'], OgrnOgrnipValidator::class]
        ];
    }
}
