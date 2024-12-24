<?php

namespace Skaleet\Interview\TransactionProcessing\Infrastructure;

use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\Exception\AccountDoesNotExistException;
use Skaleet\Interview\TransactionProcessing\Domain\Model;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Account;
use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;

class InMemoryDatabase implements TransactionRepository, AccountRegistry
{

    /** @var Model\TransactionLog[] */
    protected array $transactions = [];
    /** @var Account[] */
    protected array $accounts = [];

    /**
     * @param TransactionLog[] $transactions
     * @param Account[] $accounts
     */
    public function __construct(array $accounts = [], array $transactions = [])
    {
        foreach ($accounts as $account) {
            $this->accounts[$account->number] = clone $account;
        }
        foreach ($transactions as $transaction) {
            $this->transactions[$transaction->id] = clone $transaction;
        }
    }


    /**
     * @throws AccountDoesNotExistException
     */
    public function add(TransactionLog $transaction): void
    {
        foreach ($transaction->accounting as $entry) {
            $account = $this->accounts[$entry->accountNumber] ?? null;
            if (!$account) {
                throw new AccountDoesNotExistException($entry->accountNumber);
            }
            $account->balance = $entry->newBalance;
        }
        $this->transactions[$transaction->id] = clone $transaction;
    }

    public function loadByNumber($accountNumber): ?Account
    {
        $account = $this->accounts[$accountNumber] ?? null;
        return $account ? clone $account : null;
    }

    /**
     * @return array
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @return array
     */
    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function loadByTransactionId(string $transactionId): ?TransactionLog
    {
        $transaction = $this->transactions[$transactionId] ?? null;
        return $transaction ? clone $transaction : null;
    }


}
