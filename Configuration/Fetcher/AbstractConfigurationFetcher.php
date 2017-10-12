<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Thomas Prelot <tprelot@gmail.com>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Configuration\Fetcher;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractConfigurationFetcher implements ConfigurationFetcherInterface
{
    /**
     * {@inheritdoc}
     */
    public function fetch(array $parameters = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultParameters($resolver);

        return $this->doFetch($resolver->resolve($parameters));
    }

    /**
     * Set default parameters.
     *
     * @param OptionsResolver $resolver
     */
    protected function setDefaultParameters(OptionsResolver $resolver)
    {
    }

    /**
     * Fetch the configuration.
     *
     * @param array $parameters
     *
     * @return array
     *
     * @throw  FetchConfigurationException
     */
    abstract protected function doFetch(array $parameters = array());
}
