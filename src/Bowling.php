<?php
namespace Kata;

/**
 * Class Bowling
 * https://github.com/codingdojo-org/codingdojo.org/blob/master/content/kata/Bowling.md
 * @package Kata
 */
class Bowling
{
    public $character = [];

    /**
     * @param string $sequence ex : 'X X X X X X X X X X X X'
     * @return int
     */
    public function score($sequence)
    {
        $totalScore = 0;
        $frame = explode(" ", $sequence);

        foreach ($frame as $key => $round) {
            // 單看該局的分數(strike, spare都是10)
            $score[] = $this->transScore($round);
        }

        // 1~10局，每一局會去判斷前1~2局的狀況
        for ($i=0; $i < 10; $i++) {
            //本局得分
            $totalScore += $score[$i];

            //上一局是strike，要加上這局分數
            if ($i > 0 && $this->character[$i-1] == 'X') {
                $totalScore += $score[$i];
            }
            //上上一局是strike
            if ($i > 1 && $this->character[$i-2] == 'X') {
                //如果上一局也是strike
                if ($this->character[$i-1] == 'X') {
                    if ($this->character[$i] == 'X') {
                        $totalScore += $score[$i];
                    } else {
                        $thisRound = str_split($frame[$i]);
                        $totalScore += $thisRound[0];
                    }
                }
            }
            //如果上一局是spare，要加上這一局的第一次分數
            if ($i > 0 && $this->character[$i-1] == '/') {
                if ($this->character[$i] == 'X') {
                    $totalScore += 10;
                } else {
                    $thisRound = str_split($frame[$i]);
                    $totalScore += $thisRound[0];
                }
            }
        }

        // 有丟第12局(代表第10局是strike)
        if (isset($score[11])) {
            // 第9局是strike，要加上第11局的分數
            if ($this->character[8] == 'X') {
                $totalScore += $score[10];
            }
            // 第10局是strike，要加上第11,12局的分數
            if ($this->character[9] == 'X') {
                $totalScore += $score[10] + $score[11];
            }
        }

        // 第10局是spare
        if ($this->character[9] == '/') {
            // 加上加投一球的分數
            $tenRound = str_split($frame[9]);
            
            $totalScore += ($tenRound[2] == 'X')? 10 : $tenRound[2];
        }

        return $totalScore;
    }

    private function transScore($character)
    {
        if ($character == 'X') {
            $this->character[] = 'X';
            return 10;
        } elseif (strpos($character, '-') !== false) {
            $this->character[] = '-';
            $nums = str_split($character);
            return (strpos($character, '-') == 0)? $nums[1] : $nums[0];
        } elseif (strpos($character, '/') !== false) {
            $this->character[] = '/';
            return 10;
        } elseif (preg_match('/^\d{1,2}$/', $character)) {
            $this->character[] = 'd';
            $nums = str_split($character);
            return $nums[0] + $nums[1];
        }
    }
}
