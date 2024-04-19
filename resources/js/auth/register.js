$('#daftar-button').click(function () {
    var name = $('#name').val();
    var email = $('#email').val();
    var password = $('#password').val();
    var password_verify = $('#password_verify').val();

    if (name == '' || email == '' || password == '' || password_verify == '') {
        Swal.fire({
            icon: 'error',
            title: 'Semua kolom harus diisi'
        });
        return;
    }

    // verif email
    var emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Format email tidak valid'
        });
        return;
    }

    // verif password
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!regex.test(password)) {
        Swal.fire({
            icon: 'error',
            title: 'Password harus terdiri dari minimal 8 karakter, 1 kapital, 1 karakter spesial, dan 1 angka'
        });
        return;
    }

    // verif password verify
    if (password != password_verify) {
        Swal.fire({
            icon: 'error',
            title: 'Password konfirmasi tidak sama'
        });
        return;
    }

    // show modal
    countdown(120, '#countdown');
    $('#staticBackdrop').modal('show');
});

function countdown(seconds, element) {
    var interval = setInterval(function () {
        seconds--;
        // minute : second
        let minutes = Math.floor(seconds / 60);
        let second = seconds % 60;
        $(element).text(minutes + ':' + second);

        if (seconds == 0) {
            clearInterval(interval);
            $('#staticBackdrop').modal('hide');
            Swal.fire({
                icon: 'error',
                title: 'Waktu habis, coba lagi'
            });
        }

    }, 1000);
}
