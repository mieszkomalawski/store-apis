<?php


namespace AppBundle;

use ArrayIterator;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSimpleStreamStrategy;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

class EventStoreFactory
{
    /**
     * @param FQCNMessageFactory $FQCNMessageFactory
     * @param \Doctrine\DBAL\Driver\Connection $connection
     * @param MySqlSimpleStreamStrategy $simpleStreamStrategy
     * @param StreamName $streamName
     * @return MySqlEventStore
     */
    public static function create(
        FQCNMessageFactory $FQCNMessageFactory,
        \Doctrine\DBAL\Driver\Connection $connection,
        MySqlSimpleStreamStrategy $simpleStreamStrategy,
        StreamName $streamName
    ): EventStore {
        $eventStore = new MySqlEventStore(
            $FQCNMessageFactory,
            $connection,
            $simpleStreamStrategy
        );
        $singleStream = new Stream($streamName, new ArrayIterator());

        if (!$eventStore->hasStream($streamName)) {
            $eventStore->create($singleStream);
        }

        return $eventStore;
    }
}
