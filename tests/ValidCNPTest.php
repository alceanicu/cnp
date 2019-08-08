<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class ValidCNPTest extends TestCase
{
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
