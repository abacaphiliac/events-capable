<?php

namespace abacaphiliac\EventsCapable;

use Zend\EventManager\EventsCapableInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\ParametersInterface;

class EventsCapableOptions extends AbstractOptions
{
    /** @var  ParametersInterface */
    private $eventsCapable = array();

    /**
     * @return ParametersInterface
     */
    public function getEventsCapable()
    {
        if (!$this->eventsCapable instanceof ParametersInterface) {
            $this->eventsCapable = new Parameters($this->eventsCapable);
        }
        
        return $this->eventsCapable;
    }

    /**
     * @param \mixed[] $eventsCapable
     */
    public function setEventsCapable($eventsCapable)
    {
        $this->eventsCapable = $eventsCapable;
    }

    /**
     * @param EventsCapableInterface $eventsCapable
     * @return mixed[]
     */
    public function getListeners(EventsCapableInterface $eventsCapable)
    {
        $name = get_class($eventsCapable);

        $services = $this->getEventsCapable();

        return $services->get($name, array());
    }
}
