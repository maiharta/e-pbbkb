@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'on',
    'icon' => '',
    'disabled' => false,
    'readonly' => false,
    'options' => collect(),
    'multiple' => false,
])


<div class="form-group mb-3">
    <label class="col-form-label fw-bold"
           for="{{ $name }}">{{ $label }}</label>
    <select @if ($multiple) multiple @endif
            class="form-select @error($name) is-invalid @enderror"
            id="{{ $name }}"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}">

        @if (!$multiple)
            <option value=""></option>
        @endif
        @foreach ($options as $option)
            <option value="{{ $option['key'] }}">{{ $option['value'] }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">
            <i class="isax isax-info-circle"></i>
            {{ $message }}
        </div>
    @enderror
</div>


@push('scripts')
    <script>
        $('#{{ $name }}').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true
        });

        @if (!is_null($value))
            $('#{{ $name }}').val('{{ $value }}').trigger('change');
        @endif
    </script>
@endpush
