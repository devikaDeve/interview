<?php

namespace Skaleet\Interview\TransactionProcessing\UseCase;

use DateTimeImmutable;
use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;
use Skaleet\Interview\TransactionProcessing\Domain\Exception\AccountDoesNotExistException;
use Skaleet\Interview\TransactionProcessing\Domain\Model\AccountingEntry;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Amount;
use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;

class PayByCardCommandHandler
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AccountRegistry       $accountRegistry,
    )
    {
    }


    public function handle(PayByCardCommand $command): void
    {
        // 1. Validate Amount
        if ($command->amount <= 0) {
            throw new \InvalidArgumentException("Amount must be strictly positive.");
        }

        // 2. Load Accounts
        $clientAccount = $this->accountRegistry->loadByNumber($command->clientAccountNumber);
        if (!$clientAccount) {
            throw new AccountDoesNotExistException($command->clientAccountNumber);
        }

        $merchantAccount = $this->accountRegistry->loadByNumber($command->merchantAccountNumber);
        if (!$merchantAccount) {
            throw new AccountDoesNotExistException($command->merchantAccountNumber);
        }

        // 3. Validate Currency
        if ($clientAccount->balance->currency !== $merchantAccount->balance->currency ||
            $clientAccount->balance->currency !== $command->currency) {
            throw new \InvalidArgumentException("Currencies must match.");
        }

        // 4. Check for Sufficient Funds
        if ($clientAccount->balance->value < $command->amount) {
            throw new \DomainException("Insufficient funds in the client account.");
        }

        // 5. Calculate Fee
        $feePercentage = 0.02;
        $maxFee = new Amount(300, $command->currency); // €3.00 in cents
        $feeAmount = new Amount(min($command->amount * $feePercentage, $maxFee->value), $command->currency); 

        // 6. Calculate New Balances
        $transactionAmount = new Amount($command->amount, $command->currency);
        $clientNewBalance = new Amount($clientAccount->balance->value - $command->amount, $command->currency);
        $merchantNewBalance = new Amount($merchantAccount->balance->value + $command->amount - $feeAmount->value, $command->currency);
        $bankNewBalance = new Amount($merchantAccount->balance->value + $feeAmount->value, $command->currency);

        // 7. Validate Merchant Balance Limits
        if ($merchantNewBalance->value > 300000) { // €3,000 in cents
            throw new \DomainException("Merchant's balance cannot exceed €3,000.");
        }
        if ($merchantNewBalance->value < -100000) { // -€1,000 in cents
            throw new \DomainException("Merchant's balance cannot be less than -€1,000.");
        }

        // 8. Create Accounting Entries
        $accountingEntries = [
            new AccountingEntry($clientAccount->number, $transactionAmount, $clientNewBalance),
            new AccountingEntry($merchantAccount->number, new Amount(-$command->amount, $command->currency), $merchantNewBalance),
            new AccountingEntry($command->bankAccountNumber, new Amount($feeAmount->value, $command->currency), $bankNewBalance), 
        ];

        // 9. Create Transaction Log
        $transactionLog = new TransactionLog(
            uniqid(),
            new DateTimeImmutable(),
            $accountingEntries
        );

        // 10. Persist Transaction
        $this->transactionRepository->add($transactionLog);
    }
}
