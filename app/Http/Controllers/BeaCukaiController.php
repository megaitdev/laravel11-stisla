<?php

namespace App\Http\Controllers;

use App\Models\MutasiBahanBaku;
use App\Models\MutasiBarangJadi;
use App\Models\MutasiMesin;
use App\Models\PemasukanBarang;
use App\Models\PengeluaranBarang;
use App\Models\SisaScrap;
use App\Models\Wip;
use Illuminate\Http\Request;

class BeaCukaiController extends Controller
{

    function clearData($table, $month, $year)
    {
        // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT clearData!');
        $month = intval($month);
        switch ($table) {
            // #1 Pemasukan
            case 'pemasukan':
                try {
                    $pemasukan = PemasukanBarang::whereYear('tgl', $year)
                        ->whereMonth('tgl', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $pemasukan]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #2 Pengeluaran
            case 'pengeluaran':
                try {
                    $pengeluaran = PengeluaranBarang::whereYear('tgl', $year)
                        ->whereMonth('tgl', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $pengeluaran]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #3 WIP
            case 'wip':
                try {
                    $wip = Wip::where('tahun', $year)
                        ->where('bulan', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $wip]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #4 Mutasi Bahan Baku
            case 'mutasi-bahan-baku':
                try {
                    $mutasiBahanBaku = MutasiBahanBaku::where('tahun', $year)
                        ->where('bulan', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $mutasiBahanBaku]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #5 Mutasi Barang Jadi
            case 'mutasi-barang-jadi':
                try {
                    $mutasiBarangJadi = mutasiBarangJadi::where('tahun', $year)
                        ->where('bulan', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $mutasiBarangJadi]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #6 Mutasi Mesin
            case 'mutasi-mesin':
                try {
                    $mutasiMesin = MutasiMesin::where('tahun', $year)
                        ->where('bulan', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $mutasiMesin]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #7 Sisa Scrap
            case 'sisa-scrap':
                try {
                    $sisaScrap = SisaScrap::where('tahun', $year)
                        ->where('bulan', $month)
                        ->delete();
                    return response()->json(['success' => true, 'data' => $sisaScrap]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Table not found'], 500);
                break;
        }
    }

    function getData($table, $month, $year)
    {
        // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT getData!');
        $month = intval($month);
        switch ($table) {
            // #1 Pemasukan
            case 'pemasukan':
                try {
                    $pemasukan = PemasukanBarang::whereYear('tgl', $year)
                        ->whereMonth('tgl', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($pemasukan), 'data' => $pemasukan]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #2 Pengeluaran
            case 'pengeluaran':
                try {
                    $pengeluaran = PengeluaranBarang::whereYear('tgl', $year)
                        ->whereMonth('tgl', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($pengeluaran), 'data' => $pengeluaran]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #3 WIP
            case 'wip':
                try {
                    $wip = Wip::where('tahun', $year)
                        ->where('bulan', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($wip), 'data' => $wip]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #4 Mutasi Bahan Baku
            case 'mutasi-bahan-baku':
                try {
                    $mutasiBahanBaku = MutasiBahanBaku::where('tahun', $year)
                        ->where('bulan', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($mutasiBahanBaku), 'data' => $mutasiBahanBaku]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #5 Mutasi Barang Jadi
            case 'mutasi-barang-jadi':
                try {
                    $mutasiBarangJadi = mutasiBarangJadi::where('tahun', $year)
                        ->where('bulan', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($mutasiBarangJadi), 'data' => $mutasiBarangJadi]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #6 Mutasi Mesin
            case 'mutasi-mesin':
                try {
                    $mutasiMesin = MutasiMesin::where('tahun', $year)
                        ->where('bulan', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($mutasiMesin), 'data' => $mutasiMesin]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;
            // #7 Sisa Scrap
            case 'sisa-scrap':
                try {
                    $sisaScrap = SisaScrap::where('tahun', $year)
                        ->where('bulan', $month)
                        ->get();
                    return response()->json(['success' => true, 'count' => count($sisaScrap), 'data' => $sisaScrap]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }
                break;

            default:
                return response()->json(['success' => false, 'message' => 'Table not found'], 500);
                break;
        }
    }

    public function storePemasukan(Request $request)
    {
        try {
            $pemasukan = PemasukanBarang::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Pemasukan! Number : ' . $pemasukan->id);
            return response()->json(['success' => true, 'data' => $pemasukan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function storePengeluaran(Request $request)
    {
        try {
            $pengeluaran = PengeluaranBarang::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Pengeluaran! Number : ' . $pengeluaran->id);
            return response()->json(['success' => true, 'data' => $pengeluaran]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeMutasiBahanBaku(Request $request)
    {
        try {
            $mutasi_bahan_baku = MutasiBahanBaku::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Mutasi Bahan Baku! Number : ' . $mutasi_bahan_baku->id);
            return response()->json(['success' => true, 'data' => $mutasi_bahan_baku]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeWip(Request $request)
    {
        try {
            $wip = Wip::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT WIP! Number : ' . $wip->id);
            return response()->json(['success' => true, 'data' => $wip]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeMutasiBarangJadi(Request $request)
    {
        try {
            $mutasi_barang_jadi = MutasiBarangJadi::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Mutasi Barang Jadi! Number : ' . $mutasi_barang_jadi->id);
            return response()->json(['success' => true, 'data' => $mutasi_barang_jadi]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeMutasiMesin(Request $request)
    {
        try {
            $mutasi_mesin = MutasiMesin::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Mutasi Mesin! Number : ' . $mutasi_mesin->id);
            return response()->json(['success' => true, 'data' => $mutasi_mesin]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeSisaScrap(Request $request)
    {
        try {
            $sisa_scrap = SisaScrap::create($request->all());
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Sisa Scrap! Number : ' . $sisa_scrap->id);
            return response()->json(['success' => true, 'data' => $sisa_scrap]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
