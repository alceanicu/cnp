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
 *      echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
 *      echo "Person is " . ($cnp->isPersonMajor() ? '' : 'not' ) . ' major' . PHP_EOL;
 *      echo "Person have an Identity Card " . ($cnp->hasIdentityCard() ? 'YES' : 'NO' );
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
 * @property string $cnp
 * @property array $_cnp
 * @property string $_year
 * @property string $_month
 * @property string $_day
 * @property string $_cc
 */
class Cnp
{
    private $_isValid = false;
    private $cnp = '';
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
            $this->cnp = trim($cnp);
            $this->_cnp = str_split($this->cnp);
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
        if (count($this->_cnp) != 13) {
            return false;
        }

        if (!ctype_digit($this->cnp)) {
            return false;
        }

        $this->setYear();
        $this->setMonth();
        $this->setDay();
        $this->setCounty();

        if ($this->checkYear() && $this->checkMonth() && $this->checkDay() && $this->checkCounty()) {
            return ($this->_cnp[12] == $this->calculateHash());
        }

        return false;
    }

    private function setYear()
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
                $this->_year = 0;
        }
    }

    private function setMonth()
    {
        $this->_month = $this->_cnp[3] . $this->_cnp[4];
    }

    private function setDay()
    {
        $this->_day = $this->_cnp[5] . $this->_cnp[6];
    }

    private function setCounty()
    {
        $this->_cc = $this->_cnp[7] . $this->_cnp[8];
    }

    /**
     * @return bool
     */
    private function checkYear()
    {
        return ($this->_year >= 1800) && ($this->_year <= 2099);
    }

    /**
     * @return bool
     */
    private function checkMonth()
    {
        $month = (int)$this->_month;
        return ($month >= 1) && ($month <= 12);
    }

    /**
     * @return boolean
     */
    private function checkDay()
    {
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
     * @return boolean
     */
    private function checkCounty()
    {
        return array_key_exists($this->_cc, self::$countyCode);
    }

    /**
     * @return int
     */
    private function calculateHash()
    {
        $hashSum = 0;

        for ($i = 0; $i < 12; $i++) {
            $hashSum += $this->_cnp[$i] * self::$controlKey[$i];
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
    public function isValid()
    {
        return $this->_isValid;
    }

    /**
     * Get Birth Place from Romanian Social Security Number (CNP)
     * @param mixed|string $invalidReturn
     * @return mixed|string
     */
    public function getBirthCountyFromCNP($invalidReturn = '')
    {
        return ($this->_isValid) ? self::$countyCode[$this->_cc] : $invalidReturn;
    }

    /**
     * Get Birth Date from Romanian Social Security Number (CNP)
     * @param string $format
     * @param mixed|string $invalidReturn
     * @return string
     */
    public function getBirthDateFromCNP($format = 'Y-m-d', $invalidReturn = '')
    {
        if ($this->_isValid) {
            return \DateTime::createFromFormat('Y-m-d', "{$this->_year}-{$this->_month}-{$this->_day}")
                ->format($format);
        }

        return $invalidReturn;
    }

    /**
     * Get gender from Romanian Social Security Number (CNP)
     * @param string $m
     * @param string $f
     * @param string $invalidReturn
     * @return string
     */
    public function getGenderFromCNP($m = 'M', $f = 'F', $invalidReturn = '')
    {
        if ($this->_isValid) {
            if (in_array($this->_cnp[0], [1, 3, 5, 7])) {
                return $m;
            } elseif (in_array($this->_cnp[0], [2, 4, 6, 8])) {
                return $f;
            }
        }

        return $invalidReturn;
    }

    /**
     * @return string
     */
    public function getSerialNumberFromCNP()
    {
        return ($this->_isValid) ? $this->_cnp[9] . $this->_cnp[10] . $this->_cnp[11] : '';
    }

    /**
     * Verifica daca titularul CNP este major (>=18 years)
     * @return boolean
     */
    public function isPersonMajor()
    {
        return ($this->_isValid) ? ($this->getAgeInYears() >= 18) : false;
    }

    /**
     * Are carte de identitate emisa de politie (emiterea se face la implinirea varstei de 14 ani)
     * @return boolean
     */
    public function hasIdentityCard()
    {
        return ($this->_isValid) ? ($this->getAgeInYears() >= 14) : false;
    }

    /**
     * @return int
     */
    private function getAgeInYears()
    {
        try {
            $time = "{$this->_year}-{$this->_month}-{$this->_day}";
            $birthDate = \DateTime::createFromFormat('Y-m-d', $time);
            $now = (new \DateTime())->setTime(0, 0, 0);
            return (int)($birthDate->diff($now)->format('%y'));
        } catch (\Throwable $e) {
            return 0;
        }

    }

}
