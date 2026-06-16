<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ticket = new SupportTicket();
        $ticket->message = $request->message;
        $file_name = '';
        /*
        if ($request->file) {
            $file = $request->file;
            $file_name = time() . "." . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/tickets", $file_name);
        }
        */
        $ticket->file = $file_name;
        $ticket->user_id = auth()->user()->id;
        $ticket->save();
        return redirect()->route('front.home')->with('message', 'Your message send successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
