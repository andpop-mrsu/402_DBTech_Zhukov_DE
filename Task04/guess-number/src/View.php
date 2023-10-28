<?php

namespace keyjay77\GuessNumber\View;

use function cli\line;

class GameView
{
    public function showGame()
    {
        line("This is a guess-number game! In this game you enter a number from 1 to " . MAX_NUM);

    }
    public function exitGame()
    {
        line("See you soon!");
    }

    public function outOfRange()
    {
        line("The number is out of range, guess a little bit less number.");
    }
    public function showWinMessage($user_num_attempt)
    {
        line("You win!");
    }

    public function showLoseMessage($guessing_num)
    {
        line("You lose! The number was " . $guessing_num);
    }

    public function biggerOrLesser($message, $user_num_attempt, $attempt_number)
    {
        if ($user_num_attempt != $attempt_number) {
            line("This number is " . $message . " than guessing number!");
            line($attempt_number - $user_num_attempt . " left.");
        }
    }
    public function showGames($games, $mode = "ALL")
    {
        print("\033[2J\033[;H");
        if ($mode == "ALL") {
            line("List of all games:");
        } elseif ($mode == "WIN") {
            line("List of games won by players");
        } elseif ($mode == "LOSE") {
            line("List of games lost by players");
        }
        line("|  id |                 dateGame | player | maxNumber | guessingNumber | gameResult |");
        foreach ($games as $game) {
            printf(
                "| %3s | %20s | %6s | %10s | %13s | %6s |\n",
                $game['idGame'],
                $game['dateGame'],
                $game['player'],
                $game['maxNumber'],
                $game['guessingNumber'],
                $game['gameResult']
            );
        }
        line("Press ENTER to return");
    }
    public function showTopPlayers($players)
    {
        print("\033[2J\033[;H");
        line("List of top players");
        line("|   name | wins | losses |");
        foreach ($players as $player) {
            printf("| %6s | %4s | %6s |\n", $player['player'], $player['wins'], $player['losses']);
        }
        line("Press ENTER to return");
    }
    public function NaN()
    {
        line("I don't guess any kind of symbols beside numbers, try again.");
    }
    public function menuInterface()
    {
        line("Choose an option:
        \t1. Start a new game
        \t2. Show saved games
        \t3. Show a list of all the games won by the players
        \t4. Show a list of all the games lost by the players
        \t5. Show top players of the ladder
        \t6. Replay any previous saved game
        \t0. Exit");
    }
    public function showReplayOfGame($gameId, $replays)
    {
        print("\033[2J\033[;H");
        line("Replay of game with id $gameId");
        line("| attempt | entered number |   reply |");
        foreach ($replays as $replay) {
            printf("| %7s | %14s | %7s |\n", $replay['attemptNumber'], $replay['inputNumber'], $replay['replay']);
        }
        line("Press ENTER to return");
    }
}
