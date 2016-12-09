<?php

namespace Cnp;

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
 * $cnp = new \Cnp\Cnp($cnpToValidate);
 * if ($cnp->isValid()) {
 *      echo "CNP {$cnpToValidate} - is valid" . PHP_EOL;
 *      echo "Birth Date: {$cnp->getBirthDateFromCNP('Y/m/d')}" . PHP_EOL;
 *      echo "Birth Place: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
 * 	echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
 * } else {
 *      echo "CNP {$cnpToValidate} is invalid" . PHP_EOL;
 * }
 * ```
 *
 * @see https://ro.wikipedia.org/wiki/Cod_numeric_personal
 * @author Alcea Nicolae <nicu(dotta)alcea(atta)gmail(dotta)com>
 *
 * @property array $controlKey
 * @property array $monthCode
 * @property array $countyCode
 * @property string $_cnp
 * @property boolean $_isValid
 * @property array $_cnpArray
 * @property int|boolean $_year
 */
class Cnp
{

    private static $controlKey = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
    private static $monthCode = [
        '01' => 'Ianuarie',
        '02' => 'Februarie',
        '03' => 'Martie',
        '04' => 'Aprilie',
        '05' => 'Mai',
        '06' => 'Iunie',
        '07' => 'Iulie',
        '08' => 'August',
        '09' => 'Septembrie',
        '10' => 'Octombrie',
        '11' => 'Noiembrie',
        '12' => 'Decembrie'
    ];
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
    private $_cnp;
    private $_isValid;
    private $_cnpArray;
    private $_year;

    /**
     * CNP constructor.
     * @param string $cnp
     */
    public function __construct($cnp)
    {
        $this->_cnp = trim($cnp);
        $this->_cnpArray = str_split($this->_cnp);
        $this->_year = $this->setYear();
        $this->_isValid = $this->validateCnp();
    }

    /**
     * @return bool|int
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
                    if ($year > (int) date('Y') - 14) {
                        $year -= 100;
                    }
                }
                break;
            default : {
                    return false;
                }
                break;
        }

        return $year;
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
        $cnpArray = $this->_cnpArray;
        $hashArray = self::$controlKey;
        $hashSum = 0;
        // All characters must be numeric
        for ($i = 0; $i < 13; $i++) {
            if (!is_numeric($cnpArray[$i])) {
                return false;
            }
            if ($i < 12) {
                $hashSum += (int) $cnpArray[$i] * (int) $hashArray[$i];
            }
        }
        // Check Year
        $year = $this->_year;
        if (($year < 1800) || ($year > 2099)) {
            return false;
        }
        // Check for month
        $mm = $cnpArray[3] . $cnpArray[4];
        if (!in_array($mm, array_keys(self::$monthCode))) {
            return false;
        }
        // Check for day
        $dd = (int) ($cnpArray[5] . $cnpArray[6]);
        if ($dd < 1) {
            return false;
        }
        if ($dd > 28) {
            if ($dd > 31) {
                return false;
            }
            if ($dd <= 31) {
                // validate date for day of month - 28, 29, 30 si 31
                if (checkdate((int) $mm, $dd, $year) === false) {
                    return false;
                }
            }
        }
        // Check for county code
        $cc = $cnpArray[7] . $cnpArray[8];
        if (!in_array($cc, array_keys(self::$countyCode))) {
            return false;
        }
        $hashSum = $hashSum % 11;
        if ($hashSum == 10) {
            $hashSum = 1;
        }

        return ($cnpArray[12] == $hashSum);
    }

    /**
     * 
     * @return type
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
            $cnp = $this->_cnpArray;
            $cc = $cnp[7] . $cnp[8];

            return array_key_exists($cc, self::$countyCode) ? self::$countyCode[$cc] : $defaultReturn;
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
            $cnp = $this->_cnpArray;
            $year = $this->_year;
            if (($year < 1800) || ($year > 2099)) {
                return false;
            }
            $mm = $cnp[3] . $cnp[4];
            $dd = $cnp[5] . $cnp[6];
            return \DateTime::createFromFormat('Y-m-d', "{$year}-{$mm}-{$dd}")->format($format);
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
