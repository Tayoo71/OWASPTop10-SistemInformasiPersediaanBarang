<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kartu Stok</title>
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
                /* Nomor Transaksi lebih kecil */
            }

            td:nth-child(2),
            td:nth-child(3),
            td:nth-child(4),
            td:nth-child(5),
            td:nth-child(6),
            td:nth-child(7) {
                width: auto;
                /* Kolom lainnya proporsional */
            }

            .date {
                text-align: right;
                font-size: 12px;
                color: #555;
                margin-bottom: 5px;
            }

            .barang,
            .filter-info {
                text-align: left;
                color: #000;
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>
        <h1>Kartu Stok</h1>

        <!-- Tanggal terakhir update -->
        <div class="date">
            Terakhir Update: {{ $date }}
            <p>User Cetak: {{ Auth::id() }}</p>
        </div>

        <!-- Informasi Filter -->
        <div class="filter-info">
            Filter:
            <ul>
                <li>Range Tanggal: {{ $start }} - {{ $end }}</li>
                <li>Gudang: {{ $gudang }}</li>
                <li>Pencarian: {{ $search }}</li>
            </ul>
        </div>

        <!-- Informasi Barang -->
        <div class="barang">
            Barang: {{ $barang }}
        </div>

        <!-- Tabel Kartu Stok -->
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
                        <td>{{ $data['nomor_transaksi'] }}</td>
                        <td>{{ $data['gudang'] }}</td>
                        <td>{{ $data['tanggal'] }}</td>
                        <td>{{ $data['tipe_transaksi'] }}</td>
                        <td>{{ $data['jumlah'] }}</td>
                        <td>{{ $data['saldo_stok'] }}</td>
                        <td>{{ $data['keterangan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
