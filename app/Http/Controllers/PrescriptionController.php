<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $contact_id
     * @return \Illuminate\Http\Response
     */
    public function index($contact_id)
    {
        if (!auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $prescriptions = \App\Prescription::where('contact_id', $contact_id)
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('contact.partials.prescriptions_list', compact('prescriptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only([
                'contact_id', 'od_sphere', 'od_cylinder', 'od_axis', 'od_addition', 'od_prism', 'od_base', 'od_pd',
                'os_sphere', 'os_cylinder', 'os_axis', 'os_addition', 'os_prism', 'os_base', 'os_pd', 'notes'
            ]);
            $input['created_by'] = auth()->user()->id;

            \App\Prescription::create($input);

            $output = ['success' => true,
                        'msg' => 'Prescription added successfully.'
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => false,
                        'msg' => 'Something went wrong, please try again'
                    ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $prescription = \App\Prescription::findOrFail($id);
                $prescription->delete();

                $output = ['success' => true,
                            'msg' => 'Prescription deleted successfully.'
                        ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                
                $output = ['success' => false,
                            'msg' => 'Something went wrong, please try again'
                        ];
            }

            return $output;
        }
    }
}
