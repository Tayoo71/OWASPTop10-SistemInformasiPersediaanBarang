<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stok Opname</title>
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
                margin-bottom: 20px;
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
            }

            td:nth-child(1) {
                width: 8%;
            }

            td:nth-child(2),
            td:nth-child(3) {
                width: 10%;
            }

            td:nth-child(4) {
                width: 8%;
            }

            td:nth-child(5) {
                width: 18%;
            }

            td:nth-child(6) {
                width: 8%;
            }

            td:nth-child(7) {
                width: 8%;
            }

            td:nth-child(8) {
                width: 8%;
                /* Lebar lebih kecil untuk Selisih */
            }

            td:nth-child(9) {
                width: 15%;
                /* Lebar sedikit lebih besar untuk Keterangan */
            }

            td:nth-child(10) {
                width: 8%;
                /* Lebar lebih kecil untuk User Buat */
            }

            td:nth-child(11) {
                width: 8%;
                /* Lebar lebih kecil untuk Status Barang */
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
        <h1>Stok Opname</h1>

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
                        <td>{{ $data['kode_gudang'] }}</td>
                        <td>{{ $data['nama_item'] }}</td>
                        <td>{{ $data['stok_buku'] }}</td>
                        <td>{{ $data['stok_fisik'] }}</td>
                        <td>{{ $data['selisih'] }}</td>
                        <td>{{ $data['keterangan'] }}</td>
                        <td>{{ $data['user_buat_id'] }}</td>
                        <td>{{ $data['statusBarang'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
