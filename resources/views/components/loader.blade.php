<div class="loader-container">
    <div class="loader"
         id="loader"></div>
         <p class="text-center text-white fw-bold mt-3">Loading</p>
</div>

@push('styles')
    <style>
        .loader-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 16px solid #f3f3f3;
            /* Light grey */
            border-top: 16px solid blue;
            border-bottom: 16px solid blue;
            /* Blue */
            border-radius: 50%;
            width: 100px;
            height: 100px;
            animation: spin 2s linear infinite;

        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function showLoader() {
            document.querySelector('.loader-container').style.display = 'flex';
        }

        function hideLoader() {
            document.querySelector('.loader-container').style.display = 'none';
        }
    </script>
@endpush
