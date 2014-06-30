<?php

namespace Zoya\Loader\ChangeIdentity;

use Assert\Assertion;

class Batch implements ChangeIdentityInterface
{
    protected $requestsCount;
    protected $batchSize;

    const DEFAULT_BATCH_SIZE = 10;

    public function setBatchSize($batchSize)
    {
        Assertion::integer($batchSize, 'Batch size should be integer');
        Assertion::min($batchSize, 1, 'Batch size should be more than zero');
        $this->batchSize = $batchSize;
        return $this;
    }

    public function getBatchSize()
    {
        return isset($this->batchSize)? $this->batchSize : self::DEFAULT_BATCH_SIZE;
    }

    public function changeIdentity()
    {
        $this->requestsCount++;
        if ($this->getBatchSize() == $this->requestsCount) {
            return true;
        }
        return false;
    }
}
