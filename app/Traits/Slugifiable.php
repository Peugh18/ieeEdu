<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Slugifiable
{
    /**
     * Boot the trait and attach the saving event listener.
     */
    protected static function bootSlugifiable()
    {
        static::saving(function ($model) {
            $slugSource = $model->slug_source ?? 'title';
            
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->$slugSource);
                
                // Ensure uniqueness
                $count = static::whereRaw("slug RLIKE '^{$model->slug}(-[0-9]+)?$'")->count();
                if ($count > 0) {
                    $model->slug .= '-' . ($count + 1);
                }
            }
        });
    }

}
