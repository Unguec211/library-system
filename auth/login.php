<?php
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    redirect('dashboard/index.php');
}

$username = '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    require_once __DIR__ . '/../config/database.php';

    $username = post_text('username');
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    $stmt = $pdo->prepare('SELECT user_id, password_hash, full_name, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $account = $stmt->fetch();

    if ($account && password_verify($password, $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int) $account['user_id'];
        $_SESSION['full_name'] = $account['full_name'];
        $_SESSION['role']      = $account['role'];
        flash('success', 'Welcome back, ' . $account['full_name'] . '.');
        redirect('dashboard/index.php');
    }

    $error = 'That username and password do not match.';
}

$page_title = 'Log in';
require __DIR__ . '/../includes/header.php';
?>
<section class="login-shell" aria-label="Library sign in panel">
    <div class="login-shell__brand">
        <div class="login-shell__image-wrap">
            <div class="login-shell__image" aria-hidden="true"></div>
        </div>
        <p class="login-shell__eyebrow">LIBRARY</p>
        <h1>Sign in</h1>
        <p class="login-shell__text">Books in order. Loans on track.</p>
        <ul class="login-shell__list" aria-label="Key benefits">
            <li>Manage loans</li>
            <li>Track members</li>
            <li>Stay current</li>
        </ul>
    </div>

    <div class="login-shell__panel">
        <div class="login-shell__header">
            <p class="login-shell__tag">Staff access</p>
            
        </div>

        <?php if ($error) : ?>
            <p class="flash flash--error" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post" class="form login-form">
            <?= csrf_field() ?>
            <label for="username">Username</label>
            <input id="username" name="username" value="<?= e($username) ?>" autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>

            <button class="btn" type="submit">Log in</button>
        </form>
    </div>
</section>

<style>
    .page > .login-shell {
        display: grid;
        grid-template-columns: 1.04fr 0.96fr;
        width: min(100%, 64rem);
        margin: 0 auto;
        border: 1px solid var(--color-border);
        border-radius: 2rem;
        background: var(--color-surface-glass);
        overflow: hidden;
        box-shadow: 0 28px 70px -36px var(--color-overlay), 0 8px 18px var(--color-blue-tint);
        backdrop-filter: blur(16px);
    }

    .page {
        display: grid;
        place-items: center;
        min-height: calc(100vh - 5rem);
        padding-top: 2rem;
        padding-bottom: 2rem;
        background: var(--color-background);
        font-family: var(--font-sans);
    }

    .login-shell__brand {
        padding: clamp(1.5rem, 2vw, 2.25rem);
        background: var(--color-blue-light);
        color: var(--color-text);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
    }

    .login-shell__badge {
        display: inline-grid;
        place-items: center;
        width: 3.2rem;
        height: 3.2rem;
        margin-bottom: 1.5rem;
        border-radius: 1.1rem;
        background: var(--color-surface-glass);
        border: 1px solid var(--color-blue-border);
        box-shadow: inset 0 1px 0 var(--color-blue-tint), 0 12px 22px var(--color-blue-tint);
        font: 700 1.5rem/1 var(--font-sans);
        color: var(--color-blue-dark);
    }

    .login-shell__image-wrap {
        position: relative;
        width: min(75%, 22rem);
        height: 14rem;
        margin: 0 0 1rem;
        border-radius: 1.2rem;
        overflow: hidden;
        border: 1px solid var(--color-blue-border);
        box-shadow: 0 18px 30px var(--color-blue-tint);
        background: var(--color-surface-glass);
    }

    .login-shell__image-wrap::after {
        content: "";
        position: absolute;
        inset: 0;
        background: var(--color-overlay);
    }

    .login-shell__image {
        display: block;
        width: 100%;
        height: 100%;
        background-image: url("../assets/images/download.jpg");
        background-size: cover;
        background-position: center;
        filter: saturate(0.8) contrast(1.04) brightness(0.96);
        transform: scale(1.04);
    }

    

    .login-shell__brand h1 {
        margin: 0 0 0.7rem;
        font-size: clamp(1.9rem, 2.2vw, 2.8rem);
        line-height: 0.96;
        letter-spacing: -0.065em;
        font-weight: 700;
        color: var(--color-text);
    }

    .login-shell__text {
        margin: 0;
        max-width: 18rem;
        color: var(--color-text-muted);
        font-size: 0.96rem;
        line-height: 1.5;
        letter-spacing: -0.02em;
    }

    .login-shell__list {
        margin: 1rem 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 0.55rem;
        color: var(--color-text-muted);
        font-size: 0.87rem;
        font-weight: 500;
    }

    .login-shell__list li {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .login-shell__list li::before {
        content: "";
        width: 0.52rem;
        height: 0.52rem;
        background: var(--color-blue);
        border-radius: 50%;
        box-shadow: 0 0 0 0.24rem var(--color-blue-tint);
    }

    .login-shell__panel {
        padding: clamp(1.7rem, 3vw, 2.5rem);
        background: var(--color-surface-glass);
    }

    .login-shell__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .login-shell__tag {
        margin: 0;
        font: 700 0.7rem/1.4 var(--font-mono);
        text-transform: uppercase;
        letter-spacing: 0.16em;
        color: var(--color-blue-strong);
    }

    .login-shell__status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.32rem 0.6rem;
        border-radius: 999px;
        background: var(--color-success-light);
        border: 1px solid var(--color-success);
        color: var(--color-success);
        font: 600 0.72rem/1 var(--font-sans);
    }

    .login-shell__status::before {
        content: "";
        width: 0.45rem;
        height: 0.45rem;
        border-radius: 50%;
        background: var(--color-success);
        box-shadow: 0 0 0 0.18rem var(--color-blue-tint);
    }

    .login-form {
        max-width: 100%;
        margin-top: 1rem;
    }

    .login-form label {
        color: var(--color-text);
        font-size: 0.82rem;
        letter-spacing: -0.01em;
    }

    .login-form input {
        border: 1px solid var(--color-blue-border);
        border-radius: 0.95rem;
        background: var(--color-blue-tint);
        padding: 0.86rem 0.95rem;
        font-size: 1rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .login-form input:focus {
        border-color: var(--color-blue);
        box-shadow: 0 0 0 0.26rem var(--color-blue-tint);
        background: var(--color-surface-soft);
        outline: none;
    }

    .login-form .btn {
        margin-top: 0.7rem;
        width: 100%;
        padding: 0.9rem 1rem;
        border-radius: 0.95rem;
        border: 0;
        background: var(--color-blue-strong);
        color: var(--color-white);
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: -0.01em;
        box-shadow: 0 16px 24px -18px var(--color-blue-strong);
    }

    .login-form .btn:hover {
        filter: brightness(1.04);
    }

    .flash {
        margin: 0 0 1rem;
        border-radius: 0.85rem;
        border-left: 0.32rem solid currentColor;
        font-weight: 600;
    }

    .flash--error {
        background: var(--color-blue-tint);
        color: var(--color-blue-strong);
    }

    @media (max-width: 48rem) {
        .page > .login-shell {
            grid-template-columns: 1fr;
            border-radius: 1.4rem;
        }

        .page {
            padding-top: 1rem;
        }
    }
</style>
<?php require __DIR__ . '/../includes/footer.php'; ?>
