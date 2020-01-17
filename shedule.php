<?php

// manage scheduled time table for calculations
class Schedules {
    
    public function __construct(array $schedules){
        $this->schedules = $schedules;
        $this->repeatingschedules = array_merge($schedules, $schedules); // handle overflow
    }
    
	// get the resulting datetime after several hours elapsed
    public function getEnd(int $hours, $now = null) : DateTime
	{
        $now = $now ?? new DateTime;
        $schedulehourstotal = array_sum(array_column($this->schedules, 2));
        $weekstotal = floor($hours / $schedulehourstotal);
        $remaininghours = $hours % $schedulehourstotal;
        
        $now->modify("+ {$weekstotal} weeks"); // skip full weeks
        foreach($this->repeatingschedules as $schedule){
            if($schedule[0] === $now->format('D')){
                if($remaininghours > $schedule[2]){ // full day covered by scheduled time
                    $now->modify('+1 day');
                    $remaininghours -= $schedule[2];
                }
                else { // add rest hours on the scheduled start time
                    $now->setTime(...explode(':', $schedule[1]));
                    $now->modify("+ {$remaininghours} hours");
                    break;
                }
            }
        }
        
        return $now;
    }
    
}



$schedules = [
    ['Mon', '07:01', 8],
    ['Tue', '07:02', 8],
    ['Wed', '07:03', 8],
    ['Thu', '07:04', 8],
    ['Fri', '07:05', 8],
    ['Sat', '08:06', 4],
    ['Sun', '00:07', 0],
];


$S = new Schedules($schedules);

var_dump($S->getEnd(64, new DateTime('2020-01-25 11:06:00')));

var_dump($S->getEnd(33, new DateTime('2020-02-26 11:06:00')));

var_dump($S->getEnd(55));