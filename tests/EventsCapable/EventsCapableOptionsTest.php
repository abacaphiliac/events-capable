<?php

namespace abacaphiliacTest\EventsCapable;

use abacaphiliac\EventsCapable\EventsCapableOptions;
use Zend\EventManager\EventsCapableInterface;

class EventsCapableOptionsTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventsCapableOptions */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new EventsCapableOptions();
    }
    
    public function testGetListeners()
    {
        $eventsCapable = $this->getMock(EventsCapableInterface::class);
        
        $this->sut->setFromArray(array(
            'eventsCapable' => array(
                get_class($eventsCapable) => array(
                    $listenerName = 'MyListener',
                ),
            ),
        ));
        
        $actual = $this->sut->getListeners($eventsCapable);
        
        $this->assertContains($listenerName, $actual);
    }
}
