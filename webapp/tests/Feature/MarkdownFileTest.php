<?php

namespace Tests\Feature;

use App\Models\MarkdownFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarkdownFileTest extends TestCase
{
    /** @test */
    public function it_can_use_files_that_end_with_md_extension()
    {
        $path = "testing-tmp";
        $page = "page.md";
        Storage::disk('local')->put('testing-tmp/page.md', trim("
---
title: Testing
---
Hello world.
        "));

        $markdown = new MarkdownFile($page, "storage/app/$path");

        $this->assertThat($markdown->title, $this->equalTo('Testing'));
    }

    /** @test */
    public function it_can_use_files_that_dont_end_with_md_extension()
    {
        $path = "testing-tmp";
        $page = "page";
        Storage::disk('local')->put('testing-tmp/page.md', trim("
---
title: Testing
---
Hello world.
        "));

        $markdown = new MarkdownFile($page, "storage/app/$path");

        $this->assertThat($markdown->title, $this->equalTo('Testing'));
    }

    /** @test */
    public function markdown_body_take_precedence_over_yml_body_attribute()
    {
        $path = "testing-tmp";
        $page = "page";
        Storage::disk('local')->put('testing-tmp/page.md', trim("
---
body: hello
---
world
        "));

        $markdown = new MarkdownFile($page, "storage/app/$path");

        $this->assertThat($markdown->body, $this->stringContains('world'));
    }
}
