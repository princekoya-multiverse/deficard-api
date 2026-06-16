<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiUserController extends Controller
{
    /**
     * Get the authenticated user's profile.
     *
     * GET /api/users/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $kyc = KycVerification::where('user_id', $user->id)->first();
        $kycStatus = $kyc ? strtolower($kyc->status) : 'none';

        return response()->json([
            'id'              => $user->id,
            'email'           => $user->email,
            'first_name'      => $user->first_name,
            'last_name'       => $user->last_name,
            'middle_name'     => $user->middle_name,
            'phone'           => $user->phone,
            'kyc_status'      => $kycStatus,
            'gateway_address' => $user->gateway_address,
            'is_admin'        => (bool) $user->is_admin,
            'created_at'      => $user->created_at->toIso8601String(),
            'updated_at'      => $user->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Update the authenticated user's password.
     *
     * PUT /api/users/me/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password'      => 'required|string',
            'new_password'          => 'required|string|min:8',
            'confirm_password'      => 'required|string|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    }
}
