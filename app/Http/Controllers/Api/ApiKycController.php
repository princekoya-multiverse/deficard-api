<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiKycController extends Controller
{
    /**
     * Show the authenticated user's KYC submission.
     *
     * GET /api/kyc
     */
    public function index(Request $request): JsonResponse
    {
        $kyc = KycVerification::where('user_id', $request->user()->id)->first();

        if (! $kyc) {
            return response()->json([
                'status' => 'none',
                'message' => 'No KYC submission found',
            ]);
        }

        return response()->json([
            'id'             => $kyc->id,
            'status'         => strtolower($kyc->status),
            'status_message' => $kyc->status_message,
            'first_name'     => $kyc->first_name,
            'last_name'      => $kyc->last_name,
            'email'          => $kyc->email,
            'phone'          => $kyc->phone,
            'submitted_at'   => $kyc->created_at->toIso8601String(),
        ]);
    }

    /**
     * List all KYC submissions (admin only).
     *
     * GET /api/kyc/all
     */
    public function all(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = KycVerification::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $kycList = $query->orderBy('id', 'desc')->paginate($perPage);

        $data = $kycList->map(function ($kyc) {
            return [
                'id'             => $kyc->id,
                'user_id'        => $kyc->user_id,
                'first_name'     => $kyc->first_name,
                'last_name'      => $kyc->last_name,
                'email'          => $kyc->email,
                'status'         => strtolower($kyc->status),
                'status_message' => $kyc->status_message,
                'created_at'     => $kyc->created_at->toIso8601String(),
                'file_urls'      => [
                    'front' => $kyc->file1 ? url('uploads/kyc_files/' . $kyc->file1) : null,
                    'back'  => $kyc->file2 ? url('uploads/kyc_files/' . $kyc->file2) : null,
                ],
            ];
        });

        return response()->json([
            'data'     => $data,
            'total'    => $kycList->total(),
            'page'     => $kycList->currentPage(),
            'per_page' => $kycList->perPage(),
        ]);
    }

    /**
     * Approve a KYC submission (admin only).
     *
     * POST /api/kyc/{id}/approve
     */
    public function approve(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $kyc = KycVerification::find($id);
        if (! $kyc) {
            return response()->json(['message' => 'KYC record not found'], 404);
        }

        $kyc->status = 'Approved';
        if ($request->has('status_message')) {
            $kyc->status_message = $request->status_message;
        }
        $kyc->save();

        // Note: kyc_status is derived via join, not a column on users table.
        // The admin dashboard uses leftJoin to show it.
        // No explicit user.kyc_status update needed.

        return response()->json([
            'message' => 'KYC approved',
            'user_id' => $kyc->user_id,
        ]);
    }

    /**
     * Reject a KYC submission (admin only).
     *
     * POST /api/kyc/{id}/reject
     */
    public function reject(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status_message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kyc = KycVerification::find($id);
        if (! $kyc) {
            return response()->json(['message' => 'KYC record not found'], 404);
        }

        $kyc->status = 'Rejected';
        $kyc->status_message = $request->status_message;
        $kyc->save();

        return response()->json([
            'message' => 'KYC rejected',
            'user_id' => $kyc->user_id,
        ]);
    }

    /**
     * Update a KYC record's status message (admin only).
     *
     * PUT /api/kyc/{id}
     */
    public function updateMessage(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status_message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kyc = KycVerification::find($id);
        if (! $kyc) {
            return response()->json(['message' => 'KYC record not found'], 404);
        }

        $kyc->status_message = $request->status_message;
        $kyc->save();

        return response()->json(['message' => 'KYC message updated']);
    }
}
