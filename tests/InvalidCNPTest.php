<?php

use alcea\cnp\Cnp;
use PHPUnit\Framework\TestCase;

final class InvalidCNPTest extends TestCase
{
    public function test_CNP_invalid_return_parameter()
    {
        $cnp = new Cnp(22222);

        $this->assertFalse($cnp->isValid());
        $this->assertEquals('N/A', $cnp->getBirthCountyFromCNP('N/A'));
        $this->assertEquals('', $cnp->getBirthCountyFromCNP());

        $this->assertEquals('', $cnp->getBirthDateFromCNP());
        $this->assertEquals('-', $cnp->getBirthDateFromCNP('Y-m-d','-'));

        $this->assertEquals('', $cnp->getGenderFromCNP());
        $this->assertEquals('xxx', $cnp->getGenderFromCNP('M', 'F', 'xxx'));
    }
}
