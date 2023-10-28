<?php

namespace keyjay77\GuessNumber\Model;

use Keyjay77\GuessNumber\View;
use keyjay77\GuessNumber\PlayersData\PlayersData;
use keyjay77\GuessNumber\Controller;
use SQLite3;
use function cli\input;
use function cli\prompt;


define("MAX_NUM", 100);
define("ATTEMPS", 10);




class GameModel
{
    private $db_path;
    private $max_number;
    private $attempt_number;

    public function __construct($db_path)
    {
        $this->db_path = $db_path;
        $this->createTables();
        $this->setSettings(MAX_NUM, ATTEMPS);
        $settings = $this->getSettings();
        $this->max_number = $settings["maxNumber"];
        $this->attempt_number = $settings["attemptNumber"];
    }

    public function createTables()
    {
        if (!file_exists($this->db_path)) {
            $db = new SQLite3($this->db_path);
            $game_data_table = "CREATE TABLE GamesData(
                idGame INTEGER PRIMARY KEY,
                dateGame DATETIME,
                player TEXT,
                maxNumber INTEGER,
                guessingNumber INTEGER,
                gameResult TEXT)";

            $setting_table = "CREATE TABLE GameSettings(
                maxNumber INTEGER,
                attemptNumber INTEGER
                )";

            $replay_table = "CREATE TABLE Replays(
                idGame INTEGER,
                attemptNumber INTEGER,
                inputNumber INTEGER,
                replay TEXT)";

            $db->exec($game_data_table);
            $db->exec($setting_table);
            $db->exec($replay_table);
            $db->close();
        }
    }

    public function getSettings()
    {
        $db = new SQLite3($this->db_path);
        $query = "SELECT * FROM GameSettings";
        $res = $db->query($query);
        $settings = $res->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $settings;
    }
    private function setSettings($max_number, $attempt_number)
    {
        $db = new SQLite3($this->db_path);
        $input_settings = "INSERT INTO GameSettings VALUES($max_number, $attempt_number)";
        $db->exec($input_settings);
        $db->close();
    }
    public function randNumGen()
    {
        return rand(1, $this->max_number);
    }

    public function getGame($gameFilter = "ALL")
    {
        $db = new SQLite3($this->db_path);
        $sql = "";
        if ($gameFilter == "ALL") {
            $sql = "SELECT * FROM GamesData";
        } elseif ($gameFilter == "WIN") {
            $sql = "SELECT * FROM GamesData WHERE gameResult = 'win'";
        } elseif ($gameFilter == "LOSE") {
            $sql = "SELECT * FROM GamesData WHERE gameResult = 'lose'";
        }
        $res = $db->query($sql);
        $games = [];
        while ($game = $res->fetchArray(SQLITE3_ASSOC)) {
            $games[] = $game;
        }
        $db->close();
        return $games;
    }

    public function getTopPlayers()
    {
        $db = new SQLite3($this->db_path);
        $sql =
            "SELECT 
            player, 
            COUNT(CASE WHEN gameResult = 'win' THEN 1 END) AS wins, 
            COUNT(CASE WHEN gameResult = 'lose' THEN 1 END) AS losses 
        FROM GamesData 
        GROUP BY player 
        ORDER BY wins DESC";
        $res = $db->query($sql);
        $players = [];
        while ($player = $res->fetchArray(SQLITE3_ASSOC)) {
            $players[] = $player;
        }
        $db->close();
        return $players;
    }

    public function getReplayOfGame($idGame)
    {
        $db = new SQLite3($this->db_path);
        $sql = "SELECT * FROM Replays WHERE idGame = " . $idGame;
        $res = $db->query($sql);
        $replays = [];
        while ($replay = $res->fetchArray(SQLITE3_ASSOC)) {
            $replays[] = $replay;
        }
        $db->close();
        return $replays;
    }

    public function saveGameIntoDB($playersdata)
    {
        $db_path = 'gamedb.db';
        $db = new SQLite3($db_path);
        $query = "INSERT INTO GamesData (dateGame, player, maxNumber, guessingNumber,gameResult) VALUES(
            '" . $playersdata->date . " ',
            '" . $playersdata->player . " ',
            '" . $playersdata->max_number . " ',
            '" . $playersdata->guessing_number . " ',
            '" . $playersdata->result . " ')";
        $db->exec($query);
        $lastId = $db->lastInsertRowId();
        for ($i = 0; $i < count($playersdata->attempt_number); $i++) {
            $query = "INSERT INTO Replays (idGame, attemptNumber, inputNumber, replay) VALUES(
                '" . $lastId . "',
                '" . $playersdata->attempt_number[$i] . "',
                '" . $playersdata->input_numbers[$i] . "',
                '" . $playersdata->game_feedback[$i] . "') ";
            $db->exec($query);
        }
        $db->close();
    }
}
function guessNum($model, $view)
{
    $user_num_attempt = 0;
    $settings = $model->getSettings();
    $attempt_number = $settings['attemptNumber'];
    $max_number = $settings['maxNumber'];
    $guessing_number = $model->randNumGen();

    $playersdata = new PlayersData();
    $player_name = prompt("Enter your name: ");
    $playersdata->player = $player_name;
    $playersdata->max_number = $max_number;
    $playersdata->guessing_number = $guessing_number;

    while (++$user_num_attempt <= $attempt_number) {
        $input_number = input();
        $playersdata->input_numbers[] = $input_number;
        $playersdata->game_feedback[] = $user_num_attempt;

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
        } elseif ($guessing_number < $input_number) {
            $view->biggerOrLesser("lesser", $user_num_attempt, $attempt_number);
            $playersdata->game_feedback[] = "less";
        } else {
            $view->biggerOrLesser("bigger", $user_num_attempt, $attempt_number);
            $playersdata->game_feedback[] = "greater";
        }
        if($user_num_attempt == $attempt_number){
            $view->showLoseMessage($guessing_number);
            $playersdata->result = "lose";
            $playersdata->game_feedback[] = "defeat";
            break;
        }
    }
    $model->saveGameIntoDB($playersdata);
}

