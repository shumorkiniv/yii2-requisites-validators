<?php


namespace shumorkiniv\tests;


use shumorkiniv\validators\InnValidator;
use yii\base\Model;

class TestModel extends Model
{
    /** @var mixed Test inn attribute */
    public $inn;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inn'], InnValidator::class]
        ];
    }
}
