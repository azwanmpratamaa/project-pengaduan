<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat</title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>
    <h2 class="title-table">Laporan Keluhan (Admin)</h2>
<div style="display: flex; justify-content: center; margin-bottom: 30px">
    <a href="{{route('logout')}}" class="back-btn" style="text-align: center">Logout</a> 
    <a href="{{route('home')}}" class="back-btn" style="text-align: center">Home</a>
</div>
<div style="display: flex; justify-content: flex-end; align-items: center;">
    {{-- menggunakan method GET karena route untuk masuk ke halaman data ini menggunakan : GET --}}
    <form action="" method="GET">
        @csrf
        <input type="text" name="search" placeholder="Cari berdasarkan nama...">
        <button type="submit" class="btn-login" style="margin-top: -1px">Cari</button>
    </form>
    {{-- refresh balik lagi ke route data karena nanti pas di klik refresh mau bersihin riwayat pencarian sebelumnya dan balikin lagi nya ke halaman data lagi --}}
    <a href="{{route('data')}}" class="back-btn" style="margin-left: 10px; margin-top: -13px">Refresh</a>
    <a href="{{route('export-pdf')}}" class="back-btn" style="margin-left: 10px; margin-top: -13px">Cetak PDF</a>
    <a href="{{route('export.excel')}}" class="back-btn" style="margin-left: 10px; margin-top: -13px">Cetak Excel</a>

</div>
<div style="padding: 0 30px">
    <table>
        <thead>
        <tr>
            <th width="5%">No</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Telp</th>
            <th>Pengaduan</th>
            <th>Gambar</th>
            <th>Status</th>
            <th>Pesan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp

            @foreach ($reports as $report)
            <tr>
                <td>{{$no++}}</td>
                <td>{{$report['nik']}}</td>
                <td>{{$report['nama']}}</td>
            {{-- mengganti format no yang 08 jadi 628 --}}
            {{-- %20 fungsinya buat ngasi spasi --}}
            {{-- target="blank" untuk buka di tab baru --}}
            @php
            // substr_replace : mengubah karakter string
                // punya 3 argumen. argumen ke-1 : data mau dimasukin ke string
                // argumen ke-2 : mulai dari index mana ubanhnya
                // argumen ke-3 : sampai index mana di ubahnya
            $telp = substr_replace($report->no_telp, "62", 0, 1);
            // kalau uda di response data reportnya, chat wa nya data dari response di tampilin
                if ($report->response) {
                $pesanWA = 'Hallo' . $report->nama . '!pengaduan anda di' . $report->response['status'] . '.Berikut pesan untuk anda : ' . $report->response['pesan'];
                }
            // kalau belum di response pengaduannya, chat wa nya kaya gini
                else {
                    $pesanWA = 'Belum ada data response!';
                }
            @endphp
            {{-- yang ditampilkan tag a dengan $telp (data no_telp yang udah di ubah jadi format 628) --}}
            <td><a href="https://wa.me/{{$telp}}?text={{$pesanWA}}" target="_blank">{{$telp}}</a></td>
                <td>{{$report['pengaduan']}}</td>
                <td>
                    {{-- menampilkan gambar full layar di tab baru --}}
                    <a href="../assets/image/{{$report->foto}}"target="_blank">
                        <img src="{{asset('assets/image/'.$report->foto)}}" width="120">
                    </a>
                </td>
                <td>
                    {{-- cek apakah data report ini sudah memiliki relasi dengan data dari with('response') --}}
                    @if ($report->response)
                    {{-- kalau ada hasil relasinya, tampilkan bagian status --}}
                        {{ $report->response['status'] }}
                    @else
                    {{-- kalau gaada tampilkan tanda ini --}}
                        -
                    @endif
                </td>
                <td>
                    {{-- cek apakah data report ini sudah memiliki relasi dengan data dari with('response') --}}
                    @if ($report->response)
                    {{-- kalau ada hasil relasinya, tampilkan bagian pesan --}}
                        {{ $report->response['pesan'] }}
                    @else
                    {{-- kalau gaada tampilkan tanda ini --}}
                        -
                    @endif
                </td>
                <td>
                    <form action="/hapus/{{$report->id}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit">HAPUS</button>
                    </form>
                    <form action="{{route('cetu.pdf', $report->id)}}" method="GET" class="btn-login" style="margin-top: -15px">
                        @csrf
                        <button class="submit">Cetak PDF</button>
                </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>