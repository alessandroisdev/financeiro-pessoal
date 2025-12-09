<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionAttachmentController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $request->validate([
            'files.*' => 'file|max:5120', // até 5MB cada
        ]);

        foreach ($request->file('files', []) as $file) {
            $path = $file->store('transactions/' . $transaction->id, 'private');

            TransactionAttachment::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'type' => $request->input('type', null),
            ]);
        }

        return back()->with('success', 'Arquivo(s) anexado(s) com sucesso.');
    }

    public function download(TransactionAttachment $attachment)
    {
        abort_if($attachment->user_id !== auth()->id(), 403);

        if (!Storage::disk('private')->exists($attachment->stored_name)) {
            abort(404);
        }

        return Storage::disk('private')->download(
            $attachment->stored_name,
            $attachment->original_name
        );
    }

    public function destroy(TransactionAttachment $attachment)
    {
        abort_if($attachment->user_id !== auth()->id(), 403);

        Storage::disk('private')->delete($attachment->stored_name);
        $attachment->delete();

        return back()->with('success', 'Anexo removido com sucesso.');
    }
}
