<?php

namespace keyjay77\GuessNumber\View;
use function cli\line;


function showGame()
{
    line("This is a guess-number game! Please, enter a number from 1 to " . MAX_NUM);
    line("If you wish to exit the game write 0.");
    line("Enter a number.");
}

function showMessage($message)
{
    line($message);
}