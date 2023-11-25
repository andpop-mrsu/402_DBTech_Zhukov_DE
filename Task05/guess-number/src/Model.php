<?php

namespace keyjay77\GuessNumber\Model;

use Keyjay77\GuessNumber\View;
use keyjay77\GuessNumber\PlayersData\PlayersData;
use keyjay77\GuessNumber\Controller;
use SQLite3;

use function cli\input;
use function cli\prompt;
use function cli\line;

use RedBeanPHP\R;

class GameModel
{
    private $db_path;
    private $max_number;
    private $attempt_numbers;

    public function __construct($db_path)
    {
        $this->db_path = $db_path;
        R::setup('sqlite:' . $this->db_path);
        $this->createTables();
        $this->setSettings(100, 10);
        $settings = $this->getSettings();
        $this->max_number = $settings['max_number'];
        $this->attempt_numbers = $settings['attempt_numbers'];
    }

    public function createTables()
    {
        if (!file_exists($this->db_path)) {
            if (!R::inspect('gamesdata')) {
                $games_data_table = R::dispense('gamesdata');
                $games_data_table->idGame = 'INTEGER PRIMARY KEY';
                $games_data_table->dateGame = 'DATETIME';
                $games_data_table->player = 'TEXT';
                $games_data_table->max_number = 'INTEGER';
                $games_data_table->guessingNumber = 'INTEGER';
                $games_data_table->gameResult = 'TEXT';
                R::store($games_data_table);
            }

            if (!R::inspect('gamesettings')) {
                $setting_table = R::dispense('gamesettings');
                R::store($setting_table);
            }

            if (!R::inspect('replays')) {
                $replay_table = R::dispense('replays');
                $replay_table->idGame = 'INTEGER';
                $replay_table->attempt_number = 'INTEGER';
                $replay_table->inputNumber = 'INTEGER';
                $replay_table->replay = 'TEXT';
            }
        }
    }
    public function getDbPath()
    {
        return $this->db_path;
    }
    public function getSettings()
    {
        $settings = R::findOne('gamesettings');
        return $settings;
    }
    public function setSettings($max_number, $attempt_numbers)
    {
        $setting = R::findOne('gamesettings');
        if (!$setting) {
            $setting = R::dispense('gamesettings');
        }
        $setting->max_number = $max_number;
        $setting->attempt_numbers = $attempt_numbers;
        R::store($setting);
    }
    public function randNumGen()
    {
        return rand(1, $this->max_number);
    }

    public function getGame($gameFilter = "ALL")
    {
        $games = [];

        if ($gameFilter == "ALL") {
            $games = R::findAll('gamesdata');
        } elseif ($gameFilter == "WIN") {
            $games = R::find('gamesdata', 'gameResult = ?', ['win']);
        } elseif ($gameFilter == "LOSE") {
            $games = R::find('gamesdata', 'gameResult = ?', ['lose']);
        }

        return $games;
    }

    public function getTopPlayers()
    {
        $sql =
            "SELECT 
            player, 
            COUNT(CASE WHEN gameResult = 'win' THEN 1 END) AS wins, 
            COUNT(CASE WHEN gameResult = 'lose' THEN 1 END) AS losses 
        FROM gamesdata 
        GROUP BY player 
        ORDER BY wins DESC";
        $players = R::getAll($sql);
        return $players;
    }

    public function getReplayOfGame($idGame)
    {
        $replays = R::findAll('replays', 'idGame = ?', [$idGame]);
        return $replays;
    }

    public function saveGameIntoDB($playersdata)
    {
        $gamesData = R::dispense('gamesdata');
        $gamesData->dateGame = $playersdata->date;
        $gamesData->player = $playersdata->player;
        $gamesData->maxNumber = $playersdata->max_number;
        $gamesData->guessingNumber = $gamesData->guessing_number;
        $gamesData->gameResult = $playersdata->result;
        R::store($gamesData);

        $lastId = $gamesData->idGame;
        for ($i = 0; $i < count($playersdata->input_numbers); $i++) {
            $replay = R::dispense('replays');
            $replay->idGame = $lastId;
            $replay->attemptNumber = $playersdata->input_numbers[$i];
            $replay->replay = $playersdata->game_feedback[$i];
            R::store($replay);
        }
    }
}
function guessNum($model, $view)
{
    $user_num_attempt = 0;
    $settings = $model->getSettings();
    $attempt_number = $settings['attempt_numbers'];
    $max_number = $settings['max_number'];
    $guessing_number = $model->randNumGen();

    $playersdata = new PlayersData();
    $player_name = prompt("Enter your name: ");
    $playersdata->player = $player_name;
    $playersdata->max_number = $max_number;
    $playersdata->guessing_number = $guessing_number;
    line('Enter a number');
    while (++$user_num_attempt <= $attempt_number) {
        $input_number = input();
        $playersdata->input_numbers[] = $input_number;
        $playersdata->attempt_number[] = $user_num_attempt;


        if (filter_var($input_number, FILTER_VALIDATE_INT) === false) {
            $view->NaN();
            $playersdata->game_feedback[] = "NaN";
            if ($user_num_attempt == $attempt_number) {
                $view->showLoseMessage($guessing_number);
                $playersdata->result = "lose";
            }
            continue;
        }
        if ($input_number > $max_number) {
            $view->outOfRange();
            $playersdata->game_feedback[] = "OffRange";
            if ($user_num_attempt == $attempt_number) {
                $view->showLoseMessage($guessing_number);
                $playersdata->result = "lose";
            }
            continue;
        }
        if ($input_number == $guessing_number) {
            $view->showWinMessage($guessing_number);
            $playersdata->result = "win";
            $playersdata->game_feedback[] = "guessed";
            break;
        } elseif ($input_number == 0) {
            $view->exitGame();
            $playersdata->result = "left";
            $playersdata->game_feedback[] = "left";
            break;
        } elseif ($guessing_number > $input_number) {
            $view->biggerOrLesser("lesser", $user_num_attempt, $attempt_number);
            $playersdata->game_feedback[] = "less";
        } else {
            $view->biggerOrLesser("bigger", $user_num_attempt, $attempt_number);
            $playersdata->game_feedback[] = "greater";
        }
        if ($user_num_attempt == $attempt_number) {
            $view->showLoseMessage($guessing_number);
            $playersdata->result = "lose";
            $playersdata->game_feedback[] = "defeat";
            break;
        }
    }
    $model->saveGameIntoDB($playersdata);
}
