$('.btn-toggle-password').on('click', function () {
    var $this = $(this);
    var $input = $this.closest('.input-group').find('input');
    var type = $input.attr('type') === 'password' ? 'text' : 'password';
    $input.attr('type', type);
    // toggle the class ic-eye-slash / ic-eye
    $this.toggleClass('isax-eye-slash isax-eye');
});

// password change, check regex min 8 characters, 1 uppercase, 1 special char, 1 number
$('#register-container #password').on('keyup', function () {
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    var $this = $(this);
    // clear message
    $this.parent().parent().find('.message').remove();
    if (regex.test($this.val())) {
        var $parent = $this.parent().parent();
        $parent.append('');
    } else if ($this.val() != '') {
        var $parent = $this.parent().parent();
        $parent.append('<p class="message text-danger">Password harus terdiri dari minimal 8 karakter, 1 kapital, 1 karakter spesial, dan 1 angka </p>');
    } else {
        var $parent = $this.parent().parent();
        $parent.append('');
    }
});

// $('.BDC_CaptchaImageDiv a'). hide
$('.BDC_CaptchaImageDiv a').hide();

// hide loading when window loaded
window.addEventListener('load', function () {
    $('#loading').hide();
});
