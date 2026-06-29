<div>
    <h5>Set Category Commission</h5>

    @if (session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Category</th>
                <th>Commission Rate (%)</th>
            </tr>
        </thead>

        <tbody>
            @foreach($categories as $category)
            <tr wire:key="category-{{ $category->id }}">
                <td>{{ $category->name }}</td>
                <td>
                    <input type="number"
                        min="0"
                        max="100"
                        wire:model.defer="commissions.{{ $category->id }}"
                        class="form-control">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" wire:click="save" class="btn btn-primary">
        Save
    </button>
</div>