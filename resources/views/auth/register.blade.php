<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account · Golden Cloth House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="card border-0 shadow-sm w-100" style="max-width:460px;">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold">Create admin account</h1>
                <form method="post" action="{{ route('register.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Name</label><input class="form-control" name="name" required value="{{ old('name') }}"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input class="form-control" type="email" name="email" required value="{{ old('email') }}"></div>
                    <div class="mb-3"><label class="form-label fw-semibold">Password</label><input class="form-control" type="password" name="password" required></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Confirm password</label><input class="form-control" type="password" name="password_confirmation" required></div>
                    <button class="btn btn-dark w-100" type="submit">Create account</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
