<?php

namespace App\Command;

use App\Service\GatherLog\GatherLog;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:gather-log',
    description: 'Add a short description for your command',
)]
class GatherLogCommand extends AbstractGatherLog
{
    public function __construct(
        private GatherLog $gatherLog,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Debug option provides information on consumed memory for the process.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $gatheredLogs = $this->gatherLog
            ->gatherLogIfExists();

        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('debug')) {
            $io->caution("Memory Consumed : " . $this->formatBytes(memory_get_peak_usage()));
        }
        if ($gatheredLogs) {
            $io->success(sprintf("Successfully gathered %s of Log(s).", $gatheredLogs));
        } else {
            $io->info("No new logs has been gathered.");
        }

        return Command::SUCCESS;
    }
}
