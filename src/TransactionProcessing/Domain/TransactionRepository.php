<?php

namespace Skaleet\Interview\TransactionProcessing\Domain;

use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;

interface TransactionRepository
{

    public function add(TransactionLog $transaction): void;
}