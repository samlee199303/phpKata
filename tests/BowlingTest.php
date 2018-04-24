<?php
namespace Kata\Tests;

use Kata\Bowling;

class BowlingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider scoreTestProvider
     * @param string $sequence
     * @param int    $expectedResult
     */
    public function scoreTest($sequence, $expectedResult)
    {
        $bowling = new Bowling();
        
        
        $result = $bowling->score($sequence);
        $this->assertEquals($expectedResult, $result);
    }

    public function scoreTestProvider()
    {
        return [
            ['X X X X X X X X X X X X', 300],
            ['9- 9- 9- 9- 9- 9- 9- 9- 9- 9-', 90],
            ['5/ 5/ 5/ 5/ 5/ 5/ 5/ 5/ 5/ 5/5', 150],
            ['9- 9- X X 5/ 5/ 27 9- 5/ 9-', 136],
            ['9- 9- X X 5/ 5/ 27 9- 5/ 9/5', 142]
        ];
    }
}
