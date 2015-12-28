[![Build Status](https://travis-ci.org/abacaphiliac/events-capable.svg)](https://travis-ci.org/abacaphiliac/events-capable)

# abacaphiliac/events-capable
Tired of writing backwards-compatible logic in all of your factory code to wire up listeners? Is your service
implementation registered with Zend's service manager? Then this package might be right for you!

This package provides a config-based initializer that attaches listeners to implementations of 
`\Zend\EventManager\EventsCapableInterface`.
When the service concrete is instantiated by service manager, it will pass through the initializer provided by this
package. If your service is Events Capable, then the initializer will check the config for registered listeners
to attach to the service's event manager.

## Installation

Install the latest version with

```bash
$ composer require abacaphiliac/events-capable
```

## Basic Usage (Configuration)

1. Update `service_manager` config.
    1. Add initializer.
    1. Add listener.
1. Update `abacaphiliac/events-capable` config:
    1. Add `\Zend\EventManager\EventsCapableInterface` implementation.
    1. Add `\Zend\EventManager\ListenerAggregateInterface` implementation.

## Configuration Examples

### Update service_manager config:
```php
return [
    'service_manager' => [
        'factories' => [
            \MyListener::class => \MyListenerFactory::class,
        ],
        'initializers' => [
            \abacaphiliac\EventsCapable\EventsCapableInitializer::class,
        ],
    ],
];
```

### Update abacaphiliac/events-capable config:
```php
return [
    'abacaphiliac/events-capable' => [
        'eventsCapable' => [
            \MyEventsCapableService::class => [
                \MyListener::class,
            ],
        ],
    ],
];
```
