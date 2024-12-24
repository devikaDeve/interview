<?php

namespace Skaleet\Interview\Tests\TransactionProcessing\UseCase;

use PHPUnit\Framework\TestCase;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommand;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommandHandler;
use Throwable;

class PayByCardCommandHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $transactionRepository = null;
        $accountRegistry = null;
        $this->handler = new PayByCardCommandHandler($transactionRepository, $accountRegistry);
    }

    /**
     * @throws Throwable
     */
    public function test_example(): void
    {
        $this->handler->handle(new PayByCardCommand(
            clientAccountNumber: "dummyClientAccountEUR",
            merchantAccountNumber: "dummyMerchantAccountEUR",
            amount: 10,
            currency: "EUR"
        ));
    }


}
