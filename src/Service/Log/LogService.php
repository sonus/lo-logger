<?php

namespace App\Service\Log;

use App\Entity\Log;
use App\Repository\LogRepository;

class LogService
{
    public function __construct(private LogRepository $logRepository,)
    {
    }

    /**
     * @param array $logs
     * @return void
     */
    public function saveLog(array $logs)
    {
        foreach ($logs as $log) {
            $category = new Log();
            $category->setServiceName($log->serverName)
                ->setStartDate($this->getCurrentDate($log->stamp))
                ->setEndDate($this->getCurrentDate($log->stamp))
                ->setStatusCode($log->status);
            $this->logRepository->persist($category);
        }
        $this->logRepository->flush();
    }


    private function getCurrentDate($time): \DateTimeInterface
    {
        return new \DateTimeImmutable(
            "@" . $time,
            new \DateTimeZone("UTC")
        );
    }

}