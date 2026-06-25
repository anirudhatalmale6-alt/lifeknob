<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LifeKnob Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a1a2e;
            --green: #27ae60;
            --green-hover: #219a52;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .login-header {
            background: var(--sidebar-bg);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: #fff;
        }

        .login-header .logo-icon {
            width: 64px;
            height: 64px;
            background: var(--green);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .login-header h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }

        .login-header p {
            margin: 0.5rem 0 0;
            opacity: 0.5;
            font-size: 0.85rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #555;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e0e0e0;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.15);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: #f8f9fa;
            border: 1.5px solid #e0e0e0;
            border-right: none;
            color: #999;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--green);
            color: var(--green);
        }

        .input-group:focus-within .form-control {
            border-color: var(--green);
        }

        .btn-login {
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-login:hover {
            background: var(--green-hover);
            color: #fff;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
            border: none;
        }

        .login-footer {
            text-align: center;
            padding: 0 2rem 1.5rem;
            font-size: 0.8rem;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h2>LifeKnob</h2>
            <p>Admin Panel</p>
        </div>

        <div class="login-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= esc(session()->getFlashdata('error')) ?></div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= esc(session()->getFlashdata('success')) ?></div>
                </div>
            <?php endif; ?>

            <form action="/admin/login" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="admin@lifeknob.com" required autofocus
                               value="<?= old('email') ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>

        <div class="login-footer">
            &copy; <?= date('Y') ?> LifeKnob. Elderly Check-in System.
        </div>
    </div>
</div>

</body>
</html>
