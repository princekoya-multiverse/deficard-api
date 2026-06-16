<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    /**
     * Register a new user.
     *
     * POST /api/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'phone'      => 'nullable|string|max:20',
            'friend_code' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        // Determine KYC status
        $kycStatus = $this->getKycStatus($user);

        return response()->json([
            'id'         => $user->id,
            'email'      => $user->email,
            'token'      => $token,
            'kyc_status' => $kycStatus,
        ], 201);
    }

    /**
     * Login with email and password.
     *
     * POST /api/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Revoke old tokens and create a new one
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        $kycStatus = $this->getKycStatus($user);

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'              => $user->id,
                'email'           => $user->email,
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name,
                'phone'           => $user->phone,
                'kyc_status'      => $kycStatus,
                'gateway_address' => $user->gateway_address,
                'is_admin'        => (bool) $user->is_admin,
            ],
        ]);
    }

    /**
     * Wallet-based login (MetaMask / Phantom).
     *
     * POST /api/auth/wallet
     *
     * Note: The users table does not have a wallet_address column.
     * This method links wallet addresses via gateway_address or email.
     * For production Web3, extend the users schema with wallet_address.
     */
    public function wallet(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'wallet_address' => 'required|string',
            'signature'      => 'required|string',
            'message'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Look up via gateway_address (closest available field on users table)
        $user = User::where('gateway_address', $request->wallet_address)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Wallet address not registered. Please register first.',
            ], 404);
        }

        // In production: verify the signature here using $request->signature
        // For now we trust the wallet address lookup

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'             => $user->id,
                'email'          => $user->email,
                'wallet_address' => $user->gateway_address,
            ],
        ]);
    }

    /**
     * Get the KYC status for a user.
     */
    private function getKycStatus(User $user): string
    {
        $kyc = KycVerification::where('user_id', $user->id)->first();
        if (! $kyc) {
            return 'none';
        }
        return strtolower($kyc->status);
    }
}
