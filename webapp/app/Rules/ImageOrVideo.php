<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class ImageOrVideo
{
    public function makeRule($max_image_mb = 1, $max_video_mb = 1)
    {
        return function ($attribute, $value, $fail) use ($max_image_mb, $max_video_mb) {
            $validator = Validator::make([], []);
            $is_image = $validator->validateImage($attribute, $value);
            $is_video = $validator->validateMimetypes($attribute, $value, [
                'video/avi', 'video/mpeg', 'video/quicktime'
            ]);

            if (!$is_video && !$is_image) {
                $fail('Uploads must be image or video.');
            }

            if ($is_image) {
                $is_valid = $validator->validateMax($attribute, $value, [$max_image_mb * 1024]);
                if (!$is_valid)
                    $fail("Images must be less than {$max_image_mb} megabytes.");
            }

            if ($is_video) {
                $is_valid = $validator->validateMax($attribute, $value, [$max_video_mb * 1024]);
                if (!$is_valid)
                    $fail("Videos must be less than {$max_video_mb} megabytes.");
            }
        };
    }
}
