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
                vertical-align: middle;
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
            }

            td:nth-child(2) {
                width: 40%;
            }

            td:nth-child(3),
            td:nth-child(4),
            td:nth-child(5),
            td:nth-child(6),
            td:nth-child(7),
            td:nth-child(8) {
                width: 12.5%;
            }

            .date {
                text-align: right;
                font-size: 12px;
                color: #555;
                margin-bottom: 5px;
            }

            .filter-info {
                text-align: left;
                color: #000;
                margin-bottom: 10px;
            }

            table {
                table-layout: fixed;
                /* Menetapkan lebar kolom secara statis */
            }

            td {
                overflow-wrap: break-word;
                /* Membungkus teks jika melebihi lebar kolom */
            }
        </style>
    </head>

    <body>
        <h1>Informasi Stok Minimum</h1>

        <!-- Tanggal terakhir update -->
        <div class="date">
            Terakhir Update: {{ $date }}
            <p>User Cetak: {{ Auth::id() }}</p>
        </div>

        <div class="filter-info">
            Filter:
            <ul>
                <li>Gudang: {{ $gudang }}</li>
                <li>Pencarian: {{ $search }}</li>
            </ul>
        </div>

        <!-- Tabel Stok Minimum -->
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
