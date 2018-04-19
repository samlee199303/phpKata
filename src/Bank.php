<?php
namespace Kata;

/**
 * Class Bank
 * @package Kata
 */
class Bank
{
    public $ids = [];
    public $valids = [];
    public $results = [];
    public $origins = [];
    public $cases = [
                ' _ | ||_|   ',
                '     |  |   ',
                ' _  _||_    ',
                ' _  _| _|   ',
                '   |_|  |   ',
                ' _ |_  _|   ',
                ' _ |_ |_|   ',
                ' _   |  |   ',
                ' _ |_||_|   ',
                ' _ |_| _|   ',];

    /**
     * @param string $filename
     * @return array
     */
    public function read($filename)
    {
        $entry = [];

        $fp = __DIR__ . "/" . $filename;
        $handle = fopen($fp, 'r');

        if ($handle) {
            $count = 0;
            while (($line = fgets($handle)) !== false) {
                $entry[$count] = str_split($line, 3);
                
                // 4 line as a set
                if ($count == 3) {
                    $str = "";
                    $number = "";
                    $origins = [];
                    for ($i=0; $i < 9; $i++) {
                        $str = $entry[0][$i] . $entry[1][$i] . $entry[2][$i] . $entry[3][$i];
                        $origins[] = $str;
                        $number .= $this->convert($str);
                        $str = "";
                    }
                    $this->origins[] = $origins;
                    $this->ids[] = $number;
                    reset($entry);
                    $count = -1;
                }
                $count++;
            }
            fclose($handle);
        }

        return $this->ids;
    }

    /**
     * @return array
     */
    public function valid()
    {
        foreach ($this->ids as $id) {
            $nums = str_split($id);

            $total = 0;
            for ($i=1; $i <= 9; $i++) {
                $total += $nums[9-$i] * $i;
            }
            $this->valids[] = $total%11;
        }
        
        return $this->valids;
    }

    /**
     * @return array
     */
    public function write()
    {
        foreach ($this->ids as $key => $id) {
            if (strpos($id, '?') !== false) {
                $this->results[] = "$id ILL";
            } elseif ($this->valids[$key] != 0) {
                $this->results[] = "$id ERR";
            } else {
                $this->results[] = "$id    ";
            }
        }

        return $this->results;
    }

    /**
     * @return array
     */
    public function handleErrOrIll()
    {
        foreach ($this->results as $key => $result) {
            $content = explode(' ', $result);
            // print_r($content);
            switch ($content[1]) {
                case 'ILL':
                    $ans = $this->repair($content[0], $this->origins[$key]);
                    break;

                case 'ERR':
                    $ans = $this->repair($content[0], null, $this->valids[$key]);
                    break;
                
                default:
                    $ans = $content[0];
                    break;
            }
            if (is_array($ans)) {
                $raw = "";
                $raw .= $content[0] . " AMB [";

                for ($i=0; $i < count($ans); $i++) {
                    if ($i != count($ans) - 1) {
                        $raw .= "'" . $ans[$i] . "', ";
                    } else {
                        $raw .= "'" . $ans[$i] . "']";
                    }
                }
                $res[] = $raw;
            } else {
                $res[] = $ans;
            }
        }

        return $res;
    }

    private function convert($str)
    {
        switch ($str) {
            case $this->cases[0]:
                $number = '0';
                break;

            case $this->cases[1]:
                $number = '1';
                break;

            case $this->cases[2]:
                $number = '2';
                break;
            
            case $this->cases[3]:
                $number = '3';
                break;
            
            case $this->cases[4]:
                $number = '4';
                break;
            
            case $this->cases[5]:
                $number = '5';
                break;
            
            case $this->cases[6]:
                $number = '6';
                break;
            
            case $this->cases[7]:
                $number = '7';
                break;
            
            case $this->cases[8]:
                $number = '8';
                break;
            
            case $this->cases[9]:
                $number = '9';
                break;

            default:
                $number = '?';
                break;
        }
        return $number;
    }

    private function repair($id, $origins = null, $valid = null)
    {
        if ($origins != null) { // ILL handle
            preg_match_all('/\d*\?\d*/', $id, $matches);
            if (count($matches) == 1) {
                $pos = strpos($id, '?');
                $ans = $this->compare($origins[$pos]);

                foreach ($ans as $num) {
                    $trueIds = preg_replace('/\?/', $num, $id);

                    $nums = str_split($trueIds);

                    $total = 0;
                    for ($i=1; $i <= 9; $i++) {
                        $total += $nums[9-$i] * $i;
                    }
                    if ($total%11 == 0) {
                        return $trueIds;
                    }
                }
            }
        } elseif ($valid != null) { // ERR handle
            $nums = str_split($id);
            foreach ($nums as $key => $num) {
                $check = null;
                switch ($num) {
                    case '0': // 8
                        $check = $this->check(8, $nums, $key, $valid);
                        break;
                    
                    case '1': // 7
                    case '3': // 9
                        $check = $this->check(6, $nums, $key, $valid);
                        break;
                    
                    case '5': // 6 9
                        $check = $this->check(1, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(4, $nums, $key, $valid);
                        break;
                    
                    case '6': // 5 8
                        $check = $this->check(-1, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(2, $nums, $key, $valid);
                        break;
                        break;
                    
                    case '7': // 1
                        $check = $this->check(-6, $nums, $key, $valid);
                        break;
                    
                    case '8': // 0 6 9
                        $check = $this->check(-8, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(-2, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(1, $nums, $key, $valid);
                        break;
                    
                    case '9': // 3 5 8
                        $check = $this->check(-6, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(-4, $nums, $key, $valid);
                        if ($check != null) {
                            $ans[] = $check;
                        }
                        $check = $this->check(-1, $nums, $key, $valid);
                        break;
                    
                    default:
                        // continue;
                        break;
                }
                if ($check != null) {
                    $ans[] = $check;
                }
            }

            if (count($ans) == 1) {
                return implode("", $ans[0]);
            } elseif (count($ans) > 1) {
                foreach ($ans as $answer) {
                    $res[] = implode("", $answer);
                }
                return $res;
            }
        }
    }

    private function compare($pattern)
    {
        $patterns = str_split($pattern);
        foreach ($this->cases as $key => $case) {
            $diff = 0;
            for ($i=0; $i < 10; $i++) {
                if ($case[$i] != $patterns[$i]) {
                    $diff++;
                }
            }
            if ($diff == 1) {
                $ans[] = $key;
            }
        }
        
        return $ans;
    }

    private function check($diff, $nums, $key, $valid)
    {
        if ((($diff * (9 - $key) + $valid) % 11) == 0) {
            $tmp = $nums;
            $tmp[$key] += $diff;
            $ans = $tmp;

            return $ans;
        } else {
            return null;
        }
    }
}
