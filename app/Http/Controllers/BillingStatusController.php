<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingStatus;
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
}
