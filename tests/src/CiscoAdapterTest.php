<?php
namespace SmsSender\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-05 at 11:36:21.
 */
class CiscoAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CiscoAdapter
     */
    protected $object;
    
    private $config = [];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = include  __DIR__ . '/config.php';
        $this->object = new CiscoAdapter;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * ssh login and pass are not set
     * @covers SmsSender\Adapter\FileGateway::send
     * @expectedException \SmsSender\ConfigurationException
     * @expectedExceptionMessage SmsSender\Adapter\CiscoAdapter is not configured properly. If SSH is enabled then parameters "ssh_host" and "ssh_login" must be set.
     */
    public function testSendConfigError() {
        $this->object->setParams(['use_ssh' => true]);
        // store_path is not set
        $this->object->send(new \SmsSender\MessageModel);
    }
    
    /**
     * @covers SmsSender\Adapter\CiscoAdapter::send
     */
    public function testSend()
    {
        $message = new \SmsSender\MessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['phones'][0]);
        $result = $this->object->send($message);
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }
    
    /**
     * @covers SmsSender\Adapter\CiscoAdapter::send
     */
    public function testSendSsh()
    {
        $message = new \SmsSender\MessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['phones'][0]);
        
        $this->object->setParams(['use_ssh' => true]);
        $this->object->setParams(['ssh_login' => 'dummy_user']);
        $this->object->setParams(['ssh_host' => '127.0.0.1']);
        
        $result = $this->object->send($message);
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }

}