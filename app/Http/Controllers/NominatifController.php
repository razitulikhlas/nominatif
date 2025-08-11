<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cabang;
use App\Models\Nominatif;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\Statement;
use PhpParser\Node\Stmt\Return_;

class NominatifController extends Controller
{
    //

    public function index()
    {

        // return Auth::user();
        $cabang = Cabang::whereId(Auth::user()->id_cabang)->first();

        $data = DB::select("SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif WHERE KD_CAB_KONSOL = ? GROUP BY TANGGAL
        ORDER BY TANGGAL DESC", [$cabang->kode_cabang]);

        // return Auth::user();
        // $data = array_reverse($data);
        // return dd($data);

        return view('nominatif', [
            "data" => $data
        ]);
    }

    public function upload(Request $request)

    {

        try {

            $request->validate([

                'file' => 'required|mimes:csv,txt',

            ]);



            $file = $request->file('file');

            // return dd($file);

            $namaFile = $file->getClientOriginalName();



            $path = $file->storeAs('imports', $namaFile); // Simpan di storage/app/imports



            $tanggal = $request['tanggal_nominatif'];



            $chekkTanggal = DB::select("SELECT TANGGAL FROM tbl_nominatif where TANGGAL = ? limit 1", [$tanggal]);



            if ($chekkTanggal) {

                DB::select("DELETE FROM tbl_nominatif WHERE TANGGAL = ?", [$tanggal]);
            }



            // $tanggal = Carbon::parse($request['tanggal_nominatif'])->isoFormat('D MMMM YYYY');



            // return $path;

            $filepath = storage_path('app/private/' . $path);

            $stream = fopen($filepath, 'r');



            $csv = Reader::createFromStream($stream);

            $csv->setDelimiter(';');

            $csv->setHeaderOffset(0);





            // return dd($csv->getRecords()); // Mengembalikan iterator untuk membacaz



            //build a statement

            $stmt = new Statement()

                ->select(

                    'KD_CAB_KONSOL',

                    'KD_CAB',

                    'NO_REK',

                    'NO_NASABAH',

                    'NAMA_SINGKAT',

                    'TGL_JT',

                    'JNK_WKT_BL',

                    'PRS_BUNGA',

                    'PLAFOND',

                    'PLAFOND_AWAL',

                    'LONGGAR_TARIK',

                    'KD_STATUS',

                    'BUNGA',

                    'POKOK',

                    'KOLEKTIBILITY',

                    'KD_PRD',

                    'GL_PRD_NAME',

                    'PRD_NAME',

                    'SALDO_AKHIR',

                    'SALDO_AKHIR_NERACA',

                    'AMORSISA',

                    'KODE_APL',

                    'AMOR_BLN_INI',

                    'TOTAGUNAN',

                    'TOTAGUNAN_YDP',

                    'IMPAIREMENT',

                    'CKPN',

                    'TGLMULAI',

                    'AKMAMOR',

                    'JENIS_KREDIT',

                    'SEKTOR_EKONOMI',

                    'GOLONGAN',

                    'HUB_BANK',

                    'ACRU_BLN',

                    'TUNGG_POKOK',

                    'TUNGG_BUNGA',

                    'TREASURID',

                    'CRNBR',

                    'AMTPENPASD',

                    'JENIS_GUNA',

                    'GOL_NSB_LBU',

                    'GOL_NSB_SID',

                    'GOL_KRD_SID',

                    'CATPORTOLBU',

                    'TGL_PK',

                    'NO_PK',

                    'KD_AO',

                    'ANGS_POKOK',

                    'ANGS_BUNGA',

                    'DENDA_TUNGGBNG',

                    'DENDA_TUNGGPKK',

                    'JNSBUNGA',

                    'RATING',

                    'RECOVERY_RATE',

                    'NILAI_AGUNAN',

                    'GROUPID',

                    'QUALITYID',

                    'JML_HARI_TUNGPKK',

                    'JML_HARI_TUNGBNG',

                    'NOHP',

                    'NILAI_WAJAR',

                    'NIK',

                    'NPWP',

                    'NEW_AGUNAN_YDP',

                    'NEW_NILAI_AGUNAN',

                    'STSIMPR',

                    'TGL_AWAL_RSTRK',

                    'TGL_AKHIR_RSTRK',

                    'RESTRUKKE',

                    'QUALRPTO',

                    'RESTRUKTYPE',

                    'PARMNM',

                    'TGL_PENCAIRAN',

                    'SUMBER_DANA',

                    'KT_DEBITUR',

                    'INKLUSIF_MACROPRUDENSIAL',

                    'PEMBIAYAAN_BERKELANJUTAN',

                    'SEGMENT_KREDIT',

                    'SEK_EKO_LBUT',

                    'SEK_EKO_THI',

                    'AUTO_HIJAU_THI',

                    'KLASIFIKASI_THI',

                    'SKOR_KREDIT',

                    'RISK_GRADE'

                );





            //query your records from the document

            $records = $stmt->process($csv);





            // Mulai transaksi database

            DB::beginTransaction();



            $cleanNumeric = function ($value) {

                if ($value === null || $value === '') {

                    return null;
                }

                // 1. Hapus titik (pemisah ribuan)

                $value1 = str_replace('.', '', $value);

                // 2. Ganti koma (pemisah desimal) dengan titik

                $value2 = str_replace(',', '.', $value1);

                // 3. Konversi ke float

                return (float) $value2;
            };



            $parseDate = function ($value) {

                if (empty($value)) {

                    return null;
                }

                try {

                    // Mencoba membuat objek Carbon dari format d/m/Y

                    return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
                } catch (\Exception $e) {

                    // Jika format tidak valid, kembalikan null

                    return null;
                }
            };







            try {

                // Iterasi pada setiap record dan simpan ke database

                foreach ($records as $record) {

                    $nominatif = new Nominatif([

                        // String Columns

                        'KD_CAB_KONSOL' => $record['KD_CAB_KONSOL'] ?? null,

                        'KD_CAB' => $record['KD_CAB'] ?? null,

                        'NO_REK' => $record['NO_REK'] ?? null,

                        'NO_NASABAH' => $record['NO_NASABAH'] ?? null,

                        'NAMA_SINGKAT' => $record['NAMA_SINGKAT'] ?? null,

                        'KD_STATUS' => $record['KD_STATUS'] ?? null,

                        'KOLEKTIBILITY' => $record['KOLEKTIBILITY'] ?? null,

                        'KD_PRD' => $record['KD_PRD'] ?? null,

                        'GL_PRD_NAME' => $record['GL_PRD_NAME'] ?? null,

                        'PRD_NAME' => $record['PRD_NAME'] ?? null,

                        'KODE_APL' => $record['KODE_APL'] ?? null,

                        'JENIS_KREDIT' => $record['JENIS_KREDIT'] ?? null,

                        'SEKTOR_EKONOMI' => $record['SEKTOR_EKONOMI'] ?? null,

                        'GOLONGAN' => $record['GOLONGAN'] ?? null,

                        'HUB_BANK' => $record['HUB_BANK'] ?? null,

                        'TREASURID' => $record['TREASURID'] ?? null,

                        'CRNBR' => $record['CRNBR'] ?? null,

                        'JENIS_GUNA' => $record['JENIS_GUNA'] ?? null,

                        'GOL_NSB_LBU' => $record['GOL_NSB_LBU'] ?? null,

                        'GOL_NSB_SID' => $record['GOL_NSB_SID'] ?? null,

                        'GOL_KRD_SID' => $record['GOL_KRD_SID'] ?? null,

                        'CATPORTOLBU' => $record['CATPORTOLBU'] ?? null,

                        'NO_PK' => $record['NO_PK'] ?? null,

                        'KD_AO' => $record['KD_AO'] ?? null,

                        'JNSBUNGA' => $record['JNSBUNGA'] ?? null,

                        'RATING' => $record['RATING'] ?? null,

                        'GROUPID' => $record['GROUPID'] ?? null,

                        'QUALITYID' => $record['QUALITYID'] ?? null,

                        'NOHP' => $record['NOHP'] ?? null,

                        'NIK' => $record['NIK'] ?? null,

                        'NPWP' => $record['NPWP'] ?? null,

                        'STSIMPR' => $record['STSIMPR'] ?? null,

                        'QUALRPTO' => $record['QUALRPTO'] ?? null,

                        'RESTRUKTYPE' => $record['RESTRUKTYPE'] ?? null,

                        'PARMNM' => $record['PARMNM'] ?? null,

                        'SUMBER_DANA' => $record['SUMBER_DANA'] ?? null,

                        'KT_DEBITUR' => $record['KT_DEBITUR'] ?? null,

                        'INKLUSIF_MACROPRUDENSIAL' => $record['INKLUSIF_MACROPRUDENSIAL'] ?? null,

                        'PEMBIAYAAN_BERKELANJUTAN' => $record['PEMBIAYAAN_BERKELANJUTAN'] ?? null,

                        'SEGMENT_KREDIT' => $record['SEGMENT_KREDIT'] ?? null,

                        'SEK_EKO_LBUT' => $record['SEK_EKO_LBUT'] ?? null,

                        'SEK_EKO_THI' => $record['SEK_EKO_THI'] ?? null,

                        'AUTO_HIJAU_THI' => $record['AUTO_HIJAU_THI'] ?? null,

                        'KLASIFIKASI_THI' => $record['KLASIFIKASI_THI'] ?? null,

                        'SKOR_KREDIT' => $record['SKOR_KREDIT'] ?? null,

                        'RISK_GRADE' => $record['RISK_GRADE'] ?? null,



                        // Date Columns

                        'TGL_JT' => $parseDate($record['TGL_JT'] ?? null),

                        'TGLMULAI' => $parseDate($record['TGLMULAI'] ?? null),

                        'TGL_PK' => $parseDate($record['TGL_PK'] ?? null),

                        'TGL_AWAL_RSTRK' => $parseDate($record['TGL_AWAL_RSTRK'] ?? null),

                        'TGL_AKHIR_RSTRK' => $parseDate($record['TGL_AKHIR_RSTRK'] ?? null),

                        'TGL_PENCAIRAN' => $parseDate($record['TGL_PENCAIRAN'] ?? null),



                        // Integer Columns

                        'JNK_WKT_BL' => (int) ($record['JNK_WKT_BL'] ?? 0),

                        'JML_HARI_TUNGPKK' => (int) ($record['JML_HARI_TUNGPKK'] ?? 0),

                        'JML_HARI_TUNGBNG' => (int) ($record['JML_HARI_TUNGBNG'] ?? 0),

                        'RESTRUKKE' => (int) ($record['RESTRUKKE'] ?? 0),



                        // Double Columns

                        'PRS_BUNGA' => $cleanNumeric($record['PRS_BUNGA'] ?? null),

                        'PLAFOND' => $cleanNumeric($record['PLAFOND'] ?? null),

                        'PLAFOND_AWAL' => $cleanNumeric($record['PLAFOND_AWAL'] ?? null),

                        'LONGGAR_TARIK' => $cleanNumeric($record['LONGGAR_TARIK'] ?? null),

                        'BUNGA' => $cleanNumeric($record['BUNGA'] ?? null),

                        'POKOK' => $cleanNumeric($record['POKOK'] ?? null),

                        'SALDO_AKHIR' => $cleanNumeric($record['SALDO_AKHIR'] ?? null),

                        'SALDO_AKHIR_NERACA' => $cleanNumeric($record['SALDO_AKHIR_NERACA'] ?? null),

                        'AMORSISA' => $cleanNumeric($record['AMORSISA'] ?? null),

                        'AMOR_BLN_INI' => $cleanNumeric($record['AMOR_BLN_INI'] ?? null),

                        'TOTAGUNAN' => $cleanNumeric($record['TOTAGUNAN'] ?? null),

                        'TOTAGUNAN_YDP' => $cleanNumeric($record['TOTAGUNAN_YDP'] ?? null),

                        'IMPAIREMENT' => $cleanNumeric($record['IMPAIREMENT'] ?? null),

                        'CKPN' => $cleanNumeric($record['CKPN'] ?? null),

                        'AKMAMOR' => $cleanNumeric($record['AKMAMOR'] ?? null),

                        'ACRU_BLN' => $cleanNumeric($record['ACRU_BLN'] ?? null),

                        'TUNGG_POKOK' => $cleanNumeric($record['TUNGG_POKOK'] ?? null),

                        'TUNGG_BUNGA' => $cleanNumeric($record['TUNGG_BUNGA'] ?? null),

                        'AMTPENPASD' => $cleanNumeric($record['AMTPENPASD'] ?? null),

                        'ANGS_POKOK' => $cleanNumeric($record['ANGS_POKOK'] ?? null),

                        'ANGS_BUNGA' => $cleanNumeric($record['ANGS_BUNGA'] ?? null),

                        'DENDA_TUNGGBNG' => $cleanNumeric($record['DENDA_TUNGGBNG'] ?? null),

                        'DENDA_TUNGGPKK' => $cleanNumeric($record['DENDA_TUNGGPKK'] ?? null),

                        'RECOVERY_RATE' => $cleanNumeric($record['RECOVERY_RATE'] ?? null),

                        'NILAI_AGUNAN' => $cleanNumeric($record['NILAI_AGUNAN'] ?? null),

                        'NILAI_WAJAR' => $cleanNumeric($record['NILAI_WAJAR'] ?? null),

                        'NEW_AGUNAN_YDP' => $cleanNumeric($record['NEW_AGUNAN_YDP'] ?? null),

                        'NEW_NILAI_AGUNAN' => $cleanNumeric($record['NEW_NILAI_AGUNAN'] ?? null),



                        'TANGGAL' => $tanggal,

                    ]);



                    $nominatif->save();
                }



                // Commit transaksi jika semua data berhasil disimpan

                DB::commit();

                return redirect()->back()->with('success', 'Data berhasil diupload!');





                return "Data berhasil diimpor ke database."; // Atau redirect ke halaman lain

            } catch (\Exception $e) {

                // Rollback transaksi jika terjadi kesalahan

                DB::rollback();



                return "Terjadi kesalahan saat mengimpor data: " . $e->getMessage();
            }

            // return $path;



            // Lakukan proses import data dari file CSV

            // ...





        } catch (ValidationException $e) {

            // return "eror";

            return redirect()->back()->withErrors(['file' => 'File harus berformat CSV atau TXT.']);
        }
    }




    // public function upload(Request $request)
    // {
    //     // try {
    //         $request->validate([
    //             'file' => 'required|mimes:csv,txt',
    //         ]);

    //         $file = $request->file('file');
    //         // return dd($file);
    //         $namaFile = $file->getClientOriginalName();

    //         $path = $file->storeAs('imports', $namaFile); // Simpan di storage/app/imports

    //         $tanggal = $request['tanggal_nominatif'];

    //         $chekkTanggal = DB::select("SELECT TANGGAL FROM tbl_nominatif where TANGGAL = ? limit 1", [$tanggal]);

    //         if ($chekkTanggal) {
    //             DB::select("DELETE FROM tbl_nominatif WHERE TANGGAL = ?", [$tanggal]);
    //         }

    //         // $tanggal = Carbon::parse($request['tanggal_nominatif'])->isoFormat('D MMMM YYYY');

    //         // return $path;
    //         $filepath = storage_path('app/private/' . $path);
    //         $stream = fopen($filepath, 'r');

    //         $csv = Reader::createFromStream($stream);
    //         $csv->setDelimiter(';');
    //         $csv->setHeaderOffset(0);


    //         // return dd($csv->getRecords()); // Mengembalikan iterator untuk membacaz

    //         //build a statement
    //         $stmt = new Statement()
    //             ->select(
    //                 'KD_CAB_KONSOL',
    //                 'KD_CAB',
    //                 'NO_REK',
    //                 'NO_NASABAH',
    //                 'NAMA_SINGKAT',
    //                 'TGL_JT',
    //                 'JNK_WKT_BL',
    //                 'PRS_BUNGA',
    //                 'PLAFOND',
    //                 'PLAFOND_AWAL',
    //                 'LONGGAR_TARIK',
    //                 'KD_STATUS',
    //                 'BUNGA',
    //                 'POKOK',
    //                 'KOLEKTIBILITY',
    //                 'KD_PRD',
    //                 'GL_PRD_NAME',
    //                 'PRD_NAME',
    //                 'SALDO_AKHIR',
    //                 'SALDO_AKHIR_NERACA',
    //                 'AMORSISA',
    //                 'KODE_APL',
    //                 'AMOR_BLN_INI',
    //                 'TOTAGUNAN',
    //                 'TOTAGUNAN_YDP',
    //                 'IMPAIREMENT',
    //                 'CKPN',
    //                 'TGLMULAI',
    //                 'AKMAMOR',
    //                 'JENIS_KREDIT',
    //                 'SEKTOR_EKONOMI',
    //                 'GOLONGAN',
    //                 'HUB_BANK',
    //                 'ACRU_BLN',
    //                 'TUNGG_POKOK',
    //                 'TUNGG_BUNGA',
    //                 'TREASURID',
    //                 'CRNBR',
    //                 'AMTPENPASD',
    //                 'JENIS_GUNA',
    //                 'GOL_NSB_LBU',
    //                 'GOL_NSB_SID',
    //                 'GOL_KRD_SID',
    //                 'CATPORTOLBU',
    //                 'TGL_PK',
    //                 'NO_PK',
    //                 'KD_AO',
    //                 'ANGS_POKOK',
    //                 'ANGS_BUNGA',
    //                 'DENDA_TUNGGBNG',
    //                 'DENDA_TUNGGPKK',
    //                 'JNSBUNGA',
    //                 'RATING',
    //                 'RECOVERY_RATE',
    //                 'NILAI_AGUNAN',
    //                 'GROUPID',
    //                 'QUALITYID',
    //                 'JML_HARI_TUNGPKK',
    //                 'JML_HARI_TUNGBNG',
    //                 'NOHP',
    //                 'NILAI_WAJAR',
    //                 'NIK',
    //                 'NPWP',
    //                 'NEW_AGUNAN_YDP',
    //                 'NEW_NILAI_AGUNAN',
    //                 'STSIMPR',
    //                 'TGL_AWAL_RSTRK',
    //                 'TGL_AKHIR_RSTRK',
    //                 'RESTRUKKE',
    //                 'QUALRPTO',
    //                 'RESTRUKTYPE',
    //                 'PARMNM',
    //                 'TGL_PENCAIRAN',
    //                 'SUMBER_DANA',
    //                 'KT_DEBITUR',
    //                 'INKLUSIF_MACROPRUDENSIAL',
    //                 'PEMBIAYAAN_BERKELANJUTAN',
    //                 'SEGMENT_KREDIT',
    //                 'SEK_EKO_LBUT',
    //                 'SEK_EKO_THI',
    //                 'AUTO_HIJAU_THI',
    //                 'KLASIFIKASI_THI',
    //                 'SKOR_KREDIT',
    //                 'RISK_GRADE'
    //             );


    //         //query your records from the document
    //         $records = $stmt->process($csv);


    //         // Mulai transaksi database
    //         DB::beginTransaction();

    //         $cleanNumeric = function ($value) {
    //             if ($value === null || $value === '') {
    //                 return null;
    //             }
    //             // 1. Hapus titik (pemisah ribuan)
    //             $value1 = str_replace('.', '', $value);
    //             // 2. Ganti koma (pemisah desimal) dengan titik
    //             $value2 = str_replace(',', '.', $value1);
    //             // 3. Konversi ke float
    //             return (float) $value2;
    //         };

    //         $parseDate = function ($value) {
    //             if (empty($value)) {
    //                 return null;
    //             }
    //             try {
    //                 // Mencoba membuat objek Carbon dari format d/m/Y
    //                 return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    //             } catch (\Exception $e) {
    //                 // Jika format tidak valid, kembalikan null
    //                 return null;
    //             }
    //         };

    //         // #### BAGIAN KODE BARU YANG CEPAT DENGAN CHUNKING ####
    //         // try {
    //             $chunk = [];
    //             $chunkSize = 500; // Simpan data setiap 500 baris. Anda bisa sesuaikan angka ini.

    //             // Iterasi pada setiap record
    //             foreach ($records as $index => $record) {
    //                 // 1. Kumpulkan data baris ke dalam sebuah array
    //                 $chunk[] = [
    //                     //            // String Columns
    //                     'KD_CAB_KONSOL'             => $record['KD_CAB_KONSOL'] ?? null,
    //                     'KD_CAB'                    => $record['KD_CAB'] ?? null,
    //                     'NO_REK'                    => $record['NO_REK'] ?? null,
    //                     'NO_NASABAH'                => $record['NO_NASABAH'] ?? null,
    //                     'NAMA_SINGKAT'              => $record['NAMA_SINGKAT'] ?? null,
    //                     'KD_STATUS'                 => $record['KD_STATUS'] ?? null,
    //                     'KOLEKTIBILITY'             => $record['KOLEKTIBILITY'] ?? null,
    //                     'KD_PRD'                    => $record['KD_PRD'] ?? null,
    //                     'GL_PRD_NAME'               => $record['GL_PRD_NAME'] ?? null,
    //                     'PRD_NAME'                  => $record['PRD_NAME'] ?? null,
    //                     'KODE_APL'                  => $record['KODE_APL'] ?? null,
    //                     'JENIS_KREDIT'              => $record['JENIS_KREDIT'] ?? null,
    //                     'SEKTOR_EKONOMI'            => $record['SEKTOR_EKONOMI'] ?? null,
    //                     'GOLONGAN'                  => $record['GOLONGAN'] ?? null,
    //                     'HUB_BANK'                  => $record['HUB_BANK'] ?? null,
    //                     'TREASURID'                 => $record['TREASURID'] ?? null,
    //                     'CRNBR'                     => $record['CRNBR'] ?? null,
    //                     'JENIS_GUNA'                => $record['JENIS_GUNA'] ?? null,
    //                     'GOL_NSB_LBU'               => $record['GOL_NSB_LBU'] ?? null,
    //                     'GOL_NSB_SID'               => $record['GOL_NSB_SID'] ?? null,
    //                     'GOL_KRD_SID'               => $record['GOL_KRD_SID'] ?? null,
    //                     'CATPORTOLBU'               => $record['CATPORTOLBU'] ?? null,
    //                     'NO_PK'                     => $record['NO_PK'] ?? null,
    //                     'KD_AO'                     => $record['KD_AO'] ?? null,
    //                     'JNSBUNGA'                  => $record['JNSBUNGA'] ?? null,
    //                     'RATING'                    => $record['RATING'] ?? null,
    //                     'GROUPID'                   => $record['GROUPID'] ?? null,
    //                     'QUALITYID'                 => $record['QUALITYID'] ?? null,
    //                     'NOHP'                      => $record['NOHP'] ?? null,
    //                     'NIK'                       => $record['NIK'] ?? null,
    //                     'NPWP'                      => $record['NPWP'] ?? null,
    //                     'STSIMPR'                   => $record['STSIMPR'] ?? null,
    //                     'QUALRPTO'                  => $record['QUALRPTO'] ?? null,
    //                     'RESTRUKTYPE'               => $record['RESTRUKTYPE'] ?? null,
    //                     'PARMNM'                    => $record['PARMNM'] ?? null,
    //                     'SUMBER_DANA'               => $record['SUMBER_DANA'] ?? null,
    //                     'KT_DEBITUR'                => $record['KT_DEBITUR'] ?? null,
    //                     'INKLUSIF_MACROPRUDENSIAL'  => $record['INKLUSIF_MACROPRUDENSIAL'] ?? null,
    //                     'PEMBIAYAAN_BERKELANJUTAN'  => $record['PEMBIAYAAN_BERKELANJUTAN'] ?? null,
    //                     'SEGMENT_KREDIT'            => $record['SEGMENT_KREDIT'] ?? null,
    //                     'SEK_EKO_LBUT'              => $record['SEK_EKO_LBUT'] ?? null,
    //                     'SEK_EKO_THI'               => $record['SEK_EKO_THI'] ?? null,
    //                     'AUTO_HIJAU_THI'            => $record['AUTO_HIJAU_THI'] ?? null,
    //                     'KLASIFIKASI_THI'           => $record['KLASIFIKASI_THI'] ?? null,
    //                     'SKOR_KREDIT'               => $record['SKOR_KREDIT'] ?? null,
    //                     'RISK_GRADE'                => $record['RISK_GRADE'] ?? null,

    //                     // Date Columns
    //                     'TGL_JT'                    => $parseDate($record['TGL_JT'] ?? null),
    //                     'TGLMULAI'                  => $parseDate($record['TGLMULAI'] ?? null),
    //                     'TGL_PK'                    => $parseDate($record['TGL_PK'] ?? null),
    //                     'TGL_AWAL_RSTRK'            => $parseDate($record['TGL_AWAL_RSTRK'] ?? null),
    //                     'TGL_AKHIR_RSTRK'           => $parseDate($record['TGL_AKHIR_RSTRK'] ?? null),
    //                     'TGL_PENCAIRAN'             => $parseDate($record['TGL_PENCAIRAN'] ?? null),

    //                     // Integer Columns
    //                     'JNK_WKT_BL'                => (int) ($record['JNK_WKT_BL'] ?? 0),
    //                     'JML_HARI_TUNGPKK'          => (int) ($record['JML_HARI_TUNGPKK'] ?? 0),
    //                     'JML_HARI_TUNGBNG'          => (int) ($record['JML_HARI_TUNGBNG'] ?? 0),
    //                     'RESTRUKKE'                 => (int) ($record['RESTRUKKE'] ?? 0),

    //                     // Double Columns
    //                     'PRS_BUNGA'                 => $cleanNumeric($record['PRS_BUNGA'] ?? null),
    //                     'PLAFOND'                   => $cleanNumeric($record['PLAFOND'] ?? null),
    //                     'PLAFOND_AWAL'              => $cleanNumeric($record['PLAFOND_AWAL'] ?? null),
    //                     'LONGGAR_TARIK'             => $cleanNumeric($record['LONGGAR_TARIK'] ?? null),
    //                     'BUNGA'                     => $cleanNumeric($record['BUNGA'] ?? null),
    //                     'POKOK'                     => $cleanNumeric($record['POKOK'] ?? null),
    //                     'SALDO_AKHIR'               => $cleanNumeric($record['SALDO_AKHIR'] ?? null),
    //                     'SALDO_AKHIR_NERACA'        => $cleanNumeric($record['SALDO_AKHIR_NERACA'] ?? null),
    //                     'AMORSISA'                  => $cleanNumeric($record['AMORSISA'] ?? null),
    //                     'AMOR_BLN_INI'              => $cleanNumeric($record['AMOR_BLN_INI'] ?? null),
    //                     'TOTAGUNAN'                 => $cleanNumeric($record['TOTAGUNAN'] ?? null),
    //                     'TOTAGUNAN_YDP'             => $cleanNumeric($record['TOTAGUNAN_YDP'] ?? null),
    //                     'IMPAIREMENT'               => $cleanNumeric($record['IMPAIREMENT'] ?? null),
    //                     'CKPN'                      => $cleanNumeric($record['CKPN'] ?? null),
    //                     'AKMAMOR'                   => $cleanNumeric($record['AKMAMOR'] ?? null),
    //                     'ACRU_BLN'                  => $cleanNumeric($record['ACRU_BLN'] ?? null),
    //                     'TUNGG_POKOK'               => $cleanNumeric($record['TUNGG_POKOK'] ?? null),
    //                     'TUNGG_BUNGA'               => $cleanNumeric($record['TUNGG_BUNGA'] ?? null),
    //                     'AMTPENPASD'                => $cleanNumeric($record['AMTPENPASD'] ?? null),
    //                     'ANGS_POKOK'                => $cleanNumeric($record['ANGS_POKOK'] ?? null),
    //                     'ANGS_BUNGA'                => $cleanNumeric($record['ANGS_BUNGA'] ?? null),
    //                     'DENDA_TUNGGBNG'            => $cleanNumeric($record['DENDA_TUNGGBNG'] ?? null),
    //                     'DENDA_TUNGGPKK'            => $cleanNumeric($record['DENDA_TUNGGPKK'] ?? null),
    //                     'RECOVERY_RATE'             => $cleanNumeric($record['RECOVERY_RATE'] ?? null),
    //                     'NILAI_AGUNAN'              => $cleanNumeric($record['NILAI_AGUNAN'] ?? null),
    //                     'NILAI_WAJAR'               => $cleanNumeric($record['NILAI_WAJAR'] ?? null),
    //                     'NEW_AGUNAN_YDP'            => $cleanNumeric($record['NEW_AGUNAN_YDP'] ?? null),
    //                     'NEW_NILAI_AGUNAN'          => $cleanNumeric($record['NEW_NILAI_AGUNAN'] ?? null),

    //                     'TANGGAL' => $tanggal,
    //                 ];

    //                 // 2. Jika 'wadah' chunk sudah mencapai ukuran yang ditentukan
    //                 if (count($chunk) >= $chunkSize) {
    //                     // 3. Masukkan semua data di dalam chunk ke DB dalam SATU KALI QUERY
    //                     Nominatif::insert($chunk);
    //                     // 4. Kosongkan kembali chunk untuk diisi lagi
    //                     $chunk = [];
    //                 }
    //             }

    //             // 5. Penting! Masukkan sisa data yang mungkin ada di dalam chunk (jika jumlah total baris bukan kelipatan dari chunkSize)
    //             if (!empty($chunk)) {
    //                 Nominatif::insert($chunk);
    //             }

    //             // Commit transaksi jika semua data berhasil disimpan
    //             DB::commit();

    //             return redirect()->back()->with('success', 'Data berhasil diupload!');

    //         // } catch (\Exception $e) {
    //         //     // Rollback transaksi jika terjadi kesalahan
    //         //     DB::rollback();

    //         //     // Tampilkan pesan error yang lebih detail saat development
    //         //     return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage() . ' di baris ' . $e->getLine());
    //         // }


    //         // try {
    //         //     // Iterasi pada setiap record dan simpan ke database
    //         //     foreach ($records as $record) {
    //         //         $nominatif = new Nominatif([
    //         //            // String Columns
    //         //             'KD_CAB_KONSOL'             => $record['KD_CAB_KONSOL'] ?? null,
    //         //             'KD_CAB'                    => $record['KD_CAB'] ?? null,
    //         //             'NO_REK'                    => $record['NO_REK'] ?? null,
    //         //             'NO_NASABAH'                => $record['NO_NASABAH'] ?? null,
    //         //             'NAMA_SINGKAT'              => $record['NAMA_SINGKAT'] ?? null,
    //         //             'KD_STATUS'                 => $record['KD_STATUS'] ?? null,
    //         //             'KOLEKTIBILITY'             => $record['KOLEKTIBILITY'] ?? null,
    //         //             'KD_PRD'                    => $record['KD_PRD'] ?? null,
    //         //             'GL_PRD_NAME'               => $record['GL_PRD_NAME'] ?? null,
    //         //             'PRD_NAME'                  => $record['PRD_NAME'] ?? null,
    //         //             'KODE_APL'                  => $record['KODE_APL'] ?? null,
    //         //             'JENIS_KREDIT'              => $record['JENIS_KREDIT'] ?? null,
    //         //             'SEKTOR_EKONOMI'            => $record['SEKTOR_EKONOMI'] ?? null,
    //         //             'GOLONGAN'                  => $record['GOLONGAN'] ?? null,
    //         //             'HUB_BANK'                  => $record['HUB_BANK'] ?? null,
    //         //             'TREASURID'                 => $record['TREASURID'] ?? null,
    //         //             'CRNBR'                     => $record['CRNBR'] ?? null,
    //         //             'JENIS_GUNA'                => $record['JENIS_GUNA'] ?? null,
    //         //             'GOL_NSB_LBU'               => $record['GOL_NSB_LBU'] ?? null,
    //         //             'GOL_NSB_SID'               => $record['GOL_NSB_SID'] ?? null,
    //         //             'GOL_KRD_SID'               => $record['GOL_KRD_SID'] ?? null,
    //         //             'CATPORTOLBU'               => $record['CATPORTOLBU'] ?? null,
    //         //             'NO_PK'                     => $record['NO_PK'] ?? null,
    //         //             'KD_AO'                     => $record['KD_AO'] ?? null,
    //         //             'JNSBUNGA'                  => $record['JNSBUNGA'] ?? null,
    //         //             'RATING'                    => $record['RATING'] ?? null,
    //         //             'GROUPID'                   => $record['GROUPID'] ?? null,
    //         //             'QUALITYID'                 => $record['QUALITYID'] ?? null,
    //         //             'NOHP'                      => $record['NOHP'] ?? null,
    //         //             'NIK'                       => $record['NIK'] ?? null,
    //         //             'NPWP'                      => $record['NPWP'] ?? null,
    //         //             'STSIMPR'                   => $record['STSIMPR'] ?? null,
    //         //             'QUALRPTO'                  => $record['QUALRPTO'] ?? null,
    //         //             'RESTRUKTYPE'               => $record['RESTRUKTYPE'] ?? null,
    //         //             'PARMNM'                    => $record['PARMNM'] ?? null,
    //         //             'SUMBER_DANA'               => $record['SUMBER_DANA'] ?? null,
    //         //             'KT_DEBITUR'                => $record['KT_DEBITUR'] ?? null,
    //         //             'INKLUSIF_MACROPRUDENSIAL'  => $record['INKLUSIF_MACROPRUDENSIAL'] ?? null,
    //         //             'PEMBIAYAAN_BERKELANJUTAN'  => $record['PEMBIAYAAN_BERKELANJUTAN'] ?? null,
    //         //             'SEGMENT_KREDIT'            => $record['SEGMENT_KREDIT'] ?? null,
    //         //             'SEK_EKO_LBUT'              => $record['SEK_EKO_LBUT'] ?? null,
    //         //             'SEK_EKO_THI'               => $record['SEK_EKO_THI'] ?? null,
    //         //             'AUTO_HIJAU_THI'            => $record['AUTO_HIJAU_THI'] ?? null,
    //         //             'KLASIFIKASI_THI'           => $record['KLASIFIKASI_THI'] ?? null,
    //         //             'SKOR_KREDIT'               => $record['SKOR_KREDIT'] ?? null,
    //         //             'RISK_GRADE'                => $record['RISK_GRADE'] ?? null,

    //         //             // Date Columns
    //         //             'TGL_JT'                    => $parseDate($record['TGL_JT'] ?? null),
    //         //             'TGLMULAI'                  => $parseDate($record['TGLMULAI'] ?? null),
    //         //             'TGL_PK'                    => $parseDate($record['TGL_PK'] ?? null),
    //         //             'TGL_AWAL_RSTRK'            => $parseDate($record['TGL_AWAL_RSTRK'] ?? null),
    //         //             'TGL_AKHIR_RSTRK'           => $parseDate($record['TGL_AKHIR_RSTRK'] ?? null),
    //         //             'TGL_PENCAIRAN'             => $parseDate($record['TGL_PENCAIRAN'] ?? null),

    //         //             // Integer Columns
    //         //             'JNK_WKT_BL'                => (int) ($record['JNK_WKT_BL'] ?? 0),
    //         //             'JML_HARI_TUNGPKK'          => (int) ($record['JML_HARI_TUNGPKK'] ?? 0),
    //         //             'JML_HARI_TUNGBNG'          => (int) ($record['JML_HARI_TUNGBNG'] ?? 0),
    //         //             'RESTRUKKE'                 => (int) ($record['RESTRUKKE'] ?? 0),

    //         //             // Double Columns
    //         //             'PRS_BUNGA'                 => $cleanNumeric($record['PRS_BUNGA'] ?? null),
    //         //             'PLAFOND'                   => $cleanNumeric($record['PLAFOND'] ?? null),
    //         //             'PLAFOND_AWAL'              => $cleanNumeric($record['PLAFOND_AWAL'] ?? null),
    //         //             'LONGGAR_TARIK'             => $cleanNumeric($record['LONGGAR_TARIK'] ?? null),
    //         //             'BUNGA'                     => $cleanNumeric($record['BUNGA'] ?? null),
    //         //             'POKOK'                     => $cleanNumeric($record['POKOK'] ?? null),
    //         //             'SALDO_AKHIR'               => $cleanNumeric($record['SALDO_AKHIR'] ?? null),
    //         //             'SALDO_AKHIR_NERACA'        => $cleanNumeric($record['SALDO_AKHIR_NERACA'] ?? null),
    //         //             'AMORSISA'                  => $cleanNumeric($record['AMORSISA'] ?? null),
    //         //             'AMOR_BLN_INI'              => $cleanNumeric($record['AMOR_BLN_INI'] ?? null),
    //         //             'TOTAGUNAN'                 => $cleanNumeric($record['TOTAGUNAN'] ?? null),
    //         //             'TOTAGUNAN_YDP'             => $cleanNumeric($record['TOTAGUNAN_YDP'] ?? null),
    //         //             'IMPAIREMENT'               => $cleanNumeric($record['IMPAIREMENT'] ?? null),
    //         //             'CKPN'                      => $cleanNumeric($record['CKPN'] ?? null),
    //         //             'AKMAMOR'                   => $cleanNumeric($record['AKMAMOR'] ?? null),
    //         //             'ACRU_BLN'                  => $cleanNumeric($record['ACRU_BLN'] ?? null),
    //         //             'TUNGG_POKOK'               => $cleanNumeric($record['TUNGG_POKOK'] ?? null),
    //         //             'TUNGG_BUNGA'               => $cleanNumeric($record['TUNGG_BUNGA'] ?? null),
    //         //             'AMTPENPASD'                => $cleanNumeric($record['AMTPENPASD'] ?? null),
    //         //             'ANGS_POKOK'                => $cleanNumeric($record['ANGS_POKOK'] ?? null),
    //         //             'ANGS_BUNGA'                => $cleanNumeric($record['ANGS_BUNGA'] ?? null),
    //         //             'DENDA_TUNGGBNG'            => $cleanNumeric($record['DENDA_TUNGGBNG'] ?? null),
    //         //             'DENDA_TUNGGPKK'            => $cleanNumeric($record['DENDA_TUNGGPKK'] ?? null),
    //         //             'RECOVERY_RATE'             => $cleanNumeric($record['RECOVERY_RATE'] ?? null),
    //         //             'NILAI_AGUNAN'              => $cleanNumeric($record['NILAI_AGUNAN'] ?? null),
    //         //             'NILAI_WAJAR'               => $cleanNumeric($record['NILAI_WAJAR'] ?? null),
    //         //             'NEW_AGUNAN_YDP'            => $cleanNumeric($record['NEW_AGUNAN_YDP'] ?? null),
    //         //             'NEW_NILAI_AGUNAN'          => $cleanNumeric($record['NEW_NILAI_AGUNAN'] ?? null),

    //         //             'TANGGAL' => $tanggal,
    //         //         ]);

    //         //         $nominatif->save();
    //         //     }

    //         //     // Commit transaksi jika semua data berhasil disimpan
    //         //     DB::commit();
    //         //     return redirect()->back()->with('success', 'Data berhasil diupload!');


    //         //     return "Data berhasil diimpor ke database."; // Atau redirect ke halaman lain
    //         // } catch (\Exception $e) {
    //         //     // Rollback transaksi jika terjadi kesalahan
    //         //     DB::rollback();

    //         //     return "Terjadi kesalahan saat mengimpor data: " . $e->getMessage();
    //         // }
    //         // return $path;

    //         // Lakukan proses import data dari file CSV
    //         // ...


    //     // } catch (ValidationException $e) {
    //     //     // return "eror";
    //     //     return redirect()->back()->withErrors(['file' => 'File harus berformat CSV atau TXT.']);
    //     // }
    // }






    public function store(Request $request)
    {

        return $request->all();
        // $request->validate([
        //     'file' => 'required|mimes:csv,txt',
        // ]);

        $file = $request->file('file');

        return $file;
        $namaFile = $file->getClientOriginalName();
        $path = $file->storeAs('imports', $namaFile); // Simpan di storage/app/imports

        // Lakukan proses import data dari file CSV
        // ...

        return redirect()->back()->with('success', 'Data berhasil diupload!');
    }

    public function update(Request $request, $id) {}



    public function destroy($id)
    {
        DB::select("DELETE FROM tbl_nominatif WHERE TANGGAL = '$id'");
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
        // return $id;
        // $databeasiswa = DataBeasiswa::whereId($id)->first();
        // DataBeasiswa::destroy($id);
        // return redirect('databeasiswa/' . $databeasiswa->id_beasiswa)->with('success', 'Data beasiswa berhasil di hapus');
    }
}
