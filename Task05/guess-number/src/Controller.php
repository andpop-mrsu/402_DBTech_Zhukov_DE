<?php

namespace keyjay77\GuessNumber\Controller;

use Keyjay77\GuessNumber\Model\GameModel;
use Keyjay77\GuessNumber\View\GameView;

use function Keyjay77\GuessNumber\Model\guessNum;

use keyjay77\GuessNumber\PlayersData\PlayersData;

use function cli\prompt;
use function cli\line;
use function cli\input;

function startGame()
{
    $model = new GameModel("gamedb.db");
    $view = new GameView();
    while (true) {
        $view->showGame();
        $view->menuInterface();
        $mode_input = input();
        switch ($mode_input) {
            case 1:
                guessNum($model, $view);
                break;
            case 2:
                $games = $model->getGame();
                count($games) ? $view->showGames($games) : print("There are not saved games yet!");
                break;
            case 3:
                $games = $model->getGame("WIN");
                count($games) ? $view->showGames($games, "WIN") : print("There are not won games!");
                break;
            case 4:
                $games = $model->getGame("WIN");
                count($games) ? $view->showGames($games, "LOSE") : print("There are not lost games!");
                break;
            case 5:
                $players = $model->getTopPlayers();
                count($players) ? $view->showTopPlayers($players) : print("There are not top players in the ladder!");
                break;

            case 6:
                $gameid = prompt("Enter the game's id");
                $replay = $model->getReplayOfGame($gameid);
                $view->showReplayOfGame($gameid, $replay);
                break;
            case 0:
                exit();
            default:
                line("Invalid input");
        }
        input();
        print("\033[2J\033[;H");
    }
}
