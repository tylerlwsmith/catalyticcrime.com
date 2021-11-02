<?php

namespace Tests\Unit;

use App\Models\UI\MetaTag;
use PHPUnit\Framework\TestCase;

class MetaTagTest extends TestCase
{
    /** @test */
    public function it_can_match_a_meta_tag_with_same_key_and_attribute_type()
    {
        $meta_tag_1 = new MetaTag('name', 'description', 'I am description 1');
        $meta_tag_2 = new MetaTag('name', 'description', 'I am description 2');

        $this->assertTrue($meta_tag_1->matchesTag($meta_tag_2));
    }

    /** @test */
    public function it_wont_match_a_meta_tag_with_different_key_and_attribute_type()
    {
        $meta_tag_1 = new MetaTag('name', 'title', 'I am a title');
        $meta_tag_2 = new MetaTag('name', 'description', 'I am a description');

        $this->assertFalse($meta_tag_1->matchesTag($meta_tag_2));
    }

    /** @test */
    public function it_throws_when_attribute_type_is_not_name_or_property()
    {
        $this->expectException(\Exception::class);
        new MetaTag('i_should_throw_exception', '');
    }
}
