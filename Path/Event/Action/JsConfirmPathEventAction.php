<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Path\Event\Action;

use IDCI\Bundle\StepBundle\Form\Type\JsConfirmFormType;
use IDCI\Bundle\StepBundle\Path\Event\PathEventInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsConfirmPathEventAction extends AbstractPathEventAction
{
    /**
     * {@inheritdoc}
     */
    protected function doExecute(PathEventInterface $event, array $parameters = array())
    {
        $form = $event->getForm();

        $form
            ->add('_js_confirm', JsConfirmFormType::class, array_merge(
                array('message' => $parameters['message']),
                array('path_index' => $event->getPathIndex())
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultParameters(OptionsResolver $resolver)
    {
        $resolver->setOptional(array('message'));
    }
}
