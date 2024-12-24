<?php

namespace Skaleet\Interview\TransactionProcessing\Infrastructure\Cli;

use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommand;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommandHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PayByCardCli extends Command
{
    public const NAME = "pay";

    public function __construct(
            private PayByCardCommandHandler $handler,
    )
    {

        parent::__construct(static::NAME);
    }

    protected function configure()
    {
        $this->addArgument("clientAccountNumber", InputArgument::REQUIRED, "Client account number");
        $this->addArgument("amount", InputArgument::REQUIRED, "Amount");
        $this->addArgument("currency", InputArgument::REQUIRED, "Currency");
        $this->addArgument("merchantAccountNumber", InputArgument::REQUIRED, "Merchant account number");
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->handler->handle(new PayByCardCommand(
                clientAccountNumber: $input->getArgument("clientAccountNumber"),
                merchantAccountNumber: $input->getArgument("merchantAccountNumber"),
                amount: $input->getArgument("amount"),
                currency: $input->getArgument("currency"),
        ));
        return 0;
    }


}
