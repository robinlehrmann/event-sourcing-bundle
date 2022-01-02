<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcingBundle\DataCollector;

use Patchlevel\EventSourcing\Aggregate\AggregateChanged;
use Patchlevel\EventSourcing\Aggregate\AggregateRoot;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @psalm-type DataType = array{
 *    events: list<Event>,
 *    aggregates: array<class-string<AggregateRoot>, string>
 * }
 * @psalm-type Event = array{
 *    class: string,
 *    aggregateId: string,
 *    payload: \Symfony\Component\VarDumper\Cloner\Data,
 *    playhead: int,
 *    recordedOn: string
 * }
 * @psalm-property DataType $data
 */
class EventCollector extends AbstractDataCollector
{
    private EventListener $eventListener;
    /** @var array<class-string<AggregateRoot>, string> */
    private array $aggregates;

    /**
     * @param array<class-string<AggregateRoot>, string> $aggregates
     */
    public function __construct(EventListener $eventListener, array $aggregates)
    {
        $this->eventListener = $eventListener;
        $this->aggregates = $aggregates;
    }

    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
        $events = array_map(
            function (AggregateChanged $event) {
                return [
                    'class' => $event::class,
                    'aggregateId' => $event->aggregateId(),
                    'payload' => $this->cloneVar($event->payload()),
                    'playhead' => $event->playhead(),
                    'recordedOn' => $event->recordedOn()?->format(DATE_ATOM),
                ];
            },
            $this->eventListener->get()
        );

        $this->data = [
            'events' => $events,
            'aggregates' => $this->aggregates,
        ];
    }

    public static function getTemplate(): string
    {
        return '@PatchlevelEventSourcing/Collector/template.html.twig';
    }

    /**
     * @return list<AggregateChanged<array<string, mixed>>>
     */
    public function getEvents(): array
    {
        return $this->data['events'];
    }

    /**
     * @return array<class-string<AggregateRoot>, string> $aggregates
     */
    public function getAggregates(): array
    {
        return $this->data['aggregates'];
    }
}
