<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Data;
use App\Models\Nominatif;
use App\Models\Surat;
use App\Services\ServicesApi;
use App\Traits\ApiResponser;
use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SendMessageController extends Controller
{
    //
    use ApiResponser;
    private $servicesApi; // Nama variabel konsisten dengan parameter constructor

    public function __construct(ServicesApi $serviceAPi)
    {
        $this->servicesApi = $serviceAPi;
    }

    public function showDetail($norek)
    {
        // return "hello";
        $surat = Surat::where('nomor_rekening', $norek)->get();

        if($surat->count() == 0){
            $cek = 0;
        }elseif($surat->count() == 1){
            $cek = 1;
        }elseif($surat->count() == 2){
            $cek = 2;
        }elseif($surat->count() == 3){
            $cek = 3;
        }elseif($surat->count() == 4){
            $cek = 4;
        }

        // return $cek;
        // $nasabah = Nominatif::where('NO_REK', $norek)->first();
        $nasabah = DB::select("SELECT * FROM tbl_nominatif WHERE NO_REK = ? AND TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif)", [$norek]);

        // return $nasabah[0];


        // return dd($nasabah->DENDA_TUNGGPKK);

        if (!$nasabah) {
            // Anda bisa juga menggunakan firstOrFail() untuk otomatis 404
            return redirect()->back()->with('error', 'Data nasabah tidak ditemukan.');
        }

        // Anda mungkin perlu memuat data tambahan atau melakukan transformasi data di sini
        // sebelum meneruskannya ke view.

        return view('whatsaapDetail', [
                'nasabah' => $nasabah[0],
                'surat' => $surat,
                'cek' => $cek,
            ]
        );
    }

    public function __invoke()
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

       $data = Data::where("KOLEKTIBILITY",">=","2")->get();
    //    return dd($data);

       foreach($data as $item){

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


           $message="Bapak/ibu Yth $item->NAMA_SINGKAT,
           Nasabah Setia Bank Nagari, Semoga Bapak/ibu dan keluarga sehat selalu,
           Kami informasikan bahwa tagihan kredit/pembiayaan bapak/ibu dengan Nomor Kredit/Pembiayaan $item->NO_REK sebesar Rp $tunggakan telah jatuh tempo pada tanggal 9 bulan ini. Kami mengingatkan Bapak/ibu agar segera lakukan pembayaran  paling lambat pada hari ini.
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

    // Metode index() bisa direfaktor untuk menggunakan logika yang sama atau ServicesApi
    // dan mengembalikan HTTP response yang sesuai.
    public function index()
    {
        // return "haha";
        // return Auth::user();
        $cabang = Cabang::whereId(Auth::user()->id_cabang)->first();

        $data = Nominatif::where("KOLEKTIBILITY",">=","2")->get();

        $dataKolektibilitas = DB::select("SELECT TANGGAL, (SELECT COUNT(DISTINCT NO_REK) FROM tbl_nominatif WHERE KOLEKTIBILITY = 1) AS total_nasabah_lancar, (SELECT COUNT(DISTINCT NO_REK) FROM tbl_nominatif WHERE KOLEKTIBILITY = 2) AS total_nasabah_dpk, (SELECT COUNT(DISTINCT NO_REK) FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3) AS total_nasabah_npl, SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS LANCAR, SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK, SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL, SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif where tbl_nominatif.KD_CAB_KONSOL = $cabang->kode_cabang GROUP BY TANGGAL ORDER BY TANGGAL DESC LIMIT 1");




        $dataKolektibilitas[0]->LANCAR = $this->formatRupiah(str_replace('.',',',$dataKolektibilitas[0]->LANCAR));
        $dataKolektibilitas[0]->DPK = $this->formatRupiah(str_replace('.',',',$dataKolektibilitas[0]->DPK));
        $dataKolektibilitas[0]->NPL = $this->formatRupiah(str_replace('.',',',$dataKolektibilitas[0]->NPL));
        $dataKolektibilitas[0]->NILAI_WAJAR = $this->formatRupiah(str_replace('.',',',$dataKolektibilitas[0]->NILAI_WAJAR));


        // return $dataKolektibilitas[0];


        foreach($data as $key => $item ){
            $plafond = $item->PLAFOND ?? '0';
            $cleandePlafond = str_replace(',', '.', str_replace('.', '', $plafond));
            $plafondNumeric = (float)$cleandePlafond;
            $plafond = number_format($plafondNumeric, 0, ',', '.');
            $data[$key]["PLAFOND"] = $plafond;

            $nilaiawajar = $item->NILAI_WAJAR ?? '0';
            $cleandeNilaiAwajar = str_replace(',', '.', str_replace('.', '', $nilaiawajar));
            $nilaiawajarNumeric = (float)$cleandeNilaiAwajar;
            $nilaiawajar = number_format($nilaiawajarNumeric, 0, ',', '.');
            $data[$key]["NILAI_WAJAR"] = $nilaiawajar;
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
            $data[$key]["TUNGGAKAN"] = $tunggakan;

        }

        return view('whatsaap', [
            "data" => $data,
            "dataKolektibilitas" => $dataKolektibilitas[0],
        ]);

        // return dd($data);
    }

    function formatRupiah($angka)
    {
        $rupiah = $angka ?? '0';
        $cleandRupiah= str_replace(',', '.', str_replace('.', '', $rupiah));
        $rupiahNumeric = (float)$cleandRupiah;
        return number_format($rupiahNumeric, 0, ',', '.');
    }


}
