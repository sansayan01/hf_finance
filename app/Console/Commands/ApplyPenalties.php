<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('loans:apply-penalties')]
#[Description('Calculate and apply penalties for overdue loan installments')]
class ApplyPenalties extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\PenaltyService $penaltyService)
    {
        $this->info('Starting penalty application...');
        $count = $penaltyService->applyOverduePenalties();
        $this->info("Successfully applied penalties to {$count} installments.");
    }
}
