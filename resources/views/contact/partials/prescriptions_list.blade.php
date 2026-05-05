<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Prescriptions (Ordonnances) - {{ $prescriptions->count() }} Record(s)</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm btn-modal" data-toggle="modal" data-target="#add_prescription_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            </div>
            
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="prescriptions_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th colspan="5" class="text-center bg-info">OS (Left Eye)</th>
                                <th colspan="5" class="text-center bg-warning">OD (Right Eye)</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th class="bg-info">Sph</th><th class="bg-info">Cyl</th><th class="bg-info">Axe</th><th class="bg-info">Add</th><th class="bg-info">PD</th>
                                <th class="bg-warning">Sph</th><th class="bg-warning">Cyl</th><th class="bg-warning">Axe</th><th class="bg-warning">Add</th><th class="bg-warning">PD</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prescriptions as $p)
                                <tr>
                                    <td>{{ @format_datetime($p->created_at) }}</td>
                                    
                                    <td class="bg-info">{{ $p->os_sphere }}</td>
                                    <td class="bg-info">{{ $p->os_cylinder }}</td>
                                    <td class="bg-info">{{ $p->os_axis }}</td>
                                    <td class="bg-info">{{ $p->os_addition }}</td>
                                    <td class="bg-info">{{ $p->os_pd }}</td>

                                    <td class="bg-warning">{{ $p->od_sphere }}</td>
                                    <td class="bg-warning">{{ $p->od_cylinder }}</td>
                                    <td class="bg-warning">{{ $p->od_axis }}</td>
                                    <td class="bg-warning">{{ $p->od_addition }}</td>
                                    <td class="bg-warning">{{ $p->od_pd }}</td>

                                    <td>{{ $p->notes }}</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-danger delete_prescription_button" data-href="{{ route('prescriptions.destroy', [$p->id]) }}">
                                            <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">No prescriptions found. Add one to get started!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Prescription Modal -->
<div class="modal fade" id="add_prescription_modal" tabindex="-1" role="dialog" aria-labelledby="addPrescriptionModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => route('prescriptions.store'), 'method' => 'post', 'id' => 'add_prescription_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addPrescriptionModalLabel">Add New Prescription</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="contact_id" value="{{ $contact_id }}">
                    <!-- OS (Left Eye) Form -->
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">OS - Left Eye (Oeil Gauche)</h3>
                            </div>
                            <div class="box-body row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('os_sphere', 'Sphere (SPH):') !!}
                                        {!! Form::text('os_sphere', null, ['class' => 'form-control', 'placeholder' => '-1.00']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('os_cylinder', 'Cylinder (CYL):') !!}
                                        {!! Form::text('os_cylinder', null, ['class' => 'form-control', 'placeholder' => '-0.50']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('os_axis', 'Axis (AXE):') !!}
                                        {!! Form::text('os_axis', null, ['class' => 'form-control', 'placeholder' => '180']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('os_addition', 'Addition (ADD):') !!}
                                        {!! Form::text('os_addition', null, ['class' => 'form-control', 'placeholder' => '+2.00']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {!! Form::label('os_pd', 'Pupillary Distance (PD):') !!}
                                        {!! Form::text('os_pd', null, ['class' => 'form-control', 'placeholder' => '32.5']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OD (Right Eye) Form -->
                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">OD - Right Eye (Oeil Droit)</h3>
                            </div>
                            <div class="box-body row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('od_sphere', 'Sphere (SPH):') !!}
                                        {!! Form::text('od_sphere', null, ['class' => 'form-control', 'placeholder' => '-1.00']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('od_cylinder', 'Cylinder (CYL):') !!}
                                        {!! Form::text('od_cylinder', null, ['class' => 'form-control', 'placeholder' => '-0.50']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('od_axis', 'Axis (AXE):') !!}
                                        {!! Form::text('od_axis', null, ['class' => 'form-control', 'placeholder' => '180']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        {!! Form::label('od_addition', 'Addition (ADD):') !!}
                                        {!! Form::text('od_addition', null, ['class' => 'form-control', 'placeholder' => '+2.00']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {!! Form::label('od_pd', 'Pupillary Distance (PD):') !!}
                                        {!! Form::text('od_pd', null, ['class' => 'form-control', 'placeholder' => '32.5']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('notes', 'Prescription Notes:') !!}
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Any additional medical notes from the Optometrist...']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle deletion of prescriptions
        $(document).on('click', '.delete_prescription_button', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            swal({
                title: LANG.sure,
                text: "This prescription will be deleted and cannot be recovered.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                get_contact_prescriptions();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
