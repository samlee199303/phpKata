<?php
namespace Kata\Tests;

use Kata\Bank;

class BankTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function readTest()
    {
        $filename = "bank.txt";
        $expectedResult = ["123456789", "000000051"];

        $bank = new Bank();
        $ids = $bank->read($filename);

        for ($i=0; $i < count($ids); $i++) {
            $this->assertEquals($expectedResult[$i], $ids[$i]);
        }
        

        return $bank;
    }

    /**
     * @test
     * @depends readTest
     * @param obj $bank
     */
    public function validTest($obj)
    {
        $bank = $obj;
        $expectedResult = 0;

        $valids = $bank->valid();

        for ($i=0; $i < count($valids); $i++) {
            $this->assertEquals($expectedResult, $valids[$i]);
        }
    }

    /**
     * @test
     */
    public function writeTest()
    {
        $filename = "bank3.txt";
        $expectedResult = ["12345678? ILL", "000000051    ", "49006771? ILL"];

        $bank = new Bank();
        $bank->read($filename);
        $bank->valid();

        $results = $bank->write();

        for ($i=0; $i < count($results); $i++) {
            $this->assertEquals($expectedResult[$i], $results[$i]);
        }
    }

    /**
     * @test
     */
    public function handleErrOrIllTest()
    {
        $filename = "bank4.txt";
        $expectedResult = ["711111111", "777777177", "200800000", "333393333", "888888888 AMB ['888886888', '888888988', '888888880']", "555555555 AMB ['559555555', '555655555']", "666666666 AMB ['686666666', '666566666']", "999999999 AMB ['899999999', '993999999', '999959999']", "490067715 AMB ['490867715', '490067115', '490067719']", "123456789", "000000051", "490867715"];

        $bank = new Bank();
        $bank->read($filename);
        $bank->valid();
        $bank->write();

        $results = $bank->handleErrOrIll();
        // print_r($results);
        for ($i=0; $i < count($results); $i++) {
            $this->assertEquals($expectedResult[$i], $results[$i]);
        }
    }
    
    // public function readTestProvider()
    // {
    //     $fp = '/var/www/dev/kata/bowling-kata/bank.txt';

    //     return [
    //         [$fp, '123456789']
    //     ];
    // }
}
