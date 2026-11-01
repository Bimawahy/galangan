<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f3e6c8;
            --panel: #fbf3df;
            --accent: #a8702c;
            --accent-2: #d9a45c;
            --text: #4a3b26;
            --muted: #8a7655;
            --danger: #c0392b;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .layout { display: flex; min-height: 100vh; }

        /* ===== Sisi kiri: form login ===== */
        .left {
            flex: 0 0 440px;
            max-width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: var(--panel);
            position: relative;
            z-index: 2;
            box-shadow: 8px 0 40px rgba(110, 80, 30, .2);
        }

        .card { width: 100%; max-width: 340px; }

        .logo {
            width: 52px; height: 52px; border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid; place-items: center;
            font-size: 26px; margin-bottom: 24px;
        }

        h1 { font-size: 28px; margin-bottom: 6px; }
        .sub { color: var(--muted); margin-bottom: 28px; font-size: 14px; }

        .field { margin-bottom: 18px; }
        label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 6px; }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 14px;
            background: #f3e6c8;
            border: 1px solid #d9c394;
            border-radius: 10px;
            color: var(--text);
            font-size: 15px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(168, 112, 44, .25); }
        input.invalid { border-color: var(--danger); }

        .error { color: var(--danger); font-size: 13px; margin-top: 6px; }

        .row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; font-size: 13px; color: var(--muted);
        }
        .row label { margin: 0; display: flex; align-items: center; gap: 8px; cursor: pointer; }

        button.submit {
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent), #d9a45c);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
        }
        button.submit:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(168, 112, 44, .4); }
        button.submit:active { transform: translateY(0); }

        .hint { margin-top: 22px; text-align: center; font-size: 12px; color: var(--muted); }
        .hint code { background: #f3e6c8; padding: 2px 6px; border-radius: 4px; color: var(--accent-2); }

        /* ===== Sisi kanan: tetris ===== */
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 20% 20%, rgba(168, 112, 44, .18), transparent 45%),
                radial-gradient(circle at 80% 80%, rgba(217, 164, 92, .15), transparent 45%),
                var(--bg);
            overflow: hidden;
        }

        .game { display: flex; gap: 20px; align-items: flex-start; }

        #board {
            background: #efe0bd;
            border: 2px solid #d9c394;
            border-radius: 12px;
            box-shadow: 0 0 40px rgba(168, 112, 44, .25);
            display: block;
        }

        .side { width: 150px; display: flex; flex-direction: column; gap: 12px; }

        .box {
            background: rgba(251, 243, 223, .85);
            border: 1px solid #d9c394;
            border-radius: 10px;
            padding: 12px;
        }
        .box h3 { font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; }
        .box .val { font-size: 22px; font-weight: 700; font-variant-numeric: tabular-nums; }

        #next { display: block; margin: 0 auto; }

        .keys { font-size: 12px; color: var(--muted); line-height: 1.8; }
        kbd {
            background: #f3e6c8; border: 1px solid #d9c394; border-radius: 4px;
            padding: 1px 6px; font-size: 11px; color: var(--text);
        }

        .game-btn {
            padding: 9px; border: 1px solid #d9c394; border-radius: 8px;
            background: transparent; color: var(--text); cursor: pointer; font-size: 13px;
        }
        .game-btn:hover { background: rgba(168, 112, 44, .2); }

        @media (max-width: 900px) {
            .layout { flex-direction: column; }
            .left { flex: none; box-shadow: none; }
            .right { padding: 32px 12px; }
        }
        @media (max-width: 520px) {
            .side { display: none; }
        }
    </style>
</head>
<body>
<div class="layout">

    <!-- Kiri: form login -->
    <section class="left">
        <div class="card">
            <div class="logo">🔐</div>
            <h1>Selamat datang</h1>
            <p class="sub">Masuk untuk melanjutkan ke akun Anda.</p>

            <form method="POST" action="{{ url('/login') }}" novalidate>
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           value="{{ old('username') }}"
                           class="{{ $errors->has('username') ? 'invalid' : '' }}"
                           autocomplete="username" autofocus required>
                    @error('username')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           class="{{ $errors->has('password') ? 'invalid' : '' }}"
                           autocomplete="current-password" required>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <label><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                </div>

                <button type="submit" class="submit">Masuk</button>
            </form>

            <p class="hint">Demo: <code>admin</code> / <code>password</code></p>
        </div>
    </section>

    <!-- Kanan: tetris sebagai pemanis -->
    <section class="right">
        <div class="game">
            <canvas id="board" width="240" height="480"></canvas>
            <div class="side">
                <div class="box"><h3>Skor</h3><div class="val" id="score">0</div></div>
                <div class="box"><h3>Baris</h3><div class="val" id="lines">0</div></div>
                <div class="box"><h3>Level</h3><div class="val" id="level">1</div></div>
                <div class="box"><h3>Berikutnya</h3><canvas id="next" width="96" height="96"></canvas></div>
                <div class="box keys">
                    <kbd>←</kbd> <kbd>→</kbd> geser<br>
                    <kbd>↑</kbd> putar<br>
                    <kbd>↓</kbd> turun<br>
                    <kbd>Spasi</kbd> jatuh<br>
                    <kbd>P</kbd> jeda
                </div>
                <button class="game-btn" id="restart" type="button">Mulai ulang</button>
            </div>
        </div>
    </section>
</div>

<script>
(() => {
    const COLS = 10, ROWS = 20, SIZE = 24;
    const board = document.getElementById('board');
    const ctx = board.getContext('2d');
    const nextCtx = document.getElementById('next').getContext('2d');

    const COLORS = {
        I: '#d9a45c', O: '#fbbf24', T: '#a78bfa', S: '#4ade80',
        Z: '#fb7185', J: '#60a5fa', L: '#fb923c',
    };

    const SHAPES = {
        I: [[0,0,0,0],[1,1,1,1],[0,0,0,0],[0,0,0,0]],
        O: [[1,1],[1,1]],
        T: [[0,1,0],[1,1,1],[0,0,0]],
        S: [[0,1,1],[1,1,0],[0,0,0]],
        Z: [[1,1,0],[0,1,1],[0,0,0]],
        J: [[1,0,0],[1,1,1],[0,0,0]],
        L: [[0,0,1],[1,1,1],[0,0,0]],
    };

    let grid, piece, nextType, score, lines, level, dropMs, acc, last, paused, over, bag = [];

    const el = id => document.getElementById(id);

    function nextFromBag() {
        if (!bag.length) {
            bag = Object.keys(SHAPES);
            for (let i = bag.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [bag[i], bag[j]] = [bag[j], bag[i]];
            }
        }
        return bag.pop();
    }

    function spawn() {
        const type = nextType || nextFromBag();
        nextType = nextFromBag();
        const m = SHAPES[type].map(r => r.slice());
        piece = { type, m, x: Math.floor((COLS - m[0].length) / 2), y: 0 };
        if (collides(piece.m, piece.x, piece.y)) over = true;
        drawNext();
    }

    function collides(m, px, py) {
        for (let y = 0; y < m.length; y++)
            for (let x = 0; x < m[y].length; x++) {
                if (!m[y][x]) continue;
                const nx = px + x, ny = py + y;
                if (nx < 0 || nx >= COLS || ny >= ROWS) return true;
                if (ny >= 0 && grid[ny][nx]) return true;
            }
        return false;
    }

    function rotate(m) {
        const n = m.length;
        return m[0].map((_, x) => m.map(row => row[x]).reverse());
    }

    function lock() {
        piece.m.forEach((row, y) => row.forEach((v, x) => {
            if (v && piece.y + y >= 0) grid[piece.y + y][piece.x + x] = piece.type;
        }));
        let cleared = 0;
        for (let y = ROWS - 1; y >= 0; y--) {
            if (grid[y].every(Boolean)) {
                grid.splice(y, 1);
                grid.unshift(Array(COLS).fill(null));
                cleared++; y++;
            }
        }
        if (cleared) {
            score += [0, 100, 300, 500, 800][cleared] * level;
            lines += cleared;
            level = Math.floor(lines / 10) + 1;
            dropMs = Math.max(90, 800 - (level - 1) * 70);
            updateHud();
        }
        spawn();
    }

    function move(dx) {
        if (!collides(piece.m, piece.x + dx, piece.y)) piece.x += dx;
    }

    function softDrop() {
        if (!collides(piece.m, piece.x, piece.y + 1)) { piece.y++; score += 1; updateHud(); }
        else lock();
    }

    function hardDrop() {
        let d = 0;
        while (!collides(piece.m, piece.x, piece.y + 1)) { piece.y++; d++; }
        score += d * 2; updateHud();
        lock();
    }

    function turn() {
        const r = rotate(piece.m);
        for (const k of [0, -1, 1, -2, 2]) {
            if (!collides(r, piece.x + k, piece.y)) { piece.m = r; piece.x += k; return; }
        }
    }

    function updateHud() {
        el('score').textContent = score;
        el('lines').textContent = lines;
        el('level').textContent = level;
    }

    function cell(c, x, y, color, size = SIZE, alpha = 1) {
        c.globalAlpha = alpha;
        c.fillStyle = color;
        c.fillRect(x * size + 1, y * size + 1, size - 2, size - 2);
        c.fillStyle = 'rgba(255,255,255,.28)';
        c.fillRect(x * size + 1, y * size + 1, size - 2, 4);
        c.globalAlpha = 1;
    }

    function drawNext() {
        nextCtx.clearRect(0, 0, 96, 96);
        const m = SHAPES[nextType], s = 20;
        const ox = (96 - m[0].length * s) / 2, oy = (96 - m.length * s) / 2;
        m.forEach((row, y) => row.forEach((v, x) => {
            if (!v) return;
            nextCtx.fillStyle = COLORS[nextType];
            nextCtx.fillRect(ox + x * s + 1, oy + y * s + 1, s - 2, s - 2);
        }));
    }

    function draw() {
        ctx.clearRect(0, 0, board.width, board.height);

        ctx.strokeStyle = 'rgba(120,85,30,.1)';
        ctx.lineWidth = 1;
        for (let x = 1; x < COLS; x++) { ctx.beginPath(); ctx.moveTo(x * SIZE, 0); ctx.lineTo(x * SIZE, board.height); ctx.stroke(); }
        for (let y = 1; y < ROWS; y++) { ctx.beginPath(); ctx.moveTo(0, y * SIZE); ctx.lineTo(board.width, y * SIZE); ctx.stroke(); }

        grid.forEach((row, y) => row.forEach((t, x) => { if (t) cell(ctx, x, y, COLORS[t]); }));

        if (!over) {
            let gy = piece.y;
            while (!collides(piece.m, piece.x, gy + 1)) gy++;
            piece.m.forEach((row, y) => row.forEach((v, x) => {
                if (v) cell(ctx, piece.x + x, gy + y, COLORS[piece.type], SIZE, .18);
            }));
            piece.m.forEach((row, y) => row.forEach((v, x) => {
                if (v) cell(ctx, piece.x + x, piece.y + y, COLORS[piece.type]);
            }));
        }

        if (over || paused) {
            ctx.fillStyle = 'rgba(60,44,22,.78)';
            ctx.fillRect(0, 0, board.width, board.height);
            ctx.fillStyle = '#fbf3df';
            ctx.textAlign = 'center';
            ctx.font = 'bold 26px Segoe UI, sans-serif';
            ctx.fillText(over ? 'GAME OVER' : 'JEDA', board.width / 2, board.height / 2 - 8);
            ctx.font = '14px Segoe UI, sans-serif';
            ctx.fillStyle = '#e6d3a3';
            ctx.fillText(over ? 'Tekan Enter untuk main lagi' : 'Tekan P untuk lanjut', board.width / 2, board.height / 2 + 18);
        }
    }

    function loop(t) {
        const dt = t - last; last = t;
        if (!paused && !over) {
            acc += dt;
            if (acc >= dropMs) {
                acc = 0;
                if (!collides(piece.m, piece.x, piece.y + 1)) piece.y++;
                else lock();
            }
        }
        draw();
        requestAnimationFrame(loop);
    }

    function reset() {
        grid = Array.from({ length: ROWS }, () => Array(COLS).fill(null));
        score = 0; lines = 0; level = 1; dropMs = 800; acc = 0;
        paused = false; over = false; bag = []; nextType = null;
        updateHud(); spawn();
    }

    document.addEventListener('keydown', e => {
        // Jangan ganggu pengetikan di form
        if (e.target.matches('input, textarea, select')) return;
        const k = e.key;
        if (k === 'Enter' && over) { reset(); return; }
        if (k === 'p' || k === 'P') { if (!over) paused = !paused; return; }
        if (paused || over) return;

        if (k === 'ArrowLeft') move(-1);
        else if (k === 'ArrowRight') move(1);
        else if (k === 'ArrowDown') softDrop();
        else if (k === 'ArrowUp') turn();
        else if (k === ' ') hardDrop();
        else return;
        e.preventDefault();
    });

    el('restart').addEventListener('click', e => { reset(); e.currentTarget.blur(); });

    reset();
    last = performance.now();
    requestAnimationFrame(loop);
})();
</script>
</body>
</html>
