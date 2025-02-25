<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Barang</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                /* Mengurangi ukuran font agar lebih pas di A4 */
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
                padding: 6px;
                /* Mengurangi padding untuk lebih banyak ruang */
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
                width: 7%;
                /* Lebih kecil untuk Kode Item*/
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
        <h1>Daftar Barang</h1>

        <!-- Tanggal terakhir update -->
        <div class="date">
            Terakhir Update: {{ $date }}
            <p>User Cetak: {{ Auth::id() }}</p>
        </div>

        <!-- Informasi Filter -->
        <div class="filter-info">
            Filter:
            <ul>
                <li>Gudang: {{ $gudang }}</li>
                <li>Pencarian: {{ $search }}</li>
            </ul>
        </div>

        <!-- Tabel Barang Masuk -->
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
                        @foreach ($data as $item)
                            <td>{{ $item }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
