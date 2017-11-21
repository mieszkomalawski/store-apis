<?php


namespace AppBundle\Services;

use Prooph\EventStore\Projection\ProjectionManager;

class CartProjection
{
    /**
     * @var ProjectionManager
     */
    private $projectionManager;

    /**
     * CartProjection constructor.
     * @param ProjectionManager $projectionManager
     */
    public function __construct(ProjectionManager $projectionManager)
    {
        $this->projectionManager = $projectionManager;
    }


    public function project()
    {
        $projection = $this->projectionManager->createProjection('read_carts');

        $projection
            ->fromAll()
            ->whenAny(function ($state, $event) use ($connection) {
                switch (get_class($event)) {
                    case LoanCreated::class:
                        /**
                         * @var LoanCreated $event
                         */
                        $sql = 'INSERT INTO read_loans SET 
                    `id` = :id,
                    `amount` = :amount,
                    `remaining` = :ramaining,
                    `currency` = :currency
                ';
                        $stmt = $connection->prepare($sql);
                        $stmt->bindValue('id', $event->id());
                        $stmt->bindValue('amount', (int)$event->amount()->getAmount());
                        $stmt->bindValue('ramaining', (int)$event->amount()->getAmount());
                        $stmt->bindValue('currency', $event->amount()->getCurrency());
                        $stmt->execute();
                        break;
                    case LoanPaidOff::class:
                        /**
                         * @var LoanPaidOff $event
                         */
                        $sql = 'UPDATE read_loans SET 
                    `remaining` = remaining - :amount
                    WHERE 
                    `id` = :id
                ';
                        $stmt = $connection->prepare($sql);
                        $stmt->bindValue('id', $event->id());
                        $stmt->bindValue('amount', (int)$event->amount()->getAmount());
                        $stmt->execute();
                        break;
                }
            })
            ->run();
    }
}
