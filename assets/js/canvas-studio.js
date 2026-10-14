/**
 * Social Media Graphics & Thumbnail Canvas Studio Engine
 */

class ThumbnailStudio {
  constructor(canvasId) {
    this.canvas = document.getElementById(canvasId);
    if (!this.canvas) return;
    this.ctx = this.canvas.getContext('2d');

    // Canvas real resolution
    this.width = 1280;
    this.height = 720;
    this.canvas.width = this.width;
    this.canvas.height = this.height;

    // State
    this.bgType = 'gradient'; // 'gradient', 'solid', 'image'
    this.bgColor1 = '#1e1b4b';
    this.bgColor2 = '#090d16';
    this.bgImage = null;

    // Layers (text, stickers)
    this.layers = [
      {
        id: 1,
        type: 'text',
        text: 'HOW I GREW TO 100K',
        font: 'Outfit',
        size: 72,
        bold: true,
        color: '#ffffff',
        strokeColor: '#000000',
        strokeWidth: 6,
        glowColor: '#6366f1',
        glowBlur: 20,
        x: 640,
        y: 280,
        align: 'center'
      },
      {
        id: 2,
        type: 'text',
        text: 'IN JUST 6 MONTHS 🚀',
        font: 'Outfit',
        size: 56,
        bold: true,
        color: '#fbbf24',
        strokeColor: '#000000',
        strokeWidth: 5,
        glowColor: 'transparent',
        glowBlur: 0,
        x: 640,
        y: 380,
        align: 'center'
      },
      {
        id: 3,
        type: 'badge',
        text: '🔥 100% FREE',
        bg: '#ef4444',
        color: '#ffffff',
        x: 640,
        y: 480,
        padding: 18,
        radius: 12
      }
    ];

    this.activeLayerId = 1;
    this.isDragging = false;
    this.dragStartX = 0;
    this.dragStartY = 0;

    this.initEvents();
    this.render();
  }

  initEvents() {
    // Dimension Presets
    document.querySelectorAll('.preset-chip').forEach(chip => {
      chip.addEventListener('click', () => {
        document.querySelectorAll('.preset-chip').forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        const w = parseInt(chip.getAttribute('data-w'));
        const h = parseInt(chip.getAttribute('data-h'));
        this.setDimensions(w, h);
      });
    });

    // Background Gradient Presets
    document.querySelectorAll('.bg-gradient-preset').forEach(btn => {
      btn.addEventListener('click', () => {
        this.bgType = 'gradient';
        this.bgColor1 = btn.getAttribute('data-c1');
        this.bgColor2 = btn.getAttribute('data-c2');
        this.render();
      });
    });

    // Background Image Upload
    const imgUpload = document.getElementById('bgImageInput');
    if (imgUpload) {
      imgUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (event) => {
            const img = new Image();
            img.onload = () => {
              this.bgType = 'image';
              this.bgImage = img;
              this.render();
            };
            img.src = event.target.result;
          };
          reader.readAsDataURL(file);
        }
      });
    }

    // Add Text Layer
    const addTextBtn = document.getElementById('addTextBtn');
    if (addTextBtn) {
      addTextBtn.addEventListener('click', () => {
        const newId = Date.now();
        this.layers.push({
          id: newId,
          type: 'text',
          text: 'YOUR TEXT HERE',
          font: 'Outfit',
          size: 60,
          bold: true,
          color: '#ffffff',
          strokeColor: '#000000',
          strokeWidth: 4,
          glowColor: 'transparent',
          glowBlur: 0,
          x: this.width / 2,
          y: this.height / 2,
          align: 'center'
        });
        this.activeLayerId = newId;
        this.updateActiveLayerForm();
        this.render();
        this.updateLayersUI();
      });
    }

    // Badge click
    document.querySelectorAll('.badge-item').forEach(badge => {
      badge.addEventListener('click', () => {
        const text = badge.getAttribute('data-text') || badge.innerText;
        const color = badge.getAttribute('data-color') || '#ef4444';
        const newId = Date.now();
        this.layers.push({
          id: newId,
          type: 'badge',
          text: text,
          bg: color,
          color: '#ffffff',
          x: this.width / 2,
          y: this.height / 2,
          padding: 16,
          radius: 10
        });
        this.activeLayerId = newId;
        this.render();
        this.updateLayersUI();
      });
    });

    // Layer Form Inputs
    const textInput = document.getElementById('layerTextInput');
    if (textInput) {
      textInput.addEventListener('input', (e) => {
        const layer = this.getActiveLayer();
        if (layer) {
          layer.text = e.target.value;
          this.render();
          this.updateLayersUI();
        }
      });
    }

    const fontSizeInput = document.getElementById('layerSizeInput');
    if (fontSizeInput) {
      fontSizeInput.addEventListener('input', (e) => {
        const layer = this.getActiveLayer();
        if (layer && layer.type === 'text') {
          layer.size = parseInt(e.target.value);
          this.render();
        }
      });
    }

    const fontColorInput = document.getElementById('layerColorInput');
    if (fontColorInput) {
      fontColorInput.addEventListener('input', (e) => {
        const layer = this.getActiveLayer();
        if (layer) {
          layer.color = e.target.value;
          this.render();
        }
      });
    }

    const strokeColorInput = document.getElementById('layerStrokeColorInput');
    if (strokeColorInput) {
      strokeColorInput.addEventListener('input', (e) => {
        const layer = this.getActiveLayer();
        if (layer && layer.type === 'text') {
          layer.strokeColor = e.target.value;
          this.render();
        }
      });
    }

    const strokeWidthInput = document.getElementById('layerStrokeWidthInput');
    if (strokeWidthInput) {
      strokeWidthInput.addEventListener('input', (e) => {
        const layer = this.getActiveLayer();
        if (layer && layer.type === 'text') {
          layer.strokeWidth = parseInt(e.target.value);
          this.render();
        }
      });
    }

    const fontSelect = document.getElementById('layerFontSelect');
    if (fontSelect) {
      fontSelect.addEventListener('change', (e) => {
        const layer = this.getActiveLayer();
        if (layer && layer.type === 'text') {
          layer.font = e.target.value;
          this.render();
        }
      });
    }

    // Delete Active Layer
    const deleteLayerBtn = document.getElementById('deleteLayerBtn');
    if (deleteLayerBtn) {
      deleteLayerBtn.addEventListener('click', () => {
        this.layers = this.layers.filter(l => l.id !== this.activeLayerId);
        if (this.layers.length > 0) {
          this.activeLayerId = this.layers[this.layers.length - 1].id;
        } else {
          this.activeLayerId = null;
        }
        this.updateActiveLayerForm();
        this.render();
        this.updateLayersUI();
      });
    }

    // Mouse drag interaction
    this.canvas.addEventListener('mousedown', (e) => this.onMouseDown(e));
    window.addEventListener('mousemove', (e) => this.onMouseMove(e));
    window.addEventListener('mouseup', () => this.onMouseUp());

    // Export buttons
    const exportPngBtn = document.getElementById('exportPngBtn');
    if (exportPngBtn) {
      exportPngBtn.addEventListener('click', () => this.exportImage('png'));
    }

    const exportJpgBtn = document.getElementById('exportJpgBtn');
    if (exportJpgBtn) {
      exportJpgBtn.addEventListener('click', () => this.exportImage('jpeg'));
    }

    this.updateActiveLayerForm();
    this.updateLayersUI();
  }

  setDimensions(w, h) {
    this.width = w;
    this.height = h;
    this.canvas.width = w;
    this.canvas.height = h;
    this.render();
  }

  getActiveLayer() {
    return this.layers.find(l => l.id === this.activeLayerId);
  }

  updateActiveLayerForm() {
    const layer = this.getActiveLayer();
    const textInput = document.getElementById('layerTextInput');
    const sizeInput = document.getElementById('layerSizeInput');
    const colorInput = document.getElementById('layerColorInput');
    const strokeColorInput = document.getElementById('layerStrokeColorInput');
    const strokeWidthInput = document.getElementById('layerStrokeWidthInput');
    const fontSelect = document.getElementById('layerFontSelect');

    if (!layer) {
      if (textInput) textInput.value = '';
      return;
    }

    if (textInput) textInput.value = layer.text || '';
    if (sizeInput && layer.size) sizeInput.value = layer.size;
    if (colorInput && layer.color) colorInput.value = layer.color;
    if (strokeColorInput && layer.strokeColor) strokeColorInput.value = layer.strokeColor;
    if (strokeWidthInput && layer.strokeWidth !== undefined) strokeWidthInput.value = layer.strokeWidth;
    if (fontSelect && layer.font) fontSelect.value = layer.font;
  }

  updateLayersUI() {
    const list = document.getElementById('layersList');
    if (!list) return;

    list.innerHTML = '';
    [...this.layers].reverse().forEach(layer => {
      const item = document.createElement('div');
      item.className = `layer-item ${layer.id === this.activeLayerId ? 'active' : ''}`;
      item.innerHTML = `
        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">
          ${layer.type === 'text' ? '🔤 ' : '🏷️ '} ${layer.text}
        </span>
        <button type="button" style="color: var(--text-muted); cursor: pointer;" title="Select">✏️</button>
      `;
      item.onclick = () => {
        this.activeLayerId = layer.id;
        this.updateActiveLayerForm();
        this.updateLayersUI();
        this.render();
      };
      list.appendChild(item);
    });
  }

  onMouseDown(e) {
    const rect = this.canvas.getBoundingClientRect();
    const scaleX = this.width / rect.width;
    const scaleY = this.height / rect.height;
    const clickX = (e.clientX - rect.left) * scaleX;
    const clickY = (e.clientY - rect.top) * scaleY;

    // Check hit layer from top to bottom
    for (let i = this.layers.length - 1; i >= 0; i--) {
      const layer = this.layers[i];
      // Simple approximate hit-box
      const isHit = Math.abs(layer.x - clickX) < 200 && Math.abs(layer.y - clickY) < 60;
      if (isHit) {
        this.activeLayerId = layer.id;
        this.isDragging = true;
        this.dragStartX = clickX - layer.x;
        this.dragStartY = clickY - layer.y;
        this.updateActiveLayerForm();
        this.updateLayersUI();
        this.render();
        break;
      }
    }
  }

  onMouseMove(e) {
    if (!this.isDragging) return;
    const rect = this.canvas.getBoundingClientRect();
    const scaleX = this.width / rect.width;
    const scaleY = this.height / rect.height;
    const currentX = (e.clientX - rect.left) * scaleX;
    const currentY = (e.clientY - rect.top) * scaleY;

    const layer = this.getActiveLayer();
    if (layer) {
      layer.x = Math.round(currentX - this.dragStartX);
      layer.y = Math.round(currentY - this.dragStartY);
      this.render();
    }
  }

  onMouseUp() {
    this.isDragging = false;
  }

  render() {
    const ctx = this.ctx;
    ctx.clearRect(0, 0, this.width, this.height);

    // 1. Draw Background
    if (this.bgType === 'gradient') {
      const grad = ctx.createLinearGradient(0, 0, this.width, this.height);
      grad.addColorStop(0, this.bgColor1);
      grad.addColorStop(1, this.bgColor2);
      ctx.fillStyle = grad;
      ctx.fillRect(0, 0, this.width, this.height);
    } else if (this.bgType === 'image' && this.bgImage) {
      ctx.drawImage(this.bgImage, 0, 0, this.width, this.height);
    } else {
      ctx.fillStyle = this.bgColor1;
      ctx.fillRect(0, 0, this.width, this.height);
    }

    // Subtle dark vignette for high-end look
    const vignette = ctx.createRadialGradient(
      this.width / 2, this.height / 2, this.height * 0.3,
      this.width / 2, this.height / 2, this.width * 0.7
    );
    vignette.addColorStop(0, 'rgba(0,0,0,0)');
    vignette.addColorStop(1, 'rgba(0,0,0,0.5)');
    ctx.fillStyle = vignette;
    ctx.fillRect(0, 0, this.width, this.height);

    // 2. Render Layers
    this.layers.forEach(layer => {
      ctx.save();
      if (layer.type === 'text') {
        ctx.font = `${layer.bold ? 'bold ' : ''}${layer.size}px '${layer.font}', sans-serif`;
        ctx.textAlign = layer.align || 'center';
        ctx.textBaseline = 'middle';

        // Glow
        if (layer.glowColor && layer.glowColor !== 'transparent') {
          ctx.shadowColor = layer.glowColor;
          ctx.shadowBlur = layer.glowBlur || 15;
        }

        // Stroke
        if (layer.strokeWidth && layer.strokeWidth > 0) {
          ctx.strokeStyle = layer.strokeColor || '#000';
          ctx.lineWidth = layer.strokeWidth;
          ctx.lineJoin = 'round';
          ctx.strokeText(layer.text, layer.x, layer.y);
        }

        // Fill
        ctx.fillStyle = layer.color || '#fff';
        ctx.fillText(layer.text, layer.x, layer.y);

      } else if (layer.type === 'badge') {
        ctx.font = `bold 28px 'Outfit', sans-serif`;
        ctx.textBaseline = 'middle';
        ctx.textAlign = 'center';
        const textMetrics = ctx.measureText(layer.text);
        const padX = layer.padding || 16;
        const padY = 12;
        const bWidth = textMetrics.width + (padX * 2);
        const bHeight = 44;
        const bx = layer.x - (bWidth / 2);
        const by = layer.y - (bHeight / 2);

        // Draw rounded rect badge
        ctx.fillStyle = layer.bg || '#ef4444';
        ctx.beginPath();
        ctx.roundRect(bx, by, bWidth, bHeight, layer.radius || 8);
        ctx.fill();

        ctx.fillStyle = layer.color || '#ffffff';
        ctx.fillText(layer.text, layer.x, layer.y);
      }
      ctx.restore();
    });
  }

  exportImage(format = 'png') {
    const mime = format === 'jpeg' ? 'image/jpeg' : 'image/png';
    const dataUrl = this.canvas.toDataURL(mime, 0.95);
    const link = document.createElement('a');
    link.download = `creator-thumbnail-${Date.now()}.${format}`;
    link.href = dataUrl;
    link.click();
    window.showToast(`Exported ${format.toUpperCase()} successfully!`, 'success');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('thumbnailCanvas')) {
    window.studio = new ThumbnailStudio('thumbnailCanvas');
  }
});
