<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Afiliasi;
use App\Models\Nominatif;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\Statement;
use PhpParser\Node\Stmt\Return_;
use Throwable;

class AfiliasiController extends Controller
{
    //

    public function index()
    {

        // return $this->sendWa();

//         $data = DB::select("SELECT
//     n.*,
//     a.*,
//     (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN
// FROM
//     tbl_nominatif n
// LEFT JOIN
//     tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY >1 AND
//     n.TUNGG_POKOK != 0 AND n.TUNGG_BUNGA !=0 AND n.NOHP != '' AND
//     n.KD_PRD in (0404,0444,0524,0531,0544,0560,0562,0563,0564,0624,0631,0650,0652)");


//         return dd($data);



        return view('afiliasi');
    }


    public function sendWa()
    {
        $apiUrl = config('services.whatsapp_sender.url');
        $apiKey = config('services.whatsapp_sender.api_key');

        if (!$apiUrl || !$apiKey) {
            Log::channel('scheduler')->error('WhatsApp API URL or API Key not configured for scheduled send.');
            return Command::FAILURE; // Menandakan command gagal
        }
        // $data = Data::whereNoNasabah('0100827237')->first();
        // $tunggakan = $data->TUNGG_POKOK + $data->TUNGG_BUNGA;
        // $tunggakan = number_format($tunggakan, 0, ',', '.');

        // return $tunggakan;

        $data = DB::select("SELECT
    n.*,
    a.*,
    (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN
FROM
    tbl_nominatif n
LEFT JOIN
    tbl_afiliasi a ON n.NO_REK = a.NO_REK
WHERE
    n.KOLEKTIBILITY > 1
    AND n.TUNGG_POKOK != 0
    AND n.TUNGG_BUNGA != 0
    AND n.NOHP != ''
    AND n.KD_PRD IN (0404, 0444, 0524, 0531, 0544, 0560, 0562, 0563, 0564, 0624, 0631, 0650, 0652)
    AND a.NO_REK_AFILIASI != ''
    GROUP BY NAMA_SINGKAT");
        // return dd($data);




        foreach ($data as $item) {
            // Mengambil nilai string, default ke '0' jika null
            $tunggPokokStr = $item->TUNGG_POKOK ?? '0';
            $tunggBungaStr = $item->TUNGG_BUNGA ?? '0';

            // Membersihkan string dari format mata uang (misal: "1.234.567,89" menjadi "1234567.89")
            // 1. Hapus pemisah ribuan (titik)
            // 2. Ganti pemisah desimal (koma) dengan titik agar bisa di-cast ke float
            $cleanedPokok = str_replace(',', '.', str_replace('.', '', $tunggPokokStr));
            $cleanedBunga = str_replace(',', '.', str_replace('.', '', $tunggBungaStr));

            // Konversi ke float
            $tunggPokokNumeric = (float)$cleanedPokok;
            $tunggBungaNumeric = (float)$cleanedBunga;

            $tunggakanNumeric = $tunggPokokNumeric + $tunggBungaNumeric;
            $tunggakan = number_format($tunggakanNumeric, 0, ',', '.'); // $tunggakan menjadi string yang diformat

            $nama = $item->NAMA_SINGKAT;
            $norek = $item->NO_REK_AFILIASI;

            $message = "Bapak/ibu Yth $nama,
    Nasabah Setia Bank Nagari, Semoga Bapak/ibu dan keluarga sehat selalu,
    Kami informasikan bahwa tagihan kredit/pembiayaan bapak/ibu dengan Nomor Kredit/Pembiayaan $norek sebesar Rp $tunggakan telah jatuh tempo pada tanggal ini. Kami mengingatkan Bapak/ibu agar segera lakukan pembayaran  paling lambat pada hari ini.
    Untuk kenyamanan transaksi Bapak/ibu hindari pembayaran setelah jatuh tempo agar tidak dikenakan denda keterlambatan dan memburuknya fasilitas kredit/pembiayaan Bapak/ibu pada laporan SLIK/BI Checking dan mempengaruhi penilaian ID SCORE dalam proses kredit selanjutnya.
    Abaikan pesan ini jika anda telah melakukan pembayaran dan terima kasih telah menjadi Nasabah Setia Bank Nagari";


            $payload = [
                "api_key" => $apiKey,
                "sender"  => "6282381002236", // Anda bisa membuat ini dinamis jika perlu
                "number"  => "6282169146904", // atau mengambil dari database/request
                "message" => $message,
                "footer"  => "Bank nagari Batusangka"
            ];

            try {
                $response = Http::timeout(30)->post($apiUrl, $payload); // Timeout 30 detik

                if ($response->successful()) {
                    Log::channel('scheduler')->info('Scheduled WhatsApp message sent successfully.', ['response_body' => $response->json()]);
                    // return Command::SUCCESS; // Menandakan command sukses
                } else {
                    Log::channel('scheduler')->error('Failed to send scheduled WhatsApp message.', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                        'payload_sent' => $payload // Log payload untuk debugging
                    ]);
                    // return Command::FAILURE;
                }
            } catch (Throwable $e) { // Menangkap semua jenis error (ConnectionException, RequestException, dll.)
                Log::channel('scheduler')->critical('Exception during scheduled WhatsApp message sending.', [
                    'error_message' => $e->getMessage(),
                    'payload_sent' => $payload,
                    'trace' => $e->getTraceAsString() // Untuk debugging lebih detail jika perlu
                ]);
                // return Command::FAILURE;
            }
        }
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
                    'ACCNBR',
                    'AFFACCNBR',
                );

            //query your records from the document
            $records = $stmt->process($csv);


            // Mulai transaksi database
            DB::beginTransaction();



            try {
                // Iterasi pada setiap record dan simpan ke database
                foreach ($records as $record) {
                    $afiliasi = new Afiliasi([
                        'NO_REK' => $record['ACCNBR'] ?? null,
                        'NO_REK_AFILIASI' => $record['AFFACCNBR'] ?? null,
                    ]);

                    $afiliasi->save();
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

    public function update(Request $request, $id) {}



    public function destroy($account) {}
}
