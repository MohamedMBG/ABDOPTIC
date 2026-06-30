<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OpticianWorkflowController extends Controller
{
    protected function statuses()
    {
        return [
            'prescription_received' => 'Ordonnance reçue',
            'lenses_ordered' => 'Verres commandés',
            'in_assembly' => 'Montage',
            'ready_for_pickup' => 'Prêt pour retrait',
            'delivered' => 'Livré',
        ];
    }

    /**
     * Display the workflow dashboard/list.
     */
    public function index()
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $statuses = $this->statuses();

        // Fetch non-delivered optical orders
        $orders = \App\Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    // Assuming we only want optical specific orders here:
                    // Either it has a prescription, or an optician status already
                    ->where(function($q) {
                        $q->whereNotNull('prescription_id')
                          ->orWhereNotNull('optician_status');
                    })
                    // Non-delivered only (null status = not started, keep it).
                    ->where(function($q) {
                        $q->whereNull('optician_status')
                          ->orWhere('optician_status', '!=', 'delivered');
                    })
                    ->select([
                        'id',
                        'business_id',
                        'contact_id',
                        'invoice_no',
                        'transaction_date',
                        'updated_at',
                        'prescription_id',
                        'optician_status',
                    ])
                    ->with([
                        'contact:id,name',
                        'sell_lines:id,transaction_id,product_id,quantity',
                        'sell_lines.product:id,name',
                    ])
                    ->orderBy('transaction_date', 'desc')
                    ->get();

        return view('optician.workflow.index')->with(compact('orders', 'statuses'));
    }

    /**
     * Update the status of an optical order.
     */
    public function updateStatus(Request $request, $id)
    {
        if (! auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $statuses = array_keys($this->statuses());
            $request->validate([
                'optician_status' => ['required', Rule::in($statuses)],
                'notes' => ['nullable', 'string'],
            ]);
            $status = $request->input('optician_status');
            $notes = $request->input('notes');

            $transaction = \App\Transaction::where('business_id', $business_id)
                            ->findOrFail($id);

            $transaction->optician_status = $status;
            $transaction->save();

            // Log history
            \App\OpticianStatusHistory::create([
                'transaction_id' => $id,
                'status' => $status,
                'notes' => $notes,
                'created_by' => request()->session()->get('user.id'),
                'customer_notified' => 0 // To be implemented with automation later
            ]);

            $output = ['success' => 1,
                            'msg' => 'Status updated successfully!'
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "Something went wrong"
                        ];
        }

        return redirect()->back()->with('status', $output);
    }
    /**
     * Shows modal to edit optician workflow status.
     */
    public function updateStatusModal($id)
    {
        if (! auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $transaction = \App\Transaction::where('business_id', $business_id)
                                ->findOrFail($id);

        $statuses = $this->statuses();

        // Fetch history
        $histories = \App\OpticianStatusHistory::where('transaction_id', $id)
                        ->whereHas('transaction', function ($query) use ($business_id) {
                            $query->where('business_id', $business_id);
                        })
                        ->with(['user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('optician.workflow.update_status_modal')
               ->with(compact('transaction', 'statuses', 'histories'));
    }
}
