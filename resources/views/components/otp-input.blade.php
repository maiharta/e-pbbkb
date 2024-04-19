<div>
    <form id="otpForm">
        <div class="d-flex gap-3"
             id="otp">
            @for ($i = 0; $i < 6; $i++)
                <input autocomplete="off"
                       autofocus
                       class="form-control border-sm text-center"
                       id="otp{{ $i }}"
                       maxlength="1"
                       name="otp[]"
                       oninput="moveFocusAfter(this, 'otp{{ $i + 1 }}')"
                       onkeydown="moveFocusBefore(this, 'otp{{ $i - 1 }}')"
                       placeholder="-"
                       required
                       type="text">
            @endfor
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function OTPInput() {
            const inputs = document.querySelectorAll('#otp > *[id]');
            for (let i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('keydown', function(event) {
                    if (event.key === "Backspace") {
                        inputs[i].value = '';
                        if (i !== 0) inputs[i - 1].focus();
                    } else {
                        if (i === inputs.length - 1 && inputs[i].value !== '') {
                            return true;
                        } else if (event.keyCode > 47 && event.keyCode < 58) {
                            inputs[i].value = event.key;
                            if (i !== inputs.length - 1) inputs[i + 1].focus();
                            event.preventDefault();
                        } else if (event.keyCode > 64 && event.keyCode < 91) {
                            inputs[i].value = String.fromCharCode(event.keyCode);
                            if (i !== inputs.length - 1) inputs[i + 1].focus();
                            event.preventDefault();
                        }
                    }
                });
            }
        }
        OTPInput();
    </script>
@endpush
