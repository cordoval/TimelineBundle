<?php

namespace Spy\TimelineBundle\Driver\ORM\QueryBuilder;

use Spy\TimelineBundle\Driver\ORM\QueryBuilder\Criteria\CriteriaCollection;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Asserter;
use Spy\Timeline\Driver\QueryBuilder\Criteria\Operator;

class OperatorVisitor implements VisitorInterface
{
    /**
     * @var string
     */
    protected $dql;

    /**
     * {@inheritdoc}
     */
    public function visit($operator, CriteriaCollection $criteriaCollection)
    {
        if (!$operator instanceof Operator) {
            throw new \Exception('OperatorVisitor accepts only Operator instance');
        }

        $dqlParts = array();

        foreach ($operator->getCriterias() as $criteria) {
            if ($criteria instanceof Operator) {
                $visitor = new OperatorVisitor();
            } elseif ($criteria instanceof Asserter) {
                $visitor = new AsserterVisitor();
            } else {
                throw new \Exception('Not supported');
            }

            $visitor->visit($criteria, $criteriaCollection);

            $dqlParts[] = $visitor->getDql();
        }

        $this->dql = sprintf('(%s)', implode(' '.$operator->getType().' ', $dqlParts));
    }

    /**
     * {@inheritdoc}
     */
    public function getDql()
    {
        return $this->dql;
    }
}
