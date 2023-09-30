<?php

namespace Keyjay77\GuessNumber\Controller;
use function Keyjay77\GuessNumber\View\showGame;
use function cli\line;

function startGame()
{
    line("The guess-number game has been started!\n Good luck!");
}