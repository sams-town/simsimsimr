<?php
function count_ews_score($aware, $blood_pressure, $pulse_frequency, $breath_frequency, $temperature, $oxygen_saturation, $oxygen) {
    $aware_score = 0;
    if ($aware != 0) {
        $aware_score = 3;
    }

    // echo "aware = " . $aware_score;
    // echo "\n";

    $blood_pressure_score = 0;
    if ($blood_pressure > 210 || $blood_pressure < 81) {
        $blood_pressure_score = 3;
    } elseif ($blood_pressure > 80 && $blood_pressure < 91) {
        $blood_pressure_score = 2;
    } elseif ($blood_pressure > 90 && $blood_pressure < 101) {
        $blood_pressure_score = 1;
    }

    // echo "blood_pressure = " . $blood_pressure_score;
    // echo "\n";

    $pulse_score = 0;
    if ($pulse_frequency > 120 || $pulse_frequency < 40) {
        $pulse_score = 3;
    } elseif ($pulse_frequency > 100 && $pulse_frequency < 121) {
        $pulse_score = 2;
    } elseif ($pulse_frequency > 80 && $pulse_frequency < 101) {
        $pulse_score = 1;
    }

    // echo "pulse = " . $pulse_score;
    // echo "\n";

    $breath_score = 0;
    if ($breath_frequency>= 25 || $breath_frequency<= 8) {
        $breath_score = 3;
    } elseif ($breath_frequency>= 21 && $breath_frequency<= 24) {
        $breath_score = 2;
    } elseif ($breath_frequency>= 9 && $breath_frequency<= 11) {
        $breath_score = 1;
    }

    // echo "breath = " . $breath_score;
    // echo "\n";
    $temperature_score = 0;
    if ($temperature < 35) {
        $temperature_score = 3;
    } elseif ($temperature > 38) {
        $temperature_score = 2;
    } elseif ($temperature > 37 && $temperature < 38.1) {
        $temperature_score = 1;
    }
    // echo "temperature = " . $temperature_score;
    // echo "\n";

    $oxygen_saturation_score = 0;
    if ($oxygen_saturation <= 91) {
        $oxygen_saturation_score = 3;
    } elseif ($oxygen_saturation >= 92 && $oxygen_saturation <= 93) {
        $oxygen_saturation_score = 2;
    } elseif ($oxygen_saturation >= 94 && $oxygen_saturation <= 95) {
        $oxygen_saturation_score = 1;
    }
    // echo "oxygen_saturation = " . $oxygen_saturation_score;
    // echo "\n";

    $oxygen_score = 0;
    if ($oxygen == 2) {
        $oxygen_score = 2;
    }
    // echo "oxygen = " . $oxygen_score;
    // echo "\n";

    return $aware_score + $blood_pressure_score + $pulse_score + $breath_score + $temperature_score + $oxygen_saturation_score + $oxygen_score;
}