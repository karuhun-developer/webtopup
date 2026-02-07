<?php

namespace App\Actions\Cms\Setting\Setting;

use App\Models\Setting\Setting;
use App\Traits\WithSaveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class SaveSettingAction
{
    use WithSaveFile;

    /**
     * Handle the action.
     */
    public function handle(array $data): bool
    {
        // Forget the cache for global settings to ensure that the updated settings are reflected immediately.
        Cache::forget('global:settings');

        $setting = Setting::first();

        if ($data['value']['manual_transfer_bank_logo'] ?? null instanceof UploadedFile) {
            $file = $this->saveFile(
                file: $data['value']['manual_transfer_bank_logo'],
                base_file_name: 'transfer_bank_logo_'.time(),
            );
            $data['value']['manual_transfer_bank_logo'] = '/storage/'.$file;
        }

        return $setting->update([
            'value' => [
                ...$setting->value,
                ...$data['value'],
            ],
        ]);
    }
}
