<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\FrontService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(private FrontService $front)
    {
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'contactName' => 'required|max:50',
                'contactEmail' => 'required|max:50',
                'contactHP' => 'required|max:20',
                'contactDesc' => 'required|max:500',
            ]);

            Contact::create([
                'contact_name' => $data['contactName'],
                'contact_email' => $data['contactEmail'],
                'contact_hp' => $data['contactHP'],
                'contact_desc' => $data['contactDesc'],
            ]);

            return redirect(site_url('contact'))->with('msg_flash', success_message('Terima kasih sudah menghubungi kami. Kami akan merespon kritik dan saran anda.'));
        }

        return view('front.contact', ['title' => 'Kontak - Beranda Hukum']);
    }
}
