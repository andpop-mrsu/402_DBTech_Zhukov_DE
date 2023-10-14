<?php

namespace keyjay77\GuessNumber\Controller;
use function Keyjay77\GuessNumber\View\showGame;
use function Keyjay77\GuessNumber\Model\guessNum;

function startGame()
{
    showGame();
    guessNum();
    
}