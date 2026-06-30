<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content tw-rounded-xl tw-overflow-hidden tw-shadow-2xl">
        <div class="modal-header tw-text-white tw-border-b-0 tw-p-5" style="background:#1c1a17">
            <button type="button" class="close tw-text-white tw-opacity-80 hover:tw-opacity-100" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title tw-font-bold tw-text-lg" style="font-family:'Fraunces',serif"><i class="fas fa-glasses tw-mr-2"></i> Mise à jour du Statut <span style="opacity:.7">· {{ $transaction->invoice_no }}</span></h4>
        </div>
        
        {!! Form::open(['url' => action([\App\Http\Controllers\OpticianWorkflowController::class, 'updateStatus'], [$transaction->id]), 'method' => 'POST', 'id' => 'update_optician_status_form']) !!}
        <div class="modal-body tw-px-6 tw-py-4">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="tw-text-gray-700 tw-font-bold tw-mb-2">Nouveau Statut du Laboratoire <span class="tw-text-red-500">*</span></label>
                        {!! Form::select('optician_status', $statuses, $transaction->optician_status ?? 'prescription_received', ['class' => 'form-control select2 tw-rounded-lg tw-border-gray-300', 'required']); !!}
                        <small class="tw-text-gray-500 tw-mt-1 tw-block">Le statut actuel est présélectionné.</small>
                    </div>

                    <div class="form-group tw-mt-4">
                        <label class="tw-text-gray-700 tw-font-bold tw-mb-2">Notes & Remarques (Internes)</label>
                        {!! Form::textarea('notes', null, ['class' => 'form-control tw-rounded-lg tw-border-gray-300', 'rows' => 4, 'placeholder' => 'Ex: Verres attendus pour jeudi, commande passée chez le fournisseur X...']); !!}
                    </div>

                    <div class="alert tw-bg-blue-50 tw-border tw-border-blue-200 tw-text-blue-800 tw-rounded-lg tw-mt-4">
                        <i class="fas fa-bell tw-mr-2 tw-text-blue-600"></i> Des <strong>notifications automatiques</strong> (SMS/Email) seront envoyées au patient si le statut passe à "Prêt pour retrait" ou "Livré".
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="tw-bg-gray-50 tw-rounded-lg tw-p-4 tw-border tw-border-gray-200 tw-h-full">
                        <h4 class="tw-font-bold tw-text-gray-700 tw-mb-4 tw-border-b tw-pb-2"><i class="fas fa-history tw-mr-2"></i> Historique du Suivi</h4>
                        
                        <div style="max-height: 280px; overflow-y: auto;" class="tw-pr-2">
                            @if($histories->isEmpty())
                                <p class="tw-text-gray-500 tw-italic tw-text-center tw-mt-8">Aucun historique disponible pour ce dossier.</p>
                            @else
                                <ul class="timeline tw-m-0">
                                    @foreach($histories as $history)
                                    <li>
                                        <i class="fa fa-info tw-bg-indigo-500 tw-text-white"></i>
                                        <div class="timeline-item tw-shadow-sm tw-rounded-lg tw-border tw-border-gray-100">
                                            <span class="time tw-text-gray-500"><i class="fas fa-clock"></i> {{ @format_datetime($history->created_at) }}</span>
                                            <h3 class="timeline-header tw-text-sm tw-border-b-0 tw-pb-0">
                                                <span class="tw-font-bold text-primary">{{ $history->user->user_full_name ?? 'Système' }}</span>
                                            </h3>
                                            <div class="timeline-body tw-pt-1">
                                                <span class="label tw-bg-indigo-100 tw-text-indigo-800 tw-rounded-full tw-px-3 tw-py-1 tw-text-xs tw-font-bold">
                                                    {{ $statuses[$history->status] ?? $history->status }}
                                                </span>
                                                @if(!empty($history->notes))
                                                    <p class="tw-mt-2 tw-mb-0 tw-text-gray-600 tw-bg-gray-100 tw-p-2 tw-rounded tw-text-sm">
                                                        <i class="fas fa-comment-alt tw-text-gray-400 tw-mr-1"></i> {{ $history->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="modal-footer tw-bg-gray-50 tw-border-t">
            <button type="button" class="btn btn-default tw-rounded-lg tw-font-semibold" data-dismiss="modal">@lang('messages.close')</button>
            <button type="submit" class="btn tw-rounded-lg tw-font-semibold tw-px-6 tw-text-white tw-border-none tw-shadow-md" style="background:#1c1a17">@lang('messages.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
