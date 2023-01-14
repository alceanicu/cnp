<?php

namespace alcea\cnp;

use DateTime;

/**
 * Class Cnp - Validates Personal Identification Number for Romanian citizens and residents.
 * Validation for Personal Identification Number (CNP)
 *
 * Valid format:
 * |S|YY|MM|DD|CC|XXX|C|
 * where :
 * |S|  - Gender number for:
 *      1 = Male, born between 1900 - 1999
 *      2 = Female, born between 1900 - 1999
 *      3 = Male, born between 1800 - 1899
 *      4 = Female, born between 1800 - 1899
 *      5 = Male, born between 2000 - 2099
 *      6 = Female, born between 2000 - 2099
 *      7 = Male resident (century does not apply)
 *      8 = Female resident (century does not apply)
 *      9 = Foreign people (century does not apply)
 *
 * |YY| - year of birth - 00 - 99
 * |MM| - birth month - 01 - 12
 * |DD| - birthday - 01 - 28/29/30/31
 * |CC| - county code - for a valid value check CNP::COUNTY_CODE
 * |XXX|- the serial number assigned to the person - 000 - 999
 * |C|  - check Digit
 *
 * ```php
 * use alcea\cnp\Cnp;
 *
 * $cnpToValidate = '5110102441483';
 * $cnp = new Cnp($cnpToValidate);
 * if ($cnp->isValid()) {
 *      // extract info from CNP
 *      echo "CNP {$cnpToValidate} - is valid" . PHP_EOL;
 *      echo "Birth Date: {$cnp->getBirthDateFromCNP('Y/m/d')}" . PHP_EOL;
 *      echo "Birthplace: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
 *      echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
 *      echo "Person is " . ($cnp->isPersonMajor() ? '' : 'not' ) . ' major' . PHP_EOL;
 *      echo "Person have an Identity Card " . ($cnp->hasIdentityCard() ? 'YES' : 'NO' );
 *      echo "Person is resident " . ($cnp->isResident() ? 'YES' : 'NO' );
 * } else {
 *      echo "CNP {$cnpToValidate} is invalid" . PHP_EOL;
 * }
 *
 * // or call static
 *
 * echo "CNP {$cnpToValidate} is " () . Cnp::validate($cnpToValidate) ? 'valid' : 'invalid';
 *
 * ```
 * @see https://ro.wikipedia.org/wiki/Cod_numeric_personal
 * @see https://github.com/vimishor/cnp-spec/blob/master/spec.md
 * @author Niku Alcea <nicu(dotta)alcea(atta)gmail(dotta)com>
 *
 * @property bool $isValid
 * @property array $cnpArray
 * @property int $year
 * @property string $month
 * @property string $day
 * @property string $countyCode
 */
class Cnp
{
    public const CONTROL_KEY = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
    public const COUNTY_CODE = [
        '01' => 'Alba',
        '02' => 'Arad',
        '03' => 'Argeş',
        '04' => 'Bacău',
        '05' => 'Bihor',
        '06' => 'Bistriţa-Năsăud',
        '07' => 'Botoşani',
        '08' => 'Braşov',
        '09' => 'Brăila',
        '10' => 'Buzău',
        '11' => 'Caraş-Severin',
        '12' => 'Cluj',
        '13' => 'Constanţa',
        '14' => 'Covasna',
        '15' => 'Dâmboviţa',
        '16' => 'Dolj',
        '17' => 'Galaţi',
        '18' => 'Gorj',
        '19' => 'Harghita',
        '20' => 'Hunedoara',
        '21' => 'Ialomiţa',
        '22' => 'Iaşi',
        '23' => 'Ilfov',
        '24' => 'Maramureş',
        '25' => 'Mehedinţi',
        '26' => 'Mureş',
        '27' => 'Neamţ',
        '28' => 'Olt',
        '29' => 'Prahova',
        '30' => 'Satu Mare',
        '31' => 'Sălaj',
        '32' => 'Sibiu',
        '33' => 'Suceava',
        '34' => 'Teleorman',
        '35' => 'Timiş',
        '36' => 'Tulcea',
        '37' => 'Vaslui',
        '38' => 'Vâlcea',
        '39' => 'Vrancea',
        '40' => 'Bucureşti',
        '41' => 'Bucureşti Sector 1',
        '42' => 'Bucureşti Sector 2',
        '43' => 'Bucureşti Sector 3',
        '44' => 'Bucureşti Sector 4',
        '45' => 'Bucureşti Sector 5',
        '46' => 'Bucureşti Sector 6',
        '47' => 'Bucureşti Sector 7 (now defunct)',
        '48' => 'Bucureşti Sector 8 (now defunct)',
        '51' => 'Călăraşi',
        '52' => 'Giurgiu',
    ];

    private bool $isValid = false;
    private array $cnpArray = [];
    private int $year;
    private string $month = '';
    private string $day = '';
    private DateTime $date;
    private string $countyCode = '';

    /**
     * CNP constructor.
     * @param string|int $cnp
     */
    public function __construct($cnp)
    {
        $this->validateCNP((string)$cnp);
    }

    /**
     * @param string|int $cnp
     * @return bool
     */
    public static function validate($cnp): bool
    {
        return (new Cnp($cnp))->isValid();
    }

    /**
     * @param string $cnp
     * @return void
     */
    private function validateCNP(string $cnp): void
    {
        if ((strlen($cnp) === 13) && ctype_digit($cnp)) {
            foreach (str_split($cnp) as $val) {
                $this->cnpArray[] = intval($val);
            }

            $this->setYear();
            $this->month = $this->cnpArray[3] . $this->cnpArray[4];
            $this->day = $this->cnpArray[5] . $this->cnpArray[6];

            if ($this->checkDate()) {
                $this->isValid = $this->cnpArray[12] === $this->calculateHash();
            }
        }
    }

    /**
     * @return bool
     */
    private function checkDate(): bool
    {
        $isDateValid = $this->checkYear() && $this->checkMonth() && $this->checkDay();
        if ($isDateValid) {
            $this->date = new DateTime("{$this->year}-{$this->month}-{$this->day} 00:00:00");
        }

        return $isDateValid;
    }

    /**
     * @return void
     */
    private function setYear(): void
    {
        $year = ($this->cnpArray[1] * 10) + $this->cnpArray[2];
        $this->year = 0;

        if (in_array($this->cnpArray[0], [1, 2])) {
            # romanian citizen born between 1900.01.01 and 1999.12.31
            $this->year = $year + 1900;
        } elseif (in_array($this->cnpArray[0], [3, 4])) {
            # romanian citizen born between 1800.01.01 and 1899.12.31
            $this->year = $year + 1800;
        } elseif (in_array($this->cnpArray[0], [5, 6])) {
            # romanian citizen born between 2000.01.01 and 2099.12.31
            $this->year = $year + 2000;
        } elseif (in_array($this->cnpArray[0], [7, 8, 9])) {
            # residents && people with foreign citizenship
            $this->year = $year + 2000;
            if ($this->year > (int)date('Y') - 14) {
                $this->year -= 100;
            }
        }
    }

    /**
     * @return bool
     */
    private function checkYear(): bool
    {
        return ($this->year >= 1800) && ($this->year <= 2099);
    }

    /**
     * @return bool
     */
    private function checkMonth(): bool
    {
        $month = (int)$this->month;
        return ($month >= 1) && ($month <= 12);
    }

    /**
     * @return boolean
     */
    private function checkDay(): bool
    {
        $day = (int)$this->day;
        if (($day < 1) || ($day > 31)) {
            return false;
        }

        if ($day > 28) {
            # validate date for day of month - 28, 29, 30 si 31
            return checkdate((int)$this->month, $day, $this->year);
        }

        return true;
    }

    /**
     * @return boolean
     */
    private function checkCounty(): bool
    {
        /*
         * 47 = Bucureşti District 7 (now defunct)
         * 48 = Bucureşti District 8 (now defunct)
         */
        if (in_array($this->countyCode, ['47', '48'])) {
            $checkDate = new DateTime('1979-12-19 00:00:00');
            return $this->date <= $checkDate;
        }

        return array_key_exists($this->countyCode, self::COUNTY_CODE);
    }

    /**
     * @return int
     */
    private function calculateHash(): int
    {
        $hashSum = 0;

        for ($i = 0; $i < 12; $i++) {
            $hashSum += $this->cnpArray[$i] * self::CONTROL_KEY[$i];
        }

        $hashSum = $hashSum % 11;
        if ($hashSum == 10) {
            $hashSum = 1;
        }

        return $hashSum;
    }

    /**
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Get Birthplace from Romanian Social Security Number (CNP)
     * @param string $invalidReturn
     * @return string
     */
    public function getBirthCountyFromCNP(string $invalidReturn = ''): string
    {
        return $this->isValid && array_key_exists($this->countyCode, self::COUNTY_CODE) ? self::COUNTY_CODE[$this->countyCode] : $invalidReturn;
    }

    /**
     * Get Birth Date from Romanian Social Security Number (CNP)
     * @param string $format
     * @param string $invalidReturn
     * @return string
     */
    public function getBirthDateFromCNP(string $format = 'Y-m-d', string $invalidReturn = ''): string
    {
        return $this->isValid ? $this->date->format($format) : $invalidReturn;
    }

    /**
     * Get gender from Romanian Social Security Number (CNP)
     * @param string $male
     * @param string $female
     * @param string $invalidReturn
     * @return string
     */
    public function getGenderFromCNP(string $male = 'M', string $female = 'F', string $invalidReturn = ''): string
    {
        if ($this->isValid) {
            if (in_array($this->cnpArray[0], [1, 3, 5, 7])) {
                return $male;
            } elseif (in_array($this->cnpArray[0], [2, 4, 6, 8])) {
                return $female;
            }
        }

        return $invalidReturn;
    }

    /**
     * @param string $invalidReturn
     * @return string
     */
    public function getSerialNumberFromCNP(string $invalidReturn = ''): string
    {
        return $this->isValid ? $this->cnpArray[9] . $this->cnpArray[10] . $this->cnpArray[11] : $invalidReturn;
    }

    /**
     * Check if persoana is majora (has more then 18 years)
     * @return boolean
     */
    public function isPersonMajor(): bool
    {
        return $this->isValid && $this->getAgeInYears() >= 18;
    }

    /**
     * Are carte de identitate emisa de politie (emiterea se face la implinirea varstei de 14 ani)
     * @return boolean
     */
    public function hasIdentityCard(): bool
    {
        return $this->isValid && $this->getAgeInYears() >= 14;
    }

    /**
     * @return int
     */
    private function getAgeInYears(): int
    {
        return (int)$this
            ->date
            ->diff((new DateTime())->setTime(0, 0))
            ->format('%y');
    }
}
