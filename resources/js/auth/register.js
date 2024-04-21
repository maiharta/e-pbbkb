$('#generate_otp').click(function () {
    if (!validateInput()) {
        return;
    }
    var email = $('#email').val();

    var route_otp = $('#route_otp').val();
    var csrf_token = $('#csrf_token').val();

    $.ajax({
        url: route_otp,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            if (response.status == 'success') {
                // show modal
                $('#last_otp_at').text(response.data.last_otp_at);
                countdown(response.data.resend_after, '#countdown');
                $('#staticBackdrop').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });
            }
        },
        error: function () {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: 'Terjadi kesalahan'
            });

            $('#generate_otp').attr('disabled', false);
            $('#generate_otp').html('Daftar');
        },
    });
});

$('#resend-otp').click(function () {
    if (!validateInput()) {
        return;
    }
    var email = $('#email').val();

    var route_otp = $('#route_otp').val();
    var csrf_token = $('#csrf_token').val();

    $.ajax({
        url: route_otp,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            if (response.status == 'success') {
                $('#last_otp_at').text(response.data.last_otp_at);
                countdown(response.data.resend_after, '#countdown');
                $('#countdown-container').removeClass('d-none');
                $('#countdown-container').addClass('d-block');
                $('#resend-otp-container').addClass('d-none');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });
            }
        },
        error: function () {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: 'Terjadi kesalahan'
            });

            $('#generate_otp').attr('disabled', false);
            $('#generate_otp').html('Daftar');
        },
    });
});

$('#register').click(function(){
    var route_register = $('#route_register').val();
    var csrf_token = $('#csrf_token').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var password_verify = $('#password_verify').val();
    var otp_code = '';
    document.querySelectorAll('#otp > *[id]').forEach(function (element) {
        otp_code += element.value;
    })

    console.log(otp_code);

    $.ajax({
        url: route_register,
        method: 'POST',
        data: {
            _token: csrf_token,
            email: email,
            password: password,
            password_verify: password_verify,
            otp_code: otp_code
        },
        beforeSend: function () {
            // Swal loading
            Swal.fire({
                title: 'Mohon tunggu',
                html: 'Sedang mengirim kode OTP',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (response) {
            Swal.close();
            // show swal success then redirect to login page
            if (response.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi berhasil',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(function () {
                    window.location.href = response.data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi gagal',
                    text: response.message
                });
            }
        },
        error: function () {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Registrasi gagal',
                text: 'Terjadi kesalahan'
            });
        },
    });
});

function countdown(seconds, element) {
    // delete previous interval
    clearInterval(interval);
    $('#countdown-container').removeClass('d-none');
    $('#countdown-container').addClass('d-block');
    $('#resend-otp-container').addClass('d-none');
    var interval = setInterval(function () {
        seconds--;
        // minute : second
        let minutes = Math.floor(seconds / 60);
        let second = seconds % 60;
        $(element).text(minutes + ':' + second);

        if (seconds == 0) {
            clearInterval(interval);
            $('#countdown-container').addClass('d-none');
            $('#resend-otp-container').addClass('d-block');
            $('#resend-otp-container').removeClass('d-none');
        }

    }, 1000);
}

function validateInput(){
    var email = $('#email').val();
    var password = $('#password').val();
    var password_verify = $('#password_verify').val();

    if (email == '' || password == '' || password_verify == '') {
        Swal.fire({
            icon: 'error',
            title: 'Semua kolom harus diisi'
        });
        return false;
    }

    // verif email
    var emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Format email tidak valid'
        });
        return false;
    }

    // verif password
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!regex.test(password)) {
        Swal.fire({
            icon: 'error',
            title: 'Password harus terdiri dari minimal 8 karakter, 1 kapital, 1 karakter spesial, dan 1 angka'
        });
        return false;
    }

    // verif password verify
    if (password != password_verify) {
        Swal.fire({
            icon: 'error',
            title: 'Password konfirmasi tidak sama'
        });
        return false;
    }

    return true;
}
