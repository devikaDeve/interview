<?php

namespace Skaleet\Interview\Util\Cli;

use Skaleet\Interview\TransactionProcessing\Domain\Model\Amount;
use Skaleet\Interview\TransactionProcessing\Infrastructure\PersistentDatabase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DatabaseDumpCommand extends Command
{
    const NAME = "database:dump";

    public function __construct(
            private PersistentDatabase $database,
    )
    {
        parent::__construct(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->displayAccounts($io, $this->database->getAccounts());
        $this->displayTransactions($io, $this->database->getTransactions());
        return 0;
    }


    private function displayTransactions(SymfonyStyle $io, array $transactions): void
    {
        $io->title("Transactions");
        $rows = [];
        foreach ($transactions as $transaction) {
            $accounting = "";
            foreach ($transaction->accounting as $entry) {
                $accounting .= "#$entry->accountNumber : {$this->formatAmount($entry->amount)} (balance: {$this->formatAmount($entry->newBalance)})\n";
            }

            $rows[] = [$transaction->id, $transaction->date->format("d/m H:i:s"), $accounting];

        }
        $io->table(["Id", "Date", "Accounting"], $rows);
    }


    private function displayAccounts(SymfonyStyle $io, array $accounts): void
    {
        $io->title("Accounts");
        $rows = [];
        foreach ($accounts as $account) {
            $rows[] = ["#$account->number", $this->formatAmount($account->balance)];
        }
        $io->table(["Number", "Balance"], $rows);
    }

    private function formatAmount(Amount $amount): string
    {
        $value = number_format($amount->value / 100, 2, ".", " ");
        if ($amount->value >= 0) {
            $value = "+$value";
        }
        return "$value $amount->currency";
    }


}
