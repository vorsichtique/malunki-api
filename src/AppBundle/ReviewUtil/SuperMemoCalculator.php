<?php

namespace AppBundle\ReviewUtil;


/**
 * Class SuperMemoCalculator
 * @package AppBundle\ReviewUtil
 *
 * See https://www.supermemo.com/english/ol/sm2.htm

        1. Split the knowledge into smallest possible items.
        2. With all items associate an E-Factor equal to 2.5.
        3. Repeat items using the following intervals:
            I(1):=1
            I(2):=6
            for n>2: I(n):=I(n-1)*EF
            where:
                I(n) - inter-repetition interval after the n-th repetition (in days),
                EF - E-Factor of a given item
            If interval is a fraction, round it up to the nearest integer.
        4. After each repetition assess the quality of repetition response in 0-5 grade scale:
            5 - perfect response
            4 - correct response after a hesitation
            3 - correct response recalled with serious difficulty
            2 - incorrect response; where the correct one seemed easy to recall
            1 - incorrect response; the correct one remembered
            0 - complete blackout.
        5. After each repetition modify the E-Factor of the recently repeated item according to the formula:
            EF':=EF+(0.1-(5-q)*(0.08+(5-q)*0.02))
            where:
                EF' - new value of the E-Factor,
                EF - old value of the E-Factor,
                q - quality of the response in the 0-5 grade scale.
            If EF is less than 1.3 then let EF be 1.3.
        6. If the quality response was lower than 3 then start repetitions for the item from the beginning without changing the E-Factor (i.e. use intervals I(1), I(2) etc. as if the item was memorized anew).
        7. After each repetition session of a given day repeat again all items that scored below four in the quality assessment. Continue the repetitions until all of these items score at least four.
 */
class SuperMemoCalculator
{

    public function calcInterval($repetition = 1, $eFactor = 2.5, $oldInterval = 1)
    {
        if ($repetition < 1) {
            throw new \Exception('The number of repetitions must be 1 or higher');
        }
        if ($repetition == 1) {
            $interval = 1;
        } elseif ($repetition == 2) {
            $interval = 6;
        } else {
            $interval = $oldInterval * $eFactor;
        }
        return ceil($interval);
    }

    public function calcNewEFactor($oldEFactor = 2.5, $quality = 4)
    {
        if ($quality > 5 || $quality < 0) {
            throw new \Exception('Quality must be between 0 and 5');
        }
        $newEFactor = $oldEFactor + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        return max($newEFactor, 1.3);
    }
}
