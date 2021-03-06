<?php

/**
 * @author:  Benjamin TARDY  <benjamin.tardy@tessi.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Path\Event\Action;

use Symfony\Component\OptionsResolver\OptionsResolver;
use IDCI\Bundle\StepBundle\Path\Event\PathEventInterface;

class ChangeFinalDestinationPathEventAction extends AbstractPathEventAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(PathEventInterface $event, array $parameters = array())
    {
        $event
            ->getNavigator()
            ->setFinalDestination($parameters['final_destination'])
        ;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultParameters(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('final_destination'))
            ->setAllowedTypes('final_destination', array('string'))
        ;
    }
}
