<?php

namespace App\Service\GatherLog;

use Kassner\LogParser\FormatException;

class GatherLog extends AbstractGatherLogger
{


    public function gatherLogIfExists(): int
    {
        $config = $this->configService->getConfig('service_logs');
        $processed = $config->getProcessed();
        $iterator = $this->readTheFile($processed);
        $line = 0;
        $logs = [];
        foreach ($iterator as $iteration) {
            print ".";
            $line++;
            try {
                $logs[] = $this->logParser->parse($iteration . PHP_EOL);
            } catch (FormatException $e) {
                // echo 'Invalid Service Log ', $e->getMessage(), " at : \n", "\n\n", $iteration . PHP_EOL, "\n\n";
            }
        }
        $this->logService->saveLog($logs);
        $this->configService->saveConfig('service_logs', $processed + $line);
        return $line;
    }
}