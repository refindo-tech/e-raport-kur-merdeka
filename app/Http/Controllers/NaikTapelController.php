<?php

namespace App\Http\Controllers;

use App\Services\NaikTapelService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class NaikTapelController extends Controller
{
    public function create(NaikTapelService $service)
    {
        try {
            $sumber = $service->sumberAktif();
        } catch (InvalidArgumentException $e) {
            return redirect()->route('tapel.index')->withFailed($e->getMessage());
        }

        return view('pages.tapel.naik', [
            'sumber' => $sumber,
            'bolehSemester' => (string) $sumber->semester === '1',
        ]);
    }

    public function preview(Request $request, NaikTapelService $service)
    {
        $request->validate([
            'jenis' => 'required|in:semester,tahun_baru',
        ]);

        try {
            $data = $service->pratinjau($service->sumberAktif(), $request->jenis);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('tapel.naik')->withFailed($e->getMessage());
        }

        return view('pages.tapel.naik-pratinjau', $data);
    }

    public function store(Request $request, NaikTapelService $service)
    {
        $request->validate([
            'jenis' => 'required|in:semester,tahun_baru',
            'aksi' => 'required|array',
            'aksi.*' => 'required|in:naik,tinggal,tetap,lulus',
        ]);

        try {
            $hasil = $service->jalankan($service->sumberAktif(), $request->jenis, $request->aksi);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('tapel.naik')->withFailed($e->getMessage());
        }

        return view('pages.tapel.naik-hasil', $hasil);
    }
}
