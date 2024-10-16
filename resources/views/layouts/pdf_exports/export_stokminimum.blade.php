<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Stok Minimum</title>
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
            /* Rata tengah secara horizontal */
            vertical-align: middle;
            /* Rata tengah secara vertikal */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        td {
            word-wrap: break-word;
        }

        /* Mengatur proporsi kolom */
        td:nth-child(1) {
            width: 10%;
            /* id lebih kecil */
        }

        td:nth-child(2) {
            width: 40%;
            /* nama barang lebih besar */
        }

        td:nth-child(3),
        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6),
        td:nth-child(7),
        td:nth-child(8) {
            width: 12.5%;
            /* Kolom lainnya proporsional */
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
    <h1>Informasi Stok Minimum</h1>
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
                    <td>{{ $data['id'] }}</td>
                    <td>{{ $data['nama_item'] }}</td>
                    <td>{{ $data['stok'] }}</td>
                    <td>{{ $data['stok_minimum'] }}</td>
                    <td>{{ $data['jenis'] }}</td>
                    <td>{{ $data['merek'] }}</td>
                    <td>{{ $data['rak'] }}</td>
                    <td>{{ $data['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
