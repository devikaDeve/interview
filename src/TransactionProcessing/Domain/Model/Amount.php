<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

class Amount
{
    public function __construct(
            public int    $value,
            public string $currency,
    )
    {
    }


}
