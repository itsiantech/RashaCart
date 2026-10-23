
<div class="cust-dash">
    <div class="hp-container cust-wrap">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($needsPhone): ?>
        <div class="alert alert-warning d-flex flex-wrap gap-2 align-items-center" role="alert">
            <i class="fas fa-phone"></i>
            <span class="flex-grow-1">Please add your phone number to complete your account.</span>
            <div class="d-flex gap-2 w-100">
                <label class="visually-hidden" for="needsPhone">Phone number</label>
                <input id="needsPhone" type="tel" class="form-control" wire:model.defer="phone" placeholder="01XXXXXXXXX" autocomplete="tel">
                <button class="btn btn-warning flex-shrink-0" wire:click="savePhoneStep">Save</button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small w-100"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="row g-3">
        
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="card cust-side shadow-sm">
                <div class="cust-user text-center p-3">
                    <div class="cust-avatar"><?php echo e(strtoupper(substr(auth()->user()->name ?? 'C', 0, 1))); ?></div>
                    <div class="fw-bold"><?php echo e(auth()->user()->name); ?></div>
                    <div class="text-muted small"><?php echo e(auth()->user()->email); ?></div>
                </div>
                <nav class="list-group list-group-flush" aria-label="Dashboard">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['overview' => 'fa-gauge-high|Overview', 'profile' => 'fa-user|Profile', 'track' => 'fa-truck-fast|Track Order', 'orders' => 'fa-bag-shopping|Orders', 'payments' => 'fa-credit-card|Payments', 'invoices' => 'fa-file-invoice|Invoices', 'settings' => 'fa-gear|Settings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php [$icon, $label] = explode('|', $meta); ?>
                        <button wire:click="setTab('<?php echo e($key); ?>')" class="list-group-item list-group-item-action <?php echo e($tab === $key ? 'active' : ''); ?>" aria-current="<?php echo e($tab === $key ? 'page' : 'false'); ?>">
                            <i class="fas <?php echo e($icon); ?> me-2"></i><?php echo e($label); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <form method="POST" action="<?php echo e(route('customer.logout')); ?>" class="p-2">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-outline-danger w-100"><i class="fas fa-right-from-bracket me-2"></i>Logout</button>
                    </form>
                </nav>
            </div>
        </aside>

        
        <div class="col-12 col-lg-9">
            
            <div class="cust-mtabs d-lg-none mb-3" role="tablist" aria-label="Dashboard sections">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['overview' => 'Overview', 'orders' => 'Orders', 'track' => 'Track', 'payments' => 'Payments', 'invoices' => 'Invoices', 'profile' => 'Profile', 'settings' => 'Settings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button wire:click="setTab('<?php echo e($key); ?>')" class="cust-mtab <?php echo e($tab === $key ? 'active' : ''); ?>" role="tab" aria-selected="<?php echo e($tab === $key ? 'true' : 'false'); ?>"><?php echo e($label); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div wire:loading class="cust-skel" aria-hidden="true"><div class="skeleton"></div><div class="skeleton"></div><div class="skeleton"></div></div>

            <div wire:loading.remove>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'overview'): ?>
                
                <div class="row g-2 mb-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [['Total Orders', $summary['total'], 'fa-bag-shopping', 'primary'], ['Pending', $summary['pending'], 'fa-clock', 'warning'], ['Delivered', $summary['delivered'], 'fa-circle-check', 'success'], ['Total Spent', '৳'.number_format($summary['spent']), 'fa-sack-dollar', 'info']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val, $icon, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-6 col-md-3">
                            <div class="card shadow-sm text-center p-3">
                                <i class="fas <?php echo e($icon); ?> text-<?php echo e($color); ?> fs-4"></i>
                                <div class="fs-5 fw-bold"><?php echo e($val); ?></div>
                                <div class="text-muted small"><?php echo e($label); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Recent Orders</strong>
                        <button class="btn btn-sm btn-outline-primary" wire:click="setTab('orders')">View all</button>
                    </div>
                    <div class="card-body p-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom p-3">
                                <div><div class="fw-bold">#<?php echo e($o->invoice_no ?? $o->id); ?></div><div class="text-muted small"><?php echo e($o->created_at?->format('d M Y')); ?> • ৳<?php echo e(number_format($o->total_amount)); ?></div></div>
                                <span class="badge <?php echo e(\App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($o->status)); ?>"><?php echo e(ucfirst($o->status)); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center p-4 text-muted"><i class="fas fa-bag-shopping fs-2 d-block mb-2"></i>No orders yet. <a href="<?php echo e(route('home')); ?>">Start shopping</a></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            <?php elseif($tab === 'profile'): ?>
                <div class="card shadow-sm"><div class="card-header"><strong>Profile</strong></div><div class="card-body">
                    <form wire:submit.prevent="saveProfile" class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="pf-name">Name *</label><input id="pf-name" class="form-control" wire:model.defer="name" autocomplete="name"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-md-6"><label class="form-label" for="pf-user">Username</label><input id="pf-user" class="form-control" wire:model.defer="username" autocomplete="username"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-md-6"><label class="form-label" for="pf-email">Email <?php echo e($identifier !== 'phone' ? '*' : ''); ?></label><input id="pf-email" type="email" class="form-control" wire:model.defer="email" autocomplete="email"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-md-6"><label class="form-label" for="pf-phone">Phone <?php echo e($identifier !== 'email' ? '*' : ''); ?></label><input id="pf-phone" type="tel" class="form-control" wire:model.defer="phone" autocomplete="tel"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-12"><label class="form-label" for="pf-addr">Address</label><textarea id="pf-addr" class="form-control" rows="2" wire:model.defer="address" autocomplete="street-address"></textarea></div>
                        <div class="col-12"><button class="btn btn-primary" type="submit" wire:loading.attr="disabled">Save Changes</button></div>
                    </form>
                </div></div>
                <div class="card shadow-sm mt-3"><div class="card-header"><strong>Change Password</strong></div><div class="card-body">
                    <form wire:submit.prevent="changePassword" class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="pw-cur">Current password *</label><input id="pw-cur" type="password" class="form-control" wire:model.defer="current_password" autocomplete="current-password"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-md-4"><label class="form-label" for="pw-new">New password *</label><input id="pw-new" type="password" class="form-control" wire:model.defer="new_password" autocomplete="new-password"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="col-md-4"><label class="form-label" for="pw-conf">Confirm *</label><input id="pw-conf" type="password" class="form-control" wire:model.defer="new_password_confirmation" autocomplete="new-password"></div>
                        <div class="col-12"><button class="btn btn-outline-primary" type="submit">Update Password</button></div>
                    </form>
                </div></div>

            <?php elseif($tab === 'track'): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><strong><i class="fas fa-truck-fast me-2" style="color:#12225B"></i>Track Order</strong></div>
                    <div class="card-body p-3 p-md-4">
                        <form wire:submit.prevent="trackOrder" class="trk-form">
                            <div class="trk-input">
                                <i class="fas fa-magnifying-glass"></i>
                                <input id="trk" class="form-control" wire:model.defer="trackInvoice" placeholder="Enter order ID or invoice no (e.g. INV-26101601)" aria-label="Order ID or invoice number">
                            </div>
                            <button class="btn trk-btn flex-shrink-0" type="submit" wire:loading.attr="disabled" wire:target="trackOrder">
                                <span wire:loading.remove wire:target="trackOrder"><i class="fas fa-location-crosshairs me-1"></i>Track</span>
                                <span wire:loading wire:target="trackOrder"><span class="spinner-border spinner-border-sm me-1"></span>Checking…</span>
                            </button>
                        </form>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['trackInvoice'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-2"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trackedOrder): ?>
                            <?php $tl = \App\Livewire\Frontend\Customer\CustomerDashboard::timeline($trackedOrder); ?>
                            <div class="trk-result mt-4">
                                <div class="trk-head">
                                    <div>
                                        <div class="trk-invoice">#<?php echo e($trackedOrder->invoice_no ?? $trackedOrder->id); ?></div>
                                        <div class="text-muted small"><i class="far fa-calendar me-1"></i>Placed <?php echo e($trackedOrder->created_at?->format('d M Y, h:i A')); ?></div>
                                    </div>
                                    <span class="badge trk-badge <?php echo e(\App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($trackedOrder->status)); ?>"><?php echo e(ucfirst($trackedOrder->status)); ?></span>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tl['cancelled']): ?>
                                    <div class="alert alert-danger mt-3 mb-0"><i class="fas fa-ban me-2"></i>This order was <?php echo e($trackedOrder->status); ?>.</div>
                                <?php else: ?>
                                    <ol class="trk-steps mt-4 mb-0">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tl['steps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="<?php echo e($i < $tl['current'] ? 'done' : ($i === $tl['current'] ? 'now' : '')); ?>">
                                                <span class="ic"><i class="fas <?php echo e($i < $tl['current'] ? 'fa-check' : ($i === $tl['current'] ? 'fa-truck-fast' : 'fa-ellipsis')); ?>"></i></span>
                                                <span class="lbl"><?php echo e(ucfirst($step)); ?></span>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === $tl['current']): ?><span class="when">Current status</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </ol>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div class="trk-items mt-4">
                                    <h6 class="trk-sub"><i class="fas fa-box-open me-2"></i>Items (<?php echo e($trackedOrder->items->sum('quantity')); ?>)</h6>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $trackedOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="trk-item">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold small"><?php echo e($item->product?->name ?? 'Product'); ?></div>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_name || $item->option_label): ?><div class="text-muted" style="font-size:.72rem"><?php echo e(trim($item->variant_name.' '.$item->option_label)); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                            <div class="text-muted small me-2">৳<?php echo e(number_format($item->unit_price)); ?> × <?php echo e($item->quantity); ?></div>
                                            <div class="fw-bold small">৳<?php echo e(number_format($item->unit_price * $item->quantity)); ?></div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="trk-meta">
                                    <div class="trk-meta-row"><span>Payment Method</span><strong><?php echo e(strtoupper($trackedOrder->payment_method ?? 'COD')); ?></strong></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trackedOrder->transaction_id): ?><div class="trk-meta-row"><span>Txn ID</span><strong><?php echo e($trackedOrder->transaction_id); ?></strong></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="trk-meta-row"><span>Delivery Address</span><strong class="text-end"><?php echo e($trackedOrder->shipping_address ?? '—'); ?></strong></div>
                                    <div class="trk-meta-row"><span>Delivery Charge</span><strong>৳<?php echo e(number_format($trackedOrder->delivery_charge ?? 0)); ?></strong></div>
                                    <div class="trk-meta-row total"><span>Grand Total</span><strong>৳<?php echo e(number_format($trackedOrder->total_amount)); ?></strong></div>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            <?php elseif($tab === 'orders'): ?>
                <div class="card shadow-sm"><div class="card-header"><strong>Order History</strong></div><div class="card-body">
                    <div class="cust-filters mb-3" role="tablist" aria-label="Filter by status">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['all' => 'All', 'pending' => 'Pending', 'on_hold' => 'On Hold', 'received' => 'Received/Processing', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $fkey = $key === 'delivered' ? 'x' : $key; ?>
                            <button wire:click="setStatusFilter('<?php echo e($key === 'delivered' ? 'delivered' : $key); ?>')" class="btn btn-sm <?php echo e($statusFilter === $key || ($key === 'delivered' && in_array($statusFilter, ['delivered'])) ? 'btn-primary' : 'btn-outline-secondary'); ?>"><?php echo e($label); ?></button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border rounded p-3 mb-2 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <div><div class="fw-bold">#<?php echo e($o->invoice_no ?? $o->id); ?></div><div class="text-muted small"><?php echo e($o->created_at?->format('d M Y')); ?> • <?php echo e($o->items->sum('quantity')); ?> items • ৳<?php echo e(number_format($o->total_amount)); ?></div></div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge <?php echo e(\App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($o->status)); ?>"><?php echo e(ucfirst($o->status)); ?></span>
                                <button class="btn btn-sm btn-outline-primary" wire:click="openOrderDetails('<?php echo e($o->invoice_no ?? $o->id); ?>')"><i class="fas fa-eye me-1"></i>Details</button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center p-4 text-muted"><i class="fas fa-receipt fs-2 d-block mb-2"></i>No orders in this status.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="mt-3"><?php echo e($orders->links()); ?></div>
                </div></div>

            <?php elseif($tab === 'payments'): ?>
                <div class="card shadow-sm"><div class="card-header"><strong>Payments</strong></div>
                    <div class="table-responsive"><table class="table mb-0">
                        <thead><tr><th>Order</th><th>Method</th><th class="text-end">Amount</th><th>Txn ID</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr><td>#<?php echo e($p->invoice_no ?? $p->id); ?></td><td><?php echo e(strtoupper($p->payment_method ?? 'COD')); ?></td><td class="text-end">৳<?php echo e(number_format($p->pay_amt ?: $p->total_amount)); ?></td><td class="small"><?php echo e($p->transaction_id ?? '—'); ?></td><td class="small"><?php echo e($p->created_at?->format('d M y')); ?></td><td><span class="badge <?php echo e(\App\Livewire\Frontend\Customer\CustomerDashboard::badgeClass($p->payment_status ?? $p->status)); ?>"><?php echo e(ucfirst($p->payment_status ?? $p->status)); ?></span></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="text-center text-muted p-4">No payments yet.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table></div>
                    <div class="card-footer"><?php echo e($payments->links()); ?></div>
                </div>

            <?php elseif($tab === 'invoices'): ?>
                <div class="card shadow-sm"><div class="card-header"><strong>Invoices</strong></div><div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border rounded p-3 mb-2 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <div><div class="fw-bold">#<?php echo e($inv->invoice_no); ?></div><div class="text-muted small"><?php echo e($inv->created_at?->format('d M Y')); ?> • ৳<?php echo e(number_format($inv->total_amount)); ?></div></div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="<?php echo e(route('customer.invoice.print', $inv)); ?>?auto=1"><i class="fas fa-print me-1"></i>Print</a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener" href="<?php echo e(route('customer.invoice.print', $inv)); ?>"><i class="fas fa-file-lines me-1"></i>View / PDF</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center p-4 text-muted"><i class="fas fa-file-invoice fs-2 d-block mb-2"></i>No invoices yet.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="mt-3"><?php echo e($invoices->links()); ?></div>
                </div></div>

            <?php elseif($tab === 'settings'): ?>
                <div class="card shadow-sm"><div class="card-header"><strong>Settings</strong></div><div class="card-body">
                    <h6>Saved Address</h6>
                    <form wire:submit.prevent="saveProfile" class="mb-4"><label class="visually-hidden" for="set-addr">Saved address</label><textarea id="set-addr" class="form-control mb-2" rows="2" wire:model.defer="address"></textarea><button class="btn btn-sm btn-primary" type="submit">Save Address</button></form>
                    <h6>Notification Preferences</h6>
                    <form wire:submit.prevent="saveNotifications" class="mb-4">
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="nt-sms" wire:model="notify_sms"><label class="form-check-label" for="nt-sms">SMS notifications</label></div>
                        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="nt-email" wire:model="notify_email"><label class="form-check-label" for="nt-email">Email notifications</label></div>
                        <button class="btn btn-sm btn-primary" type="submit">Save Preferences</button>
                    </form>
                    <h6>Change Password</h6>
                    <form wire:submit.prevent="changePassword" class="row g-2 mb-4">
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="Current" wire:model.defer="current_password"></div>
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="New" wire:model.defer="new_password"></div>
                        <div class="col-md-4"><input type="password" class="form-control" placeholder="Confirm" wire:model.defer="new_password_confirmation"></div>
                        <div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Update Password</button></div>
                    </form>
                    <form method="POST" action="<?php echo e(route('customer.logout')); ?>"><?php echo csrf_field(); ?><button class="btn btn-danger"><i class="fas fa-right-from-bracket me-2"></i>Logout</button></form>
                </div></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    </div>

    
    <nav class="cust-bottomnav d-lg-none" aria-label="Dashboard">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['overview' => 'fa-gauge-high|Home', 'orders' => 'fa-bag-shopping|Orders', 'track' => 'fa-truck-fast|Track', 'profile' => 'fa-user|Account']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php [$icon, $label] = explode('|', $meta); ?>
            <button wire:click="setTab('<?php echo e($key); ?>')" class="<?php echo e($tab === $key ? 'active' : ''); ?>" aria-label="<?php echo e($label); ?>"><i class="fas <?php echo e($icon); ?>"></i><span><?php echo e($label); ?></span></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </nav>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.cust-dash{padding-bottom:76px}.cust-avatar{width:56px;height:56px;border-radius:50%;background:#12225B;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700}.cust-side .list-group-item.active{background:#12225B;border-color:#12225B}.cust-mtabs{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}.cust-mtab{flex:0 0 auto;border:1px solid #dee2e6;background:#fff;border-radius:20px;padding:6px 14px;font-size:.85rem}.cust-mtab.active{background:#12225B;color:#fff;border-color:#12225B}.cust-timeline{list-style:none;padding:0;margin:0;display:flex;flex-wrap:wrap;gap:4px}.cust-timeline li{flex:1 1 90px;display:flex;align-items:center;gap:6px;font-size:.8rem;color:#999}.cust-timeline .dot{width:14px;height:14px;border-radius:50%;background:#dee2e6;flex-shrink:0}.cust-timeline li.done{color:#198754}.cust-timeline li.done .dot{background:#198754}.cust-timeline li.now{color:#12225B;font-weight:700}.cust-timeline li.now .dot{background:#12225B;box-shadow:0 0 0 4px rgba(18,34,91,.15)}.cust-filters{display:flex;gap:6px;flex-wrap:wrap}.cust-bottomnav{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:1px solid #dee2e6;display:flex;z-index:1030;padding:6px 0 calc(6px + env(safe-area-inset-bottom))}.cust-bottomnav button{flex:1;border:0;background:none;display:flex;flex-direction:column;align-items:center;gap:2px;font-size:.7rem;color:#666}.cust-bottomnav button.active{color:#12225B;font-weight:700}.cust-skel .skeleton{height:64px;border-radius:8px;background:linear-gradient(90deg,#eee,#f7f7f7,#eee);margin-bottom:8px}
@media(min-width:992px){.cust-dash{padding-bottom:0}}
.cust-wrap{padding-top:22px;padding-bottom:44px}
.mobile-bottom-nav{display:none!important}
.trk-form{display:flex;gap:10px}
.trk-input{flex:1;display:flex;align-items:center;gap:10px;background:#f5f6fa;border:1px solid #e5e7eb;border-radius:10px;padding:0 14px}
.trk-input:focus-within{border-color:#12225B;background:#fff;box-shadow:0 0 0 3px rgba(18,34,91,.12)}
.trk-input i{color:#9ca3af}
.trk-input .form-control{background:transparent;border:0;box-shadow:none;padding:11px 0}
.trk-btn{background:#12225B;border-color:#12225B;color:#fff;border-radius:10px;padding:10px 22px}
.trk-btn:hover{background:#0e1b47;color:#fff}
.trk-head{display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;align-items:center;background:#f5f6fa;border:1px solid #eef0f4;border-radius:12px;padding:14px 16px}
.trk-invoice{font-weight:800;font-size:1.05rem;color:#12225B}
.trk-badge{font-size:.8rem;padding:.45em .8em;border-radius:8px}
.trk-steps{list-style:none;margin:0;padding:0;position:relative;max-width:420px}
.trk-steps::before{content:"";position:absolute;left:17px;top:20px;bottom:20px;width:2px;background:#e5e7eb}
.trk-steps li{position:relative;display:flex;align-items:center;gap:14px;padding:9px 0;color:#9ca3af}
.trk-steps li .ic{width:36px;height:36px;border-radius:50%;background:#fff;border:2px solid #e5e7eb;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;position:relative;z-index:1;flex-shrink:0}
.trk-steps li.done{color:#198754;font-weight:600}
.trk-steps li.done .ic{background:#198754;border-color:#198754;color:#fff}
.trk-steps li.now{color:#12225B;font-weight:700}
.trk-steps li.now .ic{background:#12225B;border-color:#12225B;color:#fff;box-shadow:0 0 0 5px rgba(18,34,91,.15)}
.trk-steps li .when{margin-left:auto;font-size:.72rem;font-weight:400;color:#9ca3af}
.trk-sub{font-size:.85rem;font-weight:700;color:#374151;margin-bottom:10px}
.trk-item{display:flex;align-items:center;gap:12px;border:1px solid #eef0f4;border-radius:10px;padding:10px 14px;margin-bottom:8px}
.trk-meta{border:1px dashed #d7dbe4;border-radius:12px;padding:6px 16px;margin-top:16px;background:#fbfcfe}
.trk-meta-row{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid #eef0f4;font-size:.85rem}
.trk-meta-row:last-child{border-bottom:0}
.trk-meta-row span{color:#6b7280}
.trk-meta-row.total{font-size:.95rem}
.trk-meta-row.total strong{color:#12225B}
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\user\bandrose\resources\views/livewire/frontend/customer/dashboard.blade.php ENDPATH**/ ?>