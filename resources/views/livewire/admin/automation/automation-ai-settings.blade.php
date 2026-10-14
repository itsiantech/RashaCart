<div>
    <a href="{{ route('admin.automation.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Automation</a>
    <h4 class="fw-bold mb-4">AI Settings</h4>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Provider</label>
                    <select class="form-select" wire:model="provider">
                        <option value="claude">Claude</option>
                        <option value="openai">OpenAI</option>
                        <option value="other">Other</option>
                    </select>
                    @error('provider') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Default model</label>
                    <input type="text" class="form-control" wire:model="model" placeholder="claude-haiku-4-5">
                    @error('model') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Small-task model</label>
                    <input type="text" class="form-control" wire:model="smallModel">
                    @error('smallModel') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">API key <small class="text-muted">(শুধু বদলানোর সময় লিখুন)</small></label>
                    <div class="input-group">
                        <input type="password" class="form-control" wire:model="apiKey" placeholder="sk-ant-...">
                        <button type="button" class="btn btn-outline-secondary" wire:click="$set('apiKey', '')">Clear</button>
                    </div>
                    <small class="text-muted">{{ $apiKeyMasked ? 'Current: '.$apiKeyMasked.' (encrypted saved)' : 'কোনো key সেট নেই' }}</small>
                    @error('apiKey') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">Daily budget (৳)</label>
                    <input type="number" min="0" class="form-control" wire:model="dailyBudget">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Monthly budget (৳)</label>
                    <input type="number" min="0" class="form-control" wire:model="monthlyBudget">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Timeout (s)</label>
                    <input type="number" min="5" max="120" class="form-control" wire:model="timeout">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="button" wire:click="save" class="btn btn-primary">Save</button>
                <button type="button" wire:click="testConnection" class="btn btn-outline-success">Test Connection</button>
            </div>
            <small class="text-muted d-block mt-2">Key encrypted থাকে, log-এ কখনো আসে না। Test ১ মিনিটে ৫ বার সীমিত।</small>
        </div>
    </div>
</div>
