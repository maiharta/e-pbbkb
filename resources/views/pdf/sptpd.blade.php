<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <title>SPTPD</title>
    <style>
        body {
            border: 1px solid black;
            padding: 0;
            margin: 0;
        }

        section.one tr td:first-child {
            width: 60%;
        }

        section.one tr td {
            font-weight: bold;
        }

        /* To ensure other columns take the remaining space */
        section.one tr td:not(:first-child) {
            width: 40%;
        }
    </style>
</head>

<body>
    <section class="one">
        <table width="100%">
            <tr>
                <td>PEMERINTAH PROVINSI BALI</td>
                <td>No. SPTPD : {{ $pelaporan->sptpd_number }}</td>
            </tr>
            <tr>
                <td>BADAN PENDAPATAN DAERAH</td>
                <td>Masa Pajak : {{ $pelaporan->bulan_name }}</td>
            </tr>
            <tr>
                <td>Jl. Kapten Cok Agung Tresna Nomor 14.</td>
                <td>Tahun Pajak : {{ $pelaporan->tahun }}</td>
            </tr>
        </table>
    </section>
    <section class="two"></section>
    <hr>
</body>
