<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Thomas Prelot <tprelot@gmail.com>
 * @license: MIT
 */

namespace IDCI\Bundle\StepBundle\Navigation;

use IDCI\Bundle\StepBundle\Flow\Flow;
use IDCI\Bundle\StepBundle\Flow\FlowData;
use IDCI\Bundle\StepBundle\Flow\FlowRecorderInterface;
use IDCI\Bundle\StepBundle\Map\MapInterface;
use IDCI\Bundle\StepBundle\Path\PathInterface;
use IDCI\Bundle\StepBundle\Step\StepInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class Navigator implements NavigatorInterface
{
    /**
     * The current form.
     *
     * @var FormInterface
     */
    protected $form;

    /**
     * The current form view.
     *
     * @var FormViewInterface
     */
    protected $formView;

    /**
     * The form factory.
     *
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FlowRecorderInterface
     */
    protected $flowRecorder;

    /**
     * @var MapInterface
     */
    protected $map;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var NavigationLoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $navigationData;

    /**
     * @var FlowInterface
     */
    protected $flow;

    /**
     * @var StepInterface
     */
    protected $currentStep;

    /**
     * @var PathInterface
     */
    protected $chosenPath;

    /**
     * @var array
     */
    protected $urlQueryParameters;

    /**
     * @var string
     */
    protected $finalDestination;

    /**
     * @var bool
     */
    protected $hasNavigated;

    /**
     * @var bool
     */
    protected $hasReturned;

    /**
     * @var bool
     */
    protected $hasFinished;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface      $formFactory  the form factory
     * @param FlowRecorderInterface     $flowRecorder the flow recorder using to store the flow
     * @param MapInterface              $map          the map to navigate
     * @param Request                   $request      the HTTP request
     * @param NavigationLoggerInterface $logger       the logger
     * @param array                     $data         the default navigation data
     */
    public function __construct(
        FormFactoryInterface      $formFactory,
        FlowRecorderInterface     $flowRecorder,
        MapInterface              $map,
        Request                   $request,
        NavigationLoggerInterface $logger = null,
        array                     $data = array()
    ) {
        $this->form = null;
        $this->formView = null;
        $this->formFactory = $formFactory;
        $this->flowRecorder = $flowRecorder;
        $this->map = $map;
        $this->request = $request;
        $this->logger = $logger;
        $this->data = $data;
        $this->flow = null;
        $this->currentStep = null;
        $this->chosenPath = null;
        $this->urlQueryParameters = array();
        $this->finalDestination = null;
        $this->hasNavigated = false;
        $this->hasReturned = false;
        $this->hasFinished = false;
    }

    /**
     * Init the flow.
     */
    protected function initFlow()
    {
        $this->setFinalDestination($this->getMap()->getFinalDestination());

        $this->flow = $this->flowRecorder->getFlow(
            $this->getMap(),
            $this->request
        );

        // The first time
        if (null === $this->flow) {
            $this->flow = new Flow();
            $this->flow->setCurrentStep($this->map->getFirstStep());

            $mapData = $this->getMap()->getData();

            foreach ($this->getMap()->getSteps() as $stepName => $step) {
                $this->data['remindedData'][$stepName] = array_replace_recursive(
                    isset($mapData[$stepName]) ?
                        $mapData[$stepName] : array(),
                    isset($this->data['remindedData'][$stepName]) ?
                        $this->data['remindedData'][$stepName] : array()
                );

                $this->data['retrievedData'][$stepName] = isset($this->data['retrievedData'][$stepName]) ?
                    $this->data['retrievedData'][$stepName] : array()
                ;

                if (!$this->getMap()->isResetFlowDataOnInitEnabled()) {
                    $this->flow->setStepData(
                        $step,
                        $this->data['remindedData'][$stepName]
                    );
                }

                $this->flow->setStepData(
                    $step,
                    $this->data['remindedData'][$stepName],
                    FlowData::TYPE_REMINDED
                );

                $this->flow->setStepData(
                    $step,
                    $this->data['retrievedData'][$stepName],
                    FlowData::TYPE_RETRIEVED
                );
            }

            $this->flowRecorder->reconstructFlowData($this->getMap(), $this->flow);
            $this->save();
        }
    }

    /**
     * Prepare the current step.
     */
    protected function prepareCurrentStep()
    {
        // Clone the map step into the navigation current step.
        $this->currentStep = clone $this->getMap()->getStep(
            $this->getFlow()->getCurrentStepName()
        );

        // Allow StepType to transform step options
        $this->currentStep->setOptions(
            $this
                ->currentStep
                ->getType()
                ->prepareNavigation(
                    $this,
                    $this->currentStep->getOptions()
                )
        );
    }

    /**
     * Returns the navigation form builder.
     *
     * @return Symfony\Component\Form\FormBuilderInterface
     */
    protected function getFormBuilder()
    {
        return $this->formFactory->createBuilder(
            NavigatorType::class,
            null,
            array('navigator' => $this)
        );
    }

    /**
     * Returns the navigation form.
     *
     * @return FormInterface the form
     */
    protected function getForm()
    {
        if (null === $this->form) {
            $this->form = $this->getFormBuilder()->getForm();
        }

        return $this->form;
    }

    /**
     * Setup the navigation url.
     *
     * @param StepInterface $step the step
     */
    protected function setupNavigationUrl(StepInterface $step)
    {
        // Add Step information following to the map configuration.
        if ($this->getMap()->isDisplayStepInUrlEnabled()) {
            $this->addUrlQueryParameter('step', $step->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function navigate()
    {
        if ($this->logger) {
            $this->logger->startNavigation();
        }

        $this->initFlow();
        $this->prepareCurrentStep();

        if ($this->hasNavigated() || $this->hasReturned() || $this->hasFinished()) {
            throw new \LogicException('The navigation has already been done');
        }

        /**
         * This must be done to dispatch form events:
         *  - 'form.pre_set_data'
         *  - 'form.post_set_data'
         * Event if the request is not a 'Post' (For the first step for exemple).
         */
        $form = $this->getForm();

        // The first http request which start the navigation workflow.
        if ($this->request->isMethod('GET')) {
            $this->setupNavigationUrl($this->currentStep);
        }

        // The others workflow http requests
        if ($this->request->isMethod('POST')) {
            $form->handleRequest($this->request);

            if (!$this->hasReturned() && $form->isSubmitted() && $form->isValid()) {
                $path = $this->getChosenPath();
                if (null === $path) {
                    throw new \LogicException(sprintf(
                        'The taken path seems to disapear magically'
                    ));
                }
                $destinationStep = $path->resolveDestination($this);

                if (null === $destinationStep) {
                    $this->hasFinished = true;
                } else {
                    $this->hasNavigated = true;
                    $this->getFlow()->setCurrentStep($destinationStep);

                    $this->setupNavigationUrl($destinationStep);
                }

                // Reset the current form.
                $this->form = null;
            }
        }

        // Save the flow even if the the request is not a 'Post'.
        $this->save();

        if ($this->logger) {
            $this->logger->stopNavigation($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function goBack($stepName = null)
    {
        $destinationStep = $this->getPreviousStep($stepName);

        if (null === $destinationStep) {
            throw new \LogicException('Could not go back to a non existing step');
        }

        $this->getFlow()->retraceTo($destinationStep);
        $this->hasReturned = true;
        $this->save();
        $this->setupNavigationUrl($destinationStep);

        // Reset the current form.
        $this->form = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
    {
        return $this->currentStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPaths()
    {
        return $this->getMap()->getPaths($this->getFlow()->getCurrentStepName());
    }

    /**
     * {@inheritdoc}
     */
    public function setChosenPath(PathInterface $path)
    {
        $this->chosenPath = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChosenPath()
    {
        return $this->chosenPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep($stepName = null)
    {
        if (null === $stepName) {
            $stepName = $this->getFlow()->getPreviousStepName();
            if (null === $stepName) {
                return null;
            }
        }

        $previousStep = $this->getMap()->getStep($stepName);

        if (null !== $previousStep && !$this->getFlow()->hasDoneStep($previousStep)) {
            throw new \LogicException(sprintf(
                'The step "%s" is not a previous step',
                $stepName
            ));
        }

        return $previousStep;
    }

    /**
     * {@inheritdoc}
     */
    public function addUrlQueryParameter($key, $value = null)
    {
        $this->urlQueryParameters[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlQueryParameters()
    {
        return $this->urlQueryParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUrlQueryParameters()
    {
        return !empty($this->urlQueryParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalDestination($url)
    {
        $this->finalDestination = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFinalDestination()
    {
        return null !== $this->finalDestination;
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalDestination()
    {
        if (!$this->hasUrlQueryParameters()) {
            return $this->finalDestination;
        }

        $glue = false !== strpos($this->finalDestination, '?') ? '&' : '?';

        return sprintf(
            '%s%s%s',
            $this->finalDestination,
            $glue,
            http_build_query($this->getUrlQueryParameters())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStepData(array $data, $type = null)
    {
        $this->getFlow()->setStepData(
            $this->getCurrentStep(),
            $data,
            $type
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStepData($type = null)
    {
        return $this->getFlow()->getStepData($this->getCurrentStep(), $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailablePaths()
    {
        return $this->getMap()->getPaths($this->getFlow()->getCurrentStepName());
    }

    /**
     * {@inheritdoc}
     */
    public function getTakenPaths()
    {
        return $this->getFlow()->getTakenPaths();
    }

    /**
     * {@inheritdoc}
     */
    public function hasNavigated()
    {
        return $this->hasNavigated;
    }

    /**
     * {@inheritdoc}
     */
    public function hasReturned()
    {
        return $this->hasReturned;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFinished()
    {
        return $this->hasFinished;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return $this->flowRecorder->serialize($this->getFlow());
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->flowRecorder->setFlow(
            $this->map,
            $this->request,
            $this->flow
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->flowRecorder->removeFlow(
            $this->map,
            $this->request
        );
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->hasFinished = true;
    }

    /**
     * {@inheritdoc}
     */
    public function createStepView()
    {
        return $this->getForm()->createView();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormView()
    {
        if (null === $this->formView) {
            $this->formView = $this->createStepView();
        }

        return $this->formView;
    }
}
