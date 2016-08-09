<?php
namespace SmsZilla\Validator;

use SmsZilla\Validator\LibphonenumberValidator;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-09 at 22:47:47.
 */
class LibphonenumberValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LibphonenumberValidator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LibphonenumberValidator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers LibphonenumberValidator::__constructor
     */
    public function testConstruct() {
        $obj = new LibphonenumberValidator('pl');
        $this->assertEquals('PL', $obj->getDefaultRegion());
    }
    
    /**
     * @covers SmsZilla\Validator\LibphonenumberValidator::isValid
     */
    public function testIsValid()
    {
        $result = $this->object->isValid('+48603123456');
        $this->assertTrue($result);

        $result = $this->object->isValid('612125600');
        $this->assertFalse($result);

        $result = $this->object->isValid('41758180020');
        $this->assertFalse($result);

        $this->object->setDefaultRegion('CH');
        $result = $this->object->isValid('41758180020');
        $this->assertTrue($result);

        $this->object->setDefaultRegion('');
        $result = $this->object->isValid('+41758180020');
        $this->assertTrue($result);

        $result = $this->object->isValid('612125600');
        $this->assertFalse($result);
        
        $result = $this->object->isValid('+48612125600');
        $this->assertFalse($result);

        $result = $this->object->isValid('+48603125600');
        $this->assertTrue($result);
    }

    /**
     * @covers SmsZilla\Validator\LibphonenumberValidator::format
     * @expectedException \RuntimeException
     * @expectedExceptionMessage SmsZilla\Validator\LibphonenumberValidator::format() method must be called after SmsZilla\Validator\LibphonenumberValidator::isValid()
     */
    public function testFormat()
    {
        $result = $this->object->format('603123456');
        $this->assertEquals('48603123456', $result);
    }

    /**
     * @covers SmsZilla\Validator\LibphonenumberValidator::getMessages
     * @covers SmsZilla\Validator\LibphonenumberValidator::isValid
     * @covers SmsZilla\Validator\LibphonenumberValidator::parseNumber
     */
    public function testGetMessages()
    {
        $result = $this->object->isValid('Dummy number');
        $this->assertFalse($result);
        $messages = $this->object->getMessages();
        $this->assertContains('Phone number has wrong format and cannot be parsed', $messages);

        $result = $this->object->isValid('+48123546987');
        $this->assertFalse($result);
        $messages = $this->object->getMessages();
        $this->assertEquals('Phone number is not mobile', $messages[1]);

        $result = $this->object->isValid('13546987');
        $this->assertFalse($result);
        $messages = $this->object->getMessages();
        $this->assertEquals('Phone number is not valid', $messages[2]);
    }

    /**
     * @covers SmsZilla\Validator\LibphonenumberValidator::getDefaultRegion
     * @covers SmsZilla\Validator\LibphonenumberValidator::setDefaultRegion
     */
    public function testDefaultRegion()
    {
        $result = $this->object->getDefaultRegion();
        $this->assertEquals('PL', $result);
        $this->object->setDefaultRegion('CH');
        $result = $this->object->getDefaultRegion();
        $this->assertEquals('CH', $result);
    }

}
