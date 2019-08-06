<?php

namespace alcea\cnp;

/**
 * Class Cnp - Validation for Romanian Social Security Number (CNP)
 *
 * Valid format:
 * |S|YY|MM|DD|CC|XXX|C|
 * where :
 * |S|  - Gender number (Male/Women) for:
 *      1/2 - romanian citizen born between 1900.01.01 and 1999.12.31
 *      3/4 - romanian citizen born between 1800.01.01 and 1899.12.31
 *      5/6 - romanian citizen born between 2000.01.01 and 2099.12.31
 *      7/8 - residents
 *      9   - people with foreign citizenship
 *
 * |YY| - year of birth - 00 - 99
 * |MM| - birth month - 01 - 12
 * |DD| - birthday - 01 - 28/29/30/31
 * |CC| - county code - for a valid value check CNP::$countyCode
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
 *      echo "Birth Place: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
 *    echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
 * } else {
 *      echo "CNP {$cnpToValidate} is invalid" . PHP_EOL;
 * }
 *
 * // or
 *
 * echo "CNP {$cnpToValidate} is " () . Cnp::validate($cnpToValidate) ? 'valid' : 'invalid';
 *
 * ```
 *
 * @see https://ro.wikipedia.org/wiki/Cod_numeric_personal
 * @author Niku Alcea <nicu(dotta)alcea(atta)gmail(dotta)com>
 *
 * @property boolean $_isValid
 * @property array $_cnp
 * @property string $_year
 * @property string $_month
 * @property string $_day
 * @property string $_cc
 */
class Cnp
{
    private $_isValid = false;
    private $_cnp = [];
    private $_year = '';
    private $_month = '';
    private $_day = '';
    private $_cc = '';

    private static $controlKey = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
    private static $countyCode = [
        '01' => 'Alba',
        '02' => 'Arad',
        '03' => 'Arges',
        '04' => 'Bacau',
        '05' => 'Bihor',
        '06' => 'Bistrita-Nasaud',
        '07' => 'Botosani',
        '08' => 'Brasov',
        '09' => 'Braila',
        '10' => 'Buzau',
        '11' => 'Caras-Severin',
        '12' => 'Cluj',
        '13' => 'Constanta',
        '14' => 'Covasna',
        '15' => 'Dambovita',
        '16' => 'Dolj',
        '17' => 'Galati',
        '18' => 'Gorj',
        '19' => 'Harghita',
        '20' => 'Hunedoara',
        '21' => 'Ialomita',
        '22' => 'Iasi',
        '23' => 'Ilfov',
        '24' => 'Maramures',
        '25' => 'Mehedinti',
        '26' => 'Mures',
        '27' => 'Neamt',
        '28' => 'Olt',
        '29' => 'Prahova',
        '30' => 'Satu Mare',
        '31' => 'Salaj',
        '32' => 'Sibiu',
        '33' => 'Suceava',
        '34' => 'Teleorman',
        '35' => 'Timis',
        '36' => 'Tulcea',
        '37' => 'Vaslui',
        '38' => 'Valcea',
        '39' => 'Vrancea',
        '40' => 'Bucuresti',
        '41' => 'Bucuresti S.1',
        '42' => 'Bucuresti S.2',
        '43' => 'Bucuresti S.3',
        '44' => 'Bucuresti S.4',
        '45' => 'Bucuresti S.5',
        '46' => 'Bucuresti S.6',
        '51' => 'Calarasi',
        '52' => 'Giurgiu'
    ];

    /**
     * CNP constructor.
     * @param string|int $cnp
     */
    public function __construct($cnp)
    {
        try {
            $this->_cnp = str_split(trim($cnp));
            $this->_isValid = $this->validateCnp();
        } catch (\Throwable $e) {
            $this->_isValid = false;
        }
    }

    /**
     * @param string|int $cnp
     * @return bool
     */
    public static function validate($cnp)
    {
        return (new static($cnp))->isValid();
    }

    /**
     * Validation for Romanian Social Security Number (CNP)
     * @return bool
     */
    private function validateCnp()
    {
        $cnpArray = $this->_cnp;

        // CNP must have 13 characters
        if (count($cnpArray) != 13) {
            return false;
        }

        // Set and check year, month, day and county
        if ($this->year() && $this->month() && $this->day() && $this->county()) {
            $hashArray = self::$controlKey;
            $hashSum = 0;
            // All characters must be numeric
            for ($i = 0; $i <= 12; $i++) {
                if (!is_numeric($cnpArray[$i])) {
                    return false;
                }
                if ($i < 12) {
                    $hashSum += (int)$cnpArray[$i] * (int)$hashArray[$i];
                }
            }

            $hashSum = $hashSum % 11;
            if ($hashSum == 10) {
                $hashSum = 1;
            }

            return ($cnpArray[12] == $hashSum);
        }

        return false;
    }

    /**
     * Check and set year
     * @return boolean
     */
    private function year()
    {
        $year = ($this->_cnp[1] * 10) + $this->_cnp[2];

        switch ($this->_cnp[0]) {
            // romanian citizen born between 1900.01.01 and 1999.12.31
            case 1 :
            case 2 :
                $this->_year = $year + 1900;
                break;
            // romanian citizen born between 1800.01.01 and 1899.12.31
            case 3 :
            case 4 :
                $this->_year = $year + 1800;
                break;
            // romanian citizen born between 2000.01.01 and 2099.12.31
            case 5 :
            case 6 :
                $this->_year = $year + 2000;
                break;
            // residents && people with foreign citizenship
            case 7 :
            case 8 :
            case 9 :
                $this->_year = $year + 2000;
                if ($this->_year > (int)date('Y') - 14) {
                    $this->_year -= 100;
                }
                break;
            default :
                return false;
        }

        return ($this->_year >= 1800) && ($this->_year <= 2099);
    }

    /**
     * Check and set month
     * @return boolean
     */
    private function month()
    {
        $this->_month = $this->_cnp[3] . $this->_cnp[4];
        $month = (int)$this->_month;

        return ($month >= 1) && ($month <= 12);
    }

    /**
     * Check and set day in month
     * @return boolean
     */
    private function day()
    {
        $this->_day = $this->_cnp[5] . $this->_cnp[6];
        $day = (int)$this->_day;

        if (($day < 1) || ($day > 31)) {
            return false;
        }

        if ($day > 28) {
            // validate date for day of month - 28, 29, 30 si 31
            if (checkdate((int)$this->_month, $day, (int)$this->_year) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check and set county code
     * @return boolean
     */
    private function county()
    {
        $this->_cc = $this->_cnp[7] . $this->_cnp[8];

        return array_key_exists($this->_cc, self::$countyCode);
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Get Birth Place from Romanian Social Security Number (CNP)
     * @param string|bool $defaultReturn
     * @return string|bool
     */
    public function getBirthCountyFromCNP($defaultReturn = false)
    {
        if ($this->_isValid) {
            return self::$countyCode[$this->_cc];
        }

        return $defaultReturn;
    }

    /**
     * Get Birth Date from Romanian Social Security Number (CNP)
     * @param string $format
     * @return string|boolean
     */
    public function getBirthDateFromCNP($format = 'Y-m-d')
    {
        if ($this->_isValid) {
            return \DateTime::createFromFormat('Y-m-d', "{$this->_year}-{$this->_month}-{$this->_day}")->format($format);
        }

        return false;
    }

    /**
     * Get gender from Romanian Social Security Number (CNP)
     * @param string $m
     * @param string $f
     * @return string|boolean
     */
    public function getGenderFromCNP($m = 'M', $f = 'F')
    {
        if ($this->_isValid) {
            if (in_array($this->_cnp[0], [1, 3, 5, 7])) {
                return $m;
            } elseif (in_array($this->_cnp[0], [2, 4, 6, 8])) {
                return $f;
            }
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getSerialNumberFromCNP()
    {
        if ($this->_isValid) {
            return $this->_cnp[9] . $this->_cnp[10] . $this->_cnp[11];
        }

        return false;
    }

}
