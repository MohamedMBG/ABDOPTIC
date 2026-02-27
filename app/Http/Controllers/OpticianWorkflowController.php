<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpticianWorkflowController extends Controller
{
    /**
     * Display the workflow dashboard/list.
     */
    public function index()
    {
        if (! auth()->user()->can('sell.view') && ! auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $statuses = [
            'prescription_received' => 'Prescription Received',
            'lenses_ordered' => 'Lenses Ordered from Lab',
            'in_assembly' => 'In Assembly',
            'ready_for_pickup' => 'Ready for Pickup',
            'delivered' => 'Delivered',
        ];

        // Fetch non-delivered optical orders
        $orders = \App\Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    // Assuming we only want optical specific orders here:
                    // Either it has a prescription, or an optician status already
                    ->where(function($q) {
                        $q->whereNotNull('prescription_id')
                          ->orWhereNotNull('optician_status');
                    })
                    ->with(['contact', 'sell_lines', 'sell_lines.product'])
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

        $statuses = [
            'prescription_received' => 'Prescription Received',
            'lenses_ordered' => 'Lenses Ordered from Lab',
            'in_assembly' => 'In Assembly',
            'ready_for_pickup' => 'Ready for Pickup',
            'delivered' => 'Delivered',
        ];

        // Fetch history
        $histories = \App\OpticianStatusHistory::where('transaction_id', $id)
                        ->with(['user'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('optician.workflow.update_status_modal')
               ->with(compact('transaction', 'statuses', 'histories'));
    }
}
