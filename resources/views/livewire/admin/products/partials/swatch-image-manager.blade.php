{{-- Swatch-linked gallery (product_images). Additive: thumbnail + gallery JSON stay as-is. --}}
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-1">Swatch Gallery <small class="text-muted">(রঙ অনুযায়ী ছবি)</small></h5>
        <small class="text-muted d-block mb-3">ছবি আপলোড করে কোন color/value-এর সাথে লিঙ্ক করুন। Product পেজে ওই swatch সিলেক্ট করলে এই ছবিগুলো আগে দেখাবে। খালি রাখলে সব ছবির জন্য common।</small>

        <div class="mb-3">
            <input type="file" wire:model="imageUploads" multiple accept="image/*" class="form-control">
            <div wire:loading wire:target="imageUploads" class="small text-muted mt-1">Uploading...</div>
            @error('imageUploads.*') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        @if (count($imageLinks) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="12%">ছবি</th>
                        <th width="20%">Sort</th>
                        <th>কোন value-এর ছবি</th>
                        <th width="10%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($imageLinks as $i => $link)
                    <tr>
                        <td><img src="{{ $link['preview'] }}" class="img-thumbnail" width="60" height="60"></td>
                        <td>
                            <input type="number" min="0" wire:model="imageLinks.{{ $i }}.sort_order"
                                class="form-control form-control-sm" style="max-width:90px;">
                        </td>
                        <td>
                            <select wire:model="imageLinks.{{ $i }}.attribute_value_id" class="form-select form-select-sm">
                                <option value="">Common (সবগুলোর জন্য)</option>
                                @foreach ($productAttributes as $attribute)
                                <optgroup label="{{ $attribute->name }}">
                                    @foreach ($attribute->values as $av)
                                    <option value="{{ $av->id }}">{{ $av->value }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="button" wire:click="removeImageLink({{ $i }})"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('ছবিটি মুছে ফেলবেন?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-light border mb-0">এখনো swatch ছবি যোগ হয়নি।</div>
        @endif
    </div>
</div>
