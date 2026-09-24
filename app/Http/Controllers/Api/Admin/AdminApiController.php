<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Basis controller API admin. Semua respons JSON.
 */
abstract class AdminApiController extends Controller
{
    protected function ok(array $data = [], int $status = 200)
    {
        return response()->json($data, $status);
    }

    protected function message(string $message, int $status = 200)
    {
        return response()->json(['message' => $message], $status);
    }

    protected function user()
    {
        return auth()->user();
    }

    protected function isAdmin(): bool
    {
        return ($this->user()->user_level ?? '') === 'admin';
    }

    /**
     * Simpan file unggahan (gambar/pdf) ke public/uploads, kembalikan nama file.
     */
    protected function storeUpload(Request $request, string $field, string $prefix, string $dir): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $name = $prefix.'_'.kode_unik().'_'.date('ymdHis').'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/'.$dir), $name);

        return $name;
    }
}
