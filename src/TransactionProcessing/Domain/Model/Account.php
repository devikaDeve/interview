<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

class Account
{
    public function __construct(
            public string $number,
            public Amount $balance,
    )
    {
    }

}
