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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BeaCukaiController extends Controller
{
    /**
     * Get valid columns from table
     */
    private function getTableColumns($tableName, $connection = 'mysql_kaber')
    {
        $columns = DB::connection($connection)
            ->getSchemaBuilder()
            ->getColumnListing($tableName);
        return $columns;
    }

    /**
     * Filter data to only include valid table columns
     */
    private function filterValidColumns($data, $tableName, $connection = 'mysql_kaber')
    {
        $validColumns = $this->getTableColumns($tableName, $connection);
        $filteredData = [];
        
        foreach ($data as $record) {
            $filteredRecord = [];
            foreach ($record as $key => $value) {
                if (in_array($key, $validColumns)) {
                    $filteredRecord[$key] = $value;
                }
            }
            $filteredData[] = $filteredRecord;
        }
        
        return $filteredData;
    }

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
            // Get raw content directly from php://input to avoid any truncation
            // Read in chunks to handle large data
            $rawData = '';
            $handle = fopen('php://input', 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $rawData .= fread($handle, 8192); // Read 8KB chunks
                }
                fclose($handle);
            }
            
            // If empty, try alternative method
            if (empty($rawData)) {
                $rawData = $request->getContent();
            }
            
            // Save raw data to file (append mode to preserve all data)
            $logFile = storage_path('logs/pemasukan_raw_' . date('Y-m-d') . '.log');
            $logEntry = '[' . now()->toDateTimeString() . '] LENGTH:' . strlen($rawData) . ' ' . $rawData . PHP_EOL;
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            
            // Try to get data from request body
            // If rawData is empty, try to get from input
            if (empty($rawData)) {
                $rawData = json_encode($request->all());
            }
            
            // If rawData is still empty, return error
            if (empty($rawData)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No data received'
                ], 400);
            }
            
            // Try to decode JSON - handle both string JSON and already decoded array
            $data = null;
            
            // If rawData is a JSON string, decode it
            if (is_string($rawData)) {
                // Remove BOM if present
                $rawData = preg_replace('/^\xEF\xBB\xBF/', '', $rawData);
                // Trim whitespace
                $rawData = trim($rawData);
                
                $data = json_decode($rawData, true);
                
                // Check if JSON decode was successful
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Invalid JSON format: ' . json_last_error_msg()
                    ], 400);
                }
            } else {
                // If it's already an array, use it directly
                $data = $rawData;
            }
            
            // If single record, convert to array
            if (!isset($data[0]) && !empty($data)) {
                $data = [$data];
            }
            
            // Validate that data is an array
            if (!is_array($data) || empty($data)) {
                return response()->json(['success' => false, 'message' => 'Data must be an array'], 400);
            }
            
            // Prepare data for upsert - ensure all required key columns are present
            $upsertData = [];
            $uniqueKeys = ['docbc', 'nobc', 'mblnr', 'matnr', 'maktx', 'tgl'];
            
            foreach ($data as $record) {
                // Validate required keys
                $hasAllKeys = true;
                foreach ($uniqueKeys as $key) {
                    if (!isset($record[$key])) {
                        $hasAllKeys = false;
                        break;
                    }
                }
                
                if (!$hasAllKeys) {
                    continue; // Skip invalid records
                }
                
                $upsertData[] = $record;
            }
            
            if (empty($upsertData)) {
                return response()->json(['success' => false, 'message' => 'No valid records to upsert. All records must have docbc, nobc, mblnr, matnr, maktx, and tgl'], 400);
            }
            
            // Filter data to only include valid table columns
            $tableName = (new PemasukanBarang())->getTable();
            $upsertData = $this->filterValidColumns($upsertData, $tableName, 'mysql_kaber');
            
            if (empty($upsertData)) {
                return response()->json(['success' => false, 'message' => 'No valid data after filtering columns'], 400);
            }
            
            // Get all possible columns from all records to determine update columns
            $allColumns = [];
            foreach ($upsertData as $record) {
                $allColumns = array_merge($allColumns, array_keys($record));
            }
            $allColumns = array_unique($allColumns);
            
            // Remove unique keys and id from update columns (they're used for matching)
            $updateColumns = array_diff($allColumns, array_merge($uniqueKeys, ['id']));
            // Convert to indexed array (upsert requires indexed array)
            $updateColumns = array_values($updateColumns);
            
            // Perform bulk upsert
            PemasukanBarang::upsert(
                $upsertData,
                $uniqueKeys, // Unique columns for matching
                $updateColumns // Columns to update if record exists
            );
            
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Pemasukan! Count : ' . count($upsertData));
            return response()->json([
                'success' => true, 
                'message' => 'Bulk upsert completed successfully',
                'count' => count($upsertData),
                'data' => $upsertData
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function storePemasukanOld(Request $request)
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
            // Get raw content directly from php://input to avoid any truncation
            // Read in chunks to handle large data
            $rawData = '';
            $handle = fopen('php://input', 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $rawData .= fread($handle, 8192); // Read 8KB chunks
                }
                fclose($handle);
            }
            
            // If empty, try alternative method
            if (empty($rawData)) {
                $rawData = $request->getContent();
            }
            
            // Save raw data to file (append mode to preserve all data)
            $logFile = storage_path('logs/pengeluaran_raw_' . date('Y-m-d') . '.log');
            $logEntry = '[' . now()->toDateTimeString() . '] LENGTH:' . strlen($rawData) . ' ' . $rawData . PHP_EOL;
            file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            
            // Try to get data from request body
            // If rawData is empty, try to get from input
            if (empty($rawData)) {
                $rawData = json_encode($request->all());
            }
            
            // If rawData is still empty, return error
            if (empty($rawData)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No data received'
                ], 400);
            }
            
            // Try to decode JSON - handle both string JSON and already decoded array
            $data = null;
            
            // If rawData is a JSON string, decode it
            if (is_string($rawData)) {
                // Remove BOM if present
                $rawData = preg_replace('/^\xEF\xBB\xBF/', '', $rawData);
                // Trim whitespace
                $rawData = trim($rawData);
                
                $data = json_decode($rawData, true);
                
                // Check if JSON decode was successful
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Invalid JSON format: ' . json_last_error_msg()
                    ], 400);
                }
            } else {
                // If it's already an array, use it directly
                $data = $rawData;
            }
            
            // If single record, convert to array
            if (!isset($data[0]) && !empty($data)) {
                $data = [$data];
            }
            
            // Validate that data is an array
            if (!is_array($data) || empty($data)) {
                return response()->json(['success' => false, 'message' => 'Data must be an array'], 400);
            }
            
            // Prepare data for upsert - ensure all required key columns are present
            $upsertData = [];
            $uniqueKeys = ['docbc', 'nobc', 'mblnr', 'matnr', 'maktx', 'tgl'];
            
            foreach ($data as $record) {
                // Validate required keys
                $hasAllKeys = true;
                foreach ($uniqueKeys as $key) {
                    if (!isset($record[$key])) {
                        $hasAllKeys = false;
                        break;
                    }
                }
                
                if (!$hasAllKeys) {
                    continue; // Skip invalid records
                }
                
                $upsertData[] = $record;
            }
            
            if (empty($upsertData)) {
                return response()->json(['success' => false, 'message' => 'No valid records to upsert. All records must have docbc, nobc, mblnr, matnr, maktx, and tgl'], 400);
            }
            
            // Filter data to only include valid table columns
            $tableName = (new PengeluaranBarang())->getTable();
            $upsertData = $this->filterValidColumns($upsertData, $tableName, 'mysql_kaber');
            
            if (empty($upsertData)) {
                return response()->json(['success' => false, 'message' => 'No valid data after filtering columns'], 400);
            }
            
            // Get all possible columns from all records to determine update columns
            $allColumns = [];
            foreach ($upsertData as $record) {
                $allColumns = array_merge($allColumns, array_keys($record));
            }
            $allColumns = array_unique($allColumns);
            
            // Remove unique keys and id from update columns (they're used for matching)
            $updateColumns = array_diff($allColumns, array_merge($uniqueKeys, ['id']));
            // Convert to indexed array (upsert requires indexed array)
            $updateColumns = array_values($updateColumns);
            
            // Perform bulk upsert
            PengeluaranBarang::upsert(
                $upsertData,
                $uniqueKeys, // Unique columns for matching
                $updateColumns // Columns to update if record exists
            );
            
            // $this->megacanSendMessage('628989227992', 'Bea Cukai API HIT Pengeluaran! Count : ' . count($upsertData));
            return response()->json([
                'success' => true, 
                'message' => 'Bulk upsert completed successfully',
                'count' => count($upsertData),
                'data' => $upsertData
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function storePengeluaranOld(Request $request)
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
