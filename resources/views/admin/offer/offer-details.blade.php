@extends('admin.layouts.apps')

@section('styles')
    <style>
        /* ========= Premium Look & Feel ========= */
        :root{
            --brand:#4f46e5;       /* indigo */
            --brand-2:#06b6d4;     /* cyan */
            --ink:#0f172a;         /* slate-900 */
            --muted:#667085;       /* slate-500 */
            --line:#eef2f7;
            --soft:#f8fafc;
        }

        .card.premium{
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }
        .card-header.premium{
            background: linear-gradient(180deg, #fff, #fbfcff);
            border-bottom: 1px solid var(--line);
        }
        .subtitle{ color: var(--muted); font-size: .875rem; }

        /* Inputs */
        .form-control:focus, .form-select:focus{
            box-shadow: 0 0 0 .15rem rgba(79,70,229,.15);
            border-color: rgba(79,70,229,.45);
        }
        .input-group-text svg{ stroke: currentColor; }

        /* Live Preview */
        .preview{
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
        }
        .preview .chip{
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 12px; border-radius: 999px;
            background: #f2f5ff; color: #263; border: 1px solid rgba(79,70,229,.2);
            font-weight: 600; margin-right: 8px; margin-bottom: 8px;
        }
        .preview svg{ stroke: var(--brand); }

        /* Offer Card Mock */
        .offer-card{
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }
        .offer-media{
            position: relative; background: #f3f6ff; height: 180px; display:flex; align-items:center; justify-content:center;
        }
        .offer-media img{ height: 100%; width: 100%; object-fit: cover; }
        .discount-badge{
            position: absolute; top: 12px; left: 12px;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            color: #fff; padding: 6px 10px; border-radius: 999px; font-weight: 700;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .offer-body{ padding: 12px; }
        .offer-title{ font-weight: 800; color: var(--ink); margin: 0; }
        .offer-sub{ color: var(--muted); margin: 4px 0 8px; }
        .offer-meta{ font-size:.875rem; color:#475467; }
        .offer-list{ margin:0; padding-left:18px; }
        .offer-list li{ margin-bottom: 4px; }

        /* Buttons */
        .btn-brand{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none; color: #fff;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .btn-brand:hover{ opacity:.96 }
        .btn-outline-brand{
            border-color: var(--brand);
            color: var(--brand);
        }
        .btn-outline-brand:hover{
            background: #eef3ff;
            border-color: var(--brand);
            color: var(--brand);
        }

        /* Tiny helper text */
        .form-text{ font-size: 12px; color: var(--muted); }

        /* Image input pretty */
        .image-help{ color:#475467; font-size:.85rem; }
        .image-preview{ border:1px dashed #d9e0f3; border-radius:12px; padding:8px; }
        .image-preview img{ max-height: 140px; border-radius: 8px; display:block; }

        /* Add/Remove group buttons */
        .group-btn{ min-width: 42px; }
    </style>
@endsection

@section('content')
    <div class="card premium">
        <div class="card-header premium d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Create Offer</h5>
                <div class="subtitle">Craft a promotional offer with headers, discount, duration, menu items and packages.</div>
            </div>
            <span class="badge rounded-pill" style="background:#eef3ff;color:#4f46e5;border:1px solid rgba(79,70,229,.25);padding:.45rem .8rem;">
                Marketing · Offers
            </span>
        </div>

        <div class="card-body">
            {{-- Live chips --}}
            <div class="preview mb-3">
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                    <span id="pv-header">Main header…</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span id="pv-dates">No date range</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                        <rect x="12" y="10" width="6" height="4" rx="1"></rect>
                        <line x1="3" y1="8" x2="21" y2="8"></line>
                    </svg>
                    <span id="pv-discount">0% off</span>
                </div>
                <div class="chip">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12l7 7 11-11"></path>
                    </svg>
                    <span id="pv-counts">0 items · 0 packages</span>
                </div>
            </div>

            {{-- Offer preview card --}}
            <div class="offer-card mb-4">
                <div class="offer-media">
                    <img id="imagePreview" alt="Offer image preview" style="display:none;">
                    <div class="discount-badge" id="badgeDiscount">0% OFF</div>
                </div>
                <div class="offer-body">
                    <h5 class="offer-title" id="cardHeader">Main Header</h5>
                    <div class="offer-sub" id="cardSub">Sub header appears here…</div>
                    <div class="offer-meta" id="cardDates">No dates chosen</div>
                    <ul class="offer-list" id="cardList">
                        <li>Add menu items to see them here.</li>
                    </ul>
                </div>
            </div>

            <form action="{{ route('admin.saveOfferDetails') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="row g-3">
                    {{-- Main / Sub header --}}
                    <div class="col-md-4">
                        <label for="main_header" class="form-label">Main Header</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 6h16M4 12h8M4 18h16"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control @error('main_header') is-invalid @enderror"
                                   id="main_header" name="main_header" required value="{{ old('main_header') }}">
                            @error('main_header') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Make it catchy and concise (e.g., “Festive Sale”).</div>
                    </div>

                    <div class="col-md-4">
                        <label for="sub_header" class="form-label">Sub Header</label>
                        <input type="text" class="form-control @error('sub_header') is-invalid @enderror"
                               id="sub_header" name="sub_header" value="{{ old('sub_header') }}">
                        @error('sub_header') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Discount --}}
                    <div class="col-md-4">
                        <label for="discount" class="form-label">Discount (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                   id="discount" name="discount" min="0" max="100" value="{{ old('discount', 0) }}">
                            <span class="input-group-text">%</span>
                            @error('discount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">0–100 allowed.</div>
                    </div>

                    {{-- Dates --}}
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <div class="input-group">
                            <span class="input-group-text" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <path d="M8 3v4M16 3v4"></path>
                                </svg>
                            </span>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text" id="dateHelp">Choose start and end to form a range.</div>
                    </div>

                    {{-- Image --}}
                    <div class="col-md-4">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                               id="image" name="image" accept="image/*">
                        @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="image-help mt-1">Recommended 1200×600 JPG/PNG (under 1MB).</div>
                        <div class="image-preview mt-2">
                            <img id="thumbPreview" alt="Preview" style="display:none;">
                        </div>
                    </div>

                    {{-- Menu Items --}}
                    <div class="col-md-6">
                        <label class="form-label">Menu Items</label>
                        <div id="menu-items-container">
                            <div class="input-group mb-2 menu-item-group">
                                <input type="text" name="menu_items[]" class="form-control" placeholder="Enter menu item" required>
                                <button type="button" class="btn btn-success group-btn add-menu-item" title="Add">
                                    +
                                </button>
                            </div>
                        </div>
                        <div class="form-text">Add key items that this offer highlights.</div>
                    </div>

                    {{-- Packages --}}
                    <div class="col-md-6">
                        <label class="form-label">Packages</label>
                        <div id="package-container">
                            <div class="input-group mb-2 package-group">
                                <select name="product_id[]" class="form-select">
                                    <option value="">Select Package</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->product_id }}">{{ $package->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success group-btn add-package" title="Add">
                                    +
                                </button>
                            </div>
                        </div>
                        <div class="form-text">Optional: link to one or more subscription packages.</div>
                    </div>

                    {{-- Content --}}
                    <div class="col-md-12">
                        <label for="content" class="form-label">Content <span class="text-muted">(optional)</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror"
                                  id="content" name="content" rows="3"
                                  placeholder="Short description (displayed on offer banners and listings)…">{{ old('content') }}</textarea>
                        @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="d-flex justify-content-between form-text">
                            <span>Keep it short and clear.</span>
                            <span id="contentCount">0 / 300</span>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-brand px-4">
                            <svg width="18" height="18" viewBox="0 0 24 24" class="me-2" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <path d="M17 21v-8H7v8"></path>
                                <path d="M7 3v5h8"></path>
                            </svg>
                            Save Offer
                        </button>
                        <button type="reset" class="btn btn-outline-brand">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Session alerts
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: @json(session('success')),
            confirmButtonColor: '#3085d6'
        });
        @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: @json(session('error')),
            confirmButtonColor: '#d33'
        });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Template for adding new package dropdown
            const packageOptions = `@foreach ($packages as $package)
<option value="{{ $package->product_id }}">{{ $package->name }}</option>
@endforeach`;

            // Elements
            const mainEl = document.getElementById('main_header');
            const subEl  = document.getElementById('sub_header');
            const discEl = document.getElementById('discount');
            const sdEl   = document.getElementById('start_date');
            const edEl   = document.getElementById('end_date');
            const imgEl  = document.getElementById('image');
            const contentEl = document.getElementById('content');

            const pvHeader = document.getElementById('pv-header');
            const pvDates  = document.getElementById('pv-dates');
            const pvDiscount = document.getElementById('pv-discount');
            const pvCounts = document.getElementById('pv-counts');

            const badgeDiscount = document.getElementById('badgeDiscount');
            const cardHeader = document.getElementById('cardHeader');
            const cardSub    = document.getElementById('cardSub');
            const cardDates  = document.getElementById('cardDates');
            const cardList   = document.getElementById('cardList');
            const imagePreview = document.getElementById('imagePreview');
            const thumbPreview = document.getElementById('thumbPreview');
            const contentCount = document.getElementById('contentCount');
            const dateHelp = document.getElementById('dateHelp');

            const menuContainer = document.getElementById('menu-items-container');
            const packageContainer = document.getElementById('package-container');

            // Helpers
            const clamp = (n, min, max)=> Math.max(min, Math.min(max, n));
            const fmtRange = (a,b)=> (a && b) ? `${a} → ${b}` : (a ? `${a} → ?` : (b ? `? → ${b}` : 'No date range'));
            const limitText = (txt, n)=> (txt || '').length > n ? (txt.slice(0,n-1) + '…') : (txt || '');

            // Sync preview summary chips & card
            function syncPreview(){
                const m = mainEl.value || 'Main header…';
                const s = subEl.value || 'Sub header appears here…';
                const d = clamp(Number(discEl.value || 0), 0, 100);
                const a = sdEl.value || '';
                const b = edEl.value || '';

                pvHeader.textContent = limitText(m, 40);
                pvDiscount.textContent = `${d}% off`;
                pvDates.textContent = fmtRange(a, b);

                badgeDiscount.textContent = `${d}% OFF`;
                cardHeader.textContent = m || 'Main Header';
                cardSub.textContent    = s || 'Sub header appears here…';
                cardDates.textContent  = (a || b) ? fmtRange(a,b) : 'No dates chosen';

                // Build list preview from menu inputs (first 4 items)
                const items = Array.from(document.querySelectorAll('input[name="menu_items[]"]'))
                                   .map(i => i.value.trim()).filter(Boolean);
                cardList.innerHTML = items.length
                    ? items.slice(0,4).map(i=>`<li>${i}</li>`).join('') + (items.length>4 ? `<li>+${items.length-4} more</li>`:'')
                    : '<li>Add menu items to see them here.</li>';

                // Counts
                const pkgCount = Array.from(document.querySelectorAll('select[name="product_id[]"]'))
                                      .filter(s => s.value).length;
                pvCounts.textContent = `${items.length} items · ${pkgCount} packages`;
            }

            // Image preview
            function previewImage(file, img){
                if(!file){ img.style.display='none'; return; }
                const reader = new FileReader();
                reader.onload = e => { img.src = e.target.result; img.style.display='block'; imagePreview.src = e.target.result; imagePreview.style.display='block'; };
                reader.readAsDataURL(file);
            }

            // Content counter
            function syncContentCount(){
                const max = 300;
                const val = (contentEl.value || '').slice(0, max);
                if (val.length !== contentEl.value.length) contentEl.value = val;
                contentCount.textContent = `${val.length} / ${max}`;
            }

            // Date validation
            function validateDates(){
                const start = sdEl.value ? new Date(sdEl.value) : null;
                const end   = edEl.value ? new Date(edEl.value) : null;

                sdEl.setCustomValidity('');
                edEl.setCustomValidity('');
                dateHelp.classList.remove('text-danger');

                if (start && end && start > end) {
                    edEl.setCustomValidity('End date must be on/after start date');
                    dateHelp.textContent = 'End date must be on/after start date.';
                    dateHelp.classList.add('text-danger');
                } else {
                    dateHelp.textContent = 'Choose start and end to form a range.';
                }
            }

            // MENU ITEM logic (add/remove)
            menuContainer.addEventListener('click', function(e) {
                if (e.target.closest('.add-menu-item')) {
                    const group = document.createElement('div');
                    group.className = 'input-group mb-2 menu-item-group';
                    group.innerHTML = `
                        <input type="text" name="menu_items[]" class="form-control" placeholder="Enter menu item" required>
                        <button type="button" class="btn btn-danger group-btn remove-menu-item" title="Remove">–</button>
                    `;
                    menuContainer.appendChild(group);
                }
                if (e.target.closest('.remove-menu-item')) {
                    e.target.closest('.menu-item-group').remove();
                    syncPreview();
                }
            });

            // PACKAGE logic (add/remove) — keep name="product_id[]"
            packageContainer.addEventListener('click', function(e) {
                if (e.target.closest('.add-package')) {
                    const group = document.createElement('div');
                    group.className = 'input-group mb-2 package-group';
                    group.innerHTML = `
                        <select name="product_id[]" class="form-select">
                            <option value="">Select Package</option>
                            ${packageOptions}
                        </select>
                        <button type="button" class="btn btn-danger group-btn remove-package" title="Remove">–</button>
                    `;
                    packageContainer.appendChild(group);
                    syncPreview();
                }
                if (e.target.closest('.remove-package')) {
                    e.target.closest('.package-group').remove();
                    syncPreview();
                }
            });

            // Listeners
            [mainEl, subEl, discEl, sdEl, edEl].forEach(el => el.addEventListener('input', () => { validateDates(); syncPreview(); }));
            contentEl.addEventListener('input', syncContentCount);
            imgEl.addEventListener('change', () => previewImage(imgEl.files[0], thumbPreview));
            document.addEventListener('input', function(e){
                if (e.target.name === 'menu_items[]' || e.target.name === 'product_id[]') {
                    syncPreview();
                }
            });

            // Initial
            syncPreview();
            syncContentCount();
            validateDates();
        });
    </script>
@endsection
