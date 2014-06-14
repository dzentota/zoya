<?php

namespace Zoya\Loader\ChangeIdentity;

class Random extends Generic
{

    public function changeIdentity()
    {
        $probability = rand(1, 100);
        $rnd = rand(1, 100);
        if ($rnd <= $probability) {
            $this->proxies->next();
        }
    }

}
