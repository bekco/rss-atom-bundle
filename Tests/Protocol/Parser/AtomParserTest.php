<?php

namespace Debril\RssAtomBundle\Protocol\Parser;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-27 at 00:26:35.
 */
class AtomParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AtomParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AtomParser;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::parse
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::canHandle
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testCannotHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-rss.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertFalse($this->object->canHandle($xmlBody));
        $this->object->parse($xmlBody, new FeedContent, new \DateTime);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::canHandle
     */
    public function testCanHandle()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->assertTrue($this->object->canHandle($xmlBody));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::checkBodyStructure
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testParseError()
    {
        $file = dirname(__FILE__) . '/../../../Resources/truncated-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));
        $this->object->parse($xmlBody, new FeedContent, new \DateTime);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::parse
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::parseBody
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::parseContent
     */
    public function testParse()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-atom.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat("Y-m-d", "2002-10-10");
        $feed = $this->object->parse($xmlBody, new FeedContent, $date);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedIn", $feed);

        $this->assertNotNull($feed->getId(), "feed->getId() should not return an empty value");
        $this->assertGreaterThan(0, $feed->getItemsCount());
        $this->assertInstanceOf("\DateTime", $feed->getLastModified());
        $this->assertNotNull($feed->getLink());
        $this->assertInternalType("string", $feed->getLink());
        $this->assertNotNull($feed->getDescription());
        $this->assertNotNull($feed->getTitle());

        $item = current($feed->getItems());
        $this->assertEquals('John Doe', $item->getAuthor());
        $this->assertEquals(\Debril\RssAtomBundle\Protocol\AtomItem::XHTML, $item->getContentType());
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::setDateFormats
     * @covers Debril\RssAtomBundle\Protocol\Parser\AtomParser::__construct
     */
    public function testSetDateFormats()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);
        $this->assertEquals($default, $this->readAttribute($this->object, 'dateFormats'));
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::guessDateFormat
     */
    public function testGuessDateFormat()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);

        $date = '2003-12-13T18:30:02Z';
        $format = $this->object->guessDateFormat($date);

        $this->assertEquals(\DateTime::RFC3339, $format);
    }

    /**
     * @covers Debril\RssAtomBundle\Protocol\Parser::guessDateFormat
     * @expectedException Debril\RssAtomBundle\Protocol\Parser\ParserException
     */
    public function testGuessDateFormatException()
    {
        $default = array(
            \DateTime::RFC3339,
            \DateTime::RSS,
        );

        $this->object->setdateFormats($default);

        $date = '2003-13T18:30:02Z';
        $format = $this->object->guessDateFormat($date);

        $this->assertEquals(\DateTime::RFC3339, $format);
    }

    /**
     *
     */
    public function testHtmlContent()
    {
        $file = dirname(__FILE__) . '/../../../Resources/sample-atom-html.xml';
        $xmlBody = new \SimpleXMLElement(file_get_contents($file));

        $date = \DateTime::createFromFormat("Y-m-d", "2002-10-10");
        $feed = $this->object->parse($xmlBody, new FeedContent, $date);

        $this->assertInstanceOf("Debril\RssAtomBundle\Protocol\FeedIn", $feed);
        $item = current($feed->getItems());

        $this->assertTrue(strlen($item->getDescription()) > 0);
    }

}
