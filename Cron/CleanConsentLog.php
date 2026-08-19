<?php
declare(strict_types=1);

namespace MiniOrange\PDProtect\Cron;

use MiniOrange\PDProtect\Model\ConsentLogCleaner;

class CleanConsentLog
{
    private readonly ConsentLogCleaner $cleaner;

    public function __construct(
        ConsentLogCleaner $cleaner
    ) {
        $this->cleaner = $cleaner;
    }

    public function execute(): void
    {
        $this->cleaner->execute();
    }
}
