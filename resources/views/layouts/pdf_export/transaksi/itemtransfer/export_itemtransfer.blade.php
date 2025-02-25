<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Item Transfer</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 10px;
                /* Mengurangi ukuran font agar lebih pas di A4 */
                margin: 10px;
            }

            h1 {
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                table-layout: fixed;
                width: 100%;
                border-collapse: collapse;
            }

            table,
            th,
            td {
                border: 1px solid #000;
            }

            th,
            td {
                padding: 4px;
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
                overflow-wrap: break-word;
            }

            /* Mengatur proporsi kolom */
            td:nth-child(1) {
                width: 8%;
                /* Lebih kecil untuk Nomor Transaksi */
            }

            td:nth-child(2),
            td:nth-child(3) {
                width: 9%;
                /* Lebar sedang untuk Tanggal Buat dan Ubah */
            }

            td:nth-child(4) {
                width: 8%;
                /* Gudang lebih kecil */
            }

            td:nth-child(5) {
                width: 8%;
                /* Lebar normal untuk Nama Barang */
            }

            td:nth-child(6) {
                width: 10%;
                /* Lebar sedang untuk Jumlah Stok Keluar */
            }

            td:nth-child(7) {
                width: 14%;
                /* Lebih besar untuk Keterangan */
            }

            td:nth-child(8),
            td:nth-child(9),
            td:nth-child(10) {
                width: 9%;
                /* Lebih kecil untuk User Buat, User Ubah, dan Status Barang */
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
        </style>
    </head>

    <body>
        <h1>Item Transfer</h1>

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

        <!-- Tabel Barang keluar -->
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
                        <td>{{ $data['created_at'] }}</td>
                        <td>{{ $data['updated_at'] }}</td>
                        <td>{{ $data['gudang_asal'] }}</td>
                        <td>{{ $data['gudang_tujuan'] }}</td>
                        <td>{{ $data['nama_item'] }}</td>
                        <td>{{ $data['jumlah_stok_transfer'] }}</td>
                        <td>{{ $data['keterangan'] }}</td>
                        <td>{{ $data['user_buat_id'] }}</td>
                        <td>{{ $data['user_update_id'] }}</td>
                        <td>{{ $data['statusBarang'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
