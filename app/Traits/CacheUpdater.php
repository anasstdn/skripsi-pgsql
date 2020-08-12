<?php
  namespace App\Traits;

  trait CacheUpdater
  {
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
          $model->updateCache();
          $model->updateCache1();
          $model->updateCache2();
        });

        static::deleted(function ($model) {
          $model->updateCache();
          $model->updateCache1();
          $model->updateCache2();
        });
    }

  }
?>
