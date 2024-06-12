@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'on',
    'icon' => '',
    'is_currency' => true,
    'decimal' => 0,
])

<div class="form-group mb-3">
    <label class="col-form-label fw-bold"
           for="{{ $name }}">{{ $label }}</label>
    <input autocomplete="off"
           class="form-control @error($name) is-invalid @enderror"
           id="{{ $name }}"
           name="{{ $name }}"
           placeholder="{{ $placeholder }}"
           type="text"
           value="{{ $value }}">
    @error($name)
        <div class="invalid-feedback">
            <i class="isax isax-info-circle"></i>
            {{ $message }}
        </div>
    @enderror
</div>

@push('scripts')
    <script>
        new AutoNumeric('#{{ $name }}', {
            @if ($is_currency)
                currencySymbol: 'Rp ',
                decimalPlaces: 2,
            @else
                decimalPlaces: {{ $decimal }},
            @endif
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            unformatOnSubmit: true,
        })
    </script>
@endpush
