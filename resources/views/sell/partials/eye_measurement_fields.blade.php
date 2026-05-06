<div class="{{ $section_class ?? 'simple-sale-details simple-sale-advanced-box' }}">
    <div class="tw-flex tw-items-center tw-justify-between tw-gap-3 tw-flex-wrap">
        <div>
            <h3 class="{{ $title_class ?? 'simple-sale-label' }}" style="margin-top: 0; margin-bottom: 6px;">
                {{ $title ?? 'Mesures des yeux' }}
            </h3>
            <p class="{{ $help_class ?? 'simple-sale-help' }}" style="margin-bottom: 0;">
                {{ $help_text ?? 'Renseignez l ordonnance du client directement pendant la vente. Les champs restent facultatifs.' }}
            </p>
        </div>
        <div class="text-muted small">
            Saisissez seulement les valeurs utiles
        </div>
    </div>

    <div class="row" style="margin-top: 18px;">
        <div class="{{ $column_class ?? 'col-md-6' }}">
            <div class="box box-info" style="border-top: 3px solid #3c8dbc;">
                <div class="box-header with-border">
                    <h3 class="box-title">OS - Oeil gauche</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('os_sphere', 'SPH', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('os_sphere', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '-1.00']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('os_cylinder', 'CYL', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('os_cylinder', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '-0.50']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('os_axis', 'AXE', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('os_axis', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '180']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                {!! Form::label('os_addition', 'ADD', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('os_addition', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '+2.00']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                {!! Form::label('os_pd', 'PD', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('os_pd', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '32.5']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="{{ $column_class ?? 'col-md-6' }}">
            <div class="box box-warning" style="border-top: 3px solid #f39c12;">
                <div class="box-header with-border">
                    <h3 class="box-title">OD - Oeil droit</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('od_sphere', 'SPH', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('od_sphere', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '-1.00']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('od_cylinder', 'CYL', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('od_cylinder', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '-0.50']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="form-group">
                                {!! Form::label('od_axis', 'AXE', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('od_axis', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '180']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                {!! Form::label('od_addition', 'ADD', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('od_addition', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '+2.00']) !!}
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                {!! Form::label('od_pd', 'PD', ['class' => $label_class ?? null]) !!}
                                {!! Form::text('od_pd', null, ['class' => $input_class ?? 'form-control', 'placeholder' => '32.5']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group {{ $notes_group_class ?? '' }}">
                {!! Form::label('prescription_notes', 'Notes de prescription:', ['class' => $label_class ?? null]) !!}
                {!! Form::textarea('prescription_notes', null, ['class' => $textarea_class ?? 'form-control', 'rows' => 2, 'placeholder' => 'Observations supplementaires du client ou de l opticien...']) !!}
            </div>
        </div>
    </div>
</div>
