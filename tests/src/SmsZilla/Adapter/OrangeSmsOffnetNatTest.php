<?php

namespace SmsZilla\Adapter;

use PHPUnit\Framework\TestCase;

class OrangeSmsOffnetNatTest extends TestCase
{
    /**
     * @var OrangeSmsOffnetNatAdapter
     */
    protected $object;

    private $config = [];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = include  __DIR__ . '/../../config.php';
        $this->object = new OrangeSmsOffnetNatAdapter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \SmsZilla\Adapter\OrangeSmsOffnetNatAdapter::send
     * @covers SmsZilla\Adapter\AbstractAdapter::getParams
     * @covers \SmsZilla\ConfigurationException::__construct
     */
    public function testSenderConfigError() {
        $this->expectExceptionMessage(
            "SmsZilla\Adapter\OrangeSmsOffnetNatAdapter is not configured properly. Please set \"sender\" parameter properly."
        );
        $this->expectException(\SmsZilla\ConfigurationException::class);
        $this->object->setParams(['token' => $this->config['orange_token']]);
        $this->object->setParams(['sender' => '']);
        $this->object->send(new \SmsZilla\SmsMessageModel);
    }

    /**
     * @covers SmsZilla\Adapter\OrangeSmsOffnetNatAdapter::send
     * @covers SmsZilla\Adapter\AbstractAdapter::getErrors
     * @covers \SmsZilla\SendingError::__construct
     */
    public function testSend() {
        $message = new \SmsZilla\SmsMessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['my_phone']);
        $this->object->setParams([
            'token' => $this->config['orange_token'],
            'sender' => $this->config['orange_sender']
        ]);
        $result = $this->object->send($message);
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }

    /**
     * @covers SmsZilla\Adapter\AbstractAdapter::setParams
     */
    public function testSetParams() {

        $this->object->setParams(['token' => $this->config['orange_token']]);
        $this->assertEquals($this->object->getParam('token'), $this->config['orange_token']);

        $this->expectException(\SmsZilla\ConfigurationException::class);
        $this->object->setParams(['dummy' => 1]);
    }
}
