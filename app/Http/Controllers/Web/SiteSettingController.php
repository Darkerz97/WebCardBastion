<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('site-settings.edit', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(SiteSettingRequest $request): RedirectResponse
    {
        $settings = SiteSetting::current();
        $settings->update($request->validated());

        return redirect()
            ->route('site-settings.edit')
            ->with('success', 'Contenido y personalizacion del sitio actualizados correctamente.');
    }
}
