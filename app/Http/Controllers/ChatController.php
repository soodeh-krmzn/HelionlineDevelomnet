<?php

namespace App\Http\Controllers;

use App\Services\Bale;
use App\Services\Database;
use Illuminate\Support\Arr;
use App\Models\Admin\Ticket;
use Illuminate\Http\Request;
use App\Models\Admin\Account;
use App\Models\Admin\TicketBody;
use Illuminate\Support\Facades\DB;
use Spatie\DbDumper\Databases\MySql;

class ChatController extends Controller
{

    public function index()
    {
        $tickets = auth()->user()->account->tickets()->orderByDesc(
            TicketBody::select('created_at')->whereColumn('ticket_bodies.ticket_id', 'tickets.id')->latest()->take(1)
        )->get();
        return view('chat.chat', compact('tickets'));
    }

    public function newTicket(Request $request)
    {
        if ($request->action == "new-chat") {

            $request->validate([
                'body' => $request->hasFile('file') ? 'nullable' : 'required',
                'file' => 'file|max:3000',
                'ticket_id' => 'required',
            ]);

            $fileName = null;
            if ($request->file('file')) {
                $fileName = doUpload($request->file('file'), ert('t-a'));
            }

            TicketBody::create([
                'user_id' => auth()->id(),
                'account_id' => auth()->user()->account_id,
                'ticket_id' => $request->ticket_id,
                'body' => $request->body,
                'file' => $fileName
            ]);

            //Send Message to Bale for Admin
            $bale = new Bale();
            $bale->send($request->body);

            Ticket::where(['id' => $request->ticket_id])->where('status', '!=', 'waiting-for-reply')
                ->update(['status' => 'waiting-for-expert']);

            return $request->ticket_id;
        }

        $request->validate([
            'subject' => "required|min:4",
            'body' => "required|min:4",
            'file' => 'file|max:3000'
        ]);

        $fileName = null;
        if ($request->file('file')) {
            $fileName = doUpload($request->file('file'), ert('t-a'));
        }

        DB::beginTransaction();

        $ticket = Ticket::create([
            'subject' => $request->subject,
            'user_id' => auth()->id(),
            'account_id' => auth()->user()->account_id,
            'status' => 'waiting-for-reply',
        ]);

        $ticket->chats()->create([
            'user_id' => auth()->id(),
            'account_id' => auth()->user()->account_id,
            'body' => $request->body,
            'file' => $fileName,
        ]);

        DB::commit();

        //Send Message to Bale for Admin
        $bale = new Bale();
        $bale->send($request->body);

        return $ticket->id;
    }

    public function getChat(Request $request)
    {
        $ticket = Ticket::findOrFail($request->id);
        $ticket->Chats()->where('account_id', '0')->update([
            'seen' => 1
        ]);
        // dd($ticket->chats);
        return view('chat.componets.chats', compact('ticket'));
    }

    public function getChatList(Request $request)
    {
        $target = $request->id;
        $tickets = auth()->user()->account->tickets()->orderByDesc(
            TicketBody::select('created_at')->whereColumn('ticket_bodies.ticket_id', 'tickets.id')->latest()->take(1)
        )->get();
        return view('chat.componets.ticket-list', compact('tickets', 'target'));
    }

    // public function test()
    // {
    //     $databaseName = '2db';
    //     $tempFile = tempnam(storage_path(), $databaseName);
    //      MySql::create()
    //         ->setDbName('2db')
    //         ->setUserName("Heli_dbUser")
    //         ->setPassword('1£4!xGki!@Y1')
    //         ->dumpToFile($tempFile);
    //     return response()->download($tempFile, $databaseName . '.sql')
    //         ->deleteFileAfterSend(true);
    // }

    public function original()
    {
        return view('chat.orignial-chat');
    }
}
