<?php

namespace App\Http\Controllers;

use App\Models\AnalisKredit;
use App\Models\Cabang;
use App\Models\ChartKredit;
use App\Models\Nominatif;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view.
     *
     * @return \Illuminate\View\View
     */





    public function index()
    {
        $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' order by nama_analis asc");
        $cabang = Cabang::all();
        // return $analisKredit;
        // return $this->sendWa();

        $data = DB::select("SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif GROUP BY TANGGAL
        ORDER BY TANGGAL ");

        // return $data;


        // foreach($data as $key => $value) {
        //     $data[$key]->TANGGAL = date('d F Y', strtotime($value->TANGGAL));
        // }

        $posisi = $data[count($data) - 1];

        $dataNBNPL = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3 AND TANGGAL = ?", [$posisi->TANGGAL]);
        $dataNBDPK = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2 AND TANGGAL = ?", [$posisi->TANGGAL]);
        // $dataNBLANCAR = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 1 AND TANGGAL = ?", [$posisi->TANGGAL]);



        // return $dataNBNPL;
        // return $posisi;

        $dataAxis = [];
        $dataNPL = [];
        $dataDPK = [];


        $lc = number_format((float)($posisi->Lancar ?? 0), 0, ',', '.');
        $np = number_format((float)($posisi->NPL ?? 0), 0, ',', '.');
        $dp = number_format((float)($posisi->DPK ?? 0), 0, ',', '.');




        $setD = [(float)$lc, (float)$dp, (float)$np];
        $setL = ["Lancar", "DPK", "NPL"];



        foreach ($data as $key => $value) {
            $dataAxis[$key] = date('d F Y', strtotime($value->TANGGAL));
            $dataNPL[$key] = number_format((float)($value->NPL ?? 0), 0, ',', '.');
            $dataDPK[$key] = number_format((float)($value->DPK ?? 0), 0, ',', '.');
            $dataLANCAR[$key] = number_format((float)($value->Lancar ?? 0), 0, ',', '.');
            $dataNilaiWajar[$key] = number_format((float)($value->NILAI_WAJAR ?? 0), 0, ',', '.');
        }
        // return $dataNPL;

        $chartNPL = new LarapexChart()->lineChart()
            ->addData('NPL', $dataNPL)
            ->setHeight(250)
            ->setColors(['#ff455f'])
            ->setGrid(true, '#3F51B5', 0.1)
            ->setMarkers(['#FF5722', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $chartDPK = new LarapexChart()->lineChart()
            ->addLine('DPK', $dataDPK)
            ->setColors(['#feb019'])
            ->setHeight(250)
            ->setGrid(true, '#1761AB', 0.1)
            ->setMarkers(['#1761AB', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $chartNilaiWajar = new LarapexChart()->lineChart()
            ->addLine('NILAI WAJAR', $dataNilaiWajar)
            ->setColors(['#006400'])
            ->setHeight(250)
            ->setGrid(true, '#1761AB', 0.1)
            ->setMarkers(['#006400', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $donut = new LarapexChart()->donutChart()
            ->addData($setD)
            ->setLabels($setL)
            ->setColors(['#00E396', '#feb019', '#ff455f']);

        // return dd();

        // return dd($chart);

        $dataBlast = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
         n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
         AND n.TUNGG_POKOK != 0
         AND n.TUNGG_BUNGA != 0
         AND n.NOHP != ''
         AND n.NOHP != '0'
         AND n.NOHP != '08'
         AND n.NOHP != '00'
         AND n.NOHP != '080'
         AND n.NOHP != '0812'
         AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
         AND a.NO_REK_AFILIASI != ''
         AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT");
        // return dd($dataBlast);

        // return $dataBlast[0]->NOHP;



        foreach($dataBlast as $key => $item ){
            $dataBlast[$key]->NOHP = substr_replace($dataBlast[$key]->NOHP, '62', 0, 1);
        }


        return view(
            'dasboard',
            [
                'cabang'   => $cabang,
                'analisKredit' => $analisKredit,
                'chartNPL' => $chartNPL,
                'chartDPK' => $chartDPK,
                'chartNilaiWajar' => $chartNilaiWajar,
                'posisi' => $posisi,
                'dataNBNPL' => $dataNBNPL,
                'dataNBDPK' => $dataNBDPK,
                'dataBlast' => $dataBlast,
                'donut' => $donut,

            ]
        );



        // return $data; // Kembalikan data untuk debugging atau penggunaan lebih lanjut

    }


    public function filter(Request $request)
    {
        // Logika untuk memfilter data berdasarkan input dari form
        // Misalnya, filter berdasarkan cabang, jenis kredit, analis, dll.
        // Anda bisa menggunakan query builder atau Eloquent untuk mengambil data yang sesuai

        // Contoh:
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir') ?? '';
        $cabangI = $request->input('cabang') ?? '';
        $jenisKredit = $request->input('jenis_kredit') ?? '';
        $kodeAnalis = $request->input('kode_analis') ?? '';
        $rincianKredit = $request->input('rincian_kredit') ?? '';

        $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' order by nama_analis asc");
        $cabang = Cabang::all();


        // --- LANGKAH 2: Siapkan query dinamis ---
        $sql = "SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif  ";

        $sqlDPK = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2";
        $sqlNPL = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3";

        $conditions = [];
        $params = [];

        // Filter untuk rentang tanggal (membutuhkan keduanya)
        if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
            // Pastikan nama kolom tanggal sesuai dengan tabel Anda
            $conditions[] = "tanggal BETWEEN '$tanggalAwal' AND '$tanggalAkhir'"; // Ganti nama kolom jika perlu
            $params[] = $tanggalAwal;
            $params[] = $tanggalAkhir;
        }

        // Filter untuk cabang
        if (!empty($cabangI)) {
            $conditions[] = "kd_cab = $cabangI"; // Ganti nama kolom jika perlu
            $params[] = $cabangI;
        }

        // Filter untuk jenis kredit
        if (!empty($jenisKredit)) {
            if ($jenisKredit == 1) {
                $conditions[] = "KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            } else {
                $conditions[] = "KD_PRD IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            }
        }

        // Filter untuk kode analis
        if (!empty($kodeAnalis)) {
            $conditions[] = " KD_AO = $kodeAnalis"; // Ganti nama kolom jika perlu
            $params[] = $kodeAnalis;
        }

        // Filter untuk rincian kredit (menggunakan LIKE untuk pencarian teks)
        if (!empty($rincianKredit)) {
            if ($rincianKredit == 1) {
                $conditions[] = "KD_PRD IN (0560,0562,0563)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 2) {
                $conditions[] = "KD_PRD IN (0404,0444,0524,0531,0544,0624,0631,0650,0652)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 3) {
                $conditions[] = "KD_PRD IN (0627,0628,0629)";
            } else if ($rincianKredit == 4) {
                $conditions[] = "KD_PRD NOT IN (0627,0628,0629,0404,0444,0524,0531,0544,0624,0631,0650,0652,0560,0562,0563)";
            } // Ganti nama kolom jika perlu
        }


        // --- LANGKAH 3: Gabungkan semua kondisi jika ada ---
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
            $sqlDPK .= " AND " . implode(" AND ", $conditions);
            $sqlNPL .= " AND " . implode(" AND ", $conditions);
        }

        // Tambahkan urutan jika perlu
        $sql .= " GROUP BY TANGGAL ORDER BY TANGGAL";

        // return $sql;

        $data = DB::select($sql);

        $posisi = $data[count($data) - 1];

        // return $posisi->TANGGAL;

        $sqlDPK .= " AND TANGGAL = '".$posisi->TANGGAL."'";
        $sqlNPL .= " AND TANGGAL = '".$posisi->TANGGAL."'";

        // return $sqlDPK;


        $dataNBNPL = DB::select($sqlNPL);
        $dataNBDPK = DB::select($sqlDPK);

        // $dataNBLANCAR = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 1 AND TANGGAL = ?", [$posisi->TANGGAL]);



        // return $dataNBNPL;
        // return $posisi;

        $dataAxis = [];
        $dataNPL = [];
        $dataDPK = [];


        $lc = number_format((float)($posisi->Lancar ?? 0), 0, ',', '.');
        $np = number_format((float)($posisi->NPL ?? 0), 0, ',', '.');
        $dp = number_format((float)($posisi->DPK ?? 0), 0, ',', '.');




        $setD = [(float)$lc, (float)$dp, (float)$np];
        $setL = ["Lancar", "DPK", "NPL"];



        foreach ($data as $key => $value) {
            $dataAxis[$key] = date('d F Y', strtotime($value->TANGGAL));
            $dataNPL[$key] = number_format((float)($value->NPL ?? 0), 0, ',', '.');
            $dataDPK[$key] = number_format((float)($value->DPK ?? 0), 0, ',', '.');
            $dataLANCAR[$key] = number_format((float)($value->Lancar ?? 0), 0, ',', '.');
            $dataNilaiWajar[$key] = number_format((float)($value->NILAI_WAJAR ?? 0), 0, ',', '.');
        }
        // return $dataNPL;

        $chartNPL = new LarapexChart()->lineChart()
            ->addData('NPL', $dataNPL)
            ->setHeight(250)
            ->setColors(['#ff455f'])
            ->setGrid(true, '#3F51B5', 0.1)
            ->setMarkers(['#FF5722', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $chartDPK = new LarapexChart()->lineChart()
            ->addLine('DPK', $dataDPK)
            ->setColors(['#feb019'])
            ->setHeight(250)
            ->setGrid(true, '#1761AB', 0.1)
            ->setMarkers(['#1761AB', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $chartNilaiWajar = new LarapexChart()->lineChart()
            ->addLine('NILAI WAJAR', $dataNilaiWajar)
            ->setColors(['#006400'])
            ->setHeight(250)
            ->setGrid(true, '#1761AB', 0.1)
            ->setMarkers(['#006400', '#E040FB'], 5, 2)
            ->setXAxis($dataAxis);

        $donut = new LarapexChart()->donutChart()
            ->addData($setD)
            ->setLabels($setL)
            ->setColors(['#00E396', '#feb019', '#ff455f']);

        // return dd();

        // return dd($chart);

        $dataBlast = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
         n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
         AND n.TUNGG_POKOK != 0
         AND n.TUNGG_BUNGA != 0
         AND n.NOHP != ''
         AND n.NOHP != '0'
         AND n.NOHP != '00'
         AND n.NOHP != '080'
         AND n.NOHP != '0812'
         AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
         AND a.NO_REK_AFILIASI != ''
         AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT");
        // return dd($dataBlast);


        return view(
            'dasboard',
            [
                'cabang'   => $cabang,
                'analisKredit' => $analisKredit,
                'chartNPL' => $chartNPL,
                'chartDPK' => $chartDPK,
                'chartNilaiWajar' => $chartNilaiWajar,
                'posisi' => $posisi,
                'dataNBNPL' => $dataNBNPL,
                'dataNBDPK' => $dataNBDPK,
                'dataBlast' => $dataBlast,
                'donut' => $donut,

            ]
        );
    }

    public function sendWa()
    {

        // return "sukses";

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

        $data = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
         n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
         AND n.TUNGG_POKOK != 0
         AND n.TUNGG_BUNGA != 0
         AND n.NOHP != ''
         AND n.NOHP != '0'
         AND n.NOHP != '08'
         AND n.NOHP != '00'
         AND n.NOHP != '080'
         AND n.NOHP != '0812'
         AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
         AND a.NO_REK_AFILIASI != ''
         AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT");
        // return dd($data);

        foreach($data as $key => $item ){
            $data[$key]->NOHP = substr_replace($data[$key]->NOHP, '62', 0, 1);
        }

        // return $data;




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
            // $tunggPokokNumeric = (float)$cleanedPokok;
            // $tunggBungaNumeric = (float)$cleanedBunga;

            // $tunggakanNumeric = $tunggPokokNumeric + $tunggBungaNumeric;
            $tunggakan = number_format($item->TOTAL_TUNGGAKAN, 0, ',', '.'); // $tunggakan menjadi string yang diformat

            $nama = $item->NAMA_SINGKAT;
            $norek = $item->NO_REK_AFILIASI;

            $message[] = "Bapak/ibu Yth $nama,
Nasabah Setia Bank Nagari, Semoga Bapak/ibu dan keluarga sehat selalu,
Kami informasikan bahwa tagihan kredit/pembiayaan bapak/ibu dengan Nomor Kredit/Pembiayaan $item->NO_REK sebesar Rp $tunggakan
telah jatuh tempo. Kami mengingatkan Bapak/ibu agar segera lakukan pembayaran ke nomor rekening $norek  paling lambat pada hari ini.
Untuk kenyamanan transaksi Bapak/ibu hindari pembayaran setelah jatuh tempo agar tidak dikenakan denda keterlambatan dan memburuknya fasilitas kredit/pembiayaan Bapak/ibu pada laporan SLIK/BI Checking dan mempengaruhi penilaian ID SCORE dalam proses kredit selanjutnya.
Abaikan pesan ini jika anda telah melakukan pembayaran dan terima kasih telah menjadi Nasabah Setia Bank Nagari";


            // $payload = [
            //     "api_key" => $apiKey,
            //     "sender"  => "628116656590", // Anda bisa membuat ini dinamis jika perlu
            //     // "number"  => $item->NOHP, // atau mengambil dari database/request
            //     "number"  => "6282169146904", // atau mengambil dari database/request
            //     "message" => $message,
            //     "footer"  => "Bank nagari Batusangkar"
            // ];

            // try {
            //     $response = Http::timeout(30)->post($apiUrl, $payload); // Timeout 30 detik

            //     if ($response->successful()) {
            //         Log::channel('scheduler')->info('Scheduled WhatsApp message sent successfully.', ['response_body' => $response->json()]);
            //         // return Command::SUCCESS; // Menandakan command sukses
            //         // return "Sukses mengirim pesan WhatsApp";
            //     } else {
            //         Log::channel('scheduler')->error('Failed to send scheduled WhatsApp message.', [
            //             'status' => $response->status(),
            //             'body'   => $response->body(),
            //             'payload_sent' => $payload // Log payload untuk debugging
            //         ]);
            //         // return Command::FAILURE;
            //         // return "Gagal mengirim pesan WhatsApp: " . $response->body();
            //     }
            // } catch (Throwable $e) { // Menangkap semua jenis error (ConnectionException, RequestException, dll.)
            //     Log::channel('scheduler')->critical('Exception during scheduled WhatsApp message sending.', [
            //         'error_message' => $e->getMessage(),
            //         'payload_sent' => $payload,
            //         'trace' => $e->getTraceAsString() // Untuk debugging lebih detail jika perlu
            //     ]);
            //     return "Terjadi kesalahan saat mengirim pesan WhatsApp: " . $e->getMessage();
            //     // return Command::FAILURE;
            // }
        }

        return $message;
    }




    function formatAngka($angka)
    {
        if ($angka >= 1000000000) {
            $format = number_format($angka / 1000000000, 1, ',', '.');
            return $format . " M";
        } elseif ($angka >= 1000000) {
            $format = number_format($angka / 1000000, 1, ',', '.');
            return $format . " J";
        } elseif ($angka >= 1000) {
            $format = number_format($angka / 1000, 1, ',', '.');
            return $format . " Rb";
        } else {
            return number_format($angka, 0, ',', '.');
        }
    }



    public function saveCSVtoDatabase()
    {
        $filepath = storage_path('app/imports/09juli2025.csv');
        $stream = fopen($filepath, 'r');

        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        //build a statement
        $stmt = new Statement()
            ->select(
                'KD_CAB_KONSOL',
                'NAMA_SINGKAT',
                'NILAI_WAJAR',
                'KD_CAB',
                'NO_REK',
                'KOLEKTIBILITY',
                'KD_PRD',
                'GL_PRD_NAME',
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
                    'NILAI_WAJAR' => $record['NILAI_WAJAR'] ?? null,
                    'KD_CAB' => $record['KD_CAB'] ?? null,
                    'NO_REK' => $record['NO_REK'] ?? null,
                    'KOLEKTIBILITY' => $record['KOLEKTIBILITY'] ?? null,
                    'KD_PRD' => $record['KD_PRD'] ?? null,
                    'GL_PRD_NAME' => $record['GL_PRD_NAME'] ?? null,
                    'SALDO_AKHIR' => $record['SALDO_AKHIR'] ?? null,
                    'SEKTOR_EKONOMI' => $record['SEKTOR_EKONOMI'] ?? null,
                    'TANGGAL' => '09 Juli 2025',
                ]);

                $nominatif->save();
            }

            // Commit transaksi jika semua data berhasil disimpan
            DB::commit();

            return "Data berhasil diimpor ke database."; // Atau redirect ke halaman lain
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return "Terjadi kesalahan saat mengimpor data: " . $e->getMessage();
        }

        return $records;
    }
}
