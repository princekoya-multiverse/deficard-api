<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiTicketController extends Controller
{
    /**
     * List the authenticated user's tickets.
     *
     * GET /api/tickets
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $ticketList = $tickets->map(function ($ticket) {
            return [
                'id'         => $ticket->id,
                'subject'    => $ticket->subject,
                'status'     => $ticket->status,
                'last_reply' => $ticket->updated_at->toIso8601String(),
                'created_at' => $ticket->created_at->toIso8601String(),
            ];
        });

        return response()->json(['tickets' => $ticketList]);
    }

    /**
     * List all tickets (admin only).
     *
     * GET /api/tickets/all
     */
    public function all(Request $request): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = SupportTicket::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 20);
        $tickets = $query->orderBy('id', 'desc')->paginate($perPage);

        $data = $tickets->map(function ($ticket) {
            return [
                'id'         => $ticket->id,
                'user'       => $ticket->user ? [
                    'id'         => $ticket->user->id,
                    'email'      => $ticket->user->email,
                    'first_name' => $ticket->user->first_name,
                    'last_name'  => $ticket->user->last_name,
                ] : null,
                'subject'    => $ticket->subject,
                'message'    => $ticket->message,
                'status'     => $ticket->status,
                'created_at' => $ticket->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'total' => $tickets->total(),
        ]);
    }

    /**
     * Create a support ticket.
     *
     * POST /api/tickets
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'file'    => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = SupportTicket::create([
            'user_id'  => $request->user()->id,
            'subject'  => $request->subject,
            'message'  => $request->message,
            'status'   => 'open',
        ]);

        // Handle optional file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/ticket_files'), $fileName);
            // Note: SupportTicket model doesn't have a file column by default.
            // If needed, extend the migration. For now we just store the ticket.
        }

        return response()->json([
            'id'         => $ticket->id,
            'status'     => $ticket->status,
            'created_at' => $ticket->created_at->toIso8601String(),
        ], 201);
    }

    /**
     * Reply to a ticket (admin).
     *
     * POST /api/tickets/{id}/reply
     */
    public function reply(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = SupportTicket::find($id);
        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        // Append the reply to the existing message
        $ticket->message = $ticket->message . "\n\n[Admin Reply]: " . $request->message;
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }
        $ticket->save();

        return response()->json([
            'message'   => 'Reply sent',
            'ticket_id' => (int) $id,
        ]);
    }

    /**
     * Close a ticket (admin).
     *
     * POST /api/tickets/{id}/close
     */
    public function close(Request $request, $id): JsonResponse
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket = SupportTicket::find($id);
        if (! $ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $ticket->status = 'closed';
        $ticket->save();

        return response()->json(['message' => 'Ticket closed']);
    }
}
