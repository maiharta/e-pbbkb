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
    'icon' => '',
])

<div class="form-group mb-3">
    <div class="input-group">
        <span class="input-group-text"
              id="basic-addon1">
            <i class="isax isax-{{ $icon }}"></i>
        </span>
        <input @if ($required) required @endif
               @if ($autofocus) autofocus @endif
               aria-describedby="basic-addon1"
               class="form-control"
               id="{{ $name }}"
               name="{{ $name }}"
               placeholder="{{ $placeholder }}"
               type="{{ $type }}"
               value="{{ $value }}" />
    </div>
</div>
