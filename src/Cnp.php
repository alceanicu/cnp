<?php

namespace Alcea\Cnp;

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
 * |CC| - county code - for a valid value check CNP::$CC
 * |XXX|- the serial number assigned to the person - 000 - 999
 * |C|  - check Digit
 *
 * ```php
 * use Alcea\Cnp\Cnp;
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
 * ```
 *
 * @see https://ro.wikipedia.org/wiki/Cod_numeric_personal
 * @author Alcea Nicolae <nicu(dotta)alcea(atta)gmail(dotta)com>
 *
 * @property string $_cnp
 * @property boolean $_isValid
 * @property array $_cnpArray
 * @property int $_year
 * @property int $_month
 * @property int $_day
 * @property string $_cc
 */
class Cnp
{
    private $_cnp;
    private $_isValid;
    private $_cnpArray;
    private $_year;
    private $_month;
    private $_day;
    private $_cc;
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
        '38' => 'Vâlcea',
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
     * @param string $cnp
     */
    public function __construct($cnp)
    {
        $this->_cnp = trim($cnp);
        $this->_cnpArray = str_split($this->_cnp);
        $this->_isValid = $this->validateCnp();
    }

    /**
     * Validation for Romanian Social Security Number (CNP)
     * @return bool
     */
    private function validateCnp()
    {
        // CNP must have 13 characters
        if (strlen($this->_cnp) != 13) {
            return false;
        }

        // Check Year
        $this->setYear();
        if (($this->_year < 1800) || ($this->_year > 2099)) {
            return false;
        }

        // Check for month
        $this->setMonth();
        if (($this->_month > 12) || ($this->_month < 1)) {
            return false;
        }

        // Check for day
        $this->setDay();
        if ($this->_day < 1) {
            return false;
        }
        if ($this->_day > 31) {
            return false;
        }
        if ($this->_day > 28) {
            // validate date for day of month - 28, 29, 30 si 31
            if (checkdate($this->_month, $this->_day, $this->_year) === false) {
                return false;
            }
        }

        // Check for county code
        $this->setCountyCode();
        if (!array_key_exists($this->_cc, self::$countyCode)) {
            return false;
        }

        $cnpArray = $this->_cnpArray;
        $hashArray = self::$controlKey;
        $hashSum = 0;
        // All characters must be numeric
        for ($i = 0; $i < 13; $i++) {
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

    /**
     *
     * @return void
     */
    private function setYear()
    {
        $cnp = $this->_cnpArray;
        $year = ($cnp[1] * 10) + $cnp[2];
        switch ($cnp[0]) {
            // romanian citizen born between 1900.01.01 and 1999.12.31
            case 1 :
            case 2 : {
                $year += 1900;
            }
                break;
            // romanian citizen born between 1800.01.01 and 1899.12.31
            case 3 :
            case 4 : {
                $year += 1800;
            }
                break;
            // romanian citizen born between 2000.01.01 and 2099.12.31
            case 5 :
            case 6 : {
                $year += 2000;
            }
                break;
            // residents && people with foreign citizenship
            case 7 :
            case 8 :
            case 9 : {
                $year += 2000;
                if ($year > (int)date('Y') - 14) {
                    $year -= 100;
                }
            }
                break;
            default : {
                $year = 0;
            }
                break;
        }

        $this->_year = $year;
    }

    /**
     * @return void
     */
    private function setMonth()
    {
        $this->_month = (int)($this->_cnpArray[3] . $this->_cnpArray[4]);
    }

    /**
     * @return void
     */
    private function setDay()
    {
        $this->_day = (int)($this->_cnpArray[5] . $this->_cnpArray[6]);
    }

    /**
     * @return void
     */
    private function setCountyCode()
    {
        $this->_cc = $this->_cnpArray[7] . $this->_cnpArray[8];
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
     * @return bool|string
     */
    public function getBirthDateFromCNP($format = 'Y-m-d')
    {
        if ($this->_isValid) {
            $time = "{$this->_year}-{$this->_month}-{$this->_day}";

            return \DateTime::createFromFormat('Y-m-d', $time)->format($format);
        }

        return false;
    }

    /**
     * Get gender from Romanian Social Security Number (CNP)
     * @param string $m
     * @param string $f
     * @return boolean
     */
    public function getGenderFromCNP($m = 'M', $f = 'F')
    {
        if ($this->_isValid) {
            $sexCode = $this->_cnpArray[0];
            switch ($sexCode) {
                case 1 :
                case 3 :
                case 5 :
                case 7 : {
                    return $m;
                }
                    break;
                case 2 :
                case 4 :
                case 6 :
                case 8 : {
                    return $f;
                }
                    break;
                default : {
                    return false;
                }
                    break;
            }
        }

        return false;
    }

}
