@extends('layouts.app')
@section('title', 'Suivi du Laboratoire (Opticien)')

@section('content')
<style>
    .kanban-board {
        display: flex;
        padding-bottom: 2rem;
        gap: 1rem;
        min-height: calc(100vh - 150px);
        align-items: stretch;
        width: 100%;
    }
    .kanban-board-container {
        width: 100%;
        display: block;
    }
    .kanban-col {
        flex: 1 1 0;
        min-width: 0; /* Allow columns to shrink properly */
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), inset 0 2px 4px 0 rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(226, 232, 240, 0.9);
    }
    .kanban-header {
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(226, 232, 240, 0.8);
        color: #1e293b;
        letter-spacing: -0.01em;
    }
    .kanban-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.03);
        border: 1px solid rgba(241, 245, 249, 1);
        border-left: 6px solid #3b82f6;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .kanban-card::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .kanban-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 14px 24px -4px rgba(0, 0, 0, 0.08), 0 6px 10px -3px rgba(0, 0, 0, 0.04);
        border-color: #e2e8f0;
    }
    .kanban-card:hover::after {
        opacity: 1;
    }
    .kanban-card-title {
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        line-height: 1.2;
    }
    .kanban-card-subtitle {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }
    .kanban-card-content {
        font-size: 0.85rem;
        color: #334155;
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        border: 1px solid #e2e8f0;
    }
    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-top: 1px dashed #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.25rem;
    }
    .status-prescription_received { border-left-color: #6366f1; }
    .status-lenses_ordered { border-left-color: #f59e0b; }
    .status-in_assembly { border-left-color: #ec4899; }
    .status-ready_for_pickup { border-left-color: #10b981; }
    .status-delivered { border-left-color: #94a3b8; opacity: 0.85; filter: grayscale(20%); }
    
    .badge-count {
        background: rgba(15, 23, 42, 0.06);
        color: #334155;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .btn-update-status {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
    }
    .btn-update-status:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.4);
        color: white;
    }
    .btn-update-status:active {
        transform: translateY(1px);
        box-shadow: 0 1px 2px rgba(79, 70, 229, 0.3);
    }

    .page-header-glass {
        background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.7);
        box-shadow: 0 4px 15px -1px rgba(0,0,0,0.03);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }
</style>

<section class="content-header tw-px-5">
    <div class="page-header-glass">
        <h1 class="tw-text-2xl md:tw-text-3xl tw-font-extrabold tw-text-gray-800 tw-tracking-tight tw-flex tw-items-center">
            <div class="tw-bg-indigo-100 tw-text-indigo-600 tw-rounded-lg tw-p-2 tw-mr-3 tw-shadow-sm">
                <i class="fas fa-microscope"></i>
            </div>
            Suivi du Laboratoire
        </h1>
        <p class="tw-text-gray-500 tw-text-base tw-mt-2 tw-ml-14 tw-font-medium">
            Gérez les commandes de lunettes de vos patients de manière visuelle et intuitive.
        </p>
    </div>
</section>

<!-- Main content -->
<section class="content tw-px-5">
    <div class="kanban-board-container">
        <div class="kanban-board">
                @foreach($statuses as $status_key => $status_label)
                    @php
                        // Assuming $orders is a Laravel Collection
                        $column_orders = collect($orders)->where('optician_status', $status_key);

                        // Icon mapping
                        $icon = 'fas fa-file-prescription';
                        if ($status_key == 'lenses_ordered') $icon = 'fas fa-industry';
                        if ($status_key == 'in_assembly') $icon = 'fas fa-tools';
                        if ($status_key == 'ready_for_pickup') $icon = 'fas fa-box-open';
                        if ($status_key == 'delivered') $icon = 'fas fa-check-double';
                    @endphp

                    <div class="kanban-col">
                        <div class="kanban-header">
                            <span><i class="{{$icon}} tw-mr-2 tw-text-gray-500"></i> {{ $status_label }}</span>
                            <span class="badge-count">{{ $column_orders->count() }}</span>
                        </div>

                        @if($column_orders->isEmpty())
                            <div class="tw-text-center tw-py-8 tw-text-sm tw-flex-1 tw-flex tw-flex-col tw-justify-center tw-items-center">
                                <div class="tw-bg-white tw-bg-opacity-50 tw-rounded-full tw-p-5 tw-mb-4 tw-shadow-sm tw-border tw-border-gray-200">
                                    <i class="fas fa-inbox tw-text-3xl tw-text-gray-300"></i>
                                </div>
                                <span class="tw-font-medium tw-text-gray-400">Aucun dossier en cours</span>
                            </div>
                        @else
                            <div class="tw-flex-1 tw-flex tw-flex-col tw-gap-4">
                            @foreach($column_orders as $order)
                            <div class="kanban-card status-{{ $status_key }}">
                                <div class="kanban-card-title">
                                    <div class="tw-bg-gray-100 tw-rounded-full tw-p-1.5 tw-mr-2 tw-text-gray-500 tw-shadow-inner mb-0">
                                        <i class="fas fa-user tw-text-sm"></i>
                                    </div>
                                    {{ $order->contact->name }}
                                </div>
                                <div class="kanban-card-subtitle">
                                    <i class="fas fa-hashtag tw-text-gray-400 tw-mr-1"></i> {{ $order->invoice_no }} 
                                    <span class="tw-mx-2">•</span> 
                                    <i class="far fa-calendar-alt tw-text-gray-400 tw-mr-1"></i> {{ @format_date($order->transaction_date) }}
                                </div>
                                
                                <div class="kanban-card-content">
                                    <strong class="tw-text-gray-700 tw-mb-1 tw-block">Détails de la commande :</strong>
                                    <ul class="tw-pl-4 tw-list-disc tw-text-gray-600">
                                    @foreach($order->sell_lines as $line)
                                        @if($line->product)
                                            <li class="tw-truncate" title="{{$line->product->name}}">
                                                {{$line->product->name}} (x{{$line->quantity}})
                                            </li>
                                        @endif
                                    @endforeach
                                    </ul>
                                </div>

                                <div class="kanban-card-footer">
                                    <div class="tw-flex tw-flex-col">
                                        <button data-href="{{ action([\App\Http\Controllers\OpticianWorkflowController::class, 'updateStatusModal'], [$order->id]) }}" class="btn-update-status btn-modal hover:tw-scale-105 active:tw-scale-95 tw-transform tw-transition tw-mb-1" data-container=".view_modal">
                                            Mettre à jour
                                        </button>
                                        <span class="tw-text-[0.65rem] tw-text-gray-400 tw-font-light italic" title="Dernière mise à jour">
                                            <i class="fas fa-clock tw-mr-1"></i> {{ \Carbon\Carbon::parse($order->updated_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            </div>
                        @endif
                        
                    </div>
                @endforeach
            </div>
    </div>
</section>
@endsection
