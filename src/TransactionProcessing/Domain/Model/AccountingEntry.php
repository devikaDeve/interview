<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

class AccountingEntry
{
    public function __construct(
            public string $accountNumber,
            public Amount $amount,
            public Amount $newBalance,
    )
    {
    }


}
