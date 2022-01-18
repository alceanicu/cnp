<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class InvalidCnpTest extends TestCase
{
    /**
     * @return array<int, array<int,bool|int|string>>
     */
    public function invalidCnpProvider(): array
    {
        return [
            // CNP, isValid, year, month, day, county, serial
            ['397090805582I', false, '', '', '', '', '', ''],
            ['1960911123655', false, '', '', '', '', '', ''],
            ['9961211302613', false, '', '', '', '', '', ''],
            ['1791220479953', false, '', '', '', '', '', ''],
            ['123', false, '', '', '', '', '', ''],
            ['614010107007A ', false, '', '', '', '', '', ''],
            ['0', false, '', '', '', '', '', ''],
            ['-1', false, '', '', '', '', '', ''],
            ['', false, '', '', '', '', '', ''],
            ['null', false, '', '', '', '', '', ''],
            ['false', false, '', '', '', '', '', ''],
            ['xxx', false, '', '', '', '', '', ''],
        ];
    }

    /**
     * @dataProvider invalidCnpProvider
     */
    public function testCnpIsInvalid(
        string $cnp,
        bool   $isValid,
        string $year,
        string $month,
        string $day,
        string $sex,
        string $county,
        string $serial
    ): void {
        $cnpObj = new Cnp($cnp);

        $this->assertEquals($cnpObj->isValid(), $isValid);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('Y'), $year);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('m'), $month);
        $this->assertEquals($cnpObj->getBirthDateFromCNP('d'), $day);
        $this->assertEquals($cnpObj->getGenderFromCNP(), $sex);
        $this->assertEquals($cnpObj->getBirthCountyFromCNP(), $county);
        $this->assertEquals($cnpObj->getSerialNumberFromCNP(), $serial);
    }

    /**
     * @dataProvider invalidCnpProvider
     */
    public function testStaticCnpValidator(string $cnp, bool $isValid): void
    {
        $this->assertEquals($isValid, Cnp::validate($cnp));
    }

    public function testCnpInvalidReturnParameter(): void
    {
        $cnp = new Cnp('22222');

        $this->assertFalse($cnp->isValid());
        $this->assertEquals('N/A', $cnp->getBirthCountyFromCNP('N/A'));
        $this->assertEquals('', $cnp->getBirthCountyFromCNP());

        $this->assertEquals('', $cnp->getBirthDateFromCNP());
        $this->assertEquals('-', $cnp->getBirthDateFromCNP('Y-m-d', '-'));

        $this->assertEquals('', $cnp->getGenderFromCNP());
        $this->assertEquals('xxx', $cnp->getGenderFromCNP('M', 'F', 'xxx'));
    }
}
