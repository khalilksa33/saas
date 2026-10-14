/**
 * Smart Short Links & Branded QR Code Studio
 */

document.addEventListener('DOMContentLoaded', () => {
  const shortenerForm = document.getElementById('shortenerForm');
  const targetUrlInput = document.getElementById('targetUrlInput');
  const customCodeInput = document.getElementById('customCodeInput');
  const linkTitleInput = document.getElementById('linkTitleInput');

  // UTM Inputs
  const utmSource = document.getElementById('utmSource');
  const utmMedium = document.getElementById('utmMedium');
  const utmCampaign = document.getElementById('utmCampaign');

  // QR Customizer Inputs
  const qrFgColor = document.getElementById('qrFgColor');
  const qrBgColor = document.getElementById('qrBgColor');
  const qrCanvas = document.getElementById('qrCanvas');
  const downloadQrBtn = document.getElementById('downloadQrBtn');

  // UTM Real-time preview
  function buildFinalUrl() {
    let url = targetUrlInput ? targetUrlInput.value.trim() : '';
    if (!url) return '';
    try {
      const parsed = new URL(url.startsWith('http') ? url : 'https://' + url);
      if (utmSource && utmSource.value.trim()) parsed.searchParams.set('utm_source', utmSource.value.trim());
      if (utmMedium && utmMedium.value.trim()) parsed.searchParams.set('utm_medium', utmMedium.value.trim());
      if (utmCampaign && utmCampaign.value.trim()) parsed.searchParams.set('utm_campaign', utmCampaign.value.trim());
      return parsed.toString();
    } catch (e) {
      return url;
    }
  }

  // Generate QR Code
  window.renderQrCode = function(text) {
    if (!qrCanvas || !text) return;
    const ctx = qrCanvas.getContext('2d');
    const size = 260;
    qrCanvas.width = size;
    qrCanvas.height = size;

    const fg = qrFgColor ? qrFgColor.value : '#6366f1';
    const bg = qrBgColor ? qrBgColor.value : '#ffffff';

    // Render Crisp QR via QuickChart QR API or local pattern fallback
    const qrImg = new Image();
    qrImg.crossOrigin = 'Anonymous';
    const cleanFg = fg.replace('#', '');
    const cleanBg = bg.replace('#', '');
    
    qrImg.onload = () => {
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, size, size);
      ctx.drawImage(qrImg, 15, 15, size - 30, size - 30);
    };

    qrImg.onerror = () => {
      // Fallback local pattern if offline
      ctx.fillStyle = bg;
      ctx.fillRect(0, 0, size, size);
      ctx.fillStyle = fg;
      ctx.font = 'bold 14px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText('QR Code for:', size / 2, size / 2 - 20);
      ctx.font = '11px monospace';
      ctx.fillText(text.slice(0, 30), size / 2, size / 2 + 10);
    };

    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&color=${cleanFg}&bgcolor=${cleanBg}&data=${encodeURIComponent(text)}`;
  };

  if (targetUrlInput) {
    targetUrlInput.addEventListener('input', () => {
      const finalUrl = buildFinalUrl();
      if (finalUrl) window.renderQrCode(finalUrl);
    });
  }

  [utmSource, utmMedium, utmCampaign, qrFgColor, qrBgColor].forEach(input => {
    if (input) {
      input.addEventListener('input', () => {
        const finalUrl = buildFinalUrl();
        if (finalUrl) window.renderQrCode(finalUrl);
      });
    }
  });

  // Shorten submit
  if (shortenerForm) {
    shortenerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const finalTarget = buildFinalUrl();
      if (!finalTarget) {
        window.showToast('Please enter a valid destination URL', 'error');
        return;
      }

      const submitBtn = shortenerForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Creating Smart Link...';

      try {
        const res = await fetch(window.APP_URL + '/api/shortener.php?action=create', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.CSRF_TOKEN || ''
          },
          body: JSON.stringify({
            target_url: finalTarget,
            code: customCodeInput ? customCodeInput.value.trim() : '',
            title: linkTitleInput ? linkTitleInput.value.trim() : '',
            utm_source: utmSource ? utmSource.value.trim() : '',
            utm_medium: utmMedium ? utmMedium.value.trim() : '',
            utm_campaign: utmCampaign ? utmCampaign.value.trim() : ''
          })
        });

        const data = await res.json();
        if (data.success) {
          window.showToast('Smart Link generated successfully!', 'success');
          // Reload or append to table
          setTimeout(() => location.reload(), 800);
        } else {
          window.showToast(data.message || 'Error creating link', 'error');
        }
      } catch (err) {
        window.showToast('Failed to create smart link', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  // QR Download
  if (downloadQrBtn && qrCanvas) {
    downloadQrBtn.addEventListener('click', () => {
      const link = document.createElement('a');
      link.download = `creator-qr-${Date.now()}.png`;
      link.href = qrCanvas.toDataURL('image/png');
      link.click();
      window.showToast('QR Code downloaded as PNG!', 'success');
    });
  }

  // Initial QR
  const initialUrl = targetUrlInput ? targetUrlInput.value : '';
  if (initialUrl) {
    window.renderQrCode(initialUrl);
  }
});
