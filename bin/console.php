<?php

use Skaleet\Interview\TransactionProcessing\Infrastructure\Cli\PayByCardCli;
use Skaleet\Interview\Util\Cli\DatabaseClearCommand;
use Skaleet\Interview\Util\Cli\DatabaseDumpCommand;
use Skaleet\Interview\Util\Locator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

require_once __DIR__ . "/../src/bootstrap.php";


$app = new Application("Skaleet interview", "1.0");
$commandLoader = new ContainerCommandLoader(Locator::container(), [
        DatabaseClearCommand::NAME => DatabaseClearCommand::class,
        DatabaseDumpCommand::NAME => DatabaseDumpCommand::class,
        PayByCardCli::NAME => PayByCardCli::class,
]);
$app->setCommandLoader($commandLoader);

$app->run();
