<?php

declare(strict_types=1);

namespace App\Gym\Domain\Ports;

use DateTimeImmutable;

interface RelojPort
{
    public function ahora(): DateTimeImmutable;
}
