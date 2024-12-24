<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

use DateTimeImmutable;

class TransactionLog
{
    public string $id;
    public DateTimeImmutable $date;
    /** @var AccountingEntry[] */
    public array $accounting;

    /**
     * @param string $id
     * @param DateTimeImmutable $date
     * @param AccountingEntry[] $accounting
     */
    public function __construct(string $id, DateTimeImmutable $date, array $accounting)
    {
        $this->id = $id;
        $this->date = $date;
        $this->accounting = $accounting;
    }


}
