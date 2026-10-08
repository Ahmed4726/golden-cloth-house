<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Golden Cloth House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="card border-0 shadow-sm w-100" style="max-width:420px;">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 fw-bold">Golden Cloth House</h1>
                <p class="text-secondary">Social commerce control center</p>
                @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
                <form method="post" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
                    <div class="mb-4"><label class="form-label fw-semibold">Password</label><input class="form-control" type="password" name="password" required></div>
                    <button class="btn btn-dark w-100" type="submit">Sign in</button>
                </form>
                <p class="text-secondary small mt-4 mb-0">First time? <a href="{{ route('register') }}">Create admin account</a></p>
            </div>
        </div>
    </main>
</body>
</html>
