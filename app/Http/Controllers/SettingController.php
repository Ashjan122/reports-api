<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $setting = $request->user()->setting;

        if (!$setting) {
            return response()->json([]);
        }

        $data = $setting->toArray();

        foreach (['header_image', 'footer_image', 'stamp_image', 'signature_image'] as $field) {
            if (!empty($data[$field])) {
                $data[$field . '_url'] = Storage::disk('public')->url($data[$field]);
            }
        }

        return response()->json($data);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'lab_name'         => 'nullable|string|max:255',
            'lab_address'      => 'nullable|string|max:500',
            'lab_phone'        => 'nullable|string|max:50',
            'lab_email'        => 'nullable|email|max:255',
            'authorized_name'  => 'nullable|string|max:255',
            'authorized_title' => 'nullable|string|max:255',
            'header_image'     => 'nullable|image|max:4096',
            'footer_image'     => 'nullable|image|max:4096',
            'stamp_image'      => 'nullable|image|max:4096',
            'signature_image'  => 'nullable|image|max:4096',
        ]);

        $setting = $request->user()->setting ?? Setting::create(['user_id' => $request->user()->id]);

        $textFields = ['lab_name', 'lab_address', 'lab_phone', 'lab_email', 'authorized_name', 'authorized_title'];
        $update = [];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                $update[$field] = $request->input($field);
            }
        }

        foreach (['header_image', 'footer_image', 'stamp_image', 'signature_image'] as $field) {
            if ($request->hasFile($field)) {
                if ($setting->$field) {
                    Storage::disk('public')->delete($setting->$field);
                }
                $update[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        $setting->update($update);

        $data = $setting->fresh()->toArray();
        foreach (['header_image', 'footer_image', 'stamp_image', 'signature_image'] as $field) {
            if (!empty($data[$field])) {
                $data[$field . '_url'] = Storage::disk('public')->url($data[$field]);
            }
        }

        return response()->json($data);
    }
}
