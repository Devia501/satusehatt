<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SatuSehat — Integrasi FHIR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fb;
            color: #111827;
            min-height: 100vh;
            font-size: 14px;
        }

        /* NAV */
        nav {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 2rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .brand { font-weight: 600; font-size: 15px; color: #111827; letter-spacing: -0.3px; }
        .brand span { color: #2563eb; }
        .nav-tag {
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 3px 10px;
            letter-spacing: 0.3px;
        }

        /* LAYOUT */
        .page { max-width: 840px; margin: 0 auto; padding: 2.5rem 1.5rem 4rem; }

        /* HEADER */
        .page-header { margin-bottom: 2.5rem; }
        .page-header h1 { font-size: 22px; font-weight: 600; color: #111827; letter-spacing: -0.4px; }
        .page-header p { color: #6b7280; margin-top: 4px; line-height: 1.6; font-size: 13.5px; }

        /* SECTION */
        .section { margin-bottom: 2rem; }
        .section-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 0.75rem;
        }

        /* CARD */
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
        }

        /* STATS ROW */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: #e5e7eb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .stat {
            background: #fff;
            padding: 1.2rem 1rem;
            text-align: center;
        }
        .stat-val { font-size: 18px; font-weight: 600; color: #111827; }
        .stat-key { font-size: 11.5px; color: #9ca3af; margin-top: 3px; }

        /* FORM */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
        .form-row.single { grid-template-columns: 1fr; }
        .field { display: flex; flex-direction: column; gap: 5px; }
        .field label { font-size: 12px; font-weight: 500; color: #374151; }
        .field input, .field select {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color 0.15s;
        }
        .field input:focus, .field select:focus { border-color: #2563eb; }
        .field input::placeholder { color: #d1d5db; }

        /* STEPS */
        .steps {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 1.25rem;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 7px;
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            transition: all 0.25s;
        }
        .step + .step { margin-left: 6px; }
        .step-n {
            width: 22px; height: 22px; border-radius: 50%;
            background: #f3f4f6;
            color: #9ca3af;
            font-size: 11px; font-weight: 600;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .step-t { font-size: 11.5px; color: #9ca3af; font-weight: 500; white-space: nowrap; }
        .step.active { border-color: #2563eb; background: #eff6ff; }
        .step.active .step-n { background: #2563eb; color: #fff; }
        .step.active .step-t { color: #2563eb; }
        .step.done { border-color: #d1fae5; background: #f0fdf4; }
        .step.done .step-n { background: #10b981; color: #fff; }
        .step.done .step-t { color: #059669; }

        /* BUTTON */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: #111827; color: #fff;
            border: none; border-radius: 8px;
            padding: 9px 20px;
            font-size: 13.5px; font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.15s;
            margin-top: 0.25rem;
        }
        .btn:hover:not(:disabled) { background: #1f2937; }
        .btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .btn-spin {
            width: 13px; height: 13px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* RESULT */
        .result { display: none; margin-top: 1.25rem; }
        .result.show { display: block; }
        .result-ok {
            border: 1px solid #a7f3d0;
            background: #f0fdf4;
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }
        .result-err {
            border: 1px solid #fca5a5;
            background: #fef2f2;
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }
        .result-title {
            font-size: 12.5px; font-weight: 600; margin-bottom: 0.6rem;
        }
        .result-ok .result-title { color: #065f46; }
        .result-err .result-title { color: #991b1b; }
        .result-code {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #374151;
            background: rgba(0,0,0,0.04);
            border-radius: 6px;
            padding: 0.75rem;
            max-height: 260px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* ENDPOINTS */
        .endpoint-list { list-style: none; display: flex; flex-direction: column; }
        .ep {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .ep:last-child { border-bottom: none; }
        .method {
            font-size: 11px; font-weight: 600;
            padding: 2px 8px; border-radius: 4px;
            min-width: 44px; text-align: center;
        }
        .method.post { background: #dcfce7; color: #15803d; }
        .method.get  { background: #dbeafe; color: #1d4ed8; }
        .ep-path { font-family: monospace; font-size: 12.5px; color: #374151; }
        .ep-desc { font-size: 12px; color: #9ca3af; margin-left: auto; }

        @media (max-width: 580px) {
            .stats-row { grid-template-columns: 1fr 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .steps { flex-wrap: wrap; gap: 6px; }
            .step + .step { margin-left: 0; }
            .ep-desc { display: none; }
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">Satu<span>Sehat</span> Integration</div>
    <span class="nav-tag">FHIR R4 · Sandbox</span>
</nav>

<div class="page">

    <div class="page-header">
        <h1>Dashboard Integrasi SatuSehat</h1>
        <p>Kelola data kunjungan pasien (Encounter) melalui FHIR R4 API — Kementerian Kesehatan RI.</p>
    </div>

    <!-- STATS -->
    <div class="stats-row">
        <div class="stat">
            <div class="stat-val">Patient</div>
            <div class="stat-key">Berdasarkan NIK</div>
        </div>
        <div class="stat">
            <div class="stat-val">Practitioner</div>
            <div class="stat-key">Tenaga Medis</div>
        </div>
        <div class="stat">
            <div class="stat-val">Location</div>
            <div class="stat-key">Ruang / Poli</div>
        </div>
        <div class="stat">
            <div class="stat-val">Encounter</div>
            <div class="stat-key">Data Kunjungan</div>
        </div>
    </div>

    <!-- FORM -->
    <div class="section">
        <div class="section-label">Buat Encounter Baru</div>

        <!-- STEP INDICATOR -->
        <div class="steps">
            <div class="step" id="s1"><div class="step-n">1</div><div class="step-t">Auth</div></div>
            <div class="step" id="s2"><div class="step-n">2</div><div class="step-t">Patient</div></div>
            <div class="step" id="s3"><div class="step-n">3</div><div class="step-t">Practitioner</div></div>
            <div class="step" id="s4"><div class="step-n">4</div><div class="step-t">Location</div></div>
            <div class="step" id="s5"><div class="step-n">5</div><div class="step-t">Encounter</div></div>
        </div>

        <div class="card">
            <form id="form">
                <div class="form-row">
                    <div class="field">
                        <label for="nik_patient">NIK Pasien</label>
                        <input type="text" id="nik_patient" name="nik_patient" value="9271060312000001" placeholder="16 digit NIK" maxlength="16">
                    </div>
                    <div class="field">
                        <label for="nik_practitioner">NIK Tenaga Medis</label>
                        <input type="text" id="nik_practitioner" name="nik_practitioner" value="7209061211900001" placeholder="16 digit NIK" maxlength="16">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="status">Status Kunjungan</label>
                        <select id="status" name="status">
                            <option value="arrived">Arrived</option>
                            <option value="in-progress">In Progress</option>
                            <option value="finished">Finished</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="class">Jenis Kunjungan</label>
                        <select id="class" name="class">
                            <option value="AMB">Ambulatory (Poli)</option>
                            <option value="IMP">Inpatient (Rawat Inap)</option>
                            <option value="EMER">Emergency (UGD)</option>
                        </select>
                    </div>
                </div>
                <div class="form-row single">
                    <div class="field">
                        <label for="location_name">Nama Lokasi</label>
                        <input type="text" id="location_name" name="location_name" value="Ruang Poli Umum" placeholder="Nama ruangan / poli">
                    </div>
                </div>
                <button type="submit" class="btn" id="submit-btn">
                    <span class="btn-spin" id="spin"></span>
                    <span id="btn-label">Kirim ke SatuSehat</span>
                </button>
            </form>

            <div class="result" id="result">
                <div class="result-ok" id="r-ok" style="display:none">
                    <div class="result-title">Encounter berhasil dibuat.</div>
                    <pre class="result-code" id="r-ok-data"></pre>
                </div>
                <div class="result-err" id="r-err" style="display:none">
                    <div class="result-title">Gagal membuat Encounter.</div>
                    <pre class="result-code" id="r-err-data"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- ENDPOINTS -->
    <div class="section">
        <div class="section-label">API Endpoints</div>
        <div class="card" style="padding: 0.25rem 1.5rem;">
            <ul class="endpoint-list">
                <li class="ep">
                    <span class="method post">POST</span>
                    <span class="ep-path">/api/encounter</span>
                    <span class="ep-desc">Buat kunjungan pasien</span>
                </li>
                <li class="ep">
                    <span class="method get">GET</span>
                    <span class="ep-path">/fhir-r4/v1/Patient?identifier=nik|{NIK}</span>
                    <span class="ep-desc">Cari pasien</span>
                </li>
                <li class="ep">
                    <span class="method get">GET</span>
                    <span class="ep-path">/fhir-r4/v1/Practitioner?identifier=nik|{NIK}</span>
                    <span class="ep-desc">Cari tenaga medis</span>
                </li>
                <li class="ep">
                    <span class="method post">POST</span>
                    <span class="ep-path">/fhir-r4/v1/Location</span>
                    <span class="ep-desc">Buat lokasi poli</span>
                </li>
                <li class="ep">
                    <span class="method post">POST</span>
                    <span class="ep-path">/oauth2/v1/accesstoken</span>
                    <span class="ep-desc">Ambil access token</span>
                </li>
            </ul>
        </div>
    </div>

</div>

<script>
    const steps = ['s1','s2','s3','s4','s5'];

    function resetSteps() {
        steps.forEach(id => document.getElementById(id).className = 'step');
    }

    function setStep(idx) {
        resetSteps();
        for (let i = 0; i < idx; i++) document.getElementById(steps[i]).classList.add('done');
        if (idx < steps.length) document.getElementById(steps[idx]).classList.add('active');
    }

    const delay = ms => new Promise(r => setTimeout(r, ms));

    document.getElementById('form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn    = document.getElementById('submit-btn');
        const spin   = document.getElementById('spin');
        const label  = document.getElementById('btn-label');
        const result = document.getElementById('result');

        btn.disabled   = true;
        spin.style.display = 'block';
        label.textContent  = 'Memproses...';
        result.classList.remove('show');
        document.getElementById('r-ok').style.display  = 'none';
        document.getElementById('r-err').style.display = 'none';

        // Animate steps
        for (let i = 0; i <= 4; i++) {
            setStep(i);
            await delay(350);
        }

        try {
            const res = await fetch('/api/encounter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nik_patient:      document.getElementById('nik_patient').value,
                    nik_practitioner: document.getElementById('nik_practitioner').value,
                    status:           document.getElementById('status').value,
                    class:            document.getElementById('class').value,
                    location_name:    document.getElementById('location_name').value,
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                steps.forEach(id => document.getElementById(id).classList.add('done'));
                document.getElementById('r-ok-data').textContent = JSON.stringify(data.data, null, 2);
                document.getElementById('r-ok').style.display = 'block';
            } else {
                document.getElementById('r-err-data').textContent = JSON.stringify(data, null, 2);
                document.getElementById('r-err').style.display = 'block';
            }

        } catch (err) {
            document.getElementById('r-err-data').textContent =
                'Koneksi gagal: ' + err.message +
                '\n\nPastikan .env sudah diisi:\n  SATUSEHAT_CLIENT_ID=...\n  SATUSEHAT_CLIENT_SECRET=...\n  SATUSEHAT_ORG_ID=...';
            document.getElementById('r-err').style.display = 'block';
        }

        result.classList.add('show');
        btn.disabled = false;
        spin.style.display = 'none';
        label.textContent  = 'Kirim ke SatuSehat';
    });
</script>

</body>
</html>
