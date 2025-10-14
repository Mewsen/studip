<?php

/**
 * Converts oembed tags to embedded content like the ckeditor does in its `previewInData` feature.
 * But we only store the oembed into the database and perform the transformation here (with purify)
 *
 * Basically replaced <oembed url="..."></oembed> with a styled iframe.
 * Currently only Youtube embed are supported
 * TODO CKEditor also supports several other embed, which could also be implemented
 */
class HTMLPurifier_Injector_OembedToIframe extends HTMLPurifier_Injector
{
    public $name = 'OEmbed';
    public $needed = ['div', 'iframe'];

    /** @var array[] mapping the corresponding embed url to all their valid urls */
    const VALID_URLS = [
        'https://www.youtube.com/embed/' => [
            // See https://github.com/ckeditor/ckeditor5/blob/master/packages/ckeditor5-media-embed/src/mediaembedediting.ts#L95
            '/^(?:m\.)?youtube\.com\/watch\?v=([\w-]+)(?:&t=(\d+))?/',
            '/^(?:m\.)?youtube\.com\/shorts\/([\w-]+)(?:\?t=(\d+))?/',
            '/^(?:m\.)?youtube\.com\/v\/([\w-]+)(?:\?t=(\d+))?/',
            '/^youtube\.com\/embed\/([\w-]+)(?:\?start=(\d+))?/',
            '/^youtu\.be\/([\w-]+)(?:\?t=(\d+))?/'
        ]
    ];

    public function handleElement(&$token)
    {
        if ($token->name !== 'oembed') {
            return;
        }

        $url = $token->attr['url'] ?? '';
        $embedUrl = self::toEmbedUrl($url);
        if (!$embedUrl) {
            $token = false;
            return;
        }

        // See https://github.com/ckeditor/ckeditor5/blob/master/packages/ckeditor5-media-embed/src/mediaembedediting.ts#L108
        // The css is in content.scss and matches the css of ckeditor5
        $token = [
            new HTMLPurifier_Token_Start('div', [
                'data-oembed-url' => $embedUrl
            ]),
            new HTMLPurifier_Token_Start('div', [
                'class' => 'ckeditor-embed-container'
            ]),
            new HTMLPurifier_Token_Empty('iframe', [
                'src' => $embedUrl,
                'class' => 'ckeditor-embed',
            ]),
            new HTMLPurifier_Token_End('div'),
            new HTMLPurifier_Token_End('div'),
        ];
    }

    /**
     * Transforms a valid url into a embed url
     * @param string $url
     * @return string|null
     */
    private static function toEmbedUrl(string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        $cleanUrl = preg_replace('/^https?:\/\/(?:www\.)?/', '', $url);
        foreach (self::VALID_URLS as $embedUrl => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $cleanUrl, $matches)) {
                    $videoId = $matches[1];
                    $timestamp = !empty($matches[2]) ? $matches[2] : null;

                    $full_url = $embedUrl . $videoId;
                    if ($timestamp) {
                        $full_url .= '?start=' . $timestamp;
                    }

                    return $full_url;
                }
            }
        }

        return null;
    }
}
