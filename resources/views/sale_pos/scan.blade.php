@extends('layouts.app')
@section('title', 'Scan POS')

@section('content')
{{--
    Premium barcode-scan POS.

    Scanner input: a USB barcode scanner behaves like a keyboard — it "types" the code
    very fast and ends with an Enter keypress. So we keep one text input permanently
    focused and act on the `Enter` key. No special driver, no mouse needed: scan -> lookup
    -> add to cart -> input auto-refocuses, ready for the next scan.

    Lookup: the scanned code is sent to /pos/scan/lookup which matches it to a product's
    sub_sku and returns name/price/stock FROM THE DATABASE. The barcode only identifies
    the product; price and stock are never taken from the scanner.

    Stock: scanning never changes stock. The cart is just a draft. Stock decreases only
    when "Complete sale" posts to /pos/scan/checkout (see BarcodeScanController).
--}}
<div class="scan-pos"
     data-location-id="{{ $default_location->id ?? '' }}"
     data-lookup-url="{{ route('pos.scan.lookup') }}"
     data-checkout-url="{{ route('pos.scan.checkout') }}"
     data-csrf="{{ csrf_token() }}">

    @unless($register_open)
        <div class="scan-banner">
            No cash register is open. The sale will still be recorded, but won't be added to a register total.
        </div>
    @endunless

    {{-- Top scan / search bar --}}
    <div class="scan-bar">
        <span class="scan-bar__icon"><i class="fas fa-barcode"></i></span>
        <input type="text" id="scanInput" class="scan-bar__input" autocomplete="off"
               placeholder="Scan a barcode or type a reference, then press Enter…" autofocus>
        <span id="scanSpinner" class="scan-bar__spinner" hidden></span>
    </div>

    <div class="scan-grid">
        {{-- Left: scanned product preview --}}
        <section class="panel preview" aria-live="polite">
            {{-- Idle state --}}
            <div id="previewIdle" class="state state--center">
                <div class="state__glyph"><i class="far fa-hand-pointer"></i></div>
                <h3 class="state__title">Ready to scan</h3>
                <p class="state__sub">Point the scanner at a product barcode to begin.</p>
            </div>

            {{-- Not found state --}}
            <div id="previewEmpty" class="state state--center" hidden>
                <div class="state__glyph state__glyph--warn"><i class="fas fa-search-minus"></i></div>
                <h3 class="state__title">Product not found</h3>
                <p class="state__sub">No product matches <code id="emptyBarcode"></code>.</p>
                <a id="createProductLink" href="#" class="btn btn--ghost">
                    <i class="fas fa-plus"></i> Create product with this barcode
                </a>
            </div>

            {{-- Product card --}}
            <div id="previewCard" class="card" hidden>
                <div class="card__media">
                    <img id="pImage" src="" alt="" class="card__img">
                    <span id="pBadge" class="badge"></span>
                </div>
                <div class="card__body">
                    <div class="card__head">
                        <h2 id="pName" class="card__title"></h2>
                        <span id="pBrand" class="card__brand"></span>
                    </div>
                    <div class="card__price"><span id="pPrice"></span></div>

                    <dl class="specs">
                        <div class="specs__row"><dt>Reference / SKU</dt><dd id="pSku"></dd></div>
                        <div class="specs__row"><dt>Barcode</dt><dd id="pBarcode"></dd></div>
                        <div class="specs__row"><dt>Category</dt><dd id="pCategory"></dd></div>
                        <div class="specs__row"><dt>In stock</dt><dd id="pStock"></dd></div>
                        <div class="specs__row"><dt>Min. stock</dt><dd id="pMin"></dd></div>
                    </dl>

                    <div id="pOptical" class="optical" hidden>
                        <h4 class="optical__title">Optical details</h4>
                        <div id="pOpticalTags" class="optical__tags"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Right: cart --}}
        <aside class="panel cart">
            <header class="cart__head">
                <h3 class="cart__title"><i class="fas fa-shopping-cart"></i> Cart</h3>
                <button id="clearCart" class="btn btn--text" type="button">Clear</button>
            </header>

            <div id="cartEmpty" class="state state--center state--sm">
                <p class="state__sub">Scanned products will appear here.</p>
            </div>

            <ul id="cartList" class="cart__list"></ul>

            <footer class="cart__foot">
                <div class="cart__total">
                    <span>Total</span>
                    <strong id="cartTotal">—</strong>
                </div>
                <button id="checkoutBtn" class="btn btn--primary btn--block" type="button" disabled>
                    <i class="fas fa-check"></i> Complete sale <kbd>F9</kbd>
                </button>
            </footer>
        </aside>
    </div>

    {{-- Toast host --}}
    <div id="toastHost" class="toast-host"></div>
</div>

<style>
/* ---- premium, self-contained styling (no build step / Tailwind JIT dependency) ---- */
.scan-pos{--bg:#f4f6fb;--card:#fff;--ink:#0f172a;--muted:#64748b;--line:#e6e9f0;
    --accent:#4f46e5;--accent-ink:#fff;--ok:#16a34a;--warn:#f59e0b;--bad:#dc2626;
    --radius:18px;--shadow:0 10px 30px rgba(17,17,26,.06),0 2px 8px rgba(17,17,26,.04);
    font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
    color:var(--ink);padding:8px 4px 40px;max-width:1280px;margin:0 auto;}
.scan-pos *{box-sizing:border-box;}
.scan-banner{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;padding:10px 16px;
    border-radius:12px;margin-bottom:14px;font-size:13px;}

/* scan bar */
.scan-bar{display:flex;align-items:center;gap:12px;background:var(--card);
    border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);
    padding:14px 20px;margin-bottom:20px;transition:box-shadow .2s,border-color .2s;}
.scan-bar:focus-within{border-color:var(--accent);box-shadow:0 0 0 4px rgba(79,70,229,.12),var(--shadow);}
.scan-bar__icon{font-size:22px;color:var(--accent);}
.scan-bar__input{flex:1;border:0;outline:0;font-size:20px;font-weight:500;background:transparent;color:var(--ink);}
.scan-bar__input::placeholder{color:#aab2c5;font-weight:400;}
.scan-bar__spinner{width:20px;height:20px;border:2.5px solid var(--line);border-top-color:var(--accent);
    border-radius:50%;animation:spin .6s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

/* layout */
.scan-grid{display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;}
.panel{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);
    box-shadow:var(--shadow);min-height:360px;}
.preview{padding:0;overflow:hidden;}

/* states */
.state{padding:48px 28px;}
.state--center{text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:360px;}
.state--sm{min-height:120px;padding:28px;}
.state__glyph{width:72px;height:72px;border-radius:50%;background:#eef2ff;color:var(--accent);
    display:grid;place-items:center;font-size:30px;margin-bottom:6px;}
.state__glyph--warn{background:#fff7ed;color:var(--warn);}
.state__title{margin:0;font-size:19px;font-weight:700;}
.state__sub{margin:0;color:var(--muted);font-size:14px;}

/* product card */
.card{display:grid;grid-template-columns:240px 1fr;gap:0;animation:rise .25s ease;}
@keyframes rise{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:none;}}
.card__media{position:relative;background:#f8fafc;display:grid;place-items:center;padding:24px;border-right:1px solid var(--line);}
.card__img{max-width:100%;max-height:200px;object-fit:contain;border-radius:12px;}
.badge{position:absolute;top:14px;left:14px;padding:5px 12px;border-radius:999px;font-size:12px;
    font-weight:700;letter-spacing:.02em;color:#fff;}
.badge--in_stock{background:var(--ok);}
.badge--low_stock{background:var(--warn);}
.badge--out_of_stock{background:var(--bad);}
.card__body{padding:24px 26px;}
.card__head{display:flex;align-items:baseline;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.card__title{margin:0;font-size:22px;font-weight:700;line-height:1.2;}
.card__brand{color:var(--muted);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
.card__price{font-size:30px;font-weight:800;color:var(--accent);margin:10px 0 18px;}
.specs{margin:0;border-top:1px solid var(--line);}
.specs__row{display:flex;justify-content:space-between;gap:16px;padding:10px 0;border-bottom:1px solid var(--line);font-size:14px;}
.specs__row dt{color:var(--muted);}
.specs__row dd{margin:0;font-weight:600;text-align:right;}
.optical{margin-top:18px;}
.optical__title{margin:0 0 8px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);}
.optical__tags{display:flex;flex-wrap:wrap;gap:8px;}
.optical__tags span{background:#eef2ff;color:#3730a3;font-size:12px;font-weight:600;padding:5px 11px;border-radius:999px;}

/* cart */
.cart{display:flex;flex-direction:column;}
.cart__head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--line);}
.cart__title{margin:0;font-size:16px;font-weight:700;}
.cart__list{list-style:none;margin:0;padding:8px;flex:1;overflow-y:auto;max-height:52vh;}
.cart__list:empty{display:none;}
.cart-item{display:grid;grid-template-columns:1fr auto;gap:4px 10px;padding:12px;border-radius:12px;animation:rise .2s ease;}
.cart-item:hover{background:#f8fafc;}
.cart-item__name{font-weight:600;font-size:14px;line-height:1.25;}
.cart-item__meta{grid-column:1;color:var(--muted);font-size:12px;}
.cart-item__line{grid-column:2;text-align:right;font-weight:700;font-size:14px;align-self:center;}
.qty{grid-column:2;display:inline-flex;align-items:center;gap:0;border:1px solid var(--line);border-radius:10px;overflow:hidden;}
.qty button{width:30px;height:30px;border:0;background:#fff;cursor:pointer;font-size:16px;color:var(--ink);line-height:1;}
.qty button:hover{background:#eef2ff;color:var(--accent);}
.qty input{width:40px;height:30px;border:0;border-left:1px solid var(--line);border-right:1px solid var(--line);
    text-align:center;font-weight:700;font-size:14px;outline:0;}
.cart-item__rm{grid-column:1;justify-self:start;background:none;border:0;color:var(--bad);cursor:pointer;font-size:12px;padding:0;}
.cart__foot{border-top:1px solid var(--line);padding:18px 20px;margin-top:auto;}
.cart__total{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:14px;}
.cart__total span{color:var(--muted);font-size:14px;}
.cart__total strong{font-size:26px;font-weight:800;}

/* buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:12px;
    font-weight:600;font-size:14px;padding:11px 18px;cursor:pointer;text-decoration:none;transition:.15s;font-family:inherit;}
.btn--primary{background:var(--accent);color:var(--accent-ink);}
.btn--primary:hover{filter:brightness(1.08);}
.btn--primary:disabled{background:#c7cbe0;cursor:not-allowed;}
.btn--block{width:100%;font-size:16px;padding:14px;}
.btn--ghost{background:#eef2ff;color:var(--accent);margin-top:8px;}
.btn--ghost:hover{background:#e0e7ff;}
.btn--text{background:none;color:var(--muted);padding:4px 8px;}
.btn--text:hover{color:var(--bad);}
.btn kbd{background:rgba(255,255,255,.22);border-radius:6px;padding:1px 7px;font-size:12px;font-family:inherit;}

/* toast */
.toast-host{position:fixed;top:20px;right:20px;display:flex;flex-direction:column;gap:10px;z-index:9999;}
.toast{display:flex;align-items:center;gap:10px;background:var(--ink);color:#fff;padding:13px 18px;
    border-radius:12px;box-shadow:var(--shadow);font-size:14px;font-weight:500;animation:slideIn .25s ease;max-width:340px;}
.toast--error{background:#dc2626;}
.toast--success{background:#16a34a;}
@keyframes slideIn{from{opacity:0;transform:translateX(20px);}to{opacity:1;transform:none;}}

/* responsive: tablet & phone stack the panels */
@media(max-width:1024px){.scan-grid{grid-template-columns:1fr;}.cart__list{max-height:none;}}
@media(max-width:640px){.card{grid-template-columns:1fr;}.card__media{border-right:0;border-bottom:1px solid var(--line);}
    .scan-bar__input{font-size:17px;}.card__price{font-size:26px;}}
</style>

<script>
(function(){
    const root = document.querySelector('.scan-pos');
    const cfg = {
        locationId: root.dataset.locationId,
        lookupUrl: root.dataset.lookupUrl,
        checkoutUrl: root.dataset.checkoutUrl,
        csrf: root.dataset.csrf,
    };

    const $ = (id) => document.getElementById(id);
    const input = $('scanInput'), spinner = $('scanSpinner');
    const cart = new Map(); // variation_id -> {line data, qty}

    // Keep the scan input focused at all times so the cashier never needs the mouse.
    function refocus(){ input.focus(); input.select(); }
    window.addEventListener('click', (e)=>{ if(!e.target.closest('input,button,a,select')) refocus(); });
    document.addEventListener('keydown', (e)=>{ if(e.key==='F9'){ e.preventDefault(); checkout(); } });

    function toast(msg, type){
        const t = document.createElement('div');
        t.className = 'toast toast--'+(type||'error');
        t.innerHTML = '<i class="fas fa-'+(type==='success'?'check-circle':'exclamation-circle')+'"></i>'+msg;
        $('toastHost').appendChild(t);
        setTimeout(()=>{ t.style.opacity='0'; setTimeout(()=>t.remove(),250); }, 3200);
    }

    function money(n){ return new Intl.NumberFormat(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}).format(n); }

    // --- Scan handler: USB scanner ends its "typing" with Enter. ---
    let busy = false;
    input.addEventListener('keydown', async (e)=>{
        if(e.key!=='Enter') return;
        e.preventDefault();
        const code = input.value.trim();
        if(!code || busy) return;
        busy = true; spinner.hidden = false;
        try { await lookup(code); }
        finally { busy = false; spinner.hidden = true; input.value=''; refocus(); }
    });

    async function lookup(code){
        const url = cfg.lookupUrl + '?barcode=' + encodeURIComponent(code) + '&location_id=' + encodeURIComponent(cfg.locationId);
        let res, data;
        try { res = await fetch(url, {headers:{'Accept':'application/json'}}); data = await res.json(); }
        catch(err){ toast('Network error during lookup'); return; }

        if(!data.found){
            showEmpty(data.barcode || code, data.create_url);
            toast('Unknown barcode: '+(data.barcode||code));
            return;
        }
        showProduct(data.product);
        if(data.product.status === 'out_of_stock'){
            toast(data.product.name+' is out of stock', 'error');
        }
        addToCart(data.product); // scan auto-adds to cart (no stock change yet)
    }

    function show(el){ ['previewIdle','previewEmpty','previewCard'].forEach(id=>$(id).hidden = id!==el); }

    function showEmpty(barcode, createUrl){
        $('emptyBarcode').textContent = barcode;
        if(createUrl) $('createProductLink').href = createUrl;
        show('previewEmpty');
    }

    function showProduct(p){
        $('pImage').src = p.image_url; $('pImage').alt = p.name;
        $('pName').textContent = p.name + (p.variation ? ' — '+p.variation : '');
        $('pBrand').textContent = p.brand || '';
        $('pPrice').textContent = p.selling_price_formatted;
        $('pSku').textContent = p.sku || '—';
        $('pBarcode').textContent = p.barcode || '—';
        $('pCategory').textContent = p.category || '—';
        $('pStock').textContent = p.enable_stock ? (p.stock_quantity+' units') : 'Not tracked';
        $('pMin').textContent = p.min_stock>0 ? (p.min_stock+' units') : '—';

        const badge = $('pBadge');
        const label = {in_stock:'In stock', low_stock:'Low stock', out_of_stock:'Out of stock'}[p.status];
        badge.textContent = label; badge.className = 'badge badge--'+p.status;

        // optical tags
        const optWrap = $('pOptical'), tags = $('pOpticalTags');
        const keys = Object.keys(p.optical||{});
        if(keys.length){
            tags.innerHTML = keys.map(k=>'<span>'+k.replace(/_/g,' ')+': '+p.optical[k]+'</span>').join('');
            optWrap.hidden = false;
        } else { optWrap.hidden = true; }

        show('previewCard');
    }

    // --- Cart (draft only; stock decreases at checkout) ---
    function addToCart(p){
        if(p.enable_stock && p.status==='out_of_stock'){ return; } // don't cart unsellable items
        const key = String(p.variation_id);
        if(cart.has(key)){ cart.get(key).qty += 1; }
        else { cart.set(key, {variation_id:p.variation_id, name:p.name, variation:p.variation,
                price:p.selling_price, max:p.enable_stock?p.stock_quantity:Infinity, qty:1}); }
        renderCart();
    }

    function setQty(key, qty){
        const item = cart.get(key); if(!item) return;
        qty = Math.max(0, qty);
        if(qty===0){ cart.delete(key); }
        else { if(qty>item.max){ toast('Only '+item.max+' in stock'); qty=item.max; } item.qty = qty; }
        renderCart();
    }

    function renderCart(){
        const list = $('cartList'); list.innerHTML='';
        $('cartEmpty').hidden = cart.size>0;
        let total = 0;
        cart.forEach((it, key)=>{
            total += it.price*it.qty;
            const li = document.createElement('li');
            li.className='cart-item';
            li.innerHTML =
                '<div class="cart-item__name">'+it.name+(it.variation?' — '+it.variation:'')+'</div>'+
                '<div class="qty"><button data-act="dec">−</button>'+
                '<input type="text" inputmode="numeric" value="'+it.qty+'"><button data-act="inc">+</button></div>'+
                '<div class="cart-item__meta">'+money(it.price)+' each</div>'+
                '<div class="cart-item__line">'+money(it.price*it.qty)+'</div>'+
                '<button class="cart-item__rm" data-act="rm"><i class="fas fa-trash-alt"></i> remove</button>';
            li.querySelector('[data-act=inc]').onclick = ()=>{ setQty(key, it.qty+1); refocus(); };
            li.querySelector('[data-act=dec]').onclick = ()=>{ setQty(key, it.qty-1); refocus(); };
            li.querySelector('[data-act=rm]').onclick = ()=>{ setQty(key, 0); refocus(); };
            li.querySelector('input').onchange = (e)=>{ setQty(key, parseInt(e.target.value)||0); refocus(); };
            list.appendChild(li);
        });
        $('cartTotal').textContent = cart.size ? money(total) : '—';
        $('checkoutBtn').disabled = cart.size===0;
    }

    $('clearCart').onclick = ()=>{ cart.clear(); renderCart(); refocus(); };

    // --- Checkout: the only place stock is written. ---
    async function checkout(){
        if(cart.size===0 || busy) return;
        busy = true;
        const btn = $('checkoutBtn'); const orig = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = '<span class="scan-bar__spinner"></span> Processing…';
        const payload = { location_id: cfg.locationId,
            cart: Array.from(cart.values()).map(it=>({variation_id:it.variation_id, quantity:it.qty})) };
        try {
            const res = await fetch(cfg.checkoutUrl, {method:'POST',
                headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':cfg.csrf},
                body: JSON.stringify(payload)});
            const data = await res.json();
            if(data.success){
                toast('Sale '+(data.invoice_no||'')+' completed', 'success');
                cart.clear(); renderCart(); show('previewIdle');
            } else {
                toast(data.msg || 'Sale failed');
            }
        } catch(err){ toast('Network error — sale not saved'); }
        finally { busy=false; btn.disabled=false; btn.innerHTML=orig; refocus(); }
    }
    $('checkoutBtn').onclick = checkout;

    renderCart(); refocus();
})();
</script>
@endsection
