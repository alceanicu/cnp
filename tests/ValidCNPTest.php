<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class ValidCNPTest extends TestCase
{
    public function validCnpProvider()
    {
        return [
            // CNP, isValid, year, month, day, county, serial
            [6140101070075, true, 2014, 1, 1, 'F', 'Botosani', '007'],
            ['6140101070075', true, 2014, 1, 1, 'F', 'Botosani', '007'],
            [' 6140101070075', true, 2014, 1, 1, 'F', 'Botosani', '007'],
            [2331214442371, true, 1933, 12, 14, 'F', 'Bucuresti S.4', '237'],
            [1960911123653, true, 1996, 9, 11, 'M', 'Cluj', '365'],
            [3970908055828, true, 1897, 9, 8, 'M', 'Bihor', '582'],
        ];
    }

    /**
     * @dataProvider validCnpProvider
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
     * @dataProvider validCnpProvider
     */
    public function test_static_CNP_validator($cnp, $isValid)
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

    public function test_Cnp()
    {
        $cnp = new Cnp(2890905230065);

        $this->assertTrue($cnp->isValid());

        $this->assertEquals('1989-09-05', $cnp->getBirthDateFromCNP());
        $this->assertEquals('1989/09/05', $cnp->getBirthDateFromCNP('Y/m/d'));
        $this->assertEquals('05.09.1989', $cnp->getBirthDateFromCNP('d.m.Y'));
        $this->assertEquals('1989', $cnp->getBirthDateFromCNP('Y'));

        $this->assertEquals('fem', $cnp->getGenderFromCNP('M', 'fem'));
        $this->assertEquals('F', $cnp->getGenderFromCNP('M', 'F'));
        $this->assertEquals('F', $cnp->getGenderFromCNP());
        $this->assertNotEquals('male', $cnp->getGenderFromCNP());
        $this->assertNotEquals('M', $cnp->getGenderFromCNP('male', 'female'));

        $this->assertEquals('Ilfov', $cnp->getBirthCountyFromCNP());
        $this->assertNotEquals('Iasi', $cnp->getBirthCountyFromCNP('-'));
    }
}
