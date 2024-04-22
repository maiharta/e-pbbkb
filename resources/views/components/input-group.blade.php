@props([
    'name' => '',
    'label' => '',
    'type' => [
        'text' => 'text',
        'username' => 'text',
        'password' => 'password',
        'password_verify' => 'password',
        'email' => 'email',
    ],
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'autocomplete' => 'on',
    'icon' => '',
    'disabled' => false,
    'readonly' => false,
])

<div class="form-group mb-3">
    <div class="input-group">
        <span class="input-group-text"
              id="basic-addon1">
            <i class="isax isax-{{ $icon }}"></i>
        </span>
        <input @if ($required) required @endif
               @if ($disabled) disabled @endif
               @if ($readonly) readonly @endif
               @if ($autofocus) autofocus @endif
               aria-describedby="basic-addon1"
               autocomplete="{{ $autocomplete }}"
               class="form-control"
               id="{{ $name }}"
               name="{{ $name }}"
               placeholder="{{ $placeholder }}"
               type="{{ $type }}"
               value="{{ $value }}">
        @if ($type === 'password')
            <button class="btn btn-toggle-password isax isax-eye-slash"
                    id="button-addon2"
                    type="button"></button>
        @endif
    </div>
</div>
