<?php

namespace IDCI\Bundle\StepBundle\Tests\Functional\Navigation;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Thomas Prelot <thomas.prelot@tessi.fr>
 */
class ConfiguredNavigationTest extends AbstractNavigationTest
{
    public function setUp()
    {
        $this->map = json_decode(
            '{
                "name": "test map",
                "data": {
                    "foo": "bar"
                },
                "steps": {
                    "intro": {
                        "type": "html",
                        "options": {
                            "title": "Introduction",
                            "description": "The first step",
                            "content": "<h1>My content</h1>"
                        }
                    },
                    "personal": {
                        "type": "form",
                        "options": {
                            "title": "Personal information",
                            "description": "The personal data step",
                            "previous_options": {
                                "label": "Back to first step"
                            },
                            "@builder": {
                                "worker": "form_builder",
                                "parameters": {
                                    "fields": [
                                        {
                                            "name": "first_name",
                                            "type": "text"
                                        },
                                        {
                                            "name": "last_name",
                                            "type": "text"
                                        }
                                    ]

                                }
                            }
                        }
                    },
                    "purchase": {
                        "type": "form",
                        "options": {
                            "title": "Purchase information",
                            "description": "The purchase data step",
                            "@builder": {
                                "worker": "form_builder",
                                "parameters": {
                                    "fields": [
                                        {
                                            "name": "item",
                                            "type": "text"
                                        },
                                        {
                                            "name": "purchase_date",
                                            "type": "datetime"
                                        }
                                    ]

                                }
                            }
                        }
                    },
                    "fork1": {
                        "type": "form",
                        "options": {
                            "title": "Fork1 information",
                            "description": "The fork1 data step",
                            "@builder": {
                                "worker": "form_builder",
                                "parameters": {
                                    "fields": [
                                        {
                                            "name": "fork1_data",
                                            "type": "textarea"
                                        }
                                    ]

                                }
                            }
                        }
                    },
                    "fork2": {
                        "type": "form",
                        "options": {
                            "title": "Fork2 information",
                            "description": "The fork2 data step",
                            "@builder": {
                                "worker": "form_builder",
                                "parameters": {
                                    "fields": [
                                        {
                                            "name": "fork2_data",
                                            "type": "textarea"
                                        }
                                    ]

                                }
                            }
                        }
                    },
                    "end": {
                        "type": "html",
                        "options": {
                            "title": "The end",
                            "description": "The last data step",
                            "content": "<h1>The end</h1>"
                        }
                    }
                },
                "paths": [
                    {
                        "type": "single",
                        "options": {
                            "source": "intro",
                            "destination": "personal",
                            "next_options": {
                                "label": "next"
                            }
                        }
                    },
                    {
                        "type": "conditional_destination",
                        "options": {
                            "source": "personal",
                            "destinations": {
                                "purchase": {
                                    "rules": []
                                },
                                "fork2": {
                                    "rules": []
                                }
                            }
                        }
                    },
                    {
                        "type": "single",
                        "options": {
                            "source": "purchase",
                            "destination": "fork1"
                        }
                    },
                    {
                        "type": "single",
                        "options": {
                            "source": "purchase",
                            "destination": "fork2",
                            "next_options": {
                                "label": "next p"
                            }
                        }
                    },
                    {
                        "type": "single",
                        "options": {
                            "source": "fork1",
                            "destination": "end",
                            "next_options": {
                                "label": "next f"
                            }
                        }
                    },
                    {
                        "type": "single",
                        "options": {
                            "source": "fork2",
                            "destination": "end",
                            "next_options": {
                                "label": "last"
                            }
                        }
                    },
                    {
                        "type": "end",
                        "options": {
                            "source": "end",
                            "next_options": {
                                "label": "end"
                            }
                        }
                    }
                ]
            }',
            true
        );
    }
}