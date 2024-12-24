<?php

namespace Skaleet\Interview\TransactionProcessing\UseCase;


class PayByCardCommand
{
    public function __construct(
        public string $clientAccountNumber,
        public string $merchantAccountNumber,
        public int    $amount,
        public string $currency,
    )
    {
    }

}