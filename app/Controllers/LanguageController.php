<?php

namespace App\Controllers;

class LanguageController extends BaseController
{
    public function set()
    {
        $locale = $this->request->getPost('locale');
        if (! is_string($locale) || ! in_array($locale, ['id', 'en'], true)) {
            return redirect()->back()->with('languageError', 'Pilihan bahasa tidak valid.');
        }

        session()->set('locale', $locale);

        return redirect()->back();
    }
}
