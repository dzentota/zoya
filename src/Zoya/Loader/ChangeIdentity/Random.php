<?php

namespace Zoya\Loader\ChangeIdentity;

class Random
{

    public function changeIdentity()
    {
        $probability = rand(1, 100);
        $rnd = rand(1, 100);
        if ($rnd <= $probability) {
            return true;
        }
        return false;
    }

}
