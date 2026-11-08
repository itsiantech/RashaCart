<?php if (! $__env->hasRenderedOnce('popup-styles')): $__env->markAsRenderedOnce('popup-styles'); ?>
    <style>
        .op-layer { position: fixed; inset: 0; z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 16px; }
        /* The cart drawer FAB sits at z-index 9970 and would float over the modal on phones */
        body.op-open .cd-fab { opacity: 0; pointer-events: none; }
        .op-backdrop { position: absolute; inset: 0; background: rgba(17, 24, 39, .58); backdrop-filter: blur(3px); }
        .op-card { position: relative; width: 100%; max-width: 470px; max-height: 92vh; overflow-y: auto;
            background: #fff; border-radius: 20px; padding: 22px; box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
            animation: op-in .18s ease-out; }
        @keyframes op-in { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }

        .op-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .op-head h3 { margin: 0; font-size: 1.12rem; font-weight: 700; color: #111827; }
        .op-head p { margin: 3px 0 0; font-size: .82rem; color: #6b7280; }
        .op-x { border: 0; background: #f3f4f6; color: #4b5563; width: 32px; height: 32px; border-radius: 50%;
            font-size: 1.1rem; line-height: 1; cursor: pointer; flex-shrink: 0; }
        .op-x:hover { background: #e5e7eb; color: #111827; }

        .op-prod { display: flex; align-items: center; justify-content: space-between; gap: 12px;
            background: #f8fafc; border: 1px solid #eef2f7; border-radius: 14px; padding: 11px 13px; margin-bottom: 14px; }
        .op-prod strong { display: block; font-size: .92rem; color: #111827; line-height: 1.35; }
        .op-prod small { font-size: .78rem; color: #6b7280; }
        .op-prod > span { font-weight: 700; color: #6366f1; white-space: nowrap; }

        .op-field { margin-bottom: 12px; }
        .op-field > label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .op-field input[type=text], .op-field input[type=tel], .op-field input[type=email], .op-field textarea {
            width: 100%; border: 1px solid #e5e7eb; border-radius: 12px; padding: 11px 13px;
            font-size: .95rem; color: #111827; background: #fff; outline: none; transition: border-color .15s, box-shadow .15s; }
        .op-field input:focus, .op-field textarea:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .14); }
        .op-field textarea { resize: vertical; min-height: 62px; }
        .op-err { display: block; margin-top: 4px; font-size: .78rem; color: #dc2626; }

        .op-zones { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .op-zone { position: relative; border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 9px 11px;
            cursor: pointer; display: flex; flex-direction: column; gap: 1px; transition: border-color .15s, background .15s; }
        .op-zone input { position: absolute; opacity: 0; pointer-events: none; }
        .op-zone span { font-size: .82rem; font-weight: 600; color: #374151; }
        .op-zone strong { font-size: .78rem; color: #6b7280; }
        .op-zone.is-active { border-color: #6366f1; background: #eef2ff; }
        .op-zone.is-active span, .op-zone.is-active strong { color: #4338ca; }

        .op-summary { border-top: 1px dashed #e5e7eb; margin: 14px 0 4px; padding-top: 12px; }
        .op-row { display: flex; justify-content: space-between; font-size: .87rem; color: #4b5563; padding: 3px 0; }
        .op-row.op-total { font-size: 1rem; font-weight: 700; color: #111827; border-top: 1px solid #f1f5f9; margin-top: 6px; padding-top: 8px; }

        .op-actions { display: flex; flex-wrap: wrap; gap: 9px; margin-top: 16px; }
        .op-btn { flex: 1 1 150px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            border: 0; border-radius: 999px; padding: 12px 16px; font-size: .93rem; font-weight: 700;
            color: #fff; cursor: pointer; text-decoration: none; transition: filter .15s, transform .05s; }
        .op-btn:active { transform: translateY(1px); }
        .op-btn:disabled { opacity: .65; cursor: default; }
        .op-btn--cod { background: #6366f1; }
        .op-btn--cod:hover:not(:disabled) { filter: brightness(1.06); }
        .op-btn--wa { background: #25d366; }
        .op-btn--wa:hover { filter: brightness(1.06); }
        .op-btn--ghost { background: #f3f4f6; color: #374151; flex: 0 0 auto; }

        .op-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }

        .op-success { text-align: center; padding: 8px 2px 2px; }
        .op-check { width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 50%; background: #dcfce7;
            color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
        .op-success h3 { margin: 0 0 6px; font-size: 1.1rem; font-weight: 700; color: #111827; }
        .op-success p { margin: 0 0 4px; font-size: .88rem; color: #4b5563; }

        .op-picker { margin-bottom: 4px; }
        .op-picked { display: flex; align-items: center; justify-content: space-between; gap: 10px;
            background: #f8fafc; border: 1px solid #eef2f7; border-radius: 11px; padding: 9px 12px; margin-bottom: 12px; }
        .op-picked span { font-size: .8rem; color: #4b5563; }
        .op-picked button { border: 0; background: transparent; color: #6366f1; font-size: .8rem; font-weight: 700; cursor: pointer; }

        /* ── Variant picker (shared by the cart popup + order popup) ── */
        .vp-group { margin-bottom: 14px; }
        .vp-label { display: flex; align-items: baseline; gap: 6px; font-size: .82rem; font-weight: 700;
            color: #374151; margin-bottom: 7px; }
        .vp-label .vp-req { color: #dc2626; }
        .vp-label .vp-opt { font-weight: 500; color: #9ca3af; }
        .vp-label .vp-sel { font-weight: 600; color: #6366f1; margin-left: auto; }
        .vp-opts { display: flex; flex-wrap: wrap; gap: 8px; }
        .vp-chip { border: 1.5px solid #e5e7eb; background: #fff; border-radius: 10px; padding: 8px 15px;
            font-size: .85rem; color: #374151; cursor: pointer; transition: border-color .12s, background .12s, color .12s; }
        .vp-chip:hover:not(:disabled) { border-color: #c7d2fe; }
        .vp-chip.is-on { border-color: #6366f1; background: #eef2ff; color: #4338ca; font-weight: 700; }
        .vp-chip:disabled { opacity: .45; cursor: not-allowed; text-decoration: line-through; }

        .vp-swatch { width: 46px; height: 46px; padding: 0; border-radius: 50%; overflow: hidden;
            border: 2px solid #fff; box-shadow: 0 0 0 1.5px #e5e7eb; cursor: pointer; background: #fff;
            display: flex; align-items: center; justify-content: center; }
        .vp-swatch img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .vp-swatch > span { width: 100%; height: 100%; display: block; }
        .vp-swatch.is-on { box-shadow: 0 0 0 2.5px #6366f1; }
        .vp-swatch:disabled { opacity: .35; cursor: not-allowed; }

        .vp-options { display: flex; flex-wrap: wrap; gap: 8px; }
        .vp-option { border: 1.5px solid #e5e7eb; background: #fff; border-radius: 11px; padding: 5px;
            cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 4px; width: 82px; }
        .vp-option img { width: 70px; height: 52px; object-fit: cover; border-radius: 7px; display: block; }
        .vp-option em { font-style: normal; font-size: .7rem; color: #4b5563; }
        .vp-option.is-on { border-color: #6366f1; background: #eef2ff; }
        .vp-option.is-on em { color: #4338ca; font-weight: 600; }
        .vp-option:disabled { opacity: .45; cursor: not-allowed; }

        .vp-qty { display: flex; align-items: center; justify-content: space-between;
            border-top: 1px dashed #e5e7eb; margin-top: 4px; padding-top: 12px; }
        .vp-qty > span { font-size: .82rem; font-weight: 700; color: #374151; }
        .vp-stepper { display: flex; align-items: center; border: 1.5px solid #e5e7eb; border-radius: 999px; overflow: hidden; }
        .vp-stepper button { border: 0; background: #fff; width: 36px; height: 34px; font-size: 1rem;
            color: #374151; cursor: pointer; }
        .vp-stepper button:disabled { opacity: .4; cursor: not-allowed; }
        .vp-stepper b { min-width: 40px; text-align: center; font-size: .9rem; }

        .vp-note { font-size: .78rem; color: #b45309; background: #fffbeb; border: 1px solid #fde68a;
            border-radius: 9px; padding: 8px 10px; margin-top: 10px; }
        .vp-note:empty { display: none; }

        @media (max-width: 575px) {
            .op-layer { align-items: flex-end; padding: 0; }
            .op-card { max-width: none; border-radius: 20px 20px 0 0; max-height: 94vh; padding: 18px 16px 0; }
            .op-btn { flex: 1 1 100%; }
            .vp-swatch { width: 42px; height: 42px; }
            /* Keep the confirm button reachable while the long picker scrolls */
            .op-actions { position: sticky; bottom: 0; z-index: 2; background: #fff;
                margin: 12px -16px 0; padding: 10px 16px 16px; border-top: 1px solid #f1f5f9; }
        }
    </style>
<?php endif; ?>
<?php /**PATH C:\Users\user\bandrose\resources\views\livewire\frontend\partials\popup-styles.blade.php ENDPATH**/ ?>