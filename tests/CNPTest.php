<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class CNPTest extends TestCase
{

    public function cnpProvider()
    {
        return [
            // CNP, isValid, year, month, day, county, serial

            // valid CNP
            [6140101070075, true, 2014, 1, 1, 'F', 'Botosani', '007'],
            ['6140101070075', true, 2014, 1, 1, 'F', 'Botosani', '007'],
            [' 6140101070075', true, 2014, 1, 1, 'F', 'Botosani', '007'],
            [2331214442371, true, 1933, 12, 14, 'F', 'Bucuresti S.4', '237'],
            [1960911123653, true, 1996, 9, 11, 'M', 'Cluj', '365'],
            [3970908055828, true, 1897, 9, 8, 'M', 'Bihor', '582'],

            // invalid CNP
            ['397090805582I', false, '', '', '', '', '', ''],
            [1960911123655, false, '', '', '', '', '', ''],
            ['123', false, '', '', '', '', '', ''],
            ['614010107007A ', false, '', '', '', '', '', ''],
            [false, false, '', '', '', '', '', ''],
            [true, false, '', '', '', '', '', ''],
            [0, false, '', '', '', '', '', ''],
            [-1, false, '', '', '', '', '', ''],
            ['', false, '', '', '', '', '', ''],
            ['xxx', false, '', '', '', '', '', ''],
            [[], false, '', '', '', '', '', ''],
            [new stdClass(), false, '', '', '', '', '', ''],
        ];
    }

    /**
     * @dataProvider cnpProvider
     */
    public function test_CNP_is_valid_or_not($cnp, $isValid, $year, $month, $day, $sex, $county, $serial)
    {
        $_cnp = new Cnp($cnp);

        $this->assertEquals($_cnp->isValid(), $isValid);
        $this->assertEquals($_cnp->getBirthDateFromCNP('Y'), $year);
        $this->assertEquals($_cnp->getBirthDateFromCNP('m'), $month);
        $this->assertEquals($_cnp->getBirthDateFromCNP('d'), $day);
        $this->assertEquals($_cnp->getGenderFromCNP(), $sex);
        $this->assertEquals($_cnp->getBirthCountyFromCNP(), $county);
        $this->assertEquals($_cnp->getSerialNumberFromCNP(), $serial);
    }

    /**
     * @dataProvider cnpProvider
     */
    public function test_static_CNP_validator($cnp, $isValid)
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

}
