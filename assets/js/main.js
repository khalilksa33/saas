/**
 * CreatorPulse - Core Global JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  // Init Toast Container
  if (!document.getElementById('toast-container')) {
    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }

  // Mobile Sidebar Toggle
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.app-sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // Global Clipboard Helper
  document.querySelectorAll('[data-copy]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      const text = el.getAttribute('data-copy');
      copyToClipboard(text);
    });
  });

  // Init Theme from LocalStorage / Cookie
  const savedTheme = localStorage.getItem('app_theme');
  if (savedTheme) {
    document.documentElement.setAttribute('data-theme', savedTheme);
  }

  // Theme Dropdown Toggle
  const themeToggles = document.querySelectorAll('.theme-dropdown-toggle');
  themeToggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const dropdown = toggle.nextElementSibling;
      if (dropdown && dropdown.classList.contains('theme-dropdown')) {
        document.querySelectorAll('.theme-dropdown.show').forEach(d => {
          if (d !== dropdown) d.classList.remove('show');
        });
        dropdown.classList.toggle('show');
      }
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.theme-dropdown.show').forEach(d => d.classList.remove('show'));
  });

  // Theme Option Click
  document.querySelectorAll('.theme-opt-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const theme = btn.getAttribute('data-theme');
      window.setTheme(theme);
      document.querySelectorAll('.theme-dropdown.show').forEach(d => d.classList.remove('show'));
    });
  });
});

/**
 * Theme Manager & Persistence
 */
window.setTheme = function(theme) {
  const allowed = ['dark', 'light', 'cyberpunk', 'sunset', 'emerald'];
  if (!allowed.includes(theme)) theme = 'dark';
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('app_theme', theme);
  document.cookie = `app_theme=${theme}; path=/; max-age=31536000; SameSite=Lax`;

  // Update active state in UI
  document.querySelectorAll('.theme-opt-btn').forEach(btn => {
    btn.classList.toggle('active', btn.getAttribute('data-theme') === theme);
  });

  // Track in Google Analytics / GTM
  window.trackEvent('theme_change', { theme: theme });
};

/**
 * Google Analytics & Tag Manager Event Tracking Helper
 */
window.trackEvent = function(eventName, eventParams = {}) {
  try {
    if (typeof window.gtag === 'function') {
      window.gtag('event', eventName, eventParams);
    }
    if (window.dataLayer && Array.isArray(window.dataLayer)) {
      window.dataLayer.push({ event: eventName, ...eventParams });
    }
  } catch (err) {
    // Silent fail if adblocker or offline
  }
};

/**
 * Toast Notification System
 */
window.showToast = function(message, type = 'info', duration = 3500) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  
  let icon = 'ℹ️';
  if (type === 'success') icon = '✅';
  if (type === 'error') icon = '❌';

  toast.innerHTML = `<span>${icon}</span> <div>${message}</div>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'fadeOutRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards';
    setTimeout(() => {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
    }, 300);
  }, duration);
};

/**
 * Copy text to clipboard with toast
 */
window.copyToClipboard = function(text, successMsg = 'Copied to clipboard!') {
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(() => {
      window.showToast(successMsg, 'success');
    }).catch(() => {
      fallbackCopy(text, successMsg);
    });
  } else {
    fallbackCopy(text, successMsg);
  }
};

function fallbackCopy(text, successMsg) {
  const textArea = document.createElement('textarea');
  textArea.value = text;
  textArea.style.position = 'fixed';
  textArea.style.left = '-9999px';
  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();
  try {
    document.execCommand('copy');
    window.showToast(successMsg, 'success');
  } catch (err) {
    window.showToast('Failed to copy to clipboard', 'error');
  }
  document.body.removeChild(textArea);
}
