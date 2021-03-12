<?php

namespace shumorkiniv\validators;

use yii\validators\Validator;

/**
 * Class InnValidator
 * InnValidator validates that the attribute value is valid INN.
 *
 * Note, this validator should only be used with string-typed or integer-typed attributes.
 *
 * @author Shumorkin Ilya <shumorkinilya@mail.ru>
 */
class InnValidator extends Validator
{
    /** @var int Divider for check sum */
    const DIVIDER = 11;
    /** @var int Index of number wich compare with control */
    const COMPARED_NUMBER_LEGAL_INDEX = 9;
    /** @var int Index of number wich compare with first control */
    const COMPARED_NUMBER_INDIVIDUAL_INDEX1 = 10;
    /** @var int Index of number wich compare with second control */
    const COMPARED_NUMBER_INDIVIDUAL_INDEX2 = 11;
    /** @var int Length of INN for legal entities */
    const LEGAL_ENTITY_LENGTH = 10;
    /** @var int Length of INN for individuals */
    const INDIVIDUAL_LENGTH = 12;

    /** @var string[] Default error messages */
    private const DEFAULT_ERROR_MESSAGES = [
        'wrongLength' => 'ИНН должен состоять из 11 или 12 чисел.',
        'wrongChar' => 'ИНН должен состоять только из цифр.',
        'invalidValue' => 'Несуществующий ИНН.',
    ];

    /** @var int[] Coefficients for check first check number of 12-digit INN */
    private const N1_COEFFICIENTS_INDIVIDUAL = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
    /** @var int[] Coefficients for check first check number of 12-digit INN */
    private const N2_COEFFICIENTS_INDIVIDUAL = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
    /** @var int[] Coefficients for check check number of 10-digit INN */
    private const N1_COEFFICIENTS_LEGAL_ENTITY = [2, 4, 10, 3, 5, 9, 4, 6, 8];

    /** @var string User wrong count message */
    public string $wrongLengthMessage;
    /** @var string User wrong character message */
    public string $wrongCharMessage;

    /** @var array */
    private array $numbers;
    /** @var int */
    private int $length;

    /**
     * @inheritdoc
     */
    protected function validateValue($value): ?array
    {
        if (!is_int($value) || !is_string($value)) {
            return [$this->message ?: self::DEFAULT_ERROR_MESSAGES['invalidValue'], []];
        }

        if (is_int($value)) {
            $value = (string)$value;
        }

        if (strlen($value) !== self::LEGAL_ENTITY_LENGTH && strlen($value) !== self::INDIVIDUAL_LENGTH) {
            return [$this->wrongLengthMessage ?: self::DEFAULT_ERROR_MESSAGES['wrongLength'], []];
        }

        if (!is_numeric($value)) {
            return [$this->wrongCharMessage ?: self::DEFAULT_ERROR_MESSAGES['wrongChar'], []];
        }

        $this->numbers = array_map('intval', str_split($value));
        $this->length = count($this->numbers);

        if ($this->length === self::LEGAL_ENTITY_LENGTH) {
            if (!$this->checkN1CheckNumber()) {
                return [$this->message ?: self::DEFAULT_ERROR_MESSAGES['invalidValue'], []];
            }
        }

        if ($this->length === self::INDIVIDUAL_LENGTH) {
            if (!$this->checkN1CheckNumber() || !$this->checkN2CheckNumber()) {
                return [$this->message ?: self::DEFAULT_ERROR_MESSAGES['invalidValue'], []];
            }
        }

        return null;
    }

    /**
     * Check of n1 - first check number
     *
     * @return bool
     */
    private function checkN1CheckNumber(): bool
    {
        $isLegal = $this->length === self::LEGAL_ENTITY_LENGTH;

        $coefficients = $isLegal ? self::N1_COEFFICIENTS_LEGAL_ENTITY : self::N1_COEFFICIENTS_INDIVIDUAL;
        $checkNumber = $this->calculateCheckNumber($this->calculateCheckSum($coefficients));

        if ($isLegal) {
            return $checkNumber === $this->numbers[self::COMPARED_NUMBER_LEGAL_INDEX];
        }

        return $checkNumber === $this->numbers[self::COMPARED_NUMBER_INDIVIDUAL_INDEX1];
    }

    /**
     * Check of n2 - seond check number (only for individual)
     *
     * @return bool
     */
    private function checkN2CheckNumber(): bool
    {
        $checkNumber = $this->calculateCheckNumber($this->calculateCheckSum($this->twelveSecondCoefficients));

        return $checkNumber === $this->numbers[self::COMPARED_NUMBER_INDIVIDUAL_INDEX2];
    }

    /**
     * Calculating of check sum
     *
     * @param int[] $coefficients
     * @return int
     */
    private function calculateCheckSum(array $coefficients): int
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
     * Calculating of check number
     *
     * @param int $checkSum
     * @return int
     */
    private function calculateCheckNumber(int $checkSum): int
    {
        $diff = $checkSum / self::DIVIDER;

        $checkNumber = $checkSum - self::DIVIDER * (int)$diff;

        if ($checkNumber > 9) {
            $checkNumber = $checkNumber % 10;
        }

        return $checkNumber;
    }
}
