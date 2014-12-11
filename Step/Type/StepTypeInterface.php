<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */
 
namespace IDCI\Bundle\StepBundle\Step\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

interface StepTypeInterface
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName();

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The options resolver.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);
}
