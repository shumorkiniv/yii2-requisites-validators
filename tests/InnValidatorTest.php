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

    /**
     * @dataProvider successDataProvider
     * @param string $innString
     * @param int $innInt
     */
    public function testValidateAttributeSuccess(string $innString, int $innInt)
    {
        $model = new TestModel();
        $validator = new InnValidator();

        $model->inn = $innString;
        $validator->validateAttribute($model, 'inn');

        $this->assertFalse($model->hasErrors());

        $model->inn = $innInt;
        $validator->validateAttribute($model, 'inn');

        $this->assertFalse($model->hasErrors());
    }

    /**
     * @dataProvider failureDataProvider
     * @param mixed $inn
     */
    public function testValidateAttributeFailure($inn)
    {
        $model = new TestModel();
        $validator = new InnValidator();

        $model->inn = $inn;
        $validator->validateAttribute($model, 'inn');

        $this->assertTrue($model->hasErrors());
    }

    public function testCustomErrorMessages()
    {
        $invalidMessage = 'Test invalid message';
        $wrongLengthMessage = 'Test wrong length message';
        $wrongCharMessage = 'Test wrong char message';

        $validator = new InnValidator([
            'wrongLengthMessage' => $wrongLengthMessage,
            'wrongCharMessage' => $wrongCharMessage,
            'message' => $invalidMessage,
        ]);

        $validator->validate('77362075431', $errorMessage);
        $this->assertEquals($wrongLengthMessage, $errorMessage);

        $validator->validate('773620754a', $errorMessage);
        $this->assertEquals($wrongCharMessage, $errorMessage);

        $validator->validate(false, $errorMessage);
        $this->assertEquals($invalidMessage, $errorMessage);
    }

    public function testDefaultErrorMessages()
    {
        $validator = new InnValidator();

        $validator->validate('77362075431', $errorMessage);
        $this->assertEquals('ИНН должен состоять из 11 или 12 чисел.', $errorMessage);

        $validator->validate('773620754a', $errorMessage);
        $this->assertEquals('ИНН должен состоять только из цифр.', $errorMessage);

        $validator->validate(false, $errorMessage);
        $this->assertEquals('Несуществующий ИНН.', $errorMessage);
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
