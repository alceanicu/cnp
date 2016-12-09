<?php

use Alcea\Cnp\Cnp;

class CnpTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testCnpValid()
    {
        $cnp = new Cnp(5110102441483);

        $this->assertTrue($cnp->isValid());
        $this->assertEquals('2011/01/02', $cnp->getBirthDateFromCNP('Y/m/d'));
        $this->assertEquals('2011-01-02', $cnp->getBirthDateFromCNP('Y-m-d'));
        $this->assertEquals('male', $cnp->getGenderFromCNP('male', 'female'));
        $this->assertEquals('M', $cnp->getGenderFromCNP('M', 'F'));
        $this->assertEquals('M', $cnp->getGenderFromCNP());
        $this->assertNotEquals('M', $cnp->getGenderFromCNP('male', 'female'));
    }

    /**
     * @test 
     */
    public function testCnpInvalid()
    {
        $cnp = new Cnp(22222);
        $this->assertFalse($cnp->isValid());
    }

}
