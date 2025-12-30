<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kalender;
use App\Models\Artikel;
use App\Models\Pura;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KalenderController extends Controller
{
    // API untuk user - get data kalender berdasarkan bulan & tahun
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|between:1,12'
        ]);
        
        $year = $request->has('year') ? $request->year : date('Y');
        $month = $request->has('month') ? $request->month : date('m');
        
        // 1. Artikel yang tampil di kalender
        $artikelKalender = Artikel::with('kategori')
            ->where('tampilkan_di_kalender', true)
            ->where('status', true)
            ->whereYear('tanggal_publikasi', $year)
            ->whereMonth('tanggal_publikasi', $month)
            ->get()
            ->map(function ($artikel) {
                return [
                    'id' => 'artikel_' . $artikel->id,
                    'type' => 'artikel',
                    'namaUpacara' => $artikel->judul,
                    'namaPura' => 'Artikel',
                    'isArtikel' => true,
                    'slug' => $artikel->slug,
                    'date' => $artikel->date_for_calendar,
                    'formatted_date' => $artikel->formatted_date,
                    'color' => 'orange',
                    'title' => $artikel->judul
                ];
            });
        
        // 2. Pengajuan yang sudah approved
        $pengajuanApproved = Kalender::with('pura')
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $year)
            ->whereMonth('tanggal_mulai', $month)
            ->get()
            ->map(function ($pengajuan) {
                return [
                    'id' => 'pengajuan_' . $pengajuan->id,
                    'type' => 'pengajuan',
                    'namaUpacara' => $pengajuan->nama_upacara,
                    'namaPura' => $pengajuan->pura->nama_pura ?? 'Pura', // PERBAIKAN: nama_pura bukan nama
                    'isArtikel' => false,
                    'isApproved' => true,
                    'pawukon' => $pengajuan->pawukon,
                    'wewaran' => $pengajuan->wewaran,
                    'deskripsi' => $pengajuan->deskripsi,
                    'date' => $pengajuan->date_for_calendar,
                    'formatted_date' => $pengajuan->formatted_date,
                    'color' => 'blue',
                    'title' => $pengajuan->nama_upacara
                ];
            });
        
        // Gabungkan data
        $events = $artikelKalender->merge($pengajuanApproved);
        
        // Group by date untuk frontend
        $groupedEvents = [];
        foreach ($events as $event) {
            $dateKey = $event['date'];
            if (!isset($groupedEvents[$dateKey])) {
                $groupedEvents[$dateKey] = [];
            }
            $groupedEvents[$dateKey][] = $event;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'month' => $month,
                'events' => $groupedEvents,
                'events_list' => $events
            ]
        ]);
    }

    // API untuk user - get events pada tanggal tertentu
    public function getEventsByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:d-m-Y'
        ]);
        
        $date = Carbon::createFromFormat('d-m-Y', $request->date);
        $dateStr = $date->format('d-m-Y');
        
        // Artikel pada tanggal tersebut
        $artikelEvents = Artikel::with('kategori')
            ->where('tampilkan_di_kalender', true)
            ->where('status', true)
            ->whereDate('tanggal_publikasi', $date->format('Y-m-d'))
            ->get()
            ->map(function ($artikel) {
                return [
                    'id' => 'artikel_' . $artikel->id,
                    'type' => 'artikel',
                    'namaUpacara' => $artikel->judul,
                    'namaPura' => 'Artikel',
                    'isArtikel' => true,
                    'slug' => $artikel->slug,
                    'excerpt' => $artikel->excerpt,
                    'color' => 'orange'
                ];
            });
        
        // Pengajuan approved pada tanggal tersebut
        $pengajuanEvents = Kalender::with('pura')
            ->where('status', 'approved')
            ->whereDate('tanggal_mulai', $date->format('Y-m-d'))
            ->get()
            ->map(function ($pengajuan) {
                return [
                    'id' => 'pengajuan_' . $pengajuan->id,
                    'type' => 'pengajuan',
                    'namaUpacara' => $pengajuan->nama_upacara,
                    'namaPura' => $pengajuan->pura->nama_pura ?? 'Pura', // PERBAIKAN
                    'isArtikel' => false,
                    'isApproved' => true,
                    'pawukon' => $pengajuan->pawukon,
                    'wewaran' => $pengajuan->wewaran,
                    'deskripsi' => $pengajuan->deskripsi,
                    'color' => 'blue'
                ];
            });
        
        $allEvents = $artikelEvents->merge($pengajuanEvents);
        
        return response()->json([
            'success' => true,
            'data' => [
                'date' => $dateStr,
                'events' => $allEvents,
                'total' => $allEvents->count()
            ]
        ]);
    }

    // API untuk user - submit pengajuan odalan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pura_id' => 'required|exists:puras,id',
            'nama_upacara' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'wewaran' => 'nullable|string',
            'pawukon' => 'nullable|string'
        ]);

        // Otomatis set kategori ke Upacara & Odalan
        $kategoriUpacara = KategoriArtikel::where('nama_kategori', 'Upacara & Odalan')->first(); // PERBAIKAN: nama_kategori
        if ($kategoriUpacara) {
            $validated['kategori_artikel_id'] = $kategoriUpacara->id;
        }

        // Set user jika ada auth
        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        $validated['status'] = 'pending';
        
        $pengajuan = Kalender::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan odalan berhasil dikirim. Menunggu persetujuan admin.',
            'data' => $pengajuan
        ], 201);
    }

    // API untuk admin - buat agenda langsung (approved)
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'pura_id' => 'required|exists:puras,id',
            'nama_upacara' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'wewaran' => 'nullable|string',
            'pawukon' => 'nullable|string'
        ]);

        // Otomatis set kategori ke Upacara & Odalan
        $kategoriUpacara = KategoriArtikel::where('nama_kategori', 'Upacara & Odalan')->first(); // PERBAIKAN
        if ($kategoriUpacara) {
            $validated['kategori_artikel_id'] = $kategoriUpacara->id;
        }

        // Admin langsung approved
        $validated['status'] = 'approved';
        $validated['approved_at'] = now();
        
        $pengajuan = Kalender::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Agenda berhasil dibuat dan langsung ditampilkan di kalender.',
            'data' => $pengajuan
        ], 201);
    }

    // API untuk admin - get pengajuan pending
    public function getPengajuanPending()
    {
        $pengajuan = Kalender::with(['pura', 'user', 'kategori'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->formatted_date,
                    'upacara' => $item->nama_upacara,
                    'lokasi' => $item->pura->nama_pura ?? 'Pura', // PERBAIKAN
                    'pura_id' => $item->pura_id,
                    'status' => $item->status,
                    'created_at' => $item->created_at->format('d/m/Y H:i'),
                    'pawukon' => $item->pawukon,
                    'wewaran' => $item->wewaran,
                    'deskripsi' => $item->deskripsi,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $pengajuan
        ]);
    }

    // API untuk admin - approve/reject pengajuan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan_admin' => 'nullable|string'
        ]);

        $pengajuan = Kalender::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin
        ];

        if ($request->status == 'approved') {
            $updateData['approved_at'] = now();
            $updateData['rejected_at'] = null;
        } elseif ($request->status == 'rejected') {
            $updateData['rejected_at'] = now();
            $updateData['approved_at'] = null;
        }

        $pengajuan->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Pengajuan berhasil di{$request->status}",
            'data' => [
                'status' => $request->status,
                'item' => [
                    'id' => $pengajuan->id,
                    'tanggal' => $pengajuan->formatted_date,
                    'upacara' => $pengajuan->nama_upacara,
                    'lokasi' => $pengajuan->pura->nama_pura ?? 'Pura', // PERBAIKAN
                    'status' => $request->status,
                ]
            ]
        ]);
    }

    // API untuk admin - riwayat validasi
    public function getRiwayatValidasi()
    {
        $riwayat = Kalender::with(['pura', 'user', 'kategori'])
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->formatted_date,
                    'upacara' => $item->nama_upacara,
                    'lokasi' => $item->pura->nama_pura ?? 'Pura', // PERBAIKAN
                    'status' => $item->status,
                    'created_at' => $item->created_at->format('d/m/Y H:i'),
                    'approved_at' => $item->approved_at ? $item->approved_at->format('d/m/Y H:i') : null,
                    'rejected_at' => $item->rejected_at ? $item->rejected_at->format('d/m/Y H:i') : null,
                    'catatan_admin' => $item->catatan_admin,
                    'pawukon' => $item->pawukon,
                    'wewaran' => $item->wewaran,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }

    // API untuk admin - delete pengajuan
    public function destroy($id)
    {
        $pengajuan = Kalender::findOrFail($id);
        $pengajuan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil dihapus'
        ]);
    }
}