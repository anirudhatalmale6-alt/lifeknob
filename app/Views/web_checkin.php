<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeKnob - Check In</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            text-align: center;
        }
        .logo { font-size: 28px; font-weight: 700; color: #27ae60; margin-bottom: 8px; }
        .subtitle { color: #666; font-size: 16px; margin-bottom: 30px; }
        .user-name { font-size: 22px; font-weight: 600; color: #333; margin-bottom: 6px; }
        .last-checkin { color: #888; font-size: 14px; margin-bottom: 30px; }
        .btn {
            display: block;
            width: 100%;
            padding: 20px;
            border: none;
            border-radius: 16px;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            margin-bottom: 14px;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
        .btn:active { transform: translateY(0); }
        .btn-ok {
            background: #27ae60;
            color: white;
            font-size: 24px;
            padding: 28px;
        }
        .btn-help {
            background: #f39c12;
            color: white;
        }
        .btn-emergency {
            background: #e74c3c;
            color: white;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .no-token {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }
        .no-token a { color: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">LifeKnob</div>
        <div class="subtitle">Web Check-In</div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (isset($user) && $user): ?>
            <div class="user-name">Hello, <?= esc($user->name) ?></div>
            <?php if (isset($lastCheckIn) && $lastCheckIn): ?>
                <div class="last-checkin">Last check-in: <?= date('M j, Y g:i A', strtotime($lastCheckIn->created_at)) ?></div>
            <?php else: ?>
                <div class="last-checkin">No check-ins yet</div>
            <?php endif; ?>

            <form method="post" action="/checkin/web">
                <input type="hidden" name="token" value="<?= esc($token) ?>">
                <button type="submit" name="type" value="ok" class="btn btn-ok">I'm OK</button>
                <button type="submit" name="type" value="help" class="btn btn-help">I Need Help</button>
                <button type="submit" name="type" value="emergency" class="btn btn-emergency"
                        onclick="return confirm('Are you sure you want to trigger an emergency alert?')">
                    Call Ambulance
                </button>
            </form>
        <?php else: ?>
            <div class="no-token">
                Please use the link from your LifeKnob app to access web check-in,
                or <a href="/">download the app</a> to get started.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
