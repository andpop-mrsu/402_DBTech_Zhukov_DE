<?php

namespace keyjay77\GuessNumber\PlayersData;

class PlayersData
{
    public $date;
    public $player;
    public $max_number;
    public $guessing_number;
    public $result;
    public $attempt_number;
    public $input_numbers; 
    public $game_feedback;   
    
    
    

    public function __construct()
    {
        $this->date = date("Y-m-d H:i:s");
        $this->attempt_number = array();
        $this->input_numbers = array();
        $this->game_feedback = array();
    }    
}