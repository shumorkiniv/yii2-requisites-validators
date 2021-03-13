<?php

namespace shumorkiniv\tests;

use PHPUnit\Framework\TestCase;
use shumorkiniv\validators\InnValidator;
use yii\base\BaseObject;

class InnValidatorTest extends TestCase
{
    /**
     * @dataProvider successDataProvider
     * @param string $innString
     * @param int $innInt
     */
    public function testValidateSuccess(string $innString, int $innInt)
    {
        $validator = new InnValidator();

        $this->assertTrue($validator->validate($innString));
        $this->assertTrue($validator->validate($innInt));
    }

    /**
     * @dataProvider failureDataProvider
     * @param mixed $inn
     */
    public function testValidateFailure($inn)
    {
        $validator = new InnValidator();

        $this->assertFalse($validator->validate($inn));
    }

    public function successDataProvider()
    {
        return [
            ['7736207543', 7736207543], //yandex
            ['7743001840', 7743001840], //mail.ru
            ['7717586110', 7717586110], //1C-Bitrix
            ['745215640540', 745215640540], //shumorkiniv
        ];
    }

    public function failureDataProvider()
    {
        return [
            ['773620754'], //wrong number of digits
            [false], //not string, not int
            [new BaseObject()], //not string, not int
            ['745215asd640540'], //wrong characters
        ];
    }
}
