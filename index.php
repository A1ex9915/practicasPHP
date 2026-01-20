<?php
// index.php
declare(strict_types=1);
session_start();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Registro seguro</title>

  <style>
    :root{
      --bg: #f4f7ff;
      --card: #ffffff;
      --text: #0f172a;
      --muted: #64748b;
      --border: #e2e8f0;
      --focus: #6366f1; /* indigo */
      --shadow: 0 18px 40px rgba(2, 8, 23, .10);
      --radius: 16px;
    }

    *{ box-sizing: border-box; }
    body{
      margin:0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
      color: var(--text);
      background:
        radial-gradient(900px 400px at 20% 10%, rgba(99,102,241,.16), transparent 60%),
        radial-gradient(900px 400px at 80% 0%, rgba(34,197,94,.10), transparent 55%),
        var(--bg);
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 32px 14px;
    }

    .wrap{
      width: 100%;
      max-width: 560px;
      background: var(--card);
      border: 1px solid rgba(226,232,240,.9);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .header{
      padding: 22px 22px 14px;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(180deg, rgba(99,102,241,.08), transparent);
    }

    .header h2{
      margin: 0 0 6px 0;
      font-size: 24px;
      letter-spacing: -.2px;
    }

    .header p{
      margin: 0;
      color: var(--muted);
      font-size: 13.5px;
      line-height: 1.4;
    }

    .content{
      padding: 18px 22px 22px;
    }

    .msg{
      padding: 12px 14px;
      border-radius: 12px;
      margin-bottom: 14px;
      font-size: 14px;
      border: 1px solid transparent;
    }
    .ok{ background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
    .err{ background:#fef2f2; color:#991b1b; border-color:#fecaca; }

    form{
      display: grid;
      gap: 14px;
    }

    .field{
      display: grid;
      gap: 7px;
    }

    label{
      font-weight: 700;
      font-size: 13.5px;
    }

    input{
      width: 100%;
      padding: 12px 12px;
      border-radius: 12px;
      border: 1px solid var(--border);
      outline: none;
      font-size: 14.5px;
      background: #fff;
      transition: box-shadow .15s ease, border-color .15s ease, transform .05s ease;
    }
    input::placeholder{ color: #94a3b8; }
    input:focus{
      border-color: rgba(99,102,241,.85);
      box-shadow: 0 0 0 4px rgba(99,102,241,.16);
    }
    input:active{ transform: scale(0.998); }

    .grid-2{
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    /* Responsive: en celular se va a 1 columna */
    @media (max-width: 540px){
      .grid-2{ grid-template-columns: 1fr; }
      .header{ padding: 18px 16px 12px; }
      .content{ padding: 14px 16px 18px; }
    }

    .field-err{
      display: none;
      color: #b91c1c;
      font-size: 12.5px;
      line-height: 1.2;
      margin-top: -2px;
    }

    .hint{
      color: var(--muted);
      font-size: 12.5px;
      margin-top: -4px;
    }

    .btn{
      margin-top: 6px;
      width: 100%;
      padding: 12px 14px;
      border: 0;
      border-radius: 12px;
      font-weight: 800;
      cursor: pointer;
      color: #fff;
      background: linear-gradient(90deg, #0f172a, #111827);
      box-shadow: 0 14px 24px rgba(2, 6, 23, .18);
      transition: transform .08s ease, filter .2s ease;
    }
    .btn:hover{ filter: brightness(1.05); }
    .btn:active{ transform: translateY(1px); }

    .footer-note{
      margin-top: 10px;
      color: var(--muted);
      font-size: 12.5px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="header">
      <h2>Registro de usuario</h2>
      <p>Campos obligatorios. La contraseña se guarda con hash seguro (no texto plano).</p>
    </div>

    <div class="content">
      <?php if ($flash): ?>
        <div class="msg <?= $flash['type'] === 'ok' ? 'ok' : 'err' ?>">
          <?= htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <form id="regForm" method="POST" action="register.php" novalidate>

        <div class="field">
          <label for="nombre">Nombre</label>
          <input id="nombre" name="nombre" type="text" maxlength="80" autocomplete="name" placeholder="Ej. Alex Alvarado" required />
          <div class="field-err" id="errNombre"></div>
        </div>

        <div class="field">
          <label for="email">Correo electrónico</label>
          <input id="email" name="email" type="email" maxlength="120" autocomplete="email" placeholder="ejemplo@correo.com" required />
          <div class="field-err" id="errEmail"></div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Mín. 8 caracteres" required />
            <div class="hint">Usa letras y números.</div>
            <div class="field-err" id="errPass"></div>
          </div>

          <div class="field">
            <label for="confirm">Confirmación</label>
            <input id="confirm" name="confirm" type="password" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Repite tu contraseña" required />
            <div class="hint">&nbsp;</div>
            <div class="field-err" id="errConfirm"></div>
          </div>
        </div>

        <div class="field">
          <label for="telefono">Número de Teléfono</label>
          <input id="telefono" name="telefono" type="tel" maxlength="20" placeholder="Ej. 7711234567" autocomplete="tel" required />
          <div class="field-err" id="errTel"></div>
        </div>

        <!-- CSRF token -->
        <input type="hidden" name="csrf" value="<?php
          if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
          echo $_SESSION['csrf'];
        ?>">

        <button class="btn" type="submit">Crear cuenta</button>
        <div class="footer-note">Tus datos se validan también en el servidor.</div>
      </form>
    </div>
  </div>

  <script>
    const form = document.getElementById('regForm');

    const showErr = (id, msg) => {
      const el = document.getElementById(id);
      el.textContent = msg;
      el.style.display = msg ? 'block' : 'none';
    };

    const isEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

    form.addEventListener('submit', (e) => {
      let ok = true;

      const nombre = form.nombre.value.trim();
      const email = form.email.value.trim();
      const pass = form.password.value;
      const confirm = form.confirm.value;
      const tel = form.telefono.value.trim();

      showErr('errNombre', '');
      showErr('errEmail', '');
      showErr('errPass', '');
      showErr('errConfirm', '');
      showErr('errTel', '');

      if (nombre.length < 2) { ok = false; showErr('errNombre', 'Ingresa un nombre válido (mínimo 2 caracteres).'); }
      if (!isEmail(email)) { ok = false; showErr('errEmail', 'Ingresa un correo válido.'); }

      if (pass.length < 8 || !/[A-Za-z]/.test(pass) || !/[0-9]/.test(pass)) {
        ok = false;
        showErr('errPass', 'La contraseña debe tener mínimo 8 caracteres e incluir letras y números.');
      }
      if (confirm !== pass) { ok = false; showErr('errConfirm', 'Las contraseñas no coinciden.'); }

      const telClean = tel.replace(/[^\d]/g,'');
      if (telClean.length < 10 || telClean.length > 15) {
        ok = false;
        showErr('errTel', 'Ingresa un teléfono válido (10 a 15 dígitos).');
      }

      if (!ok) e.preventDefault();
    });
  </script>
</body>
</html>
