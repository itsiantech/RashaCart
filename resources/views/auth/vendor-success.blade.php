<!DOCTYPE html>
<html>

<head>
    <title>Registration Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <h2 class="fw-bold mb-3">Registration Successful 🎉</h2>
                <p class="text-muted">
                    Your vendor account is under review.
                    You will be able to log in once admin approves your request.
                </p>

                <a href="{{ route('vendor.login.form') }}" class="btn btn-danger mt-3">
                    Go to Vendor Login
                </a>
            </div>
        </div>
    </div>

</body>

</html>