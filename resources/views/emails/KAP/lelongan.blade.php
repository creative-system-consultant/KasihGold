<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        /* Base */

        body,
        body *:not(html):not(style):not(br):not(tr):not(code) {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
                'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
        }

        body {
            -webkit-text-size-adjust: none;
            background-color: #ffffff;
            color: #3a3a3a;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        p,
        ul,
        ol,
        blockquote {
            line-height: 1.4;
            text-align: left;
        }

        a {
            color: #3869d4;
        }

        a img {
            border: none;
        }

        /* Typography */

        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        h3 {
            font-size: 14px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        p {
            font-size: 16px;
            line-height: 1.2em;
            margin-top: 0;
            text-align: center;
        }

        p.sub {
            font-size: 12px;
        }

        img {
            max-width: 100%;
        }

        /* Layout */

        .wrapper {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            background-color: #2c2b2b;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .content {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* Header */

        .header {
            padding: 25px 0;
            text-align: center;
        }

        .header1 {
            padding: 5px 0;
            text-align: center;
        }

        .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
        }

        /* Logo */

        .logo {
            height: 75px;
            max-height: 75px;
            width: 75px;
        }

        /* Body */

        .body {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            background-color: #2c2b2b;
            border-bottom: 1px solid #2c2b2b;
            border-top: 1px solid #2c2b2b;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .inner-body {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
            background-color: #ffffff;
            border-color: #e8e5ef;
            border-radius: 20px;
            border-width: 1px;
            margin: 0 auto;
            padding: 0;
            width: 570px;
            border-top: 5px solid rgb(255, 208, 0);
        }

        /* Subcopy */

        .subcopy {
            border-top: 1px solid #e8e5ef;
            margin-top: 25px;
            padding-top: 25px;
        }

        .subcopy p {
            font-size: 14px;
        }

        /* Footer */

        .footer {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
            margin: 0 auto;
            padding: 0;
            text-align: center;
            width: 570px;
        }

        .footer p {
            color: #b0adc5;
            font-size: 12px;
            text-align: center;
        }

        .footer a {
            color: #b0adc5;
            text-decoration: underline;
        }

        /* Tables */

        .table table {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 30px auto;
            width: 100%;
        }

        .table th {
            border-bottom: 1px solid #edeff2;
            margin: 0;
            padding-bottom: 8px;
        }

        .table td {
            font-size: 15px;
            margin: 0;
            padding: 0.5px 0;
        }

        .content-cell {
            max-width: 100vw;
            padding: 32px;
            text-align: center;
        }

        .content-cell4 {
            max-width: 100vw;
            padding: 10px;
            text-align: center;
        }

        /* Buttons */

        .action {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 30px auto;
            padding: 0;
            text-align: center;
            width: 100%;
        }

        .button {
            -webkit-text-size-adjust: none;
            border-radius: 4px;
            color: #fff;
            display: inline-block;
            overflow: hidden;
            text-decoration: none;
        }

        .button-blue,
        .button-primary {
            background-color: #2d3748;
            border-bottom: 8px solid #2d3748;
            border-left: 18px solid #2d3748;
            border-right: 18px solid #2d3748;
            border-top: 8px solid #2d3748;
        }

        .button-green,
        .button-success {
            background-color: #48bb78;
            border-bottom: 8px solid #48bb78;
            border-left: 18px solid #48bb78;
            border-right: 18px solid #48bb78;
            border-top: 8px solid #48bb78;
        }

        .button-red,
        .button-error {
            background-color: #e53e3e;
            border-bottom: 8px solid #e53e3e;
            border-left: 18px solid #e53e3e;
            border-right: 18px solid #e53e3e;
            border-top: 8px solid #e53e3e;
        }

        .button-yellow,
        .button-warning {
            background-color: #fcc200;
            border-bottom: 8px solid #fcc200;
            border-left: 18px solid #fcc200;
            border-right: 18px solid #fcc200;
            border-top: 8px solid #fcc200;
        }

        /* Panels */

        .panel {
            border-left: #2d3748 solid 4px;
            margin: 21px 0;
        }

        .panel-content {
            background-color: #edf2f7;
            color: #718096;
            padding: 16px;
        }

        .panel-content p {
            color: #718096;
        }

        .panel-item {
            padding: 0;
        }

        .panel-item p:last-of-type {
            margin-bottom: 0;
            padding-bottom: 0;
        }

        /* Utilities */

        .break-all {
            word-break: break-all;
        }

    </style>
</head>

<body>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Email Body -->
                    <tr>
                        <td class="header"></td>
                    </tr>
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="header">
                                        <a href="#" style="display: inline-block;">
                                            <img src="{{ asset('img/kasihAPGold.png') }}" style="width:150px; heigth:150px; "/>
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <table align="center" style="margin-bottom:20px; width:80%">
                                            <tr >
                                                <th colspan="2"  style="padding: 10px; font-weight: bold; font-size:14px; text-align:center; ">
                                                    MAKLUMAN PERMOHONAN LELONGAN ARRAHNU MELALUI  {{ config('app.name') }} (PERMOHONAN SEDANG DIPROSES)
                                                </th>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table align="center" style="margin-bottom:10px; width:80%">
                                            <tr>
                                                <td colspan="2" style="padding-bottom:10px; border-top: 1px solid rgb(196, 196, 196);"></td>
                                            </tr>
                                            <tr >
                                                <td  colspan="2" style="padding-left:10px; padding-right:10px; font-weight:700; font-size:12px; text-align:left; ">
                                                    Assalamualaikum dan Salam sejahtera,
                                                </td>
                                            </tr>
                                            <tr>
                                                <td   colspan="2" style="padding-left:10px; padding-right:10px; font-weight:700; padding-bottom:10px;  font-size:12px; text-align:left; ">
                                                    Maklumat tentang permohonan lelongan Arrahnu tuan/puan adalah seperti berikut:
                                                </td>
                                            </tr>
                                        </table>

                                        <table align="center" style="margin-bottom:10px; width:80%" class="table">
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">ID Pengguna Akaun</p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: {{ $userId }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">Nama Pemohon</p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: {{ $userName }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">No Kad Pengenalan </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: {{ $ic }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">No. Siri </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: {{ $siriNo }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">No. Siri Bida </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: {{ $bidId }}</p>
                                                </td>
                                            <tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">Rezab </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: RM {{ number_format($rezab, 2) }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">Harga Bida </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: RM {{ number_format($bidPrice, 2) }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="30%">
                                                    <p style="font-size:12px; text-align:left;">Status Permohonan </p>
                                                </td>
                                                <td style="padding-left: 10px; padding-right: 10px;" width="70%">
                                                    <p style="font-weight:bold; font-size:12px; text-align:left; color:black">: PERMOHONAN SEDANG DIPROSES</p>
                                                </td>
                                            </tr>
                                        </table>

                                        <table align="center" style="margin-bottom:20px; width:80%">
                                            <tr>
                                                <td colspan="2" style="font-weight: bold; font-size:12px; text-align:center;">Terima kasih kerana menggunakan CSC Gold!</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top:5px; font-weight: bold; font-size:12px; text-align:center;">Sebarang Soalan boleh menghubungi
                                                    <a href="mailto:customersupport@kasihapgold.com" style="color:#fcc200">CSC Customer Support</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td class="content-cell" align="center" style="color:rgba(253, 194, 2, 0.979)">
                                        © {{ date('Y') }} CSC Gold. @lang('All rights reserved.')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>