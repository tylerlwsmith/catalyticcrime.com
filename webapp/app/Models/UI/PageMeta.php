<?php

namespace App\Models\UI;

use Countable;
use Iterator;

class PageMeta implements Iterator, Countable
{
    protected $iteratorPosition = 0;

    public $pageTitle;
    public $pageMetaTags;
    public $siteInPageTitle = true;

    public function __construct($pageTitle, $pageMetaTags = [])
    {
        $this->pageTitle = $pageTitle;
        $this->metaTags = $this->generateTags($pageTitle, $pageMetaTags);
    }

    protected function generateTags($pageTitle, $pageMetaTags)
    {
        $description = $this->getDescriptionTag($pageMetaTags)->content ??
            "Tracking Bakersfield's catalytic converter thefts.";

        $default_tags = [
            MetaTag::description($description),
            MetaTag::image('catalytic-crime-og.png'),
            MetaTag::noIndex(),
            new MetaTag('property', 'og:locale', 'en_US'),
            new MetaTag('property', 'og:title', $pageTitle),
            new MetaTag('property', 'og:description', $description),
            new MetaTag('property', 'og:url', request()->url()),
            new MetaTag('property', 'og:site_name', env('APP_NAME')),
            new MetaTag('name', 'twitter:title', $pageTitle),
            new MetaTag('name', 'twitter:description', $description),
            new MetaTag('name', 'twitter:card', 'summary_large_image'),
            // <meta name="twitter:image" content="https://handsofrespect.com/wp-content/uploads/2017/03/smiling-pin.jpg" />
        ];

        $final_tags = collect($default_tags)
            ->flatten()
            ->filter(function (MetaTag $defaultTag) use ($pageMetaTags) {
                foreach (collect($pageMetaTags)->flatten() as $tag) {
                    if ($tag->matchesTag($defaultTag)) return false;
                }
                return true;
            })
            ->merge($pageMetaTags)
            ->sortBy([
                fn ($a, $b) => $a->attributeType <=> $b->attributeType,
                fn ($a, $b) => $a->key <=> $b->key,
            ])
            ->toArray();

        return $final_tags;
    }

    protected function getDescriptionTag($tags)
    {
        return collect($tags)->first(function (MetaTag $tag, $_key) {
            return $tag->matchesTag(MetaTag::description());
        });
    }

    public function excludeSiteInTitle()
    {
        $this->siteInPageTitle = false;
        return $this;
    }

    public function getPageTitle()
    {
        return $this->siteInPageTitle
            ? $this->pageTitle . " | " . env('APP_NAME')
            : $this->pageTitle;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->metaTags[$this->iteratorPosition];
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    public function next()
    {
        ++$this->iteratorPosition;
    }

    public function valid()
    {
        return isset($this->metaTags[$this->iteratorPosition]);
    }

    public function count()
    {
        return count($this->metaTags);
    }
}
