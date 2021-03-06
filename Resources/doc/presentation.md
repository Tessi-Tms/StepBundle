Presentation
============

Creating a Simple Map
---------------------

Building the Map
----------------

We have to create a map before rendering it.
For now, this can all be done from inside a controller.

```php
// src/AppBundle/Controller/DefaultController.php
namespace AppBundle\Controller;

use IDCI\Bundle\StepBundle\Map\MapBuilderFactoryInterface;
use IDCI\Bundle\StepBundle\Navigation\NavigatorFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/contact/", name="test")
     *
     *
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(
        Request $request,
        FormFactoryInterface $formFactory,
        NavigatorFactoryInterface $navigatorFactory,
        MapBuilderFactoryInterface $mapBuilderFactory
    ) {
    {
        $map = $mapBuilderFactory
            ->createNamedBuilder('test map')
            ->addStep('intro', 'html', array(
                'title' => 'Introduction',
                'description' => 'The first step',
                'content' => '<h1>My content</h1>',
            ))
            ->addStep('personal', 'form', array(
                'title' => 'Personal information',
                'description' => 'The personal data step',
                'builder' => $formFactory->createBuilder()
                    ->add('first_name', 'text', array(
                        'constraints' => array(
                            new \Symfony\Component\Validator\Constraints\NotBlank()
                        )
                    ))
                    ->add('last_name', 'text')
                ,
            ))
            ->addStep('end', 'html', array(
                'title' => 'End',
                'description' => 'The last step',
                'content' => '<h1>Game over :)</h1>',
            ))
            ->addPath(
                'single',
                array(
                    'source' => 'intro',
                    'destination' => 'personal',
                    'next_options' => array(
                        'label' => 'next',
                    ),
                )
            )
            ->addPath(
                'single',
                array(
                    'source' => 'personal',
                    'destination' => 'end',
                    'next_options' => array(
                        'label' => 'next',
                    ),
                )
            )
            ->addPath(
                'end',
                array(
                    'source' => 'end',
                    'next_options' => array(
                        'label' => 'end',
                    ),
                )
            )
            ->getMap($request)
        ;

        $navigator = $navigatorFactory
            ->createNavigator($request, $map)
        ;

        if ($navigator->hasFinished()) {
            $navigator->clear();

            return $this->redirect($navigator->getFinalDestination());
        }
        if ($navigator->hasNavigated() || $navigator->hasReturned()) {
            return $this->redirect($this->generateUrl('test', $navigator->getUrlQueryParameters()));
        }

        return array('navigator' => $navigator);
    }
}
```
![map build example diagram](images/mapBuildExample.png)

Rendering the Map
-----------------

Well, we have created a map, now we need to render it.
We have to use some functions and write that in the
`Contact.html.twig` file.

This is the minimal code you need to make StepBundle work.

```twig
{% extends "::base.html.twig" %}

{% block stylesheets %}
    {{ parent() }}
    {{ step_stylesheets(navigator) }}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    {{ step_javascripts(navigator) }}
{% endblock %}

{% block body %}
    {{ step(navigator) }}
{% endblock %}
```

Handling Map Submissions
------------------------

The hasFinished, hasNavigated, hasReturned are conditionals that are true at some moments in the flow navigation lifecycle.
The hasNavigated is true when the navigator navigated forward.
The hasReturned is true when the navigator navigated backward.
The hasFinished is true when the navigator reached the end of the map.

Built-in Field Types
--------------------

There are multiple step categories.

The html step is a step that contains pure html code.
```php
->addStep('intro', 'html', array(
	'title' => 'Introduction',
	'description' => 'The first step',
	'content' => '<h1>My content</h1>',
))
```

The form step is a step that contains symfony builder formated forms.
```php
->addStep('personal', 'form', array(
    'title' => 'Personal information',
    'description' => 'The personal data step',
    'builder' => $this->get('form.factory')->createBuilder()
        ->add('first_name', 'text', array(
            'constraints' => array(
                new \Symfony\Component\Validator\Constraints\NotBlank()
            )
        ))
        ->add('last_name', 'text')
))
```

There are two path categories.

The single path is a simple path that links two steps.
```php
->addPath(
    'single',
    array(
        'source' => 'intro',
        'destination' => 'personal',
    )
)
```

The conditional path is a path that reaches different steps according to conditions.

```php
->addPath(
    'conditional_destination',
    array(
        'source' => 'intro',
        'destinations' => array(
            'personal' => '{{ flow_data.data.cursus.study_city == 'Lyon' }}',
            'location' => '{{ flow_data.data.cursus.study_city == 'Paris' }}'
        )
    )
```

We just showed you how to implement a simple StepMap based form flow.
For a large presentation of the possibilities, we offer you a large exhaustive configurated map.
* [Configuration](configuration.md)
