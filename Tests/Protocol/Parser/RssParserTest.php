<?php

namespace Debril\RssAtomBundle\Protocol\Parser;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-27 at 00:26:56.
 */
class RssParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RssParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RssParser;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RssParser::canHandle
     */
    public function testCannotHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertFalse( $this->object->canHandle($xmlBody) );
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RssParser::canHandle
     */
    public function testCanHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertTrue( $this->object->canHandle($xmlBody) );
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RssParser::checkBodyStructure
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testParseError()
    {
        $file = dirname(__FILE__) . '/../../../Resources/truncated-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->object->parse($xmlBody);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\RssParser::parseBody
     */
    public function testParse()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $feed = $this->object->parse($xmlBody);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedContent", $feed);

        $this->assertNotNull($feed->getId(), "feed->getId() should not return an empty value");

        $this->assertGreaterThan(0, $feed->getItemsCount());
        $this->assertInstanceOf("\DateTime", $feed->getLastModified());
        $this->assertNotNull($feed->getLink());
        $this->assertNotNull($feed->getTitle());
    }


}