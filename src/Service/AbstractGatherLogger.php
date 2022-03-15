<?php

namespace App\Service;

use Generator;
use Kassner\LogParser\LogParser;
use SplFileObject;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

abstract class AbstractGatherLogger
{
    public LogParser $logParser;
    public SplFileObject $splFileObject;
    private Package $package;

    public function __construct(
        protected ConfigService $configService,
        protected LogImporterService $logImporterService,
        private string $logPath,
        private string $logFormat)
    {
        $this->logParser = new LogParser($this->logFormat);
        $this->package = new Package(new EmptyVersionStrategy());
        $this->splFileObject = new SplFileObject($this->package->getUrl($this->logPath));
    }


    /**
     * @param int $skip
     * @return Generator
     */
    protected function readTheFile(int $skip = 0): Generator
    {
        $this->splFileObject->seek($skip);
        while (!$this->splFileObject->eof()) {
            yield trim($this->splFileObject->current());
            $this->splFileObject->next();
        }
    }

    /**
     * @return int
     */
    abstract protected function gatherLogIfExists(): int;
}