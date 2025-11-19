<?php

namespace App\Http\Controllers;

use App\Models\KokurikulerDimension;
use App\Models\KokurikulerTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KokurikulerSettingController extends Controller
{
    public function updateDimension(Request $request, KokurikulerDimension $dimension)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isWaliKelas()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $dimension->update([
            'name' => Str::lower($validated['name']),
            'description' => Str::lower($validated['description']),
        ]);

        return response()->json(['success' => 'Dimensi berhasil diperbarui.']);
    }

    public function updateTemplates(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isWaliKelas()) {
            abort(403);
        }

        $validated = $request->validate([
            'template_tinggi' => 'required|string',
            'template_rendah' => 'required|string',
        ]);

        KokurikulerTemplate::updateOrCreate(
            ['level' => 'tinggi'],
            ['template_text' => $validated['template_tinggi']]
        );
        KokurikulerTemplate::updateOrCreate(
            ['level' => 'rendah'],
            ['template_text' => $validated['template_rendah']]
        );

        return response()->json(['success' => 'Template nilai berhasil diperbarui.']);
    }
}

