<?php

use DI\Container;
use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Account;
use Skaleet\Interview\TransactionProcessing\Domain\Model\AccountingEntry;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Amount;
use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;
use Skaleet\Interview\TransactionProcessing\Infrastructure\ExistingAccounts;
use Skaleet\Interview\TransactionProcessing\Infrastructure\PersistentDatabase;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommandHandler;
use Skaleet\Interview\Util\Locator;

require_once __DIR__ . "/../vendor/autoload.php";

$container = Locator::container();

$container->set(PersistentDatabase::class, function () {
    return new \Skaleet\Interview\TransactionProcessing\Infrastructure\PersistentDatabase(
            [
                    new Account(ExistingAccounts::BANK_EUR, new Amount(-2150_00, "EUR")),
                    new Account(ExistingAccounts::CLIENT_EUR, new Amount(150_00, "EUR")),
                    new Account(ExistingAccounts::MERCHANT_EUR, new Amount(2000_00, "EUR")),
                    new Account(ExistingAccounts::BANK_USD, new Amount(-1825_00, "USD")),
                    new Account(ExistingAccounts::CLIENT_USD, new Amount(75_00, "USD")),
                    new Account(ExistingAccounts::MERCHANT_USD, new Amount(1750_00, "USD")),
            ],
            [
                    new TransactionLog("abcd", DateTimeImmutable::createFromFormat("d/m/Y H:i:s", "30/01/2023 11:14:42"), [
                            new AccountingEntry(ExistingAccounts::BANK_EUR, new Amount(-150_00, "EUR"), new Amount(-150_00, "EUR")),
                            new AccountingEntry(ExistingAccounts::CLIENT_EUR, new Amount(150_00, "EUR"), new Amount(150_00, "EUR")),
                    ]),
                    new TransactionLog("efgh", DateTimeImmutable::createFromFormat("d/m/Y H:i:s", "30/01/2023 13:37:42"), [
                            new AccountingEntry(ExistingAccounts::BANK_EUR, new Amount(-2000_00, "EUR"), new Amount(-2150_00, "EUR")),
                            new AccountingEntry(ExistingAccounts::MERCHANT_EUR, new Amount(2000_00, "EUR"), new Amount(2000_00, "EUR")),
                    ]),

                    new TransactionLog("dcba", DateTimeImmutable::createFromFormat("d/m/Y H:i:s", "30/01/2023 14:13:37"), [
                            new AccountingEntry(ExistingAccounts::BANK_USD, new Amount(-75_00, "USD"), new Amount(-75_00, "USD")),
                            new AccountingEntry(ExistingAccounts::CLIENT_USD, new Amount(75_00, "USD"), new Amount(75_00, "USD")),
                    ]),
                    new TransactionLog("hgfe", DateTimeImmutable::createFromFormat("d/m/Y H:i:s", "30/01/2023 16:32:48"), [
                            new AccountingEntry(ExistingAccounts::BANK_USD, new Amount(-1750_00, "USD"), new Amount(-1825_00, "USD")),
                            new AccountingEntry(ExistingAccounts::MERCHANT_USD, new Amount(1750_00, "USD"), new Amount(1750_00, "USD")),
                    ]),
            ],
    );
});

$container->set(AccountRegistry::class, fn(Container $container) => $container->get(PersistentDatabase::class));
$container->set(TransactionRepository::class, fn(Container $container) => $container->get(PersistentDatabase::class));


$container->set(PayByCardCommandHandler::class, function (Container $container) {
    return new PayByCardCommandHandler(
            $container->get(TransactionRepository::class),
            $container->get(AccountRegistry::class),
    );
});
