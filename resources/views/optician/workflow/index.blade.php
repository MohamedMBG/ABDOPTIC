@extends('layouts.app')
@section('title', 'Suivi du Laboratoire (Opticien)')

@section('content')
{{-- NOTE: class names are wf-* on purpose. The bundled jKanban lib (init.css/vendor.css)
     defines global .kanban-board{float:left;width:500px} etc. — reusing those names breaks the layout. --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Outfit:wght@300;400;500;600;700&display=swap');

    .optician-wf {
        --paper: #f7f4ee;
        --ink: #1c1a17;
        --muted: #8a847a;
        --line: #e7e1d6;
        font-family: 'Outfit', sans-serif;
        background:
            radial-gradient(circle at 12% 0%, rgba(124,108,240,0.05), transparent 40%),
            radial-gradient(circle at 90% 10%, rgba(42,157,143,0.05), transparent 38%),
            var(--paper);
        display: flow-root;
        width: 100%;
        padding: 0 0 2.5rem;
        min-height: 100%;
    }
    .optician-wf *, .optician-wf *::before, .optician-wf *::after { box-sizing: border-box; }

    /* ---- Header ---- */
    .wf-head {
        padding: 2.25rem 2.5rem 1.75rem;
        border-bottom: 1px solid var(--line);
    }
    .wf-eyebrow {
        font-size: 0.72rem; font-weight: 600; letter-spacing: 0.18em;
        text-transform: uppercase; color: var(--muted);
        display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;
    }
    .wf-title {
        font-family: 'Fraunces', serif; font-optical-sizing: auto;
        font-weight: 600; font-size: 2.4rem; line-height: 1.05;
        color: var(--ink); margin: 0; letter-spacing: -0.02em;
    }
    .wf-sub { color: var(--muted); font-size: 0.98rem; margin: 0.5rem 0 0; max-width: 46rem; }

    /* ---- Board (grid: equal columns, fill width) ---- */
    .wf-board {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1.1rem;
        width: 100%;
        padding: 1.75rem 2.5rem;
        align-items: start;
    }
    .wf-col { min-width: 0; display: flex; flex-direction: column; gap: 0.9rem; }

    @media (max-width: 1200px) {            /* tablet: scroll board only */
        .wf-board {
            grid-template-columns: none;
            grid-auto-flow: column;
            grid-auto-columns: minmax(260px, 1fr);
            overflow-x: auto;
            scrollbar-width: thin;
        }
    }
    @media (max-width: 640px) {             /* mobile: stack */
        .wf-board {
            grid-auto-flow: row;
            grid-template-columns: 1fr;
            grid-auto-columns: auto;
            overflow-x: visible;
            padding: 1.25rem;
        }
    }

    /* ---- Column header ---- */
    .wf-colhead {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.35rem 0.25rem 0.85rem;
        border-bottom: 1.5px solid var(--accent);
        color: var(--ink);
    }
    .wf-dot {
        width: 10px; height: 10px; border-radius: 50%; background: var(--accent);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 18%, transparent); flex: none;
    }
    .wf-collabel { font-weight: 600; font-size: 0.92rem; letter-spacing: -0.01em; flex: 1; min-width: 0; }
    .wf-count {
        font-family: 'Fraunces', serif; font-weight: 600; font-size: 0.85rem;
        color: var(--accent); background: color-mix(in srgb, var(--accent) 12%, var(--paper));
        padding: 0.05rem 0.55rem; border-radius: 9999px; min-width: 1.7rem; text-align: center; flex: none;
    }

    /* ---- Card ---- */
    .wf-card {
        background: #fffdf9; border: 1px solid var(--line); border-radius: 14px;
        padding: 1.1rem 1.15rem 1.1rem 1.3rem; position: relative; cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        box-shadow: 0 1px 2px rgba(28,26,23,0.03);
    }
    .wf-card::before {
        content: ''; position: absolute; left: 0; top: 14px; bottom: 14px;
        width: 3px; border-radius: 3px; background: var(--accent);
    }
    .wf-card:hover {
        transform: translateY(-3px);
        border-color: color-mix(in srgb, var(--accent) 45%, var(--line));
        box-shadow: 0 12px 24px -10px color-mix(in srgb, var(--accent) 35%, transparent);
    }
    .wf-name {
        font-family: 'Fraunces', serif; font-weight: 600; font-size: 1.12rem;
        color: var(--ink); line-height: 1.15; letter-spacing: -0.01em;
        margin: 0 0 0.55rem; overflow-wrap: anywhere;
    }
    .wf-meta { display: flex; flex-wrap: wrap; gap: 0.4rem; margin: 0 0 0.75rem; }
    .wf-chip {
        font-size: 0.72rem; font-weight: 500; color: var(--muted);
        background: var(--paper); border: 1px solid var(--line);
        padding: 0.12rem 0.5rem; border-radius: 6px;
        display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap;
    }
    .wf-items { list-style: none; margin: 0 0 0.85rem; padding: 0; display: flex; flex-direction: column; gap: 0.28rem; }
    .wf-items li {
        font-size: 0.82rem; color: #44403a; display: flex; align-items: baseline; gap: 0.45rem;
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .wf-items li i { font-size: 0.5rem; color: var(--accent); flex: none; transform: translateY(-2px); }
    .wf-items .wf-qty { color: var(--muted); font-weight: 500; flex: none; }
    .wf-foot {
        display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        padding-top: 0.7rem; border-top: 1px dashed var(--line);
    }
    .wf-time { font-size: 0.68rem; color: var(--muted); display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
    .wf-btn {
        font-family: 'Outfit', sans-serif; background: var(--ink); color: #fffdf9;
        padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.76rem; font-weight: 600;
        border: none; transition: background .15s ease, transform .12s ease;
        display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap;
    }
    .wf-btn:hover { background: var(--accent); color: #fff; transform: translateY(-1px); }

    /* ---- Empty state ---- */
    .wf-empty {
        border: 1.5px dashed var(--line); border-radius: 14px; padding: 2rem 1rem;
        text-align: center; color: var(--muted); font-size: 0.85rem;
    }
    .wf-empty i { font-size: 1.4rem; opacity: 0.4; display: block; margin-bottom: 0.5rem; }

    .wf-card { animation: wfRise .4s ease backwards; }
    @keyframes wfRise { from { opacity: 0; transform: translateY(8px); } }
</style>

@php
    // Group once; treat null status as the intake column so new prescriptions are visible.
    $grouped = collect($orders)->groupBy(fn($o) => $o->optician_status ?: 'prescription_received');
    $accents = [
        'prescription_received' => '#7c6cf0',
        'lenses_ordered'        => '#e0972b',
        'in_assembly'           => '#d6597e',
        'ready_for_pickup'      => '#2a9d8f',
        'delivered'             => '#9b938a',
    ];
    $icons = [
        'prescription_received' => 'fas fa-file-prescription',
        'lenses_ordered'        => 'fas fa-industry',
        'in_assembly'           => 'fas fa-tools',
        'ready_for_pickup'      => 'fas fa-box-open',
        'delivered'             => 'fas fa-check-double',
    ];
@endphp

<div class="optician-wf">
    <section class="wf-head">
        <div class="wf-eyebrow"><i class="fas fa-glasses"></i> Atelier · Opticien</div>
        <h1 class="wf-title">Suivi du Laboratoire</h1>
        <p class="wf-sub">Suivez chaque dossier patient de l'ordonnance jusqu'au retrait. Cliquez une carte pour faire avancer son statut.</p>
    </section>

    <div class="wf-board">
        @foreach($statuses as $status_key => $status_label)
            @php
                $column_orders = $grouped->get($status_key, collect());
                $accent = $accents[$status_key] ?? '#7c6cf0';
                $icon   = $icons[$status_key] ?? 'fas fa-file-prescription';
            @endphp

            <div class="wf-col" style="--accent: {{ $accent }}">
                <div class="wf-colhead">
                    <span class="wf-dot"></span>
                    <span class="wf-collabel"><i class="{{ $icon }} tw-mr-1"></i> {{ $status_label }}</span>
                    <span class="wf-count">{{ $column_orders->count() }}</span>
                </div>

                @forelse($column_orders as $i => $order)
                    <div class="wf-card btn-modal"
                         style="animation-delay: {{ $i * 40 }}ms"
                         data-href="{{ action([\App\Http\Controllers\OpticianWorkflowController::class, 'updateStatusModal'], [$order->id]) }}"
                         data-container=".view_modal">
                        <h3 class="wf-name">{{ $order->contact->name }}</h3>
                        <div class="wf-meta">
                            <span class="wf-chip"><i class="fas fa-hashtag"></i>{{ $order->invoice_no }}</span>
                            <span class="wf-chip"><i class="far fa-calendar"></i>{{ @format_date($order->transaction_date) }}</span>
                        </div>

                        <ul class="wf-items">
                            @foreach($order->sell_lines as $line)
                                @if($line->product)
                                    <li title="{{ $line->product->name }}">
                                        <i class="fas fa-circle"></i>
                                        <span class="tw-truncate">{{ $line->product->name }}</span>
                                        <span class="wf-qty">×{{ $line->quantity + 0 }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <div class="wf-foot">
                            <button type="button" class="wf-btn">Mettre à jour <i class="fas fa-arrow-right"></i></button>
                            <span class="wf-time" title="Dernière mise à jour">
                                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($order->updated_at)->diffForHumans(null, true) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="wf-empty">
                        <i class="fas fa-inbox"></i>
                        Aucun dossier en cours
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
@endsection
