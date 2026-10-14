<?php
$pageTitle = 'Thumbnail & Canvas Studio';
$currentRoute = 'thumbnail_studio';
$extraCss = ['canvas-studio.css'];
$extraJs = ['canvas-studio.js'];
require_once __DIR__ . '/../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Social Media Thumbnail & Canvas Studio</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Design high-CTR graphics, thumbnails, and story banners directly in your browser
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <button type="button" id="exportPngBtn" class="btn btn-primary">
            📥 Download PNG (HD)
        </button>
        <button type="button" id="exportJpgBtn" class="btn btn-secondary">
            📥 Download JPG
        </button>
    </div>
</div>

<div class="studio-container">
    <!-- Left Sidebar: Controls -->
    <div class="studio-sidebar">
        <div class="studio-tabs">
            <div class="studio-tab-btn active">Design Tools</div>
        </div>

        <div class="studio-panel">
            <!-- Canvas Dimension Presets -->
            <div>
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.5rem; display: block;">Format & Dimensions</label>
                <div class="preset-chips">
                    <div class="preset-chip active" data-w="1280" data-h="720">
                        <div class="preset-name">YouTube 16:9</div>
                        <div class="preset-res">1280 × 720</div>
                    </div>
                    <div class="preset-chip" data-w="1080" data-h="1080">
                        <div class="preset-name">Instagram 1:1</div>
                        <div class="preset-res">1080 × 1080</div>
                    </div>
                    <div class="preset-chip" data-w="1080" data-h="1920">
                        <div class="preset-name">Story / Reels</div>
                        <div class="preset-res">1080 × 1920</div>
                    </div>
                    <div class="preset-chip" data-w="1200" data-h="675">
                        <div class="preset-name">Twitter / X</div>
                        <div class="preset-res">1200 × 675</div>
                    </div>
                </div>
            </div>

            <!-- Background Gradient Presets -->
            <div>
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.5rem; display: block;">Background Color</label>
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <button type="button" class="btn btn-sm btn-secondary bg-gradient-preset" data-c1="#1e1b4b" data-c2="#090d16" style="flex: 1; font-size: 0.75rem;">Indigo</button>
                    <button type="button" class="btn btn-sm btn-secondary bg-gradient-preset" data-c1="#4c0519" data-c2="#18052e" style="flex: 1; font-size: 0.75rem;">Sunset</button>
                    <button type="button" class="btn btn-sm btn-secondary bg-gradient-preset" data-c1="#05050c" data-c2="#064e3b" style="flex: 1; font-size: 0.75rem;">Cyber</button>
                    <button type="button" class="btn btn-sm btn-secondary bg-gradient-preset" data-c1="#1c1917" data-c2="#0c0a09" style="flex: 1; font-size: 0.75rem;">Dark</button>
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.75rem;">Or Upload Background Photo</label>
                    <input type="file" id="bgImageInput" accept="image/*" class="form-control" style="font-size: 0.8rem; padding: 0.45rem;">
                </div>
            </div>

            <!-- Typography & Active Layer -->
            <div style="border-top: 1px solid var(--border-glass); padding-top: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="font-size: 0.8rem;">Text & Typography</label>
                    <button type="button" id="addTextBtn" class="btn btn-sm btn-secondary" style="font-size: 0.75rem;">+ Add Text</button>
                </div>
                
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <input type="text" id="layerTextInput" class="form-control" placeholder="Layer Text..." style="font-size: 0.88rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.72rem;">Font Family</label>
                        <select id="layerFontSelect" class="form-control" style="font-size: 0.8rem; padding: 0.4rem;">
                            <option value="Outfit">Outfit</option>
                            <option value="Impact">Impact</option>
                            <option value="Anton">Anton</option>
                            <option value="Plus Jakarta Sans">Jakarta Sans</option>
                            <option value="Arial Black">Arial Black</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.72rem;">Font Size</label>
                        <input type="range" id="layerSizeInput" min="20" max="140" value="70" class="form-control" style="padding: 0.2rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.4rem; margin-bottom: 0.75rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.72rem;">Fill</label>
                        <input type="color" id="layerColorInput" value="#ffffff" style="width: 100%; height: 32px; border-radius: var(--radius-sm); cursor: pointer;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.72rem;">Stroke</label>
                        <input type="color" id="layerStrokeColorInput" value="#000000" style="width: 100%; height: 32px; border-radius: var(--radius-sm); cursor: pointer;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.72rem;">Outline</label>
                        <input type="number" id="layerStrokeWidthInput" min="0" max="20" value="6" class="form-control" style="padding: 0.3rem; font-size: 0.8rem;">
                    </div>
                </div>
            </div>

            <!-- Stickers / Badges -->
            <div style="border-top: 1px solid var(--border-glass); padding-top: 1rem;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.5rem; display: block;">Creator Badges (Click to Add)</label>
                <div class="badges-grid">
                    <div class="badge-item" data-text="🔥 100% FREE" data-color="#ef4444">🔥 100% FREE</div>
                    <div class="badge-item" data-text="⚡ VIRAL" data-color="#8b5cf6">⚡ VIRAL</div>
                    <div class="badge-item" data-text="🔴 LIVE" data-color="#dc2626">🔴 LIVE</div>
                    <div class="badge-item" data-text="🎬 MUST WATCH" data-color="#3b82f6">🎬 MUST WATCH</div>
                    <div class="badge-item" data-text="⭐ TOP 10" data-color="#f59e0b">⭐ TOP 10</div>
                    <div class="badge-item" data-text="🔔 SUBSCRIBE" data-color="#ec4899">🔔 SUBSCRIBE</div>
                </div>
            </div>

            <!-- Layer List -->
            <div style="border-top: 1px solid var(--border-glass); padding-top: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" style="font-size: 0.8rem;">Active Layers</label>
                    <button type="button" id="deleteLayerBtn" class="btn btn-sm btn-danger" style="font-size: 0.72rem; padding: 0.2rem 0.5rem;">Delete Layer</button>
                </div>
                <div class="layer-list" id="layersList"></div>
            </div>
        </div>
    </div>

    <!-- Center Viewport & Canvas Stage -->
    <div class="studio-viewport">
        <div class="studio-toolbar">
            <span style="font-size: 0.85rem; color: var(--text-secondary);">
                💡 Click & Drag text on canvas to position
            </span>
            <div style="display: flex; gap: 0.5rem;">
                <span class="badge badge-primary">HTML5 CANVAS 60FPS</span>
            </div>
        </div>

        <div class="canvas-stage">
            <canvas id="thumbnailCanvas"></canvas>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
