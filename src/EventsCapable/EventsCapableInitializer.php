<?php

namespace abacaphiliac\EventsCapable;

use Zend\EventManager\EventsCapableInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EventsCapableInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof EventsCapableInterface) {
            return;
        }
        
        $events = $instance->getEventManager();

        if ($serviceLocator->has('config')) {
            $config = $serviceLocator->get('config');
        } else {
            $config = array();
        }

        if (isset($config['abacaphiliac/events-capable'])) {
            $options = new EventsCapableOptions(
                $config['abacaphiliac/events-capable']
            );
        } else {
            $options = new EventsCapableOptions(
                array()
            );
        }
        
        $listeners = $options->getListeners($instance);
        
        foreach ($listeners as $listenerName) {
            $listener = $serviceLocator->get($listenerName);
            if ($listener instanceof ListenerAggregateInterface) {
                $listener->attach($events);
            }
        }
    }
}
