<?php


namespace shumorkiniv\validators;

use yii\validators\Validator;

/**
 * Class OgrnOgrnipValidator
 * OgrnOgrnipValidator validates that the attribute value is valid OGRN or OGRNIP
 *
 * Note, this validator should only be used with string-typed or integer-typed attributes.
 *
 * @author Shumorkin Ilya <shumorkinilya@mail.ru>
 */
class OgrnOgrnipValidator extends RequisitesValidator
{
    /** @var int Divider for OGRN check sum */
    const OGRN_DIVIDER = 11;
    /** @var int Divider for OGRNIP check sum */
    const OGRNIP_DIVIDER = 13;

    /** @var int Length of OGRN */
    private const OGRN_LENGTH = 13;
    /** @var int Length of OGRNIP */
    private const ORGNIP_LENGTH = 15;

    /**
     * @inheritdoc
     */
    protected function getDefaultErrorMessages(): array
    {
        return [
            'wrongLength' => 'ОГРН/ОГРНИП должен состоять из 13 или 15 чисел.',
            'wrongChar' => 'ОГРН/ОГРНИП должен состоять только из цифр.',
            'invalidValue' => 'Несуществующий ОГРН/ОГРНИП.',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function checkValueLength(string $value): bool
    {
        return strlen($value) === self::OGRN_LENGTH || strlen($value) === self::ORGNIP_LENGTH;
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value): ?array
    {
        $preValidate = $this->preValidate($value);

        if (!empty($preValidate)) {
            return $preValidate;
        }

        $comparedNumber = $this->numbers[$this->length - 1];
        $checkSum = $this->calculateCheckSum();

        if ($comparedNumber !== $this->calculateCheckNumber($checkSum)) {
            return [$this->message, []];
        }

        return null;
    }

    /**
     * Calculating of check sum
     *
     * @return int
     */
    private function calculateCheckSum(): int
    {
        $factor = 1;
        $checkSum = 0;

        $numbers = $this->numbers;
        array_pop($numbers);

        foreach (array_reverse($numbers) as $number) {
            $checkSum += $number * $factor;
            $factor *= 10;
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
        $divider = $this->length === self::OGRN_LENGTH ? self::OGRN_DIVIDER : self::OGRNIP_DIVIDER;

        return $checkSum % $divider;
    }
}
