<?php
namespace SmsZilla\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-02 at 10:02:11.
 */
class FileAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileAdapter
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
        $this->object = new FileAdapter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        foreach ($this->config['phones'] as $phone) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . $phone . FileAdapter::FILE_EXT;
            if (file_exists($file)) {
                unlink (__DIR__ . DIRECTORY_SEPARATOR . $phone . FileAdapter::FILE_EXT);
            }
        }
    }

    /**
     * 
     * @covers SmsZilla\Adapter\FileAdapter::__construct
     */
    public function testConstructor() {
        $this->object = new FileAdapter(['store_path' => __DIR__]);
        $this->assertEquals(__DIR__, $this->object->getParam('store_path'));
    }
    
    /**
     * @covers SmsZilla\Adapter\FileAdapter::send
     * @covers SmsZilla\ConfigurationException::__construct
     * @expectedException \SmsZilla\ConfigurationException
     * @expectedExceptionMessage SmsZilla\Adapter\FileAdapter is not configured properly. Please set "store_path" parameter.
     */
    public function testSendConfigError() {
        // store_path is not set
        $this->object->send(new \SmsZilla\SmsMessageModel);
    }
    
    /**
     * @covers SmsZilla\Adapter\FileAdapter::send
     */
    public function testSend()
    {
        $this->object->setParams(['store_path' => __DIR__]);
        $message = new \SmsZilla\SmsMessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['phones'][0]);
        $this->assertFileNotExists(__DIR__ . DIRECTORY_SEPARATOR . $this->config['phones'][0] . \SmsZilla\Adapter\FileAdapter::FILE_EXT);
        $message->addRecipient($this->config['phones'][1]);
        $this->object->send($message);
        $this->assertFileExists(__DIR__ . DIRECTORY_SEPARATOR . $this->config['phones'][0] . \SmsZilla\Adapter\FileAdapter::FILE_EXT);
        $this->assertFileExists(__DIR__ . DIRECTORY_SEPARATOR . $this->config['phones'][1] . \SmsZilla\Adapter\FileAdapter::FILE_EXT);
        
        $file = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $this->config['phones'][0] . \SmsZilla\Adapter\FileAdapter::FILE_EXT);
        $ref = '['.$this->config['phones'][0].']' . PHP_EOL . $message->getText() . PHP_EOL;
        $this->assertEquals($file, $file);
        $this->assertCount(0, $this->object->getErrors());
    }
    
    /**
     * @covers SmsZilla\Adapter\FileAdapter::setParams
     */
    public function testSetParams() {
        
        $this->object->setParams(['store_path' => __DIR__]);
        $this->assertEquals(__DIR__, $this->object->getParam('store_path'));
        
        $this->expectException(\SmsZilla\ConfigurationException::class);
        $this->object->setParams(['dummy' => 1]);
    }
}
