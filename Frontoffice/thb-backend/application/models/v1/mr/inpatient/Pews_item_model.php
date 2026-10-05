<?php class Pews_item_model extends MY_Model {
	protected $tableName = 'pews_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.observation_time' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();

    public function count_pews($birth_date, $breathing, $oxygen, $oxygen_saturation, $pulse, $skin_color, $systolic, $behavior) {
        $birth = new DateTime($birth_date);
        $now = new DateTime(date('Y-m-d'));
        
        $interval = $birth->diff($now);
        
        $year = $interval->y;
        $month = $interval->m;
        $day = $interval->d;

        // var_dump($birth_date);
        // var_dump($breathing);
        // var_dump($oxygen);
        // var_dump($oxygen_saturation);
        // var_dump($pulse);
        // var_dump($skin_color);
        // var_dump($systolic);
        // var_dump($behavior);
        // die();

        $breathing_score = null;
        $oxygen_score = null;
        $oxygen_saturation_score = null;
        $pulse_score = null;
        $skin_color_score = null;
        $systolic_score = null;
        $behavior_score = null;
        
        if ($year == 0 || ($year == 1 && $day == 0)) {
            if ($month < 3 || ($month == 3 && $day == 0)) {
                // "0 - 3 month";

                if ($breathing <= 20 || $breathing >= 60) {
                    $breathing_score = 3;
                } elseif ($breathing > 20 && $breathing < 30) {
                    $breathing_score = 2;
                } elseif ($breathing >= 30 && $breathing < 40){
                    $breathing_score = 1;
                } elseif ($breathing >= 40 && $breathing < 60) {
                    $breathing_score = 0;
                }

                if ($pulse <= 80 || $pulse >= 200) {
                    $pulse_score = 3;
                } elseif (($pulse > 80 && $pulse < 91) || ($pulse > 190 && $pulse < 200)) {
                    $pulse_score = 2;
                } elseif (($pulse > 90 && $pulse < 100) || ($pulse > 180 && $pulse < 191)) {
                    $pulse_score = 1;
                } elseif ($pulse >= 100 && $pulse <= 180) {
                    $pulse_score = 0;
                }

            } else {
                // "4 - 12 month";

                if ($breathing <= 20 || $breathing >= 60) {
                    $breathing_score = 3;
                } elseif ($breathing > 50 && $breathing < 60) {
                    $breathing_score = 2;
                } elseif (($breathing > 20 && $breathing < 30) || ($breathing > 40 && $breathing < 51)){
                    $breathing_score = 1;
                } elseif ($breathing >= 30 && $breathing <= 40) {
                    $breathing_score = 0;
                }

                if ($pulse <= 90 || $pulse >= 200) {
                    $pulse_score = 3;
                } elseif (($pulse > 80 && $pulse < 91) || ($pulse > 190 && $pulse < 200)) {
                    $pulse_score = 2;
                } elseif (($pulse > 90 && $pulse < 100) || ($pulse > 180 && $pulse < 191)) {
                    $pulse_score = 1;
                } elseif ($pulse >= 100 && $pulse <= 180) {
                    $pulse_score = 0;
                }

            }
        } elseif ($year < 4 || ($year == 4 && $day == 0)) {
            // "1 - 4 year";
            
            if ($breathing <= 20 || $breathing >= 60) {
                $breathing_score = 3;
            } elseif ($breathing > 50 && $breathing < 60) {
                $breathing_score = 2;
            } elseif ($breathing > 30 && $breathing < 51){
                $breathing_score = 1;
            } elseif ($breathing > 20 && $breathing < 31) {
                $breathing_score = 0;
            }

            if ($pulse <= 70 || $pulse >= 170) {
                $pulse_score = 3;
            } elseif (($pulse > 70 && $pulse < 80) || ($pulse > 160 && $pulse < 170)) {
                $pulse_score = 2;
            } elseif (($pulse > 80 && $pulse < 90) || ($pulse > 140 && $pulse < 161)) {
                $pulse_score = 1;
            } elseif ($pulse >= 90 && $pulse <= 140) {
                $pulse_score = 0;
            }

        } elseif ($year < 12 || ($year == 12 && $day == 0)) {
            // "5 - 12 year";

            if ($breathing <= 20 || $breathing >= 60) {
                $breathing_score = 3;
            } elseif ($breathing > 50 && $breathing < 60) {
                $breathing_score = 2;
            } elseif ($breathing > 30 && $breathing < 51){
                $breathing_score = 1;
            } elseif ($breathing > 20 && $breathing < 31) {
                $breathing_score = 0;
            }

            if ($pulse <= 60 || $pulse >= 160) {
                $pulse_score = 3;
            } elseif (($pulse > 60 && $pulse < 70) || ($pulse > 140 && $pulse < 160)) {
                $pulse_score = 2;
            } elseif (($pulse > 69 && $pulse < 80) || ($pulse > 120 && $pulse < 141)) {
                $pulse_score = 1;
            } elseif ($pulse >= 80 && $pulse <= 120) {
                $pulse_score = 0;
            }
        } else {
            // "> 12 year";

            if ($breathing <= 10 || $breathing >= 40) {
                $breathing_score = 3;
            } elseif ($breathing > 30 && $breathing < 40) {
                $breathing_score = 2;
            } elseif ($breathing > 20 && $breathing < 31){
                $breathing_score = 1;
            } elseif ($breathing > 10 && $breathing < 21) {
                $breathing_score = 0;
            }

            if ($pulse <= 60 || $pulse >= 140) {
                $pulse_score = 3;
            } elseif ($pulse > 120 && $pulse < 140) {
                $pulse_score = 2;
            } elseif (($pulse > 100 && $pulse < 121)) {
                $pulse_score = 1;
            } elseif ($pulse >= 55 && $pulse <= 100) {
                $pulse_score = 0;
            }
        }

        if ($oxygen == 2) {
            $oxygen_score = 0;
        } else {
            $oxygen_score = 2;
        }

        if ($oxygen_saturation <= 85) {
            $oxygen_saturation_score = 3;
        } elseif ($oxygen_saturation > 85 && $oxygen_saturation < 90) {
            $oxygen_saturation_score = 2;
        } elseif ($oxygen_saturation > 89 && $oxygen_saturation < 94) {
            $oxygen_saturation_score = 1;
        } elseif ($oxygen_saturation >= 94) {
            $oxygen_saturation_score = 0;
        }

        if ($skin_color == 1) {
            $skin_color_score = 0;
        } elseif ($skin_color == 2) {
            $skin_color_score = 3;
        }

        if ($systolic <= 80 || $systolic >= 140) {
            $systolic_score = 3;
        } elseif ($systolic > 129 && $systolic < 140) {
            $systolic_score = 2;
        } elseif (($systolic > 80 && $systolic < 90) || ($systolic > 119 && $systolic < 130)) {
            $systolic_score = 1;
        } elseif ($systolic >= 90 && $systolic <= 119) {
            $systolic_score = 0;
        }

        if ($behavior == 1) {
            $behavior_score = 0;
        } elseif ($behavior == 2) {
            $behavior_score = 1;
        } elseif ($behavior == 3) {
            $behavior_score = 2;
        } else {
            $behavior_score = 3;
        }

        // var_dump($breathing_score);
        // var_dump($oxygen_score);
        // var_dump($oxygen_saturation_score);
        // var_dump($pulse_score);
        // var_dump($skin_color_score);
        // var_dump($systolic_score);
        // var_dump($behavior_score);
        // die();
        
        if ($breathing_score === null || $oxygen_score === null || $oxygen_saturation_score === null || $pulse_score === null || $skin_color_score === null || $systolic_score === null || $behavior_score === null) {
            return null;
        } else {
            $total = $breathing_score + $oxygen_score + $oxygen_saturation_score + $pulse_score + $skin_color_score + $systolic_score + $behavior_score;
            return $total;
        }
    }
}?>