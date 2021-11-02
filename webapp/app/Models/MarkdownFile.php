<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class MarkdownFile
{
    public $title = "";
    public $body = "";

    /**
     * Parses markdown files located in `resources/content`. The `.md` extension
     * is optional. Nested
     *
     * @var string $page_name Name of the file
     * @var string $directory Directory of the files. Defaults to `resources/content`.
     */
    public function __construct($page_name, $directory = 'resources/content')
    {
        $file_name = preg_match("/\.md$/", $page_name) ? $page_name : "{$page_name}.md";
        $traversal_safe_name = basename($file_name);
        $absolute_path = base_path("$directory/$traversal_safe_name");
        $parsed_file = YamlFrontMatter::parse(file_get_contents($absolute_path));

        $front_matter = $parsed_file->matter();
        $body = Str::of($parsed_file->body())->markdown()->__toString();

        $page = array_merge($front_matter, ["body" => $body]);

        foreach ($page as $property => $value) {
            $this->$property = $value;
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
