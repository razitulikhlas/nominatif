<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Nominatif;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\Statement;
use PhpParser\Node\Stmt\Return_;

class NominatifController extends Controller
{
    //

    public function index(){
        $data = DB::select("SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif GROUP BY TANGGAL
        ORDER BY TANGGAL DESC");

        // $data = array_reverse($data);

        // return dd($data);

        return view('nominatif',[
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

            if($chekkTanggal){
                DB::select("DELETE FROM tbl_nominatif WHERE TANGGAL = ?", [$tanggal]);
            }

            // $tanggal = Carbon::parse($request['tanggal_nominatif'])->isoFormat('D MMMM YYYY');

            // return $path;
            $filepath = storage_path('app/private/'. $path);
            $stream = fopen($filepath, 'r');

            $csv = Reader::createFromStream($stream);
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            // return dd($csv->getRecords()); // Mengembalikan iterator untuk membacaz

            //build a statement
            $stmt = new Statement()
                ->select(
                    'KD_CAB_KONSOL',
                    'NAMA_SINGKAT',
                    'PLAFOND',
                    'NILAI_WAJAR',
                    'KD_CAB',
                    'KD_AO',
                    'NO_REK',
                    'NOHP',
                    'NO_PK',
                    'KOLEKTIBILITY',
                    'KD_PRD',
                    'GL_PRD_NAME',
                    'TUNGG_POKOK',
                    'TUNGG_BUNGA',
                    'SALDO_AKHIR',
                    'SEKTOR_EKONOMI'
                );

            //query your records from the document
            $records = $stmt->process($csv);


            // Mulai transaksi database
            DB::beginTransaction();



            try {
                // Iterasi pada setiap record dan simpan ke database
                foreach ($records as $record) {
                    $nominatif = new Nominatif([
                        'KD_CAB_KONSOL' => $record['KD_CAB_KONSOL'] ?? null,
                        'NAMA_SINGKAT' => $record['NAMA_SINGKAT'] ?? null,
                        'PLAFOND' => str_replace(',', '.',$record['PLAFOND']) ?? null,
                        'NILAI_WAJAR' => str_replace(',', '.',$record['NILAI_WAJAR']) ?? null,
                        'KD_CAB' => $record['KD_CAB'] ?? null,
                        'KD_AO' => $record['KD_AO'] ?? null,
                        'NO_REK' => $record['NO_REK'] ?? null,
                        'NOHP' => $record['NOHP'] ?? null,
                        'NO_PK' => $record['NO_PK'] ?? null,
                        'KOLEKTIBILITY' => $record['KOLEKTIBILITY'] ?? null,
                        'KD_PRD' => $record['KD_PRD'] ?? null,
                        'GL_PRD_NAME' => $record['GL_PRD_NAME'] ?? null,
                        'TUNGG_POKOK' => str_replace(',', '.',$record['TUNGG_POKOK']) ?? null,
                        'TUNGG_BUNGA' => str_replace(',', '.',$record['TUNGG_BUNGA']) ?? null,
                        'SALDO_AKHIR' => str_replace(',', '.',$record['SALDO_AKHIR']) ?? null,
                        'SEKTOR_EKONOMI' => $record['SEKTOR_EKONOMI'] ?? null,
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

    public function update(Request $request, $id)
    {


    }



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
