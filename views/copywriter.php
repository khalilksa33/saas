<?php
$pageTitle = 'Viral Hooks & Copywriting Studio';
$currentRoute = 'copywriter';
$extraJs = ['copywriter.js'];
require_once __DIR__ . '/../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Viral Hooks & Copywriting Studio</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Generate high-CTR opening hooks, video titles, captions, and viral Twitter/X threads
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.35fr; gap: 2rem; align-items: flex-start;">
    <!-- Left Column: Generator Form -->
    <div class="card">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚡</span> Content Generator
        </h3>

        <div class="form-group">
            <label class="form-label" for="copyTopicInput">What is your topic or video concept?</label>
            <textarea id="copyTopicInput" class="form-control" rows="3" placeholder="e.g., How to edit videos 3x faster in Premiere Pro without burning out"></textarea>
            <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <span style="font-size: 0.72rem; color: var(--text-muted); width: 100%;">Popular Prompts:</span>
                <button type="button" class="badge badge-primary" style="cursor: pointer;" onclick="document.getElementById('copyTopicInput').value = 'YouTube Growth for Beginners in 2026';">YouTube Growth</button>
                <button type="button" class="badge badge-primary" style="cursor: pointer;" onclick="document.getElementById('copyTopicInput').value = 'Top 5 AI Productivity Tools for Creators';">AI Tools</button>
                <button type="button" class="badge badge-primary" style="cursor: pointer;" onclick="document.getElementById('copyTopicInput').value = 'How I Landed My First $5,000 Brand Deal';">Brand Deals</button>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="copyFormatSelect">Content Format</label>
            <select id="copyFormatSelect" class="form-control">
                <option value="hooks">🎯 Viral Opening Hooks (High Retention)</option>
                <option value="youtube">🎬 YouTube Title Variations & Description Timestamps</option>
                <option value="captions">📸 Reel & TikTok Captions with Call-to-Actions</option>
                <option value="threads">🧵 Twitter / X Viral Thread (Full Structure)</option>
                <option value="hashtags">🏷️ Multi-Tier Niche Hashtags</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="copyToneSelect">Creator Voice & Tone</label>
            <select id="copyToneSelect" class="form-control">
                <option value="viral">🔥 Bold & Punchy (High Curiosity)</option>
                <option value="educational">🎓 Educational & Authority</option>
                <option value="converting">🚀 Direct & High-Converting (Sales)</option>
                <option value="casual">☕ Casual, Fun & Relatable</option>
            </select>
        </div>

        <button type="button" id="generateCopyBtn" class="btn btn-primary" style="width: 100%; padding: 0.85rem; margin-top: 0.5rem;">
            ⚡ Generate Content
        </button>

        <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-glass); font-size: 0.8rem; color: var(--text-secondary);">
            💡 <strong>100% Ready Out-of-the-Box:</strong> Powered by our high-yield algorithmic creator framework. Works instantly offline without API setup.
        </div>
    </div>

    <!-- Right Column: Results Stream -->
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700;">Generated Output</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Ready to copy & paste</span>
        </div>

        <div id="copyResults">
            <!-- Initial placeholder -->
            <div class="card" style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">✍️</div>
                <h4 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.5rem;">Your generated copy will appear here</h4>
                <p style="font-size: 0.88rem; max-width: 380px; margin: 0 auto;">
                    Select a format on the left, type in your topic, and click "Generate Content" to produce viral-ready copy!
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
