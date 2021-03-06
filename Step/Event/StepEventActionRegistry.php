<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Step\Event;

use IDCI\Bundle\StepBundle\Exception\UnexpectedTypeException;
use IDCI\Bundle\StepBundle\Step\Event\Action\StepEventActionInterface;

class StepEventActionRegistry implements StepEventActionRegistryInterface
{
    /**
     * @var StepEventActionInterface[]
     */
    private $actions = array();

    /**
     * {@inheritdoc}
     */
    public function setAction($alias, StepEventActionInterface $action)
    {
        $this->actions[$alias] = $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction($alias)
    {
        if (!is_string($alias)) {
            throw new UnexpectedTypeException($alias, 'string');
        }

        if (!isset($this->actions[$alias])) {
            throw new \InvalidArgumentException(sprintf(
                'Could not load step event action "%s". Available actions are %s',
                $alias,
                implode(', ', array_keys($this->actions))
            ));
        }

        return $this->actions[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function hasAction($alias)
    {
        if (!isset($this->actions[$alias])) {
            return true;
        }

        return false;
    }
}
