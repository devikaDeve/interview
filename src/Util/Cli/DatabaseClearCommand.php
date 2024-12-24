<?php

namespace Skaleet\Interview\Util\Cli;

use Skaleet\Interview\TransactionProcessing\Infrastructure\PersistentDatabase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseClearCommand extends Command
{
    const NAME = "database:clear";

    public function __construct(
            private PersistentDatabase $database,
    )
    {
        parent::__construct(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->database->clear();
        return 0;
    }

}
