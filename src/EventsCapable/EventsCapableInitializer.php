<?php

namespace abacaphiliac\EventsCapable;

use abacaphiliac\EventsCapable\Exception\ListenerNotCreatedException;
use Zend\EventManager\EventsCapableInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ExceptionInterface;
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
        
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
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
        
        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $listener = $this->getListener($serviceLocator, $listener);
            }
            
            if ($listener instanceof ListenerAggregateInterface) {
                $listener->attach($events);
            }
            
            // TODO Get listener spec from config (e.g. event-name, callable, and priority.
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param mixed $listenerName
     * @return object
     */
    private function getListener(ServiceLocatorInterface $serviceLocator, $listenerName)
    {
        if ($serviceLocator->has($listenerName)) {
            try {
                return $serviceLocator->get($listenerName);
            } catch (ExceptionInterface $e) {
                throw new ListenerNotCreatedException($e->getMessage(), $e->getCode(), $e);
            }
        }
        
        // TODO Check for constructor params and throw an exception guiding the dev to register with service container.
        
        if (class_exists($listenerName)) {
            return new $listenerName;
        }
        
        throw new ListenerNotCreatedException(
            'Listener must be registered in service container, or an invokable class.'
        );
    }
}
