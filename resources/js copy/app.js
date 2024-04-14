import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import "./mazer";
// import $ from 'jquery';
// import Swal from "sweetalert2";
// import DataTable from 'datatables.net-bs5';
// import 'datatables.net-rowreorder';
// import 'datatables.net-buttons-bs5';
// import 'datatables.net-buttons/js/buttons.colVis.js';
// import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
// import 'select2/dist/css/select2.min.css';
// select2 with bootstrap 5
import '/node_modules/select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css'
import '@fontsource/nunito/300.css';
import '@fontsource/nunito/400.css';
import '@fontsource/nunito/600.css';
import '@fontsource/nunito/700.css';
import '@fontsource/nunito/800.css';
// import select2 from 'select2';
import flatpickr from "flatpickr";
import '/node_modules/flatpickr/dist/flatpickr.min.css';
import '/node_modules/flatpickr/dist/l10n/id.js';
import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect/index.js';
import '/node_modules/flatpickr/dist/plugins/monthSelect/style.css';
import ApexCharts from 'apexcharts';

window.monthSelectPlugin = monthSelectPlugin;
window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 10000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
window.Swal = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-danger me-2',
        cancelButton: 'btn btn-secondary'
    },
    buttonsStyling: false
});
window.setLoading = function (status) {
    if (status) {
        $('.loading').remove();
        $('body').append('<div class="loading"><span class="spinner"></span class="mt-3 fw-bold">Loading</div>');
    } else {
        $('.loading').remove();
    }
}
flatpickr.localize(flatpickr.l10ns.id);
window.flatpickr = flatpickr;
// flatpicker with month select
// window.$ = $;
// select2();
window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

Alpine.plugin(focus);

Alpine.start();
