<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$JenisSurat}}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap');

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1;
            color: #000;
            background-color: #fff;
            margin-left: 50px;
            margin-right: 50px;
            padding-top: 2cm;
            /* padding: 2.5cm; */
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header .logo-container {
            display: flex;
            align-items: center;
        }

        .header .logo {
            width: 150px;
            height: auto;
            margin-right: 15px;
        }

        .header .tagline {
            font-size: 10pt;
            font-weight: bold;
        }

        .letter-date {
            text-align: left;
        }

        .letter-info {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .letter-info table, .recipient table {
            width: 100%;
            border-collapse: collapse;
        }

        .letter-info td, .recipient td {
            padding: 2px 0;
            vertical-align: top;
        }

        .letter-info td:first-child {
            width: 80px;
        }
        .letter-info td:nth-child(2) {
            width: 10px;
        }

        .recipient {
            margin-top: 20px;
        }

        .main-content {
            margin-top: 20px;
        }

        .main-content p {
            text-align: justify;
            margin: 15px 0;
        }

        .main-content ol {
            padding-left: 20px;
            margin: 15px 0;
        }

        .main-content li {
            padding-left: 5px;
            text-align: justify;
        }
        .arrears-table {
            margin-left: 40px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .arrears-table table {
            border-collapse: collapse;
        }

        .arrears-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .arrears-table td:nth-child(1) { width: 150px; }
        .arrears-table td:nth-child(2) { width: 10px; }
        .arrears-table td:nth-child(3) { width: 25px; }
        .arrears-table td:nth-child(4) { text-align: left; width: 120px; }

        .arrears-table tr.total td {
            border-top: 1px solid #000;
            font-weight: bold;
        }


        .closing-note {
            font-style: italic;
            margin-left: 40px;
            margin-bottom: 20px;
        }

        .signature-block {
    text-align: center;
    width: 250px; /* atau lebar yang sesuai */
    float: right;
    clear: both; /* Untuk memastikan elemen setelahnya tidak terpengaruh float */
}
/* Anda mungkin perlu menghapus display: flex dari .signature-section jika menggunakan float */
.signature-section {
    /* display: flex; */ /* Hapus atau komentari */
    /* justify-content: space-between; */
    /* align-items: flex-start; */
    margin-top: 40px;
    overflow: auto; /* Untuk contain float jika .signature-section membungkusnya */
}

        .tembusan {
             font-size: 11pt;
        }


        .signature-block .bank-logo-signature {
            width: 80px;
            margin-bottom: -10px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 70px; /* Space for signature */
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 2.5cm;
            border-top: 2px solid #000;
            font-size: 8pt;
            text-align: center;
        }

        .scanner-info {
            position: fixed;
            bottom: 5px;
            right: 10px;
            font-size: 9pt;
            color: #555;
        }

    </style>
</head>
<body>

    <div class="container">
        {{-- <div class="header">
            <div class="logo-container">
                <img src="{{ public_path('logo_nagari.png')}}" alt="Bank Nagari Logo" class="logo">
                <span class="tagline">bersama membangun indonesia</span>
            </div>
        </div> --}}

        <table style="width:100%; margin-top: 5px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="letter-info">
                        <table>
                            <tr>
                                <td>Nomor</td>
                                <td>:</td>
                                <td>SR/{{ $nosurat}}/TPBS/UM/{{ substr($surat->tanggal_surat, 5, 2)}}-{{ substr($surat->tanggal_surat, 0, 4)}}</td>
                            </tr>
                            <tr>
                                <td>Lampiran</td>
                                <td>:</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Perihal</td>
                                <td>:</td>
                                <td><b>{{ $JenisSurat}}</b></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 28%; vertical-align: top;">
                     <p class="letter-date">Tabek Patah, {{ \Carbon\Carbon::parse($surat->tanggal_surat)->isoFormat('D MMMM YYYY')}}</p>
                </td>
            </tr>
        </table>


        <p>Kepada</p>
        <p><strong>Sdr. {{$data->NAMA_SINGKAT}}</strong><br>
        {{$surat->alamat}}<br></p>

        <div class="main-content">
            <p>Menunjuk surat kami No.SR/{{ $surat_sebelumnya->nomor_surat}}/TPBS/UM/{{ substr($surat_sebelumnya->tanggal_surat, 5, 2)}}-{{ substr($surat_sebelumnya->tanggal_surat, 0, 4)}} tanggal {{ \Carbon\Carbon::parse($surat_sebelumnya->tanggal_surat)->isoFormat('D MMMM YYYY')}} perihal {{$surat1}}, dengan ini kami sampaikan sebagai berikut:</p>

            <ol>
                <li>{{$surat1}} tersebut belum menjadi perhatian Saudara.</li>
                <li>Tunggakan Saudara sampai dengan tanggal {{ \Carbon\Carbon::parse($surat->tanggal_surat)->isoFormat('D MMMM YYYY') }} tercatat sebagai berikut:</li>
            </ol>

            <div class="arrears-table">
                <table>
                    <tr>
                        <td>&bull; Tunggakan Pokok</td>
                        <td>:</td>
                        <td>Rp</td>
                        <td>{{ number_format((float)($surat->tunggakan_pokok ?? 0), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>&bull; Tunggakan Bunga</td>
                        <td>:</td>
                        <td>Rp</td>
                        <td>{{ number_format((float)($surat->tunggakan_bunga?? 0), 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>&bull; Denda</td>
                        <td>:</td>
                        <td>Rp</td>
                        <td>{{ $total_denda }}</td>
                    </tr>
                    <tr class="total">
                        <td>Jumlah</td>
                        <td>:</td>
                        <td>Rp</td>
                        <td>{{ $total }}</td>
                </table>
            </div>

            <p style="padding-left: 30px" class="closing-note">Jumlah tersebut belum termasuk bunga/denda yang sedang berjalan yang juga merupakan kewajiban Saudara.</p>

            <ol start="3">
                <li>Untuk menghindari hal-hal lain yang akan memberatkan Saudara nantinya terhadap penyelesaian kredit Saudara melalui saluran hukum yang berlaku maka dengan ini kami minta Saudara untuk melunasi tunggakan tersebut dengan segera.</li>
             </ol>
            <p>Sehubungan hal tersebut dengan ini kami minta Saudara untuk melunasi tunggakan tersebut dengan segera. Demikian surat ini disampaikan, atas perhatian Saudara kami ucapkan terima kasih.</p>

        </div>


        <div class="signature-section">

            <div class="signature-block">
                Hormat kami,<br>
                <p class="signature-name">YUDHISTIRA HADINOSYA</p>

                <p style="margin-top: -10px">Pemimpin Capem</p>

            </div>
        </div>

    </div>


</body>
</html>
