<?php

namespace shumorkiniv\validators;

use yii\validators\Validator;

/**
 * Class InnValidator
 * Валидатор ИНН
 */
class InnValidator extends Validator
{
    /** @var int  */
    const DIVISIOR = 11;
    /** @var int Индекс числа с которым сравнивается контрольное */
    const COMPARED_NUMBER_TEN_INDEX = 9;
    /** @var int Индекс числа с которым сравнивается первое контрольное */
    const COMPARED_NUMBER_TWELVE_INDEX1 = 10;
    /** @var int Индекс числа с которым сравнивается второе контрольное */
    const COMPARED_NUMBER_TWELVE_INDEX2 = 11;
    /** @var string Сообщение о неверном количестве знаков */
    const WRONG_COUNT_ERROR_MESSAGE = 'ИНН должен состоять из 11 или 12 чисел.';
    /** @var string Сообщение о неверном символе */
    const WRON_CHAR_ERROR_MESSAGE = 'ИНН должен состоять только из цифр.';
    /** @var string Сообщение при проваленной проверки контрольных чисел */
    const INVALID_VALUE_ERROR_MESSAGE = 'Несуществующий ИНН.';

    /** @var int[] Коэффициенты для проверки первой контрольной цифры 12-значного ИНН */
    private $twelveFirstCoefficients = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
    /** @var int[] Коэффициенты для проверки второй контрольной цифры 12-значного ИНН */
    private $twelveSecondCoefficients = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
    /** @var int[] Коэффициенты для проверки первой контрольной цифры 10-значного ИНН */
    private $tenCoefficients = [2, 4, 10, 3, 5, 9, 4, 6, 8];
    /** @var array */
    private $numbers;
    /** @var int */
    private $numbersCount;

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (is_int($value)) {
            $value = (string)$value;
        }

        if (strlen($value) !== 10 && strlen($value) !== 12) {
            return [self::WRONG_COUNT_ERROR_MESSAGE, ''];
        }

        if (!is_numeric($value)) {
            return [self::WRON_CHAR_ERROR_MESSAGE, ''];
        }

        $this->numbers = array_map('intval', str_split($value));
        $this->numbersCount = count($this->numbers);

        if ($this->numbersCount === 10) {
            if (!$this->checkFirstCheckNumber()) {
                return [self::INVALID_VALUE_ERROR_MESSAGE, ''];
            }
        }

        if ($this->numbersCount === 12) {
            if (!$this->checkFirstCheckNumber() || !$this->checkSecondCheckNumber()) {
                return [self::INVALID_VALUE_ERROR_MESSAGE, ''];
            }
        }

        return null;
    }

    /**
     * Проверка первого контрольного числа
     *
     * @return bool
     */
    private function checkFirstCheckNumber()
    {
        $coefficients = $this->numbersCount == 10 ? $this->tenCoefficients : $this->twelveFirstCoefficients;
        $checkNumber = $this->calculateCheckNumber($this->calculateCheckSum($coefficients));

        if ($this->numbersCount === 10) {
            return $checkNumber === $this->numbers[self::COMPARED_NUMBER_TEN_INDEX];
        }

        return $checkNumber === $this->numbers[self::COMPARED_NUMBER_TWELVE_INDEX1];
    }

    /**
     * Проверка второго контрольного числа (только для 12-значных)
     *
     * @return bool
     */
    private function checkSecondCheckNumber()
    {
        $checkNumber = $this->calculateCheckNumber($this->calculateCheckSum($this->twelveSecondCoefficients));

        return $checkNumber === $this->numbers[self::COMPARED_NUMBER_TWELVE_INDEX2];
    }

    /**
     * Вычисление контрольной суммы
     *
     * @param $coefficients
     * @return float|int
     */
    private function calculateCheckSum($coefficients)
    {
        $checkSum = 0;

        foreach ($this->numbers as $index => $number) {
            if (isset($coefficients[$index])) {
                $checkSum += $number * $coefficients[$index];
            }
        }

        return $checkSum;
    }

    /**
     * Вычисление контрольного числа
     *
     * @param $checkSum
     * @return float|int
     */
    private function calculateCheckNumber($checkSum)
    {
        $diff = $checkSum / self::DIVISIOR;

        $checkNumber = $checkSum - self::DIVISIOR * (int)$diff;

        if ($checkNumber > 9) {
            $checkNumber = $checkNumber % 10;
        }

        return $checkNumber;
    }
}
