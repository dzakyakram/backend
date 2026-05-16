<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOGIN — NusantaraMap</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<style>
  :root { --black:#0a0a0a; --white:#f5f0e8; --yellow:#FFE135; --red:#FF3B30; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Space Mono', monospace;
    background: var(--white);
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    overflow-x: hidden;
  }

  /* Left panel */
  .left-panel {
    background: var(--black);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 48px;
    position: relative;
    overflow: hidden;
  }
  .left-panel::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    border: 3px solid #222;
    border-radius: 50%;
    pointer-events: none;
  }
  .left-panel::after {
    content: '';
    position: absolute;
    bottom: 40px; left: 48px;
    width: 120px; height: 120px;
    background: var(--yellow);
    border: 3px solid #FFE135;
    z-index: 0;
    opacity: .08;
  }

  /* Right panel */
  .right-panel {
    background: var(--white);
    border-left: 3px solid var(--black);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px;
  }

  /* Inputs */
  .brut-input {
    width: 100%; padding: 12px 14px;
    font-family: 'Space Mono', monospace; font-size: 13px;
    border: 3px solid var(--black); background: #fff;
    outline: none; transition: box-shadow .1s;
  }
  .brut-input:focus { box-shadow: 4px 4px 0 var(--black); }

  /* Button */
  .btn-submit {
    width: 100%; padding: 14px;
    font-family: 'Space Mono', monospace; font-size: 13px; font-weight: 700;
    background: var(--black); color: var(--yellow);
    border: 3px solid var(--black); cursor: pointer;
    box-shadow: 4px 4px 0 #555;
    transition: transform .1s, box-shadow .1s;
    letter-spacing: 1px;
    text-transform: uppercase;
  }
  .btn-submit:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 #555; }
  .btn-submit:active { transform: translate(2px,2px); box-shadow: 2px 2px 0 #555; }

  .demo-btn {
    display: block; width: 100%; padding: 10px 12px; text-align: left;
    font-family: 'Space Mono', monospace; font-size: 11px;
    background: #f5f0e8; border: 2px solid var(--black);
    cursor: pointer; box-shadow: 2px 2px 0 var(--black);
    transition: transform .1s, box-shadow .1s; color: var(--black);
  }
  .demo-btn:hover { background: var(--yellow); transform: translate(-1px,-1px); box-shadow: 3px 3px 0 var(--black); }

  /* Grid decoration */
  .grid-bg {
    position: absolute; inset: 0;
    background-image: repeating-linear-gradient(0deg, transparent, transparent 39px, rgba(255,225,53,.06) 39px, rgba(255,225,53,.06) 40px),
                      repeating-linear-gradient(90deg, transparent, transparent 39px, rgba(255,225,53,.06) 39px, rgba(255,225,53,.06) 40px);
  }

  @media (max-width: 768px) {
    body {
      grid-template-columns: 1fr;
      min-height: 100vh;
      min-height: -webkit-fill-available;
      overflow-x: hidden;
      overflow-y: auto;
    }
    .left-panel { display: none; }
    .right-panel {
      border-left: none;
      min-height: 100vh;
      min-height: -webkit-fill-available;
      padding: 32px 20px;
      align-items: center;
      justify-content: center;
    }
    .right-panel > div {
      width: 100%;
      max-width: 380px;
    }
  }
</style>
</head>
<body>

{{-- Left decorative panel --}}
<div class="left-panel">
  <div class="grid-bg"></div>
  <div style="position:relative;z-index:1;">
    <div style="font-family:'Syne',sans-serif;font-size:42px;font-weight:800;line-height:1;color:#f5f0e8;letter-spacing:-2px;">
      NUSANTARA<br><span style="color:#FFE135;">MAP</span>
    </div>
    <div style="margin-top:12px;font-size:12px;color:#666;letter-spacing:1px;">ADMIN &amp; PENGELOLA PANEL</div>
  </div>

  <div style="position:relative;z-index:1;">
    <div style="display:grid;gap:12px;">
      <div style="padding:16px;border:2px solid #222;background:rgba(255,255,255,.03);">
        <div style="font-size:10px;font-weight:700;color:#555;letter-spacing:2px;margin-bottom:6px;">MODERASI LOKASI</div>
        <div style="font-size:28px;font-weight:800;color:#FFE135;font-family:'Syne',sans-serif;">REAL-TIME</div>
        <div style="font-size:11px;color:#444;margin-top:2px;">Validasi GPS &amp; konten wisata</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="padding:12px;border:2px solid #222;background:rgba(255,225,53,.05);">
          <div style="font-size:9px;color:#555;letter-spacing:1px;margin-bottom:4px;">KATEGORI</div>
          <div style="font-size:13px;font-weight:700;color:#f5f0e8;">Wisata · Kuliner<br>Hotel</div>
        </div>
        <div style="padding:12px;border:2px solid #222;background:rgba(0,200,83,.05);">
          <div style="font-size:9px;color:#555;letter-spacing:1px;margin-bottom:4px;">EXPORT</div>
          <div style="font-size:13px;font-weight:700;color:#00C853;">CSV &amp; GeoJSON<br>GIS Ready</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Right login panel --}}
<div class="right-panel">
  <div style="width:100%;max-width:380px;">

    <div style="margin-bottom:32px;">
      <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;letter-spacing:-1px;line-height:1;margin-bottom:8px;">MASUK KE<br>SISTEM</div>
      <div style="width:48px;height:4px;background:var(--black);margin-bottom:8px;"></div>
      <p style="font-size:12px;color:#666;">Gunakan akun admin atau pengelola untuk akses web.</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" style="display:grid;gap:16px;">
      @csrf

      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">EMAIL</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               placeholder="email@contoh.com" class="brut-input">
        @error('email')
          <p style="font-size:11px;color:var(--red);font-weight:700;margin-top:4px;">✗ {{ $message }}</p>
        @enderror
      </div>

      <div>
        <label style="display:block;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">PASSWORD</label>
        <input type="password" name="password" required placeholder="••••••••" class="brut-input">
      </div>

      <button type="submit" class="btn-submit">→ MASUK KE DASHBOARD</button>
    </form>

    {{-- DOWNLOAD APP --}}
    <div style="margin-top:28px;padding-top:20px;border-top:2px solid var(--black);">
      <div style="font-size:9px;font-weight:700;letter-spacing:2px;color:#888;text-transform:uppercase;margin-bottom:10px;">
        // DOWNLOAD APLIKASI
      </div>

      <div style="display:grid;gap:10px;">
        
        <a href="{{ asset('apk/nusantaramap.apk') }}"
          download
          class="demo-btn"
          style="text-decoration:none;padding:14px;display:block;">
          
          <span style="font-size:9px;font-weight:700;letter-spacing:1px;color:#888;display:block;">
            ANDROID APP
          </span>

          <span style="font-size:11px;">
             Download NusantaraMap
          </span>
        </a>

      </div>
    </div>
  </div>
</div>

<script>
function fillLogin(email, pass) {
  document.querySelector('[name=email]').value = email;
  document.querySelector('[name=password]').value = pass;
}
</script>
</body>
</html>