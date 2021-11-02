<?php

namespace App\Models\UI;

use Illuminate\Support\Facades\Storage;

class MetaTag
{
    /**
     * @var string The identifying key for determining the uniqueness of a tag.
     *             On the tag `<meta name="description" content="Website." />`,
     *             the key would be `description`.
     */
    public $key;

    /**
     * @var "name"|"property" The attribute type for the key. On the tag
     *                        `<meta name="description" content="Website." />`,
     *                        the `attributeType` would be `name`.
     */
    public $attributeType;

    /** @var string Meta tag content */
    public $content;

    /**
     * @param "name"|"property" $attributeType
     * @param string $key
     * @param string $content
     */
    public function __construct($attributeType, $key, $content = "")
    {
        if (!in_array($attributeType, ['name', 'property'])) {
            throw new \Exception('Attribute type must be `name` or `property`.');
        }

        $this->attributeType = $attributeType;
        $this->key = trim(strtolower($key));
        $this->content = $content;
    }

    public function matches($attributeType, $key)
    {
        if (!in_array($attributeType, ['name', 'property'])) {
            throw new \Exception('Attribute type must be `name` or `property`.');
        }

        return $this->key === trim(strtolower($key))
            && $this->attributeType === $attributeType;
    }

    public function matchesTag(MetaTag $metaTag)
    {
        return $this->matches($metaTag->attributeType, $metaTag->key);
    }

    /**
     * Static factory method for creating description tags.
     */
    public static function description($description = "", $appendSiteName = true)
    {

        return new self('name', 'description', $description);
    }

    /**
     * Static factory method for creating description tags.
     */
    public static function noIndex()
    {
        return new self('name', 'robots', 'noindex');
    }

    // https://stackoverflow.com/questions/66531379/file-get-contents-failed-to-open-stream-cannot-assign-requested-address
    public static function image($path)
    {
        $relative_path = Storage::url($path);
        // dd($path, $file_url);
        // TODO: cache this so we don't need to make a network call every time.
        // TODO: make this work with S3 conditionally
        // https://www.php.net/manual/en/function.getimagesize.php

        try {
            list($width, $height) = getimagesize("http://webserver:8080$relative_path");

            $url = url($relative_path);

            return [
                new self('name', 'twitter:image', $url),
                new self('property', 'og:image', $url),
                new self('property', 'og:image:height', $height),
                new self('property', 'og:image:width', $width)
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
