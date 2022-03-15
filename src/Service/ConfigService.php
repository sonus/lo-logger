<?php

namespace App\Service;

use App\Entity\Config;
use App\Repository\ConfigRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ConfigRepository       $configRepository,
    )
    {
    }

    public function saveConfig(string $name, int $processed): void
    {
        $config = $this->getConfig($name);
        $config->setProcessed($processed);
        $this->configRepository->persist($config);
        $this->configRepository->flush();
    }

    public function getConfig(string $name): Config
    {
        $config = $this->configRepository
            ->findConfigByName($name);
        if (is_null($config)) {
            $config = new Config();
            $config->setName($name)
                ->setProcessed(0);
        }

        return $config;
    }

}