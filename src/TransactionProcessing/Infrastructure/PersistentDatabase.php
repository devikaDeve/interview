<?php

namespace Skaleet\Interview\TransactionProcessing\Infrastructure;

use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;

class PersistentDatabase extends InMemoryDatabase
{
    private const STORAGE_PATH = __DIR__ . "/../../../db";

    public function __construct(array $accounts = [], array $transactions = [])
    {
        parent::__construct($accounts, $transactions);

        if (!file_exists(self::STORAGE_PATH)) {
            file_put_contents(self::STORAGE_PATH, serialize($this));
        } else {
            /** @var static $previousState */
            $previousState = unserialize(file_get_contents(self::STORAGE_PATH));
            $this->accounts = $previousState->accounts;
            $this->transactions = $previousState->transactions;
        }
    }

    public function add(TransactionLog $transaction): void
    {
        parent::add($transaction);
        file_put_contents(self::STORAGE_PATH, serialize($this));
    }

    public function clear(): void
    {
        unlink(self::STORAGE_PATH);
    }

}
