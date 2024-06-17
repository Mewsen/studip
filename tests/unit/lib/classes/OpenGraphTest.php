<?php
/**
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 */
class OpenGraphTest extends \Codeception\Test\Unit
{
    public function setUp(): void
    {
        static $config = [
            'OPENGRAPH_ENABLE'    => true,
        ];

        Config::set(new Config($config));
    }

    public function testURLExtraction()
    {
        $text = 'this is a link: https://example.org?foo=bar&bar=foo - believe it or not';
        $urls = OpenGraph::extractUrlsFromText($text);

        $this->assertCount(1, $urls);
        $this->assertEquals('https://example.org?foo=bar&bar=foo', $urls[0]);
    }

    public function testURLExtractionFromHTML()
    {
        $html = Studip\Markup::HTML_MARKER . '<a href="https://example.org?foo=bar&amp;bar=foo">this is a link</a> - believe it or not';
        $urls = OpenGraph::extractUrlsFromHtml($html);

        $this->assertCount(1, $urls);
        $this->assertEquals('https://example.org?foo=bar&bar=foo', $urls[0]);
    }
}
