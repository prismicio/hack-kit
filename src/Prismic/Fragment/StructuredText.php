<?hh // strict

/*
 * This file is part of the Prismic hack SDK
 *
 * Copyright 2013 Zengularity (http://www.zengularity.com).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prismic\Fragment;

use Prismic\LinkResolver;
use Prismic\Fragment\ImageView;
use Prismic\Fragment\Block\EmbedBlock;
use Prismic\Fragment\Block\HeadingBlock;
use Prismic\Fragment\Block\ImageBlock;
use Prismic\Fragment\Block\ListItemBlock;
use Prismic\Fragment\Block\ParagraphBlock;
use Prismic\Fragment\Block\PreformattedBlock;
use Prismic\Fragment\Block\BlockInterface;
use Prismic\Fragment\Link\DocumentLink;
use Prismic\Fragment\Link\MediaLink;
use Prismic\Fragment\Link\WebLink;
use Prismic\Fragment\Link\LinkInterface;
use Prismic\Fragment\Span\EmSpan;
use Prismic\Fragment\Span\HyperlinkSpan;
use Prismic\Fragment\Span\StrongSpan;
use Prismic\Fragment\Span\SpanInterface;

class StructuredText implements FragmentInterface
{
    private ImmVector<BlockInterface> $blocks;

    public function __construct(ImmVector<BlockInterface> $blocks)
    {
        $this->blocks = $blocks;
    }

    public function getBlocks(): ImmVector<BlockInterface>
    {
        return $this->blocks;
    }

    public function getFirstPreformatted(): ?PreformattedBlock
    {
        foreach ($this->blocks as $block) {
            if ($block)
                if($block instanceof PreformattedBlock) {
                    return $block;
                }
        }
        return null;
    }

    public function getFirstParagraph(): ?ParagraphBlock
    {
        foreach ($this->blocks as $block) {
            if ($block && $block instanceof ParagraphBlock) {
                return $block;
            }
        }
        return null;
    }

    public function getFirstImage(): ?ImageBlock
    {
        foreach ($this->blocks as $block) {
            if ($block && $block instanceof ImageBlock) {
                return $block;
            }
        }
        return null;
    }

    public function asHtml(?LinkResolver $linkResolver = null): string
    {
        $groups = Vector {};
        foreach ($this->blocks as $block) {
            $count = count($groups);
            if ($count > 0) {
                $lastOne = $groups[$count - 1];
                if ('ul' == $lastOne->getTag() && ($block instanceof ListItemBlock) && !$block->isOrdered()) {
                    $lastOne->addBlock($block);
                } elseif ('ol' == $lastOne->getTag() && ($block instanceof ListItemBlock) && $block->isOrdered()) {
                    $lastOne->addBlock($block);
                } elseif (($block instanceof ListItemBlock) && !$block->isOrdered()) {
                    $newBlockGroup = new BlockGroup("ul", new ImmVector(array($block)));
                    $groups->add($newBlockGroup);
                } else {
                    if (($block instanceof ListItemBlock) && $block->isOrdered()) {
                        $newBlockGroup = new BlockGroup("ol", new ImmVector(array($block)));
                        $groups->add($newBlockGroup);
                    } else {
                        $newBlockGroup = new BlockGroup(null, new ImmVector(array($block)));
                        $groups->add($newBlockGroup);
                    }
                }
            } else {
                $newBlockGroup = new BlockGroup(null, new ImmVector(array($block)));
                $groups->add($newBlockGroup);
            }
        }
        $html = "";
        foreach ($groups as $group) {
            $maybeTag = $group->getTag();
            if (!is_null($maybeTag)) {
                $html = $html . "<" . $group->getTag() . ">";
                foreach ($group->getBlocks() as $block) {
                    $html = $html . StructuredText::asHtmlBlock($block, $linkResolver);
                }
                $html = $html . "</" . $group->getTag() . ">";
            } else {
                foreach ($group->getBlocks() as $block) {
                    $html = $html . StructuredText::asHtmlBlock($block, $linkResolver);
                }
            }
        }

        return $html;
    }

    public static function asHtmlBlock(BlockInterface $block, ?LinkResolver $linkResolver = null): string
    {
        if ($block instanceof HeadingBlock) {
            return nl2br('<h' . $block->getLevel() . '>' .
                    StructuredText::asHtmlText($block->getText(), $block->getSpans(), $linkResolver) .
                    '</h' . $block->getLevel() . '>');
        } elseif ($block instanceof ParagraphBlock) {
            return nl2br('<p>' .
                   StructuredText::asHtmlText($block->getText(), $block->getSpans(), $linkResolver) . '</p>');
        } elseif ($block instanceof ListItemBlock) {
            return nl2br('<li>' .
                   StructuredText::asHtmlText($block->getText(), $block->getSpans(), $linkResolver) . '</li>');
        } elseif ($block instanceof ImageBlock) {
            return nl2br('<p>' . $block->getView()->asHtml($linkResolver) . '</p>');
        } elseif ($block instanceof EmbedBlock) {
            return nl2br($block->getObj()->asHtml());
        } elseif ($block instanceof PreformattedBlock) {
            return '<pre>' . StructuredText::asHtmlText($block->getText(), $block->getSpans(), $linkResolver) .'</pre>';
        }

        return "";
    }

    public static function asHtmlText(string $text, ImmVector<SpanInterface> $spans, ?LinkResolver $linkResolver = null): ?string
    {
        if ($spans->isEmpty()) {
            return htmlspecialchars($text);
        }

        $doc = new \DOMDocument();

        $doc->appendChild($doc->createTextNode($text));

        foreach ($spans as $span) {
            if ($span->getEnd() < $span->getStart()) {
                continue;
            }
            self::asHtmlTextChildren($doc, 0, $span, $linkResolver);
        }

        return trim($doc->saveHTML());

    }

    private static function asHtmlTextChildren(\DOMNode $node, int $start, SpanInterface $span, ?LinkResolver $linkResolver): ?string {

        $nodeLength = mb_strlen($node->textContent);

        // If this is a text node we have found the right node
        if ($node instanceof \DOMText) {

            if ($span->getEnd() - $span->getStart() > $nodeLength) {
                return null;
            }

            // Split the text node into a head, meat and tail
            $meat = $node->splitText($span->getStart() - $start);
            $tail = $meat->splitText($span->getEnd() - $span->getStart());

            // Decide element type and attributes based on span class
            $attributes = array();
            if ($span instanceof StrongSpan) {
                $nodeName = 'strong';
            } else if ($span instanceof EmSpan) {
                $nodeName = 'em';
            } else if ($span instanceof HyperlinkSpan) {
                $nodeName = 'a';
                $link = $span->getLink();
                if ($link instanceof DocumentLink) {
                    $attributes['href'] = $linkResolver ? $linkResolver->resolve($link) : '';
                } elseif($link instanceof MediaLink) {
                    $attributes['href'] = $link->getUrl();
                } elseif($link instanceof WebLink) {
                    $attributes['href'] = $link->getUrl();
                }
            } else {
                $nodeName = 'span';
            }

            // Make the new span element, and put the text from the meat
            // inside
            $spanNode = $node->ownerDocument->createElement($nodeName, htmlspecialchars($meat->textContent));
            foreach ($attributes as $k => $v) {
                $spanNode->setAttribute($k, $v);
            }

            // Replace the original meat text node with the span
            $meat->parentNode->replaceChild($spanNode, $meat);

            return null;
        }

        // Skip this node if the span start is beyond it
        if ($span->getStart() >= $start + mb_strlen($node->textContent)) {
            return null;
        }

        // Loop over child nodes to find the correct one
        if ($node->childNodes) {
            foreach ($node->childNodes as $child) {
                $nodeLength = mb_strlen($child->textContent);
                if ($span->getStart() < $start + $nodeLength) {
                    // This is the right node -- recurse
                    return self::asHtmlTextChildren($child, $start, $span);
                }
                $start += $nodeLength;
            }
        }

        // Not found
        return null;
    }

    public static function parseSpan($json): ?SpanInterface
    {
        $type = $json->type;
        $start = $json->start;
        $end = $json->end;

        if ("strong" == $type) {
            return new StrongSpan($start, $end);
        }

        if ("em" == $type) {
            return new EmSpan($start, $end);
        }

        $link = false;
        if ("hyperlink" == $type) {
            $linkType = $json->data->type;
            if ("Link.web" == $linkType) {
                $link = WebLink::parse($json->data->value);
            } elseif ("Link.document" == $linkType) {
                $link = DocumentLink::parse($json->data->value);
            } elseif ("Link.file" == $linkType) {
                $link = MediaLink::parse($json->data->value);
            }
        }

        if ($link) {
            return new HyperlinkSpan($start, $end, $link);
        }

        return null;
    }

    public static function parseText($json): ParsedText
    {
        $text = $json->text;
        $spans = new Vector();
        foreach ($json->spans as $spanJson) {
            $span = StructuredText::parseSpan($spanJson);
            if (isset($span)) {
                $spans->add($span);
            }
        }

        return new ParsedText($text, $spans->toImmVector());
    }

    public static function parseBlock($json): ?BlockInterface
    {
        if ($json->type == 'heading1') {
            $p = StructuredText::parseText($json);

            return new HeadingBlock($p->getText(), $p->getSpans(), 1);
        }

        if ($json->type == 'heading2') {
            $p = StructuredText::parseText($json);

            return new HeadingBlock($p->getText(), $p->getSpans(), 2);
        }

        if ($json->type == 'heading3') {
            $p = StructuredText::parseText($json);

            return new HeadingBlock($p->getText(), $p->getSpans(), 3);
        }

        if ($json->type == 'heading4') {
            $p = StructuredText::parseText($json);

            return new HeadingBlock($p->getText(), $p->getSpans(), 4);
        }

        if ($json->type == 'paragraph') {
            $p = StructuredText::parseText($json);

            return new ParagraphBlock($p->getText(), $p->getSpans());
        }

        if ($json->type == 'list-item') {
            $p = StructuredText::parseText($json);

            return new ListItemBlock($p->getText(), $p->getSpans(), false);
        }

        if ($json->type == 'o-list-item') {
            $p = StructuredText::parseText($json);

            return new ListItemBlock($p->getText(), $p->getSpans(), true);
        }

        if ($json->type == 'image') {
            $view = ImageView::parse($json);

            return new ImageBlock($view);
        }

        if ($json->type == 'embed') {
            return new EmbedBlock(Embed::parse($json));
        }

        if ($json->type == 'preformatted') {
            return new PreformattedBlock($json->text, new ImmVector($json->spans));
        }

        return null;
    }

    public static function parse(ImmMap<string, mixed> $json): StructuredText
    {
        $blocks = new Vector();
        foreach ($json as $blockJson) {
            $maybeBlock = StructuredText::parseBlock($blockJson);
            if (isset($maybeBlock)) {
                $blocks->add($maybeBlock);
            }
        }

        return new StructuredText($blocks->toImmVector());
    }
}
