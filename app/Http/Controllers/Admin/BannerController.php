<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('section')->orderBy('order')->get();
        return Inertia::render('admin/Banners', [
            'dbBanners' => $banners
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string',
            'order' => 'required|integer',
            'heading' => 'nullable|string',
            'subheading' => 'nullable|string',
            'button_text' => 'nullable|string',
            'button_link' => 'nullable|string',
            'position' => 'nullable|string',
            'show_text' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $banner = Banner::where('section', $validated['section'])
                        ->where('order', $validated['order'])
                        ->first();

        if (!$banner) {
            $banner = new Banner();
            $banner->section = $validated['section'];
            $banner->order = $validated['order'];
        }

        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $path = $request->file('image')->store('banners', 'public');
            $banner->image_path = '/storage/' . $path;
        }

        $banner->heading = $validated['heading'];
        $banner->subheading = $validated['subheading'];
        $banner->button_text = $validated['button_text'];
        $banner->button_link = $validated['button_link'];
        $banner->position = $validated['position'] ?? 'center';
        $banner->show_text = $request->input('show_text', '1') === '1';
        $banner->save();

        return redirect()->back()->with('success', 'Banner actualizado correctamente.');
    }
}
