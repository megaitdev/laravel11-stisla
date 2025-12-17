<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillingStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingStatusController extends Controller
{
    /**
     * Handle the creation or update of a billing status record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upsert(Request $request)
    {
        // Define validation rules for the incoming request
        $validator = Validator::make($request->all(), [
            'MBLNR' => 'required|string|max:11', // No. Dok DO (required for identification)
            'VGBEL' => 'required|string|max:11', // No. SO (required for identification)
            'WEEKS' => 'nullable|string|max:2',
            'VTWEG' => 'nullable|string|max:5',
            'BLDAT' => 'nullable|string|max:11',
            'TGLGI' => 'nullable|string|max:11',
            'LFART' => 'nullable|string|max:5',
            'VBELN' => 'nullable|string|max:11',
            'NAME1' => 'nullable|string|max:25',
            'NAME2' => 'nullable|string|max:25',
            'NOTES' => 'nullable|string|max:11',
            'FKDAT' => 'nullable|string|max:11',
            'VBELM' => 'nullable|string|max:12',
            'STB' => 'nullable|string|max:9',
            'STBK' => 'nullable|string|max:9',
            'HNA' => 'nullable|string|max:9',
            'TDISC' => 'nullable|string|max:9',
            'NETT' => 'nullable|string|max:9',
            'LFSTK' => 'nullable|string|max:5',
            'VDATU' => 'nullable|string|max:11',
            'ZTERM' => 'nullable|string|max:20',
            'FKSTK' => 'nullable|string|max:11',
            'KUNNR' => 'nullable|string|max:11',
        ]);

        // If validation fails, return a JSON error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error.',
                'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        try {
            // Data used for finding the record (unique identification)
            $findCriteria = [
                'MBLNR' => $request->MBLNR,
                'VGBEL' => $request->VGBEL,
            ];

            // Data to be updated or created
            // We use all validated data, excluding MBLNR and VGBEL from the update part
            // as they are part of the find criteria.
            $updateOrCreateData = $request->except(['MBLNR', 'VGBEL']);

            // Use updateOrCreate to find a record by MBLNR and VGBEL,
            // if found, update it; otherwise, create a new one.
            $billingStatus = BillingStatus::updateOrCreate(
                $findCriteria,
                $updateOrCreateData
            );

            // Return a success JSON response with the created/updated record
            return response()->json([
                'success' => true,
                'message' => 'Billing status record processed successfully.',
                'data' => $billingStatus
            ], $billingStatus->wasRecentlyCreated ? 201 : 200); // 201 Created or 200 OK
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    /**
     * Display billing statuses with status A.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showWithStatusA()
    {
        try {
            // Find billing status records with fkstk equal to 'A'
            $billingStatuses = BillingStatus::where('fkstk', 'A')->get();




            // If no records are found, return a JSON error response
            if ($billingStatuses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No billing status records found with status A.',
                ], 404); // Not Found
            }

            // Return a success JSON response with the billing status data
            return response()->json([
                'success' => true,
                'message' => 'Billing status records with status A retrieved successfully.',
                'data' => $billingStatuses
            ], 200); // OK
        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving billing status records with status A.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    public function getPaymentStatuses()
    {
        // Mendapatkan tanggal hari ini
        $today = Carbon::today();

        $sample = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '>', $today)
            ->first();

        dd($sample->matrixTopEkspor);

        /**
         * 1. Status pembayaran 'A' DAN tanggal pengiriman yang diminta LEBIH DARI 7 hari dari hari ini
         * (Berarti tanggal vdatu adalah 8 hari atau lebih di masa depan dari hari ini)
         */
        $paymentsMoreThan7Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '>', $today->copy()->addDays(7))
            ->get();

        /**
         * 2. Status pembayaran 'A' DAN tanggal pengiriman yang diminta SUDAH SAMA ATAU KURANG DARI 7 hari dari hari ini
         * (Berarti tanggal vdatu adalah dalam 7 hari ke depan dari hari ini, termasuk hari ini,
         * atau sudah lewat sampai dengan 7 hari yang lalu)
         */
        $paymentsWithin7DaysOrPast = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<=', $today->copy()->addDays(7)) // Termasuk hingga 7 hari ke depan
            ->where('vdatu', '>=', $today->copy()->subDays(7))  // Termasuk hingga 7 hari ke belakang
            ->get();

        /**
         * 3. Status pembayaran 'A' DAN tanggal pengiriman yang diminta SUDAH LEWAT dari hari ini
         * (Berarti tanggal vdatu adalah tanggal kemarin atau sebelumnya)
         */
        $paymentsAlreadyPassed = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<', $today)
            ->get();

        /**
         * 4. Status pembayaran 'A' DAN tanggal pengiriman yang diminta adalah HARI INI
         */
        $paymentsToday = BillingStatus::where('fkstk', 'A')
            ->whereDate('vdatu', $today)
            ->get();

        // Mendapatkan tanggal kemarin
        $yesterday = Carbon::yesterday();

        /**
         * 1. Status pembayaran 'A' DAN vdatu sudah melewati hari ini
         * (Berarti vdatu adalah tanggal kemarin atau sebelumnya)
         * DAN vdatu berada dalam rentang 7 hari terakhir dari hari ini
         * (Artinya: vdatu adalah dari (today - 7 hari) hingga (yesterday))
         */
        $overdue0To7Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<=', $yesterday) // Sudah lewat hari ini (kemarin atau sebelumnya)
            ->where('vdatu', '>=', $today->copy()->subDays(7)) // Tidak lebih tua dari 7 hari yang lalu
            ->get();

        /**
         * 2. Status pembayaran 'A' DAN vdatu sudah lebih dari 7 hari yang lalu
         * (Artinya: vdatu adalah dari (today - 14 hari) hingga (today - 8 hari))
         */
        $overdue8To14Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<', $today->copy()->subDays(7)) // Lebih tua dari 7 hari yang lalu
            ->where('vdatu', '>=', $today->copy()->subDays(14)) // Tidak lebih tua dari 14 hari yang lalu
            ->get();

        /**
         * 3. Status pembayaran 'A' DAN vdatu sudah lebih dari 14 hari yang lalu
         * (Artinya: vdatu adalah dari (today - 21 hari) hingga (today - 15 hari))
         */
        $overdue15To21Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<', $today->copy()->subDays(14)) // Lebih tua dari 14 hari yang lalu
            ->where('vdatu', '>=', $today->copy()->subDays(21)) // Tidak lebih tua dari 21 hari yang lalu
            ->get();

        /**
         * 4. Status pembayaran 'A' DAN vdatu sudah lebih dari 21 hari yang lalu
         * (Artinya: vdatu adalah dari (today - 30 hari) hingga (today - 22 hari))
         */
        $overdue22To30Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<', $today->copy()->subDays(21)) // Lebih tua dari 21 hari yang lalu
            ->where('vdatu', '>=', $today->copy()->subDays(30)) // Tidak lebih tua dari 30 hari yang lalu
            ->get();

        /**
         * 5. Status pembayaran 'A' DAN vdatu sudah lebih dari 30 hari yang lalu
         * (Artinya: vdatu adalah lebih tua dari (today - 30 hari))
         */
        $overdueMoreThan30Days = BillingStatus::where('fkstk', 'A')
            ->where('vdatu', '<', $today->copy()->subDays(30))
            ->get();

        // Menggabungkan atau mengembalikan hasil sesuai kebutuhan Anda
        return response()->json([
            'payments_more_than_7_days_from_today' => $paymentsMoreThan7Days,
            'payments_within_7_days_or_past_7_days' => $paymentsWithin7DaysOrPast,
            'payments_already_passed_today' => $paymentsAlreadyPassed,
            'payments_today' => $paymentsToday,
            'overdue_0_to_7_days' => $overdue0To7Days,
            'overdue_8_to_14_days' => $overdue8To14Days,
            'overdue_15_to_21_days' => $overdue15To21Days,
            'overdue_22_to_30_days' => $overdue22To30Days,
            'overdue_more_than_30_days' => $overdueMoreThan30Days,
        ]);
    }
}
