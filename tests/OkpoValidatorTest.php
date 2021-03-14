<?php


namespace shumorkiniv\tests;

use PHPUnit\Framework\TestCase;
use yii\base\BaseObject;

class OkpoValidatorTest extends TestCase
{
    public function testValidateSuccess()
    {

    }

    public function testValidateFailure()
    {

    }

    public function testValidateAttributeSuccess()
    {

    }

    public function testValidateAttributeFailure()
    {

    }

    public function testCustomErrorMessages()
    {

    }

    public function testDefaultErrorMessages()
    {

    }

    public function successDataProvider()
    {
        return [
            ['55187675', 55187675], //yandex
            ['52685881', 52685881], //mail.ru
            ['80715150', 80715150], //1C-Bitrix
        ];
    }

    public function failureDataProvider()
    {
        return [
            ['5518767'], //wrong number of digits
            [false], //not string, not int
            [new BaseObject()], //not string, not int
            ['5518767a'], //wrong characters
        ];
    }
}
