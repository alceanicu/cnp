<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class InvalidCNPTest extends TestCase
{
    public function invalidCnpProvider()
    {
        return [
            // CNP, isValid, year, month, day, county, serial
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
     * @dataProvider invalidCnpProvider
     */
    public function test_CNP_is_invalid($cnp, $isValid, $year, $month, $day, $sex, $county, $serial)
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
     * @dataProvider invalidCnpProvider
     */
    public function test_static_CNP_validator($cnp, $isValid)
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

    public function test_CNP_invalid_return_parameter()
    {
        $cnp = new Cnp(22222);

        $this->assertFalse($cnp->isValid());
        $this->assertEquals('N/A', $cnp->getBirthCountyFromCNP('N/A'));
        $this->assertEquals('', $cnp->getBirthCountyFromCNP());

        $this->assertEquals('', $cnp->getBirthDateFromCNP());
        $this->assertEquals('-', $cnp->getBirthDateFromCNP('Y-m-d', '-'));

        $this->assertEquals('', $cnp->getGenderFromCNP());
        $this->assertEquals('xxx', $cnp->getGenderFromCNP('M', 'F', 'xxx'));
    }
}
