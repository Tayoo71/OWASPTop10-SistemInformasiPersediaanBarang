<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Gudang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            /* Menambahkan text-align untuk header */
        }

        /* Untuk memastikan kolom Keterangan bisa membungkus teks yang panjang */
        td {
            word-wrap: break-word;
        }

        /* Membuat tabel lebih proporsional */
        td:nth-child(1) {
            width: 20%;
            /* Lebar kolom untuk Kode Gudang */
        }

        td:nth-child(2) {
            width: 30%;
            /* Lebar kolom untuk Nama Gudang */
        }

        td:nth-child(3) {
            width: 50%;
            /* Lebar kolom untuk Keterangan */
        }

        .date {
            text-align: right;
            font-size: 12px;
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h1>Daftar Gudang</h1>
    <div class="date">
        Terakhir Update: {{ $date }}
    </div>
    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <tr>
                    <td>{{ $data['kode_gudang'] }}</td>
                    <td>{{ $data['nama_gudang'] }}</td>
                    <td>{{ $data['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
