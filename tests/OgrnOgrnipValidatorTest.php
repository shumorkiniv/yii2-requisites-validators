<?php

namespace shumorkiniv\tests;

use PHPUnit\Framework\TestCase;
use shumorkiniv\validators\OgrnOgrnipValidator;
use yii\base\BaseObject;

class OgrnOgrnipValidatorTest extends TestCase
{
    /**
     * @dataProvider successDataProvider
     * @param string $stringOgrn
     * @param int $intOgrn
     */
    public function testValidateSuccess(string $stringOgrn, int $intOgrn)
    {
        $validator = new OgrnOgrnipValidator();

        $this->assertTrue($validator->validate($stringOgrn));
        $this->assertTrue($validator->validate($intOgrn));
    }

    /**
     * @dataProvider failureDataProvider
     * @param mixed $ogrn
     */
    public function testValidateFailure($ogrn)
    {
        $validator = new OgrnOgrnipValidator();

        $this->assertFalse($validator->validate($ogrn));
    }

    /**
     * @dataProvider successDataProvider
     * @param string $stringOgrn
     * @param int $intOgrn
     */
    public function testValidateAttributeSuccess(string $stringOgrn, int $intOgrn)
    {
        $model = new TestModel();
        $validator = new OgrnOgrnipValidator();

        $model->ogrn = $stringOgrn;
        $validator->validateAttribute($model, 'ogrn');

        $this->assertFalse($model->hasErrors());

        $model->ogrn = $intOgrn;
        $validator->validateAttribute($model, 'ogrn');

        $this->assertFalse($model->hasErrors());
    }

    /**
     * @dataProvider failureDataProvider
     * @param mixed $ogrn
     */
    public function testValidateAttributeFailure($ogrn)
    {
        $model = new TestModel();
        $validator = new OgrnOgrnipValidator();

        $model->ogrn = $ogrn;
        $validator->validateAttribute($model, 'ogrn');

        $this->assertTrue($model->hasErrors());
    }


    public function testCustomErrorMessages()
    {
        $invalidMessage = 'Test invalid message';
        $wrongLengthMessage = 'Test wrong length message';
        $wrongCharMessage = 'Test wrong char message';

        $validator = new OgrnOgrnipValidator([
            'wrongLengthMessage' => $wrongLengthMessage,
            'wrongCharMessage' => $wrongCharMessage,
            'message' => $invalidMessage,
        ]);

        $validator->validate('10277002291931', $errorMessage);
        $this->assertEquals($wrongLengthMessage, $errorMessage);

        $validator->validate('102770022919a', $errorMessage);
        $this->assertEquals($wrongCharMessage, $errorMessage);

        $validator->validate(false, $errorMessage);
        $this->assertEquals($invalidMessage, $errorMessage);
    }

    public function testDefaultErrorMessages()
    {
        $validator = new OgrnOgrnipValidator();

        $validator->validate('10277002291931', $errorMessage);
        $this->assertEquals('ОГРН/ОГРНИП должен состоять из 13 или 15 чисел.', $errorMessage);

        $validator->validate('102770022919a', $errorMessage);
        $this->assertEquals('ОГРН/ОГРНИП должен состоять только из цифр.', $errorMessage);

        $validator->validate(false, $errorMessage);
        $this->assertEquals('Несуществующий ОГРН/ОГРНИП.', $errorMessage);
    }

    public function successDataProvider()
    {
        return [
            ['1027700229193', 1027700229193], //yandex
            ['1027739850962', 1027739850962], //mail.ru
            ['5077746476209', 5077746476209], //1C-Bitrix
        ];
    }

    public function failureDataProvider()
    {
        return [
            ['102770022919'], //wrong number of digits
            [false], //not string, not int
            [new BaseObject()], //not string, not int
            ['102770022919a'], //wrong characters
        ];
    }
}
