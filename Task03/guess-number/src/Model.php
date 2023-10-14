<?php

namespace Keyjay77\GuessNumber\Model;
use function Keyjay77\GuessNumber\View\showMessage;
use function cli\input;

define("MAX_NUM", 100);
define("ATTEMPS", 10);

function randNumGen()
{
    return rand(1, MAX_NUM);
}

function guessNum()
{
    $user_num_attempt = 0;
    $guessing_num = randNumGen();
    while ($user_num_attempt < ATTEMPS)
    {
        $user_input = input();        
        if($user_input == "0")
        {
            showMessage("You have left the game");
            return;
        }
        elseif($user_input < $guessing_num)
        {
            showMessage("Guessing number is greater than your input number");
        }
        elseif($user_input > $guessing_num)
        {
            showMessage("Guessing number is lower than your input number");
        }
        elseif($user_input = $guessing_num)
        {
            showMessage("Hooray! You've guessed the number!");
            return;
        }
        $user_num_attempt++;
    }
    showMessage("You've failed!");
}

