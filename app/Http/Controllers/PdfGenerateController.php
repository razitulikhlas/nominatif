<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Data;
use App\Models\Nominatif;
use App\Models\Surat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;

class PdfGenerateController extends Controller
{
    //

    public function index(){
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML('<h1>Test</h1>');
        $pdf = Pdf::loadView("surat_peringatan")->setPaper('A4', 'portrait');
        return $pdf->stream();
    }

    public function show($id){

        // $pdf = Pdf::loadView("surat_berita_acara_klaim")->setPaper('A4', 'portrait');
        // return $pdf->stream();

        $surat = Surat::whereId($id)->first();


        $total_denda = $surat->denda_pokok + $surat->denda_bunga;
        $total = $surat->tunggakan_pokok + $surat->tunggakan_bunga + $total_denda;
        $total = number_format($total, 0, ',', '.');
        $total_denda = number_format($total_denda, 0, ',', '.');

        // return $total_denda;

        $norek = $surat->nomor_rekening;
        $data = Nominatif::where('NO_REK', $norek)->first();

        // return $data;

    //     $tanggalPendek =str_replace('/','-',$data->TGL_PK);
    //     $prefixTahun = "20";
    //     $hari = substr($tanggalPendek, 0, 2); // "25"
    //     $bulan = substr($tanggalPendek, 3, 2); // "06"
    //     $tahunDuaDigit = substr($tanggalPendek, 6, 2); // "24"
        $nosurat= \Carbon\Carbon::now()->isoFormat('MM-YYYY');
        $nosurat= $surat->nomor_surat;

    // // Menggabungkan dengan prefix tahun
    // return $tanggalPanjang = $hari . '-' . $bulan . '-' . $prefixTahun . $tahunDuaDigit; // "25-06-2024"

    // $tgl_pk= $tanggalPanjang ? \Carbon\Carbon::parse($tanggalPanjang)->isoFormat('D MMMM YYYY') : '-' ;
        $tgl_pk = str_replace('/', '-', $data->TGL_PK);

        if($surat->jenis_surat == 0){

        // return $jumlahTunggakan = (float) $data->TUNGG_POKOK ?? 0 + (float) $data->TUNGG_BUNGA ?? 0 + (float) $data->DENDA_TUNGGBNG ?? 0 + (float) $data->DENDA_POKOK ?? 0;
        // return dd($data);

        // return $tgl_pk;
        $pdf = Pdf::loadView("surat_teguran",[
            "data"=>$data,
            "tgl_pk"=>$tgl_pk,
            "surat"=>$surat,
            "total_denda"=>$total_denda,
            "total"=>$total,
            "terbilang"=> $this->terbilang($data->PLAFOND)?? 0,
            "nosurat"=>$nosurat,
            ])->setPaper('A4', 'portrait');
        return $pdf->stream();
        }else if($surat->jenis_surat == 1){
            $st = Surat::where('nomor_rekening', $norek)->where('jenis_surat',0)->first();
            // return $st->nomor_surat;
            $pdf = Pdf::loadView("surat_peringatan",[
                "data"=>$data,
                "tgl_pk"=>$tgl_pk,
                "surat"=>$surat,
                "total_denda"=>$total_denda,
                "total"=>$total,
                "terbilang"=> $this->terbilang($data->PLAFOND)?? 0,
                "nosurat"=>$nosurat,
                "JenisSurat"=>"Surat Peringatan 1",
                "surat1"=>"Surat Teguran",
                "surat_sebelumnya"=>$st,
                ])->setPaper('A4', 'portrait');
            return $pdf->stream();
        }else if($surat->jenis_surat == 2){
            $st = Surat::where('nomor_rekening', $norek)->where('jenis_surat',1)->first();
            // return $st->nomor_surat;
            $pdf = Pdf::loadView("surat_peringatan",[
                "data"=>$data,
                "tgl_pk"=>$tgl_pk,
                "surat"=>$surat,
                "total_denda"=>$total_denda,
                "total"=>$total,
                "terbilang"=> $this->terbilang($data->PLAFOND)?? 0,
                "nosurat"=>$nosurat,
                "surat1"=>"Surat Peringatan 1",
                "JenisSurat"=>"Surat Peringatan 2",
                "surat_sebelumnya"=>$st,
                ])->setPaper('A4', 'portrait');
            return $pdf->stream();
        }else if($surat->jenis_surat == 3){
            $st = Surat::where('nomor_rekening', $norek)->where('jenis_surat',2)->first();
            // return $st->nomor_surat;
            $pdf = Pdf::loadView("surat_peringatan3",[
                "data"=>$data,
                "tgl_pk"=>$tgl_pk,
                "surat"=>$surat,
                "total_denda"=>$total_denda,
                "total"=>$total,
                "terbilang"=> $this->terbilang($data->PLAFOND)?? 0,
                "nosurat"=>$nosurat,
                "surat1"=>"Surat Peringatan 2",
                "JenisSurat"=>"Surat Peringatan 2",
                "surat_sebelumnya"=>$st,
                ])->setPaper('A4', 'portrait');
            return $pdf->stream();
        }

        return $surat;


        // return str_replace('/','-',$data->TGL_PK);

        // Memecah string



    }

    function terbilang($x) {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($x < 12)
          return " " . $angka[$x];
        elseif ($x < 20)
          return $this->terbilang($x - 10) . " belas ";
        elseif ($x < 100)
          return $this->terbilang($x / 10) . "  puluh " . $this->terbilang($x % 10);
        elseif ($x < 200)
          return "seratus" . $this->terbilang($x - 100);
        elseif ($x < 1000)
          return $this->terbilang($x / 100) . " ratus " . $this->terbilang($x % 100);
        elseif ($x < 2000)
          return "seribu" . $this->terbilang($x - 1000);
        elseif ($x < 1000000)
          return $this->terbilang($x / 1000) . " ribu " . $this->terbilang($x % 1000);
        elseif ($x < 1000000000)
          return $this->terbilang($x / 1000000) . " juta " . $this->terbilang($x % 1000000);
        else if(($x < 100000000000))
          return $this->terbilang($x / 1000000000) . " milyar " . $this->terbilang($x % 1000000000);
        else if(($x < 1000000000000))
          return $this->terbilang($x / 1000000000000) . " triliun " . $this->terbilang($x % 1000000000000);
        else
          return "";
    }





}
