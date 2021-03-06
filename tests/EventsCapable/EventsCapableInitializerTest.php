<?php

namespace abacaphiliacTest\EventsCapable;

use abacaphiliac\EventsCapable\EventsCapableInitializer;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorPluginManager;

class EventsCapableInitializerTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventsCapableInitializer */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new EventsCapableInitializer();
    }
    
    public function testInitialize()
    {
        $events = new EventManager();
        
        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);
        
        $listener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');
        $listener->expects($this->once())->method('attach')->with($events);
        
        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('MyListener', $listener);
        $serviceLocator->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        'MyListener',
                    ),
                ),
            ),
        ));
        
        $this->sut->initialize($instance, $serviceLocator);
    }
    
    public function testInitializeWithoutConfig()
    {
        $events = new EventManager();
        
        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);
        
        $actual = $this->sut->initialize($instance, new ServiceManager());
        
        $this->assertNull($actual);
    }
    
    public function testInitializeInvalidInstance()
    {
        $actual = $this->sut->initialize(new \stdClass(), new ServiceManager());

        $this->assertNull($actual);
    }

    public function testInitializeFromPluginManager()
    {
        $events = new EventManager();

        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);

        $listener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');
        $listener->expects($this->once())->method('attach')->with($events);

        $services = new ServiceManager();
        $services->setService('MyListener', $listener);
        $services->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        'MyListener',
                    ),
                ),
            ),
        ));
        
        $validators = new ValidatorPluginManager();
        $validators->setServiceLocator($services);

        $this->sut->initialize($instance, $validators);
    }
    
    public function testUseInstantiatedListenerProvidedByConfig()
    {
        $events = new EventManager();

        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);

        $listener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');
        $listener->expects($this->once())->method('attach')->with($events);

        $services = new ServiceManager();
        $services->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        $listener,
                    ),
                ),
            ),
        ));

        $this->sut->initialize($instance, $services);
    }
    
    public function testInstantiateListener()
    {
        $events = new EventManager();

        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);

        $listener = $this->getMock('\Zend\EventManager\ListenerAggregateInterface');

        $services = new ServiceManager();
        $services->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        get_class($listener),
                    ),
                ),
            ),
        ));

        $actual = $this->sut->initialize($instance, $services);
        
        // Since we're instantiating a new instance of the mock, I don't have anything to assert :(
        $this->assertNull($actual);
    }

    /**
     * @expectedException \abacaphiliac\EventsCapable\Exception\ListenerNotCreatedException
     */
    public function testUnexpectedListenerType()
    {
        $events = new EventManager();

        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);

        $services = new ServiceManager();
        $services->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        'ClassNameThatDoesNotExist',
                    ),
                ),
            ),
        ));

        $this->sut->initialize($instance, $services);
    }

    /**
     * @expectedException \abacaphiliac\EventsCapable\Exception\ListenerNotCreatedException
     */
    public function testServiceManagerFailsToCreateListener()
    {
        $events = new EventManager();

        $instance = $this->getMock('\Zend\EventManager\EventsCapableInterface');
        $instance->method('getEventManager')->willReturn($events);

        $services = new ServiceManager();
        $services->setFactory('MyListener', function () {
            throw new ServiceNotCreatedException();
        });
        $services->setService('config', array(
            'abacaphiliac/events-capable' => array(
                'eventsCapable' => array(
                    get_class($instance) => array(
                        'MyListener',
                    ),
                ),
            ),
        ));

        $this->sut->initialize($instance, $services);
    }
}
