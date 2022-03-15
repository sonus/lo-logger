<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractGatherLog extends Command
{

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = array("b", "kb", "mb", "gb", "tb");

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . " " . $units[$pow];
    }

}