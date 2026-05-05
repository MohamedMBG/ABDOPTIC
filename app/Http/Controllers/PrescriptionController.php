<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        $contact = $this->getAccessibleContact($contact_id);

        $prescriptions = Prescription::where('contact_id', $contact->id)
                            ->where('business_id', $contact->business_id)
                            ->orderBy('created_at', 'desc')
                            ->get();
        
        return view('contact.partials.prescriptions_list', [
            'prescriptions' => $prescriptions,
            'contact_id' => $contact->id,
        ]);
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
            $business_id = $request->session()->get('user.business_id');
            $request->validate([
                'contact_id' => [
                    'required',
                    Rule::exists('contacts', 'id')->where(function ($query) use ($business_id) {
                        $query->where('business_id', $business_id);
                    }),
                ],
            ]);

            $contact = $this->getAccessibleContact($request->input('contact_id'));
            $input = $request->only([
                'contact_id', 'od_sphere', 'od_cylinder', 'od_axis', 'od_addition', 'od_prism', 'od_base', 'od_pd',
                'os_sphere', 'os_cylinder', 'os_axis', 'os_addition', 'os_prism', 'os_base', 'os_pd', 'notes'
            ]);
            $input['contact_id'] = $contact->id;
            $input['business_id'] = $contact->business_id;
            $input['created_by'] = auth()->user()->id;

            Prescription::create($input);

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
                $prescription = $this->getScopedPrescription($id);
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

    protected function getAccessibleContact($contact_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $query = Contact::where('business_id', $business_id)
            ->whereIn('type', ['customer', 'both'])
            ->where('id', $contact_id);

        if (! auth()->user()->can('customer.view') && auth()->user()->can('customer.view_own')) {
            $user_id = auth()->user()->id;
            $query->leftJoin('user_contact_access as uca', 'contacts.id', '=', 'uca.contact_id')
                ->where(function ($q) use ($user_id) {
                    $q->where('contacts.created_by', $user_id)
                        ->orWhere('uca.user_id', $user_id);
                })
                ->select('contacts.*')
                ->distinct();
        }

        return $query->firstOrFail();
    }

    protected function getScopedPrescription($id)
    {
        $business_id = request()->session()->get('user.business_id');

        return Prescription::where('id', $id)
            ->where('business_id', $business_id)
            ->firstOrFail();
    }
}
