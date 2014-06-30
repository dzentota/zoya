<?php

namespace Zoya\Loader\ChangeIdentity;

use Assert\Assertion;

class Probability implements ChangeIdentityInterface
{
    protected  $percents;
    const DEFAULT_PERCENTS = 10;
    
    public function setPercents($percents)
    {
        Assertion::integer($percents, 'Number of percents should be integer');
        Assertion::min(1, 'Number of percents should be more than zero');
        $this->percents = $percents;
        return $this;
    }

    public function getPercents()
    {
        return isset($this->percents)? $this->percents : self::DEFAULT_PERCENTS;
    }

    public function changeIdentity()
    {
        $rnd = rand(1, 100);
        if ($rnd <= $this->getPercents()) {
            return true;
        }
        return false;
    }
}
