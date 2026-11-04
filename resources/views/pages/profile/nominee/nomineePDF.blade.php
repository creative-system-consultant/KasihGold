<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
        <style>
            /**
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
            **/
                @page {
                margin: 50px 0px 40px 0px;
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                font-family: 'Times', sans-serif;
                font-weight: 400; font-size: 12px;
                line-height: 1.4;
                margin-left: 1.5cm;
                margin-right: 1.5cm;
            }
            main{
                    page-break-inside: auto;
            }
            .header{
                background-color: #f1f1f1;
                padding: 8px;
                font-size: 11px;
            }
            td {
                padding: 8px;
                font-size: 10px;
            }
            h1 {
                font-size: 14px;
                text-align: center;
            }
            ul, ol {
                list-style-type: upper-roman;
                padding-left: 44px;
                font-size: 11px;
            }
            </style>
    </head>
    <body>
        <main>
            <div class="text-center ">
                <h1 class="font-bold">BORANG PENAMAAN</h1>
            </div>
            <div class="mt-3">
                <table class="w-full" style="border:1px solid black;">
                    <tr>
                        <th class="text-left header" style="border:1px solid black;"colspan="5" >(A) MAKLUMAT PENGEDAR</th>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="5">NAMA : <b>{{ strtoupper(auth()->user()->name) }}</b></td>
                    </tr>
                    <tr>
                        <td class="text-left"colspan="3">PG CODE : <b>{{ strtoupper(auth()->user()->profile->code) }}</b></td>
                        <td class="text-left" colspan="2">I/C NO :  <b>{{ strtoupper(auth()->user()->profile->ic) }}</b></td>
                    </tr>
                    <tr>
                        <th class="text-left header" style="border:1px solid black;"colspan="5" >(B) PERSETUJUAN PENGEDAR BERHUBUNG PENAMAAN</th>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="5" style="padding-left: 30px;">
                            1.Penama adalah benefisiari untuk menerima insentif bagi akaun CSC Gold saya. 
                            <br>
                            2.Pembayaran insentif saya kepada penama akan terbatal JIKA : - <br>
                            <ol>
                                <li>Penama belum mencapai umur 18 tahun 
                                    <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>ATAU</b>
                                </li>
                                <li>
                                    Tiada notis kematian diserah oleh penama kepada CSC Gold dalam tempoh 3 bulan dari tarikh kematian saya.
                                </li>
                            </ol>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-left header" style="border:1px solid black;"colspan="5" >(C) MAKLUMAT PENAMA</th>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="5">
                            Saya dengan ini memohon untuk menamakan individu yang disenaraikan di bawah ini sebagai PENAMA kepada akaun CSC Gold saya.
                            <br>
                            <br>
                            <table class="w-full" style="border:1px solid black;border-collapse:collapse;">
                                <tr>
                                    <th style="border:1px solid black;" width='3%'>
                                        <b>BIL</b>
                                    </th>
                                    <th style="border:1px solid black;" width='25%'>
                                        <b>NAMA PENAMA</b><br>
                                        <p class="font-normal">(MENGIKUT DOKUMEN PENGENALAN DIRI)</p>
                                    </th>
                                    <th style="border:1px solid black;" width='12%'>
                                        <b>NOMBOR DOKUMEN PENGENALAN DIRI</b>
                                    </th>
                                    <th style="border:1px solid black;" width='12%'>
                                        <b>HUBUNGAN DENGAN PENGEDAR</b>
                                    </th>
                                    <th style="border:1px solid black;" width='10%'>
                                        <b>BAHAGIAN / PERATUSAN</b>
                                    </th>
                                </tr>
                                @if (!is_null($nomineeList))
                                    @foreach ($nomineeList as $nominee)
                                        <tr>
                                            <td style="border:1px solid black;">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td style="border:1px solid black;">
                                                {{ strtoupper($nominee->nominee_name) }}
                                            </td>
                                            <td style="border:1px solid black;">
                                                {{ strtoupper($nominee->nominee_id) }}
                                            </td>
                                            <td style="border:1px solid black;">
                                                {{ strtoupper($nominee->memberRelationship->description) }}
                                            </td>
                                            <td style="border:1px solid black;">
                                                {{ strtoupper($nominee->percentage) }}%
                                            </td>
                                            <br>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-left header" style="border:1px solid black;"colspan="5" >(D) BUTIRAN PENGEDAR</th>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="5">ALAMAT SURAT- MENYURAT: <b>{{ strtoupper(auth()->user()->profile->fullAddress) }}</b></td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="3">ALAMAT E-MEL : <b>{{ auth()->user()->email }}</b></td>
                        <td class="text-left" colspan="2">NO. TEL RUMAH : <b>{{ (Str::substr(auth()->user()->profile->phone1, 0, 2) == "03") ? auth()->user()->profile->phone1 : "" }}</b></td>
                    </tr>
                    <tr>
                        <td class="text-left" colspan="3">NO. TEL BIMBIT : <b>{{ (Str::substr(auth()->user()->profile->phone1, 0, 2) == "01") ? auth()->user()->profile->phone1 : "" }}</b></td>
                        <td class="text-left" colspan="2">NO. TEL PEJABAT : <b> </b></td>
                    </tr>
                    <tbody style="border:1px solid black;"colspan="5" >
                        <tr>
                            <td class="py-6 text-left" colspan="3">
                                <p>TANDATANGAN PEMOHON : 
                                    <input type="text" value="" readonly="" style="border-bottom: 1px solid black;">
                                </p>
                            </td>
                            <td class="py-6 text-left" colspan="2">
                                <p>TARIKH :
                                    <input type="text" value="" readonly="" style="border-bottom: 1px solid black;">
                                </p>
                            </td>
                        </tr>
                    </tbody>
                    <tbody style="border:1px solid black;"colspan="5" >
                        <tr>
                            <td class="pt-6 text-left" colspan="5"><b>MAKLUMAT PENTING</b></td>
                        </tr>
                        <tr>
                            <td class="text-left" style="font-size:10px; padding-left: 30px;" colspan="5">
                                1. Orang Yang Layak Dinamakan : <br>
                                <p style="padding-left: 44px">
                                    a.Tuan/puan adalah dinasihatkan supaya menamakan waris/benefisiari yang berhak menerima insentif tuan/puan.<br>
                                    b.Tuan/puan juga dinasihatkan supaya sentiasa menyemak status penamaan dari semasa ke semasa dan mengemaskinikannya, 
                                    sekiranya perlu.
                                </p>
                                2.Syarat-syarat Untuk Pengedar Membuat Penamaan <br>
                                <p style="padding-left: 44px">
                                    a.Telah mencapai umur 18 tahun atau lebih.<br>
                                    b.Penamaan perlu dibuat oleh pengedar sendiri.<br>
                                    c.Borang Penamaan mestilah diisi dengan lengkap. (Dokumen pengenalan diri pengedar & penama, sila cetak bahagian hadapan & belakang 
                                    <br>&nbsp;&nbsp;&nbsp;Mykad atas muka surat yang sama kertas A4).
                                </p>
                                3.Kuat kuasa Penamaan:- <br>
                                <p style="padding-left: 44px">
                                    a.Borang penamaan hendaklah diterima oleh CSC Gold semasa pengedar masih hidup.<br> 
                                    b.Penamaan berkuat kuasa pada tarikh CSC Gold menerima Borang Penamaan daripada pengedar.<br>
                                    c.Insentif pengedar akan dibayar kepada penama selepas CSC Gold menerima maklumat dan butiran yang lengkap (pengenalan diri 
                                    <br>&nbsp;&nbsp;&nbsp;Pengedar dan Penama, Certified True Copy-Sijil Kematian) selepas kematian pengedar. 
                                    <br>&nbsp;&nbsp;&nbsp;(Tiada bayaran balik dari tarikh kematian pengedar sehinggatarikh kemaskini)
                                </p>
                                4.Penamaan Terbatal Apabila:-
                                <p style="padding-left: 44px">
                                    a.Pengedar membatalkan penamaan.<br> 
                                    b.Penama meninggal dunia sebelum pengedar.<br>
                                    c.Penamaan baharu dibuat.<br>
                                    d.Maklumat yang dikemukakan dalam Penamaan pengedar adalah maklumat palsu.<br>
                                    e.Penama meninggal dunia (selepas kematian pengedar) dan insentif pengedar belum dibayar kepada penama.<br>
                                    f.Tiada notis dibuat oleh penama kepada CSC Gold dalam tempoh 3 bulan dari tarikh kematian pengedar.<br>
                                </p>
                                <br>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>    
        </main>
    </body>
</html>
