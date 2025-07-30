<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surats = Surat::all();
        return view('surats.index', compact('surats')); // Gantilah 'surats.index' dengan view yang sesuai
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surats.create'); // Gantilah 'surats.create' dengan view yang sesuai
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return 0;
        // $request->validate([
        //     'nomor_rekening' => 'required|string|max:255',
        //     'nomor' => 'required|string|max:255',
        //     'jenis_surat' => 'required|integer',
        //     'tunggakan_pokok' => 'required|numeric',
        //     'tunggakan_bunga' => 'required|numeric',
        //     'denda_pokok' => 'required|numeric',
        //     'denda_bunga' => 'required|numeric',
        // ]);

        // return $request['nomor_rekening'];

                // Konversi koma menjadi titik
                $request['tunggakan_pokok'] = str_replace(',', '.', $request['tunggakan_pokok']);
                $request['tunggakan_bunga'] = str_replace(',', '.', $request['tunggakan_bunga']);
                $request['denda_pokok'] = str_replace(',', '.', $request['denda_pokok']);
                $request['denda_bunga'] = str_replace(',', '.', $request['denda_bunga']);

        Surat::create($request->all());

        return redirect()->route('nasabah.detail', $request['nomor_rekening']) // Gantilah 'surats.index' dengan route yang sesuai
            ->with('success', 'Surat berhasil ditambahkan.'); // Gantilah 'surats.index' dengan route yang sesuai
    }

    /**
     * Display the specified resource.
     */
    public function show(Surat $surat)
    {
        return view('surats.show', compact('surat')); // Gantilah 'surats.show' dengan view yang sesuai
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Surat $surat)
    {
        return view('surats.edit', compact('surat')); // Gantilah 'surats.edit' dengan view yang sesuai
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Surat $surat)
    {
        $request->validate([
            'nomor_rekening' => 'required|string|max:255',
            'nomor' => 'required|string|max:255',
            'jenis_surat' => 'required|integer',
            'tunggakan_pokok' => 'required|numeric',
            'tunggakan_bunga' => 'required|numeric',
            'denda_pokok' => 'required|numeric',
            'denda_bunga' => 'required|numeric',
        ]);

        $surat->update($request->all());

        return redirect()->route('surats.index')
            ->with('success', 'Surat berhasil diperbarui.'); // Gantilah 'surats.index' dengan route yang sesuai
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Surat $surat)
    {
        $surat->delete();

        return redirect()->route('nasabah.detail', $surat->nomor_rekening) // Gantilah 'surats.index' dengan route yang sesuai
            ->with('success', 'Surat berhasil dihapus');

    }
}
