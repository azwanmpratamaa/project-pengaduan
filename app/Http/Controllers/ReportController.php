<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\ReportsExport;
use App\Models\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF()
    {
    // ambil data yang akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya dan jangan gunakan pagination
    // jangan lupa konvert ke array dengan toArray()
    $data = Report::with('response')->get()->toArray();
    // kirim data yang diambil kepada view yang akan ditampilkan, kirim dengan inisial
    view()->share('reports', $data);
    // panggil view blade yang akan dicetak pdf serta data yang akan digunakan
    $pdf = PDF::loadView('print', $data)->setPaper('a4', 'landscape');
    // download PDF file dengan nama tertentu
    return $pdf->download('data_pengaduan_keseluruhan.pdf');
    }

    public function cetuPDF($id)
    {
    // ambil data yang akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya dan jangan gunakan pagination
    // jangan lupa konvert ke array dengan toArray()
    $data = Report::with('response')->where('id', $id)->get()->toArray();
    // kirim data yang diambil kepada view yang akan ditampilkan, kirim dengan inisial
    view()->share('reports', $data);
    // panggil view blade yang akan dicetak pdf serta data yang akan digunakan
    $pdf = PDF::loadView('print', $data)->setPaper('a4', 'landscape');
    // download PDF file dengan nama tertentu
    return $pdf->download('data_pengaduan_keseluruhan.pdf');
    }

    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        // validasi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // ambil data dan simpan di variable
        $user = $request->only('email', 'password');
        // simpan data ke auth dengan Auth::attempt
        // cek proses penyimpanan ke auth berhasil atau tidak lewat if else
        if (Auth::attempt($user)) {
            // nesting if, if bersarang, if didalam
            // kalau data login uda masuk ke fitur Auth, dicek lagi pake if-else
            // kalau data Auth tersebut role nya admin maka masuk ke route data
            // kalau data Auth role nya petugas maka masuk ke route data.petugas
            if (Auth::user()->role == 'admin') {
                return redirect()->route('data');
            }elseif(Auth::user()->role == 'petugas') {
                return redirect()->route('data.petugas');
            }
        }else {
            return redirect()->back()->with('gagal', 'Gagal login, coba lagi!');
        }
    }

    public function exportExcel()
    {
        // nama file yang akan terdownload
        $file_name = 'data_keseluruhan_pengaduan.xlsx';
        // memanggil file ReportExport dan mendownloadnya dengan nama seperti $file_name
        return Excel::download(new ReportsExport, $file_name);
    }

    public function index()
    {
        // ASC : Ascending -> terkecil ke terbesar 1-100 / a-z
        // DESC : Descending -> terbesar ke terkecil 100-1 / z-a
        $reports = Report::orderBy('created_at', 'DESC')
        ->simplePaginate(2);
        return view('index', compact ('reports'));
    }
    // Request $ request ditambahkan karna pada halaman data ada fitur search nya, 
    //dan akan mengambil teks yang di input search
    public function data(Request $request)
    {
        // ambil data yang diinput ke input yang name nya search
        $search = $request->search;
        // where akan mencari data berdasarkan column nama
        // data yang diambil merupakan data yang 'LIKE' (terdapat) teks yang dimasukin ke input search
        // contoh: ngisi input search dengan 'fem'
        // bakal nyari ke db yang column nama nya ada isi 'fem' nya
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data', compact('reports'));
    }

    public function dataPetugas(Request $request)
    {
        $search = $request->search;
        // with : ambil relasi (nama fungsi hasOne/hasMany/belongTo di modelnya), ambil data dari relasi itu
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data_petugas', compact('reports'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validasi
        $request->validate([
            'nik' => 'required|numeric',
            'nama' => 'required',
            'no_telp' => 'required|max:13',
            'pengaduan' => 'required',
            'foto' => 'required|image|mimes:jpg,jpeg,png,svg',
        ]);

        //pindah foto ke folder public
        $image = $request->file('foto');
        $imgName = rand() . '.' . $image->extension(); // foto.jpg : 1234
        $path = public_path('assets/image/');
        $image->move($path, $imgName);
        //tambah data ke db
        Report::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'pengaduan' => $request->pengaduan,
            'foto' => $imgName,
        ]);
        return redirect()->back()->with('success', 'Berhasil menambahkan pengaduan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // cari apa yang dimaksud
        $data = Report::where('id', $id)->firstOrFail();
        // data isinya -> nik sampe foto dari pengaduan
        // bikin variable nya yang isinya ngarah ke file foto terkait
        // public_path nyari file di folder public yang namanya sama kaya $data bagian foto
        $image = public_path('assets/image/'.$data['foto']);
        // uda menu posisi fotonya, tinggal dihapus fotonya pake unlink
        unlink($image);
        // hapus data dari database
        $data->delete();
        Response::where('report_id', $id)->delete();
        // setelahnya dikembalikan lagi ke halaman awal
        return redirect()->back();
    }
}
