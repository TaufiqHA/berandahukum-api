<?php

return [
    /*
     * Nonaktifkan reCAPTCHA di luar produksi (login admin & form pertanyaan).
     */
    'disable_recaptcha' => env('DISABLE_RECAPTCHA', env('APP_ENV', 'production') !== 'production'),

    'google_recaptcha_secret' => env('GOOGLE_RECAPTCHA_SECRET', '6LclA_gUAAAAAGbYwKC1nEIGICsv3hKs-Lx-_G19'),
];
