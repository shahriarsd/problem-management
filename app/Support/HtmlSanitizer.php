<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

class HtmlSanitizer
{
    /**
     * Tags that are safe to store and render from the rich text editor.
     *
     * @var list<string>
     */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li',
        'a', 'h1', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre', 'hr',
    ];

    /**
     * Remove unsafe markup and attributes from editor HTML.
     */
    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', '', $html) ?? $html;
        $html = strip_tags($html, '<'.implode('><', self::ALLOWED_TAGS).'>');

        $document = new DOMDocument;

        $previousErrorSetting = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="sanitizer-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        $root = $document->getElementById('sanitizer-root')
            ?? $document->getElementsByTagName('div')->item(0);

        if (! $root) {
            return '';
        }

        foreach ($root->getElementsByTagName('*') as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($element->tagName);
            $href = $tag === 'a' ? trim($element->getAttribute('href')) : '';
            $listType = $tag === 'li' ? $element->getAttribute('data-list') : '';

            foreach (iterator_to_array($element->attributes) as $attribute) {
                $element->removeAttribute($attribute->name);
            }

            if ($tag === 'a' && preg_match('~^(https?://|mailto:|/|#)~i', $href) === 1) {
                $element->setAttribute('href', $href);
                $element->setAttribute('target', '_blank');
                $element->setAttribute('rel', 'noopener noreferrer');
            }

            if (in_array($listType, ['bullet', 'ordered'], true)) {
                $element->setAttribute('data-list', $listType);
            }
        }

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Determine whether the given HTML contains no readable content.
     */
    public static function isBlank(?string $html): bool
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        return trim($text) === '';
    }
}
