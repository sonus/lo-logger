<?php
declare(strict_types=1);

namespace App\Service\Log;

use Symfony\Component\Validator\Constraints as Assert;

class SearchLogDto
{
    /**
     * @Assert\DateTime(format="Y-m-d H:i:s")
     * @var string A "Y-m-d H:i:s" formatted value
     */
    public $startDate;

    /**
     * @Assert\DateTime(format="Y-m-d H:i:s")
     * @var string A "Y-m-d H:i:s" formatted value
     */
    public $endDate;

    /**
     * @var string[]|null
     */
    public $serviceNames;

    /**
     * @Assert\Type(type={"string", "null"})
     * @var string|null
     */
    public $statusCode;

}