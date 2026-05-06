<div class="{{ $section_class ?? 'simple-sale-details simple-sale-advanced-box' }}">
    <style>
        .eye-rx-card {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
            overflow: hidden;
        }

        .eye-rx-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f7;
            background: #fbfcfe;
        }

        .eye-rx-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .eye-rx-help {
            margin: 4px 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        .eye-rx-tip {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            white-space: nowrap;
        }

        .eye-rx-table {
            margin-bottom: 0;
            table-layout: fixed;
        }

        .eye-rx-table th,
        .eye-rx-table td {
            vertical-align: middle !important;
            padding: 10px !important;
        }

        .eye-rx-table thead th {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            background: #f8fafc;
        }

        .eye-rx-label-cell {
            width: 110px;
            font-weight: 700;
            color: #111827;
            background: #f9fafb;
        }

        .eye-rx-od-head,
        .eye-rx-od-cell {
            background: #fff8ec;
        }

        .eye-rx-os-head,
        .eye-rx-os-cell {
            background: #eff8ff;
        }

        .eye-rx-input {
            min-height: 44px;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
            font-size: 15px;
        }

        .eye-rx-note {
            padding: 14px 16px 16px;
            border-top: 1px solid #eef2f7;
            background: #fcfcfd;
        }

        .eye-rx-note label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
        }

        .eye-rx-note textarea {
            border-radius: 10px;
        }

        @media (max-width: 767px) {
            .eye-rx-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .eye-rx-tip {
                white-space: normal;
            }

            .eye-rx-table {
                min-width: 540px;
            }
        }
    </style>
    <div class="eye-rx-card">
        <div class="eye-rx-head">
            <div>
                <h3 class="eye-rx-title">{{ $title ?? 'Mesures des yeux' }}</h3>
                <p class="eye-rx-help">{{ $help_text ?? 'Renseignez l ordonnance du client directement pendant la vente. Les champs restent facultatifs.' }}</p>
            </div>
            <div class="eye-rx-tip">Droit au centre, gauche a droite</div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered eye-rx-table">
                <thead>
                    <tr>
                        <th class="eye-rx-label-cell">Mesure</th>
                        <th class="eye-rx-od-head">OD - Oeil droit</th>
                        <th class="eye-rx-os-head">OS - Oeil gauche</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="eye-rx-label-cell">Sphere</td>
                        <td class="eye-rx-od-cell">{!! Form::text('od_sphere', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '-1.00']) !!}</td>
                        <td class="eye-rx-os-cell">{!! Form::text('os_sphere', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '-1.00']) !!}</td>
                    </tr>
                    <tr>
                        <td class="eye-rx-label-cell">Cylindre</td>
                        <td class="eye-rx-od-cell">{!! Form::text('od_cylinder', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '-0.50']) !!}</td>
                        <td class="eye-rx-os-cell">{!! Form::text('os_cylinder', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '-0.50']) !!}</td>
                    </tr>
                    <tr>
                        <td class="eye-rx-label-cell">Axe</td>
                        <td class="eye-rx-od-cell">{!! Form::text('od_axis', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '180']) !!}</td>
                        <td class="eye-rx-os-cell">{!! Form::text('os_axis', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '180']) !!}</td>
                    </tr>
                    <tr>
                        <td class="eye-rx-label-cell">Addition</td>
                        <td class="eye-rx-od-cell">{!! Form::text('od_addition', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '+2.00']) !!}</td>
                        <td class="eye-rx-os-cell">{!! Form::text('os_addition', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '+2.00']) !!}</td>
                    </tr>
                    <tr>
                        <td class="eye-rx-label-cell">PD</td>
                        <td class="eye-rx-od-cell">{!! Form::text('od_pd', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '32.5']) !!}</td>
                        <td class="eye-rx-os-cell">{!! Form::text('os_pd', null, ['class' => ($input_class ?? 'form-control') . ' eye-rx-input', 'placeholder' => '32.5']) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-group eye-rx-note {{ $notes_group_class ?? '' }}">
            {!! Form::label('prescription_notes', 'Notes de prescription:') !!}
            {!! Form::textarea('prescription_notes', null, ['class' => $textarea_class ?? 'form-control', 'rows' => 2, 'placeholder' => 'Optionnel']) !!}
        </div>
    </div>
</div>
