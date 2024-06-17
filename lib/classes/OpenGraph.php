<?php
/**
 * Open Graph class that extracts open graph urls from a given string.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 3.4
 */
class OpenGraph
{
    /**
     * Extracts urls and their according open graph infos from a given string
     *
     * @param string $string Text to extract urls and open graph infos from
     * @return OpenGraphURLCollection containing the extracted urls
     */
    public static function extract(string $string): OpenGraphURLCollection
    {
        $collection = new OpenGraphURLCollection();

        if (!Config::get()->OPENGRAPH_ENABLE) {
            return $collection;
        }

        if (Studip\Markup::isHtml($string)) {
            $urls = self::extractUrlsFromHtml($string);
        } else {
            $urls = self::extractUrlsFromText($string);
        }

        foreach ($urls as $url) {
            $og_url = OpenGraphURL::fromURL($url);
            if ($og_url && !$collection->find($og_url->id)) {
                $og_url->store();

                $collection[] = $og_url;
            }
        }

        return $collection;
    }

    public static function filterURLs(array $urls): array
    {
        return array_filter($urls, function (string $url): bool {
            if (!$url) {
                return false;
            }

            return !isLinkIntern($url);
        });
    }

    public static function extractUrlsFromText(string $text): array
    {
        $regexp = StudipCoreFormat::getStudipMarkup('links')['start'];
        preg_match_all('/' . $regexp . '/ums', $text, $matches, PREG_SET_ORDER);
        $urls = array_column($matches, 2);

        return self::filterURLs($urls);
    }

    public static function extractUrlsFromHtml(string $html): array
    {
        $document = new DOMDocument();
        $document->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);

        $elements = $document->getElementsByTagName('a');

        $urls = [];
        foreach ($elements as $element) {
            if (!$element->hasAttribute('href')) {
                continue;
            }

            $urls[] = $element->getAttribute('href');
        }

        return self::filterURLs($urls);
    }
}
