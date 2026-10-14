/**
 * Bio-Link Live Builder & Phone Mockup Controller
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('bioBuilderForm');
  if (!form) return;

  const linksContainer = document.getElementById('linksList');
  const addLinkBtn = document.getElementById('addLinkBtn');
  const saveBtn = document.getElementById('saveBioBtn');

  // Input listeners for real-time preview
  const inputsToListen = ['displayNameInput', 'bioTextInput', 'avatarUrlInput', 'themeSelect'];
  inputsToListen.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', updatePreview);
      el.addEventListener('change', updatePreview);
    }
  });

  // Social inputs
  document.querySelectorAll('.social-input').forEach(input => {
    input.addEventListener('input', updatePreview);
  });

  // Add Link button
  if (addLinkBtn) {
    addLinkBtn.addEventListener('click', () => {
      addLinkRow({
        id: 'new_' + Date.now(),
        title: 'New Link Title',
        url: 'https://',
        icon: 'link',
        badge: '',
        animation: 'none'
      });
      updatePreview();
    });
  }

  // Save via AJAX
  if (saveBtn) {
    saveBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      saveBioProfile();
    });
  }

  // Initial preview render
  bindLinkItemEvents();
  updatePreview();
});

function bindLinkItemEvents() {
  document.querySelectorAll('.link-item-row').forEach(row => {
    row.querySelectorAll('input, select').forEach(input => {
      input.removeEventListener('input', updatePreview);
      input.addEventListener('input', updatePreview);
    });

    const removeBtn = row.querySelector('.remove-link-btn');
    if (removeBtn) {
      removeBtn.onclick = () => {
        row.remove();
        updatePreview();
      };
    }
  });
}

function addLinkRow(data = {}) {
  const container = document.getElementById('linksList');
  const row = document.createElement('div');
  row.className = 'link-item-row card';
  row.style.padding = '1rem';
  row.style.marginBottom = '1rem';
  row.innerHTML = `
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
      <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem;">
        <span>☰</span> Link Item
      </span>
      <button type="button" class="btn btn-sm btn-danger remove-link-btn">Delete</button>
    </div>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
      <input type="text" class="form-control link-title" placeholder="Button Title (e.g. My Podcast)" value="${data.title || ''}">
      <input type="text" class="form-control link-badge" placeholder="Badge (e.g. NEW, 50% OFF)" value="${data.badge || ''}">
    </div>
    <div style="margin-bottom: 0.75rem;">
      <input type="url" class="form-control link-url" placeholder="Destination URL (https://...)" value="${data.url || ''}">
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
      <div>
        <label class="form-label" style="font-size: 0.75rem;">Icon Emoji/Keyword</label>
        <select class="form-control link-icon">
          <option value="🔗" ${data.icon === '🔗' ? 'selected' : ''}>🔗 Link</option>
          <option value="🎥" ${data.icon === '🎥' ? 'selected' : ''}>🎥 Video</option>
          <option value="🎙️" ${data.icon === '🎙️' ? 'selected' : ''}>🎙️ Podcast</option>
          <option value="🎨" ${data.icon === '🎨' ? 'selected' : ''}>🎨 Design / Assets</option>
          <option value="🛍️" ${data.icon === '🛍️' ? 'selected' : ''}>🛍️ Store / Product</option>
          <option value="💻" ${data.icon === '💻' ? 'selected' : ''}>💻 Tech Gear</option>
          <option value="📬" ${data.icon === '📬' ? 'selected' : ''}>📬 Newsletter</option>
          <option value="⭐" ${data.icon === '⭐' ? 'selected' : ''}>⭐ Featured</option>
        </select>
      </div>
      <div>
        <label class="form-label" style="font-size: 0.75rem;">Attention Animation</label>
        <select class="form-control link-animation">
          <option value="none" ${data.animation === 'none' ? 'selected' : ''}>None</option>
          <option value="pulse" ${data.animation === 'pulse' ? 'selected' : ''}>Subtle Pulse</option>
          <option value="shake" ${data.animation === 'shake' ? 'selected' : ''}>Gentle Shake</option>
          <option value="bounce" ${data.animation === 'bounce' ? 'selected' : ''}>Hover Bounce</option>
        </select>
      </div>
    </div>
  `;
  container.appendChild(row);
  bindLinkItemEvents();
}

function updatePreview() {
  const phoneScreen = document.getElementById('phoneScreen');
  if (!phoneScreen) return;

  const displayName = document.getElementById('displayNameInput')?.value || 'Your Name';
  const bioText = document.getElementById('bioTextInput')?.value || 'Digital Creator & Storyteller';
  const avatarUrl = document.getElementById('avatarUrlInput')?.value || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80';
  const theme = document.getElementById('themeSelect')?.value || 'midnight';

  // Update theme class on mockup screen
  phoneScreen.className = `phone-screen theme-${theme}`;

  // Update avatar
  const avatarEl = document.getElementById('previewAvatar');
  if (avatarEl) avatarEl.src = avatarUrl;

  // Update name and bio
  const nameEl = document.getElementById('previewName');
  if (nameEl) nameEl.textContent = displayName;

  const bioEl = document.getElementById('previewBio');
  if (bioEl) bioEl.textContent = bioText;

  // Update Socials
  const socialsContainer = document.getElementById('previewSocials');
  if (socialsContainer) {
    let socialHtml = '';
    const socialPlatforms = [
      { id: 'social_youtube', icon: '▶️', label: 'YouTube' },
      { id: 'social_instagram', icon: '📸', label: 'Instagram' },
      { id: 'social_tiktok', icon: '🎵', label: 'TikTok' },
      { id: 'social_twitter', icon: '✖️', label: 'X' },
      { id: 'social_spotify', icon: '🎧', label: 'Spotify' },
      { id: 'social_github', icon: '💻', label: 'GitHub' }
    ];

    socialPlatforms.forEach(p => {
      const val = document.getElementById(p.id)?.value;
      if (val && val.trim() !== '') {
        socialHtml += `<a href="${val}" target="_blank" class="bio-social-link" title="${p.label}">${p.icon}</a>`;
      }
    });

    socialsContainer.innerHTML = socialHtml;
  }

  // Update Links Stack
  const linksStack = document.getElementById('previewLinks');
  if (linksStack) {
    let linksHtml = '';
    document.querySelectorAll('.link-item-row').forEach(row => {
      const title = row.querySelector('.link-title')?.value || 'Link';
      const url = row.querySelector('.link-url')?.value || '#';
      const badge = row.querySelector('.link-badge')?.value || '';
      const icon = row.querySelector('.link-icon')?.value || '🔗';
      const anim = row.querySelector('.link-animation')?.value || 'none';

      let animClass = anim !== 'none' ? `anim-${anim}` : '';
      let badgeHtml = badge ? `<span class="bio-link-badge badge badge-pro">${badge}</span>` : '';

      linksHtml += `
        <a href="${url}" target="_blank" class="bio-link-btn ${animClass}">
          <div class="bio-link-left">
            <span class="bio-link-icon">${icon}</span>
            <span class="bio-link-title">${title}</span>
          </div>
          ${badgeHtml}
        </a>
      `;
    });

    linksStack.innerHTML = linksHtml || '<div style="text-align:center; opacity:0.6; padding: 2rem 0;">Add your first link on the left 👈</div>';
  }
}

async function saveBioProfile() {
  const saveBtn = document.getElementById('saveBioBtn');
  const originalText = saveBtn.innerHTML;
  saveBtn.disabled = true;
  saveBtn.innerHTML = 'Saving Changes...';

  const theme = document.getElementById('themeSelect')?.value || 'midnight';
  const displayName = document.getElementById('displayNameInput')?.value || '';
  const bioText = document.getElementById('bioTextInput')?.value || '';
  const avatarUrl = document.getElementById('avatarUrlInput')?.value || '';

  const socials = {
    youtube: document.getElementById('social_youtube')?.value || '',
    instagram: document.getElementById('social_instagram')?.value || '',
    tiktok: document.getElementById('social_tiktok')?.value || '',
    twitter: document.getElementById('social_twitter')?.value || '',
    spotify: document.getElementById('social_spotify')?.value || '',
    github: document.getElementById('social_github')?.value || ''
  };

  const links = [];
  document.querySelectorAll('.link-item-row').forEach(row => {
    links.push({
      title: row.querySelector('.link-title')?.value || '',
      url: row.querySelector('.link-url')?.value || '',
      badge: row.querySelector('.link-badge')?.value || '',
      icon: row.querySelector('.link-icon')?.value || '🔗',
      animation: row.querySelector('.link-animation')?.value || 'none'
    });
  });

  try {
    const response = await fetch(window.APP_URL + '/api/links.php?action=save_profile', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.CSRF_TOKEN || ''
      },
      body: JSON.stringify({
        theme,
        display_name: displayName,
        bio_text: bioText,
        avatar_url: avatarUrl,
        socials,
        links
      })
    });

    const data = await response.json();
    if (data.success) {
      window.showToast('Profile and Links saved successfully!', 'success');
    } else {
      window.showToast(data.message || 'Failed to save', 'error');
    }
  } catch (err) {
    window.showToast('Network error while saving profile', 'error');
  } finally {
    saveBtn.disabled = false;
    saveBtn.innerHTML = originalText;
  }
}
