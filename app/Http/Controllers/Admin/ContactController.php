<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contact.index', ['title' => 'Daftar Kontak', 'contact' => Contact::orderByDesc('contact_id')->get()]);
    }
}
