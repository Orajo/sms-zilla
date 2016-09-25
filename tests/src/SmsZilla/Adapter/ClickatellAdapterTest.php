<?php

namespace SmsZilla\Adapter;

class ClickatellAdapterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ClickatellAdapter
     */
    protected $object;

    private $config = [];
        
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->config = include  __DIR__ . '/../../config.php';
        $this->object = new ClickatellAdapter;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers SmsZilla\Adapter\SmsCenterAdapter::send
     * @todo   Implement testSend().
     */
    public function testSend() {
        $message = new \SmsZilla\SmsMessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['my_phone']);
        $this->object->setParams([
            'token' => $this->config['clickatell_token']]
        );
        $result = $this->object->send($message, false);
        var_dump($result);
        var_dump($this->object->getErrors());
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }
    
        
    /**
     * @covers SmsZilla\Adapter\SmsCenterAdapter::setParams
     */
    public function testSetParams() {
        $this->object->setParams([
            'token' => $this->config['clickatell_token']]
        );
        $this->assertEquals($this->object->getParam('token'), $this->config['clickatell_token']);
        
        $this->expectException(\SmsZilla\ConfigurationException::class);
        $this->object->setParams(['dummy' => 1]);
    }

}
