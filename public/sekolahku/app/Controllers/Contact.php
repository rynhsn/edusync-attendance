<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\ContactMessageModel;

class Contact extends BaseController
{
    /**
     * Display the contact page.
     *
     * @return string
     */
    public function index()
    {
        $this->cachePage(5); // cache page for 5 minutes

        $settingModel = new SettingModel();

        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'site_logo_text', 'site_logo_icon',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'theme_color',
            'page_banners',
        ]);

        $data = [
            'site_name'        => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'     => $settings['site_tagline'] ?? '',
            'site_description' => $settings['site_description'] ?? '',
            'site_logo_text'   => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'   => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'    => $settings['contact_phone'] ?? '',
            'contact_email'    => $settings['contact_email'] ?? '',
            'contact_address'  => $settings['contact_address'] ?? '',
            'contact_hours'    => $settings['contact_hours'] ?? '',
            'social_facebook'  => $settings['social_facebook'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_youtube'   => $settings['social_youtube'] ?? '',
            'social_tiktok'    => $settings['social_tiktok'] ?? '',
            'footer_description' => $settings['footer_description'] ?? '',
            'footer_copyright'   => $settings['footer_copyright'] ?? '',
            'footer_services'    => $settings['footer_services'] ?? '',
            'footer_links'       => $settings['footer_links'] ?? '',
            'theme_color'       => $settings['theme_color'] ?? 'blue',
            'page_banners'      => $settings['page_banners'] ?? null,
            'page_title'       => 'Kontak - ' . ($settings['site_name'] ?? ''),
        ];

        return view('pages/contact', $data);
    }

    /**
     * Process the contact form submission.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
     */
    public function send()
    {
        // Honeypot check
        if (!empty($this->request->getPost('website'))) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your message has been sent.',
            ]);
        }

        // Rate limiting — max 1 message per 30 seconds
        $last = session()->get('contact_last_sent');
        if ($last && (time() - $last) < 30) {
            $wait = 30 - (time() - $last);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors'  => ['Tunggu ' . $wait . ' detik sebelum mengirim lagi.'],
                ]);
            }
            return redirect()->back()->with('error', 'Tunggu ' . $wait . ' detik sebelum mengirim lagi.');
        }

        $rules = [
            'name'    => 'required|min_length[3]|max_length[100]',
            'email'   => 'required|valid_email',
            'subject' => 'required|min_length[3]|max_length[200]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors'  => $this->validator->getErrors(),
                ]);
            }

            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contactModel = new ContactMessageModel();
        $contactModel->insert([
            'name'    => strip_tags($this->request->getPost('name')),
            'email'   => strip_tags($this->request->getPost('email')),
            'subject' => strip_tags($this->request->getPost('subject')),
            'message' => strip_tags($this->request->getPost('message')),
            'is_read' => 0,
        ]);

        session()->set('contact_last_sent', time());

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesan berhasil dikirim. Kami akan menghubungi Anda segera.',
            ]);
        }

        return redirect()->back()->with('success', 'Pesan berhasil dikirim. Kami akan menghubungi Anda segera.');
    }
}
