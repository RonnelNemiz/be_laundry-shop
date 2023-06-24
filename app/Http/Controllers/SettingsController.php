<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class SettingsController extends Controller
{
    public function changeSMSSettings($status)
    {
        $smsSetting = Setting::where('name', 'SMS')->first();

        if ($smsSetting) {
            $smsSetting->update([
                'value' => $status
            ]);
            $smsSetting->save();

            if ($smsSetting->save()) {
                return response()->json([
                    'status' => 200,
                    'message' => 'SMS Setting Saved!'
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'error' => $smsSetting->getError()
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'error' => $smsSetting->getError()
            ]);
        }
    }

    public function getSMSStatus()
    {
        $status = Setting::where('name', 'SMS')->first();

        return response()->json([
            'status' => 200,
            'sms_status' => $status
        ]);
    }
}
