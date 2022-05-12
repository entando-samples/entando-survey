<?php

namespace App\Observers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

class AppSettingObserver
{
    /**
     * Handle the AppSetting "created" event.
     *
     * @param  \App\Models\AppSetting  $appSetting
     * @return void
     */
    public function created(AppSetting $appSetting)
    {
        //
    }

    /**
     * Handle the AppSetting "updated" event.
     *
     * @param  \App\Models\AppSetting  $appSetting
     * @return void
     */
    public function updated(AppSetting $appSetting)
    {
        Cache::forget('appsetting');
    }

    /**
     * Handle the AppSetting "deleted" event.
     *
     * @param  \App\Models\AppSetting  $appSetting
     * @return void
     */
    public function deleted(AppSetting $appSetting)
    {
        //
    }

    /**
     * Handle the AppSetting "restored" event.
     *
     * @param  \App\Models\AppSetting  $appSetting
     * @return void
     */
    public function restored(AppSetting $appSetting)
    {
        //
    }

    /**
     * Handle the AppSetting "force deleted" event.
     *
     * @param  \App\Models\AppSetting  $appSetting
     * @return void
     */
    public function forceDeleted(AppSetting $appSetting)
    {
        //
    }
}
