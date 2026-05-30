<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCompanySettingsRequest;
use App\Services\SiteSettingsService;
use Inertia\Inertia;

class CompanySettingsController extends Controller
{
    public function edit()
    {
        return Inertia::render('admin/CompanySettings', [
            'settings' => SiteSettingsService::sharedForInertia(),
        ]);
    }

    public function update(UpdateCompanySettingsRequest $request)
    {
        SiteSettingsService::update($request->validated());

        return back()->with('success', 'Configuración de empresa actualizada.');
    }
}
