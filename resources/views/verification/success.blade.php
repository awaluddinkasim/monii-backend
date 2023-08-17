<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi Email</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style>
        html body {
            background-color: rgba(40, 0, 120, .1);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body px-4">
                        <div class="row">
                            <div class="col-md-6">
                                <img src="{{ asset('assets/images/success.svg') }}" alt="Verifikasi Berhasil"
                                    width="100%">
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <h3>{{ $message }}</h3>
                                    <p>Email Anda telah berhasil diverifikasi.</p>
                                    <p>Silakan login ke aplikasi menggunakan akun Anda untuk mulai menggunakan
                                        fitur-fitur yang ada.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
