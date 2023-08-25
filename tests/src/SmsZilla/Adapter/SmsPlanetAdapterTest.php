<?php
namespace src\SmsZilla\Adapter;

use SmsZilla\Adapter\SmsPlanetAdapter;

class SmsPlanetAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SmsPlanetAdapter
     */
    protected $object;

    private $params = [];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->config = include  __DIR__ . '/../../config.php';
        $this->object = new SmsPlanetAdapter();
        $this->params = [
            'token' => $this->config['smsplanet_token'],
            'test' => $this->config['smsplanet_test'],
            'password' => $this->config['smsplanet_password'],
            'sender' => $this->config['smsplanet_sender']
        ];
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    /**
     * @covers SmsZilla\Adapter\InfobipAdapter::send
     */
    public function testSend()
    {
        $message = new \SmsZilla\SmsMessageModel();
        $message->setText($this->config['message']);
        foreach ($this->config['my_phones'] as $no) {
            $message->addRecipient($no);
        }
        $this->object->setParams($this->params);
        $result = $this->object->send($message);

        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }

    public function testSendError() {
        $message = new \SmsZilla\SmsMessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['wrong_phone']);
        $this->object->setParams($this->params);
        $result = $this->object->send($message);

        $this->assertFalse($result);
        $this->assertCount(1, $this->object->getErrors());
    }
}
