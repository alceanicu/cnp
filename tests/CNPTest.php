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
            [1960911123655, false, false, false, false, false, false, false],
            ['123', false, false, false, false, false, false, false],
            ['614010107007A ', false, false, false, false, false, false, false],
            [false, false, false, false, false, false, false, false],
            [true, false, false, false, false, false, false, false],
            [0, false, false, false, false, false, false, false],
            [-1, false, false, false, false, false, false, false],
            ['', false, false, false, false, false, false, false],
            ['xxx', false, false, false, false, false, false, false],
            [[], false, false, false, false, false, false, false],
            [new stdClass(), false, false, false, false, false, false, false],
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
    public function test_static_CNP_validator($cnp, $isValid, $year, $month, $day, $sex, $county, $serial)
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

    /**
     * TESTCASE - CNP 2910627308894
     * BirthDate: [
     *      (Y/m/d) => 1991/06/27
     *      (d.m.Y) => 27.06.1991
     * ]
     * Gender : [
     *      ('M', 'F') => F
     *      ('Masc', 'Fem') => Fem
     * ]
     * County: Satu Mare
     *
     * @test
     */
    public function testCNP_2910627308894_is_valid()
    {
        $cnp = new Cnp(2910627308894);

        $this->assertTrue($cnp->isValid());
        $this->assertEquals('1991-06-27', $cnp->getBirthDateFromCNP());
        $this->assertEquals('1991/06/27', $cnp->getBirthDateFromCNP('Y/m/d'));
        $this->assertEquals('27.06.1991', $cnp->getBirthDateFromCNP('d.m.Y'));
        $this->assertEquals('F', $cnp->getGenderFromCNP());
        $this->assertEquals('Fem', $cnp->getGenderFromCNP('Masc', 'Fem'));
        $this->assertEquals('Satu Mare', $cnp->getBirthCountyFromCNP());
        $this->assertNotEquals('Iasi', $cnp->getBirthCountyFromCNP());
    }

    /**
     * TESTCASE - CNP 2890905230065
     * BirthDate: [
     *      (Y/m/d) => 1989/09/05
     *      (d.m.Y) => 05.09.1989
     * ]
     * Gender : [
     *      ('M', 'F') => F
     *      ('Masc', 'Fem') => Fem
     * ]
     * County: Ilfov
     *
     * @test
     */
    public function testCNP_2890905230065_is_valid()
    {
        $cnp = new Cnp(2890905230065);

        $this->assertTrue($cnp->isValid());
        $this->assertNotEquals('1991-06-27', $cnp->getBirthDateFromCNP());
        $this->assertEquals('1989/09/05', $cnp->getBirthDateFromCNP('Y/m/d'));
        $this->assertEquals('05.09.1989', $cnp->getBirthDateFromCNP('d.m.Y'));
        $this->assertEquals('1989', $cnp->getBirthDateFromCNP('Y'));
        $this->assertNotEquals('male', $cnp->getGenderFromCNP('male', 'female'));
        $this->assertEquals('F', $cnp->getGenderFromCNP('M', 'F'));
        $this->assertEquals('F', $cnp->getGenderFromCNP());
        $this->assertNotEquals('M', $cnp->getGenderFromCNP('male', 'female'));
        $this->assertEquals('Ilfov', $cnp->getBirthCountyFromCNP());
        $this->assertNotEquals('Iasi', $cnp->getBirthCountyFromCNP());
    }

    /**
     * @test
     */
    public function testCNP_22222_is_invalid()
    {
        $cnp = new Cnp(22222);

        $this->assertFalse($cnp->isValid());
        $this->assertFalse($cnp->getBirthCountyFromCNP());
        $this->assertFalse($cnp->getBirthDateFromCNP());
        $this->assertFalse($cnp->getGenderFromCNP());
    }
}
