<?php

namespace Patchlevel\EventSourcingBundle\Tests\Fixtures;

use Patchlevel\EventSourcing\Aggregate\AggregateChanged;
use Patchlevel\EventSourcing\Aggregate\AggregateRoot;
use Patchlevel\EventSourcing\Aggregate\SnapshotableAggregateRoot;

class SnapshotableProfile extends SnapshotableAggregateRoot
{
    public function aggregateRootId(): string
    {
        return '1';
    }

    protected function apply(AggregateChanged $event): void
    {
        // do nothing
    }

    protected function serialize(): array
    {
        return [];
    }

    protected static function deserialize(array $payload): self
    {
        return new self();
    }
}
