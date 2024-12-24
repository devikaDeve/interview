<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Exception;


class AccountDoesNotExistException extends \Exception
{
    public function __construct(string $accountNumber)
    {
        parent::__construct("Account #$accountNumber does not exist");
    }

}