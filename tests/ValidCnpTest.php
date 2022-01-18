<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class ValidCnpTest extends TestCase
{
    /**
     * @return array<int, array<int,bool|int|string>>
     */
    public function validCnpProvider(): array
    {
        return [
            // CNP, isValid, year, month, day, sex,  county, serial, hasIdentityCard, isPersonMajor
            ['1791219479952', true, 1979, 12, 19, 'M', 'Bucureşti Sector 7 (now defunct)', '995', true, true],
            ['2520619474609', true, 1952, 6, 19, 'F', 'Bucureşti Sector 7 (now defunct)', '460', true, true],
            // Bucureşti Sector 7 (now defunct) nust be invalid because is after 1979-12-19 00:00:00
            ['1791220479953', false, '', '', '', '', '', '', false, false],

            ['4010228349590', true, 1801, 2, 28, 'F', 'Teleorman', '959', true, true],
            ['3970908055828', true, 1897, 9, 8, 'M', 'Bihor', '582', true, true],

            ['2331214442371', true, 1933, 12, 14, 'F', 'Bucureşti Sector 4', '237', true, true],
            ['1480809214609', true, 1948, 8, 9, 'M', 'Ialomiţa', '460', true, true],
            ['1960911123653', true, 1996, 9, 11, 'M', 'Cluj', '365', true, true],

            ['8001005376996', true, 2000, 10, 5, 'F', 'Vaslui', '699', true, true],

            // will fail in future
            ['6140101070075', true, 2014, 1, 1, 'F', 'Botoşani', '007', false, false],
            ['5160620302638', true, 2016, 6, 20, 'M', 'Satu Mare', '263', false, false],
        ];
    }

    /**
     * @dataProvider validCnpProvider
     */
    public function testCnpIsValidOrNot(
        string $cnp,
        bool   $isValid,
               $year,
               $month,
               $day,
        string $sex,
        string $county,
        string $serial,
        bool   $hasIdentityCard,
        bool   $isPersonMajor
    ): void
    {
        $cnpObj = new Cnp($cnp);

        $this->assertEquals($cnpObj->isValid(), $isValid);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('Y'), $year);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('m'), $month);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('d'), $day);
        $this->assertEquals($cnpObj->getGenderFromCNP(), $sex);
        $this->assertEquals($cnpObj->getBirthCountyFromCNP(), $county);
        $this->assertEquals($cnpObj->getSerialNumberFromCNP(), $serial);
        $this->assertEquals($cnpObj->hasIdentityCard(), $hasIdentityCard);
        $this->assertEquals($cnpObj->isPersonMajor(), $isPersonMajor);
    }

    /**
     * @dataProvider validCnpProvider
     * @param string $cnp
     * @param bool $isValid
     */
    public function testStaticCnpValidator(string $cnp, bool $isValid): void
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

    public function testCnp(): void
    {
        $cnp = new Cnp('2890905230065');

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
