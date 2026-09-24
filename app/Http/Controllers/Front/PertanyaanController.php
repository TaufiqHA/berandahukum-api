<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Services\FrontService;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    public function __construct(private FrontService $front)
    {
    }

    public function index()
    {
        $list = Pertanyaan::where('pertanyaan_status', 1)
            ->orderByDesc('pertanyaan_date')->paginate(10);

        return view('front.list_pertanyaan', [
            'title' => 'Daftar Pertanyaan - Beranda Hukum',
            'daftarpertanyaan' => $list,
        ]);
    }

    public function form(Request $request)
    {
        if ($request->isMethod('post') && $request->has('btn_kirim')) {
            $recaptchaOk = config('beranda.disable_recaptcha') ? true : $this->verifyRecaptcha($request);

            if ($recaptchaOk) {
                $data = $request->validate([
                    'tanyaNama' => 'required',
                    'tanyaEmail' => 'required',
                    'pertanyaan' => 'required',
                ]);

                Pertanyaan::create([
                    'pertanyaan_date' => date('Y-m-d H:i:s'),
                    'pertanyaan_nama' => $data['tanyaNama'],
                    'pertanyaan_email' => $data['tanyaEmail'],
                    'pertanyaan' => $data['pertanyaan'],
                    'pertanyaan_status' => 0,
                ]);

                return redirect('kirimpertanyaan')->with('msg_flash', success_message('Terima kasih sudah mengirimkan pertanyaan anda. Kami akan memberikan jawaban pertanyaan tersebut secepatnya.'));
            }

            return redirect('kirimpertanyaan')->with('msg_flash', error_message('Sorry Google Recaptcha Unsuccessful!!'));
        }

        $daftarpertanyaan = Pertanyaan::where('pertanyaan_status', 1)
            ->orderByDesc('pertanyaan_date')->get();

        return view('front.form_pertanyaan', [
            'title' => 'Form Kirim Pertanyaan - Beranda Hukum',
            'daftarpertanyaan' => $daftarpertanyaan,
        ]);
    }

    private function verifyRecaptcha(Request $request): bool
    {
        $secret = '6LclA_gUAAAAAGbYwKC1nEIGICsv3hKs-Lx-_G19';
        $response = $request->input('g-recaptcha-response');

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response.'&remoteip='.$request->ip());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $status = json_decode($output, true);

        return ! empty($status['success']);
    }
}
