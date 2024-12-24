<?php

namespace Skaleet\Interview\TransactionProcessing\Domain;

use Skaleet\Interview\TransactionProcessing\Domain\Model\Account;

interface AccountRegistry
{

    public function loadByNumber($accountNumber): ?Account;
    
}