<?php

namespace Schlaus\Tagline\Test;

use Schlaus\Tagline\Tagline;

use InvalidArgumentException;
use LogicException;

/**
 * @coversDefaultClass \Schlaus\Tagline\Tagline
 */
class TaglineTest extends \PHPUnit_Framework_TestCase
{

    protected $resources;

    protected function setup()
    {
        $this->resources = realpath(dirname(__FILE__).'/resources');
    }

    /**
     * @covers ::findTag
     * @expectedException LogicException
     */
    public function testFindWithoutInputWillFail()
    {
        Tagline::findTag('sometag');
    }

    /**
     * @covers ::findTag
     * @covers ::anyToArray
     * @expectedException InvalidArgumentException
     */
    public function testFindWithInvalidFileWillFail()
    {
        Tagline::findTag('sometag', $this->resources.'/testfilex');
    }

    /**
     * @covers ::findTag
     * @covers ::findNext
     * @covers ::anyToArray
     */
    public function testFindWithFilenameWorks()
    {
        $tagOnLine = Tagline::findTag('sometag', $this->resources.'/testfile1');

        $this->assertEquals(3, $tagOnLine);
    }

    /**
     * @covers ::findTag
     * @covers ::findNext
     * @covers ::anyToArray
     */
    public function testFindWithStreamWorks()
    {
        $handle = fopen($this->resources.'/testfile1', 'a+');
        $tagOnLine = Tagline::findTag('testtag', $handle);
        fclose($handle);

        $this->assertEquals(2, $tagOnLine);
    }

    /**
     * @covers ::findTag
     * @covers ::findNext
     * @covers ::anyToArray
     */
    public function testFindWithFileObjectWorks()
    {
        $object = new \SplFileObject($this->resources.'/testfile1');
        $tagOnLine = Tagline::findTag('testtag', $object);

        $this->assertEquals(2, $tagOnLine);
    }

    /**
     * @covers ::findTag
     * @covers ::anyToArray
     * @expectedException InvalidArgumentException
     */
    public function testFindWithStreamInWrongModeWillFail()
    {
        $handle = fopen($this->resources.'/testfile1', 'a');
        Tagline::findTag('testtag', $handle);
        fclose($handle);
    }

    /**
     * @covers ::findNext
     */
    public function testFindNextWorks()
    {
        Tagline::findTag('testtag', $this->resources.'/testfile1');
        $tagOnLine = Tagline::findNext();


        $this->assertEquals(4, $tagOnLine);
    }

    /**
     * @covers ::anyToArray
     */
    public function testArrayToArrayConversionWorks()
    {
        $subject = array(1,2,3);
        $result  = Tagline::anyToArray($subject);

        $this->assertEquals($subject, $result);
    }

    /**
     * @covers ::anyToArray
     */
    public function testStringToArrayConversionWorks()
    {
        $expected = array('Hello', 'World!');

        $subject = "Hello\r\nWorld!";
        $result  = Tagline::anyToArray($subject);

        $this->assertEquals($expected, $result);

        $subject = "Hello\nWorld!";
        $result  = Tagline::anyToArray($subject);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers ::anyToArray
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCanForceWrongType()
    {
        $result = Tagline::anyToArray(array(), 'string');

        $this->assertFalse($result);
    }

    /**
     * @coversNothing
     */
    public function testUsingOffsetWorks()
    {
        $result = Tagline::findTag('testtag', $this->resources.'/testfile1', 2);

        $this->assertEquals(4, $result);
    }
}
