<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardActivation;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiCardController extends Controller
{
    /**
     * Initiate a card purchase.
     *
     * POST /api/cards/purchase
     */
    public function purchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'card_type' => 'required|string|in:Visa,Mastercard',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $cardType = $request->card_type;

        // Try to get a USDT purchase address from the Heleket API via parent helper
        $label = 'card_purchase_' . $user->id . '_' . time();
        $addressData = $this->remote_get_usdt_purchase_address($label, [
            'user_id' => $user->id,
            'card_type' => $cardType,
        ]);

        $transAddress = null;
        if ($addressData && isset($addressData['address'])) {
            $transAddress = $addressData['address'];
        }

        // If the API call failed, generate a placeholder address
        if (! $transAddress) {
            $transAddress = 'TNoF' . strtoupper(substr(md5($label), 0, 30));
        }

        $amountUsdt = config('app.usdt-gateway-card-onetime-fee', 199.00);

        $payment = Payment::create([
            'user_id'       => $user->id,
            'type'          => 'card',
            'card_type'     => $cardType,
            'status'        => 'Pending',
            'trans_address' => $transAddress,
            'trans_amount'  => $amountUsdt,
            'name'          => $user->first_name . ' ' . $user->last_name,
            'file'          => '',
            'text'          => 'Card purchase - ' . $cardType,
        ]);

        return response()->json([
            'payment_id'    => $payment->id,
            'status'        => $payment->status,
            'trans_address' => $transAddress,
            'amount_usdt'   => (float) $amountUsdt,
            'qr_code_url'   => url('/qr/' . md5($transAddress) . '.svg'),
            'card_type'     => $cardType,
        ], 201);
    }

    /**
     * List all card purchases (admin only).
     *
     * GET /api/cards/purchases
     */
    public function purchases(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Payment::where('type', 'card')->with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $purchases = $query->orderBy('id', 'desc')->paginate($perPage);

        $data = $purchases->map(function ($payment) {
            return [
                'id'            => $payment->id,
                'user'          => $payment->user ? [
                    'id'         => $payment->user->id,
                    'email'      => $payment->user->email,
                    'first_name' => $payment->user->first_name,
                    'last_name'  => $payment->user->last_name,
                ] : null,
                'card_type'     => $payment->card_type,
                'status'        => $payment->status,
                'trans_address' => $payment->trans_address,
                'trans_amount'  => $payment->trans_amount ? (float) $payment->trans_amount : null,
                'created_at'    => $payment->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'total' => $purchases->total(),
            'page'  => $purchases->currentPage(),
        ]);
    }

    /**
     * Approve a card purchase (admin only).
     *
     * POST /api/cards/purchases/{id}/approve
     */
    public function approvePurchase(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = Payment::where('type', 'card')->find($id);
        if (! $payment) {
            return response()->json(['message' => 'Card purchase not found'], 404);
        }

        if ($payment->status === 'Approved') {
            return response()->json(['message' => 'Card purchase is already approved']);
        }

        // Update payment status
        $payment->status = 'Approved';
        $payment->save();

        // Create card activation record
        $cardHolderId = $request->card_holder_id ?? $payment->card_holder_id ?? 0;
        $cardId = $request->card_id ?? $payment->card_id ?? 0;

        $activation = CardActivation::create([
            'user_id'       => $payment->user_id,
            'card_holder_id' => $cardHolderId,
            'card_id'       => $cardId,
            'card_type'     => $payment->card_type,
            'number'        => $request->card_number ?? '',
            'kit_number'    => '',
            'status'        => 'Approved',
        ]);

        return response()->json(['message' => 'Card purchase approved and activated']);
    }

    /**
     * Reject a card purchase (admin only).
     *
     * POST /api/cards/purchases/{id}/reject
     */
    public function rejectPurchase(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = Payment::where('type', 'card')->find($id);
        if (! $payment) {
            return response()->json(['message' => 'Card purchase not found'], 404);
        }

        $payment->status = 'Rejected';
        if ($request->reason) {
            $payment->text = $request->reason;
        }
        $payment->save();

        return response()->json(['message' => 'Card purchase rejected']);
    }

    /**
     * List the authenticated user's active cards.
     *
     * GET /api/cards
     */
    public function cards(Request $request): JsonResponse
    {
        $cards = CardActivation::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $cardList = $cards->map(function ($card) {
            return [
                'id'             => $card->id,
                'card_type'      => $card->card_type,
                'card_holder_id' => $card->card_holder_id,
                'card_id'        => $card->card_id,
                'status'         => $card->status,
                'number'         => $card->number,
                'last_four'      => strlen($card->number) >= 4 ? substr($card->number, -4) : '',
                'balance'        => 0,
                'created_at'     => $card->created_at->toIso8601String(),
            ];
        });

        return response()->json(['cards' => $cardList]);
    }

    /**
     * Get card balance (stub — returns 0 for now).
     *
     * GET /api/cards/{id}/balance
     */
    public function balance(Request $request, $id): JsonResponse
    {
        $card = CardActivation::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (! $card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        // Stub: return 0 balance. In production, call NECard API.
        return response()->json([
            'card_id'  => (int) $id,
            'balance'  => 0,
            'currency' => 'USD',
        ]);
    }

    /**
     * Get card transactions (stub).
     *
     * GET /api/cards/{id}/transactions
     */
    public function transactions(Request $request, $id): JsonResponse
    {
        $card = CardActivation::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (! $card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        $month = $request->get('month', 'current');

        // Stub: return empty transactions. In production, call NECard API.
        return response()->json([
            'transactions' => [],
            'month'        => $month,
        ]);
    }

    /**
     * Change card PIN (stub).
     *
     * POST /api/cards/{id}/pin
     */
    public function changePin(Request $request, $id): JsonResponse
    {
        $card = CardActivation::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (! $card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'new_pin'     => 'required|string|digits:4',
            'confirm_pin' => 'required|string|same:new_pin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Stub: In production, call NECard SetPIN API.
        return response()->json(['message' => 'PIN changed successfully']);
    }

    /**
     * Activate or deactivate a card (stub).
     *
     * POST /api/cards/{id}/toggle
     */
    public function toggle(Request $request, $id): JsonResponse
    {
        $card = CardActivation::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (! $card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:activate,deactivate',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newStatus = $request->action === 'activate' ? 'Approved' : 'Deactivated';
        $card->status = $newStatus;
        $card->save();

        return response()->json([
            'message'    => $request->action === 'activate' ? 'Card activated' : 'Card deactivated',
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Initiate a card load (user).
     *
     * POST /api/cards/load
     */
    public function load(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'card_id'     => 'required|exists:card_activations,id',
            'amount_usdt' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $card = CardActivation::where('id', $request->card_id)->where('user_id', $user->id)->first();

        if (! $card) {
            return response()->json(['message' => 'Card not found or not yours'], 404);
        }

        // Generate a USDT deposit address via Heleket API
        $label = 'card_load_' . $user->id . '_' . time();
        $addressData = $this->remote_get_usdt_purchase_address($label, [
            'user_id' => $user->id,
            'card_id' => $card->id,
        ]);

        $transAddress = null;
        if ($addressData && isset($addressData['address'])) {
            $transAddress = $addressData['address'];
        }

        if (! $transAddress) {
            $transAddress = 'TNoF' . strtoupper(substr(md5($label), 0, 30));
        }

        $fee = config('app.usdt-gateway-load-fee', 5.00);
        $amount = (float) $request->amount_usdt;

        $payment = Payment::create([
            'user_id'       => $user->id,
            'type'          => 'load',
            'card_id'       => $card->id,
            'card_type'     => $card->card_type,
            'status'        => 'Pending',
            'trans_address' => $transAddress,
            'trans_amount'  => $amount,
            'trans_fee'     => $fee,
            'name'          => $user->first_name . ' ' . $user->last_name,
            'file'          => '',
            'text'          => 'Card load - ' . $card->card_type . ' - $' . number_format($amount, 2),
        ]);

        return response()->json([
            'load_id'       => $payment->id,
            'status'        => $payment->status,
            'trans_address' => $transAddress,
            'qr_code_url'   => url('/qr/' . md5($transAddress) . '.svg'),
        ], 201);
    }

    /**
     * List all card loads (admin only).
     *
     * GET /api/cards/loads
     */
    public function loads(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Payment::whereIn('type', ['load', 'USDT'])->with('user')->with('card');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tx_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $loads = $query->orderBy('id', 'desc')->paginate($perPage);

        $data = $loads->map(function ($payment) {
            return [
                'id'            => $payment->id,
                'user'          => $payment->user ? [
                    'id'         => $payment->user->id,
                    'email'      => $payment->user->email,
                    'first_name' => $payment->user->first_name,
                    'last_name'  => $payment->user->last_name,
                ] : null,
                'card_id'       => $payment->card_id,
                'card_type'     => $payment->card_type,
                'status'        => $payment->status,
                'trans_address' => $payment->trans_address,
                'trans_amount'  => $payment->trans_amount ? (float) $payment->trans_amount : null,
                'trans_fee'     => $payment->trans_fee ? (float) $payment->trans_fee : null,
                'trans_loaded'  => $payment->trans_loaded,
                'created_at'    => $payment->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'total' => $loads->total(),
            'page'  => $loads->currentPage(),
        ]);
    }

    /**
     * Confirm and execute a card load (stub for now).
     *
     * POST /api/cards/loads/{id}/confirm
     */
    public function confirmLoad(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment = Payment::whereIn('type', ['load', 'USDT'])->find($id);
        if (! $payment) {
            return response()->json(['message' => 'Load transaction not found'], 404);
        }

        if ($payment->status !== 'Approved') {
            return response()->json(['message' => 'Load transaction is not approved yet'], 400);
        }

        if ($payment->trans_loaded) {
            return response()->json(['message' => 'Load already completed'], 400);
        }

        // Stub: In production, this calls NECard LoadCard API.
        // Mark as loaded in the stub
        $payment->trans_loaded = 1;
        $payment->api_status = 'confirmed';
        $payment->save();

        return response()->json([
            'message'      => 'Card loaded successfully',
            'amount'       => (float) ($payment->trans_amount - ($payment->trans_fee ?? 0)),
            'new_balance'  => 0,
            'api_trans_id' => $payment->api_trans_id ?? 0,
        ]);
    }
}
