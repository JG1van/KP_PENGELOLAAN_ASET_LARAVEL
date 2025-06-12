<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <style>
        :root {
            --warna0: white;
            --warna1: black;
            --warna2: #753422;
            --warna3: #b05b3b;
            --warna4: #d79771;
            --warna5: #ffebc9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--warna5);
            color: var(--warna1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: var(--warna0);
            border: 2px solid var(--warna3);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .login-title {
            font-size: 2rem;
            color: var(--warna2);
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-size: 1rem;
            color: var(--warna3);
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid var(--warna4);
            border-radius: 4px;
            background-color: var(--warna0);
            color: var(--warna1);
        }

        input:focus {
            border-color: var(--warna2);
            outline: none;
        }

        .btn-login {
            display: block;
            text-align: center;
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            color: var(--warna0);
            background-color: var(--warna2);
            border: none;
            border-radius: 4px;
            margin-top: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: var(--warna3);
        }

        .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1 class="login-title">Login</h1>

        @if (session('status'))
            <div class="error">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="Masukkan email"
                    value="{{ old('email') }}" required autofocus />
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Masukkan password" required />
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>

</html>
