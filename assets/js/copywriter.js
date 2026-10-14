/**
 * Creator Copywriting, Viral Hooks & Caption Studio Engine
 */

document.addEventListener('DOMContentLoaded', () => {
  const generateBtn = document.getElementById('generateCopyBtn');
  if (!generateBtn) return;

  const topicInput = document.getElementById('copyTopicInput');
  const formatSelect = document.getElementById('copyFormatSelect');
  const toneSelect = document.getElementById('copyToneSelect');
  const resultsContainer = document.getElementById('copyResults');

  generateBtn.addEventListener('click', async () => {
    const topic = topicInput.value.trim();
    if (!topic) {
      window.showToast('Please enter a topic or concept first!', 'error');
      topicInput.focus();
      return;
    }

    const format = formatSelect.value;
    const tone = toneSelect.value;

    generateBtn.disabled = true;
    generateBtn.innerHTML = '⚡ Generating High-CTR Content...';

    // Simulate snappy generation or fetch from API
    setTimeout(() => {
      const generatedItems = generateContent(topic, format, tone);
      renderResults(generatedItems);
      generateBtn.disabled = false;
      generateBtn.innerHTML = '⚡ Generate Content';
      window.showToast('Generated fresh creator copy!', 'success');
    }, 400);
  });
});

/**
 * High-Yield Creator Copywriting Engine
 */
function generateContent(topic, format, tone) {
  const items = [];

  if (format === 'hooks') {
    items.push({
      title: '🎯 The Contrarian / Myth-Buster Hook',
      content: `Most creators think ${topic} is about talent. They are completely wrong.\n\nHere is the exact truth nobody is talking about (and how to use it before everyone else catches on):`
    });
    items.push({
      title: '🔥 The Curiosity Gap Hook',
      content: `I spent the last 30 days analyzing ${topic} so you don't have to.\n\nHere are the 3 non-obvious rules that changed everything for me:`
    });
    items.push({
      title: '⚡ The "Pain to Breakthrough" Hook',
      content: `If you are struggling with ${topic}, stop doing what 99% of people tell you.\n\nHere is the 4-step framework that actually works:`
    });
    items.push({
      title: '⏳ The 1-Year Ahead Hook',
      content: `If I had to start over with ${topic} from $0 and zero audience, this is the exact blueprint I would execute step-by-step:`
    });

  } else if (format === 'youtube') {
    items.push({
      title: '🎬 High-CTR Video Package',
      content: `TITLES TO A/B TEST:
1. The Brutal Truth About ${topic} in 2026
2. Why 99% of Creators Fail at ${topic} (Fix This)
3. How to Master ${topic} (Step-by-Step Blueprint)

VIDEO DESCRIPTION & TIMESTAMPS:
In this video, we break down the definitive blueprint for ${topic}. 
Whether you are just starting or scaling up, these proven frameworks will save you months of trial and error.

TIMESTAMPS:
0:00 - The Biggest Mistake with ${topic}
01:45 - The Core Framework Explained
04:20 - Real-World Examples & Case Studies
07:15 - How to Avoid Common Traps
09:40 - Action Checklist for This Week

RESOURCES & LINKS:
🔗 Grab the free creator templates in bio!
🔔 Subscribe for weekly creator breakdowns.`
    });

  } else if (format === 'captions') {
    items.push({
      title: '📸 High-Engagement Reel & TikTok Caption',
      content: `Stop scrolling if you care about ${topic} 🛑👇

Here is the one thing I wish someone told me before I started:

1️⃣ Master the fundamentals first.
2️⃣ Consistency beats intensity every single time.
3️⃣ Don't overcomplicate your workflow.

Save this post so you don't lose it later 📌

Drop a "YES" in the comments if you want the full checklist sent to your DMs! 💬`
    });
    items.push({
      title: '✨ Storytelling & Value Caption',
      content: `The secret to ${topic} isn't doing more—it's doing the right things with crazy focus.

When I first tackled ${topic}, I made every rookie mistake in the book. But once I simplified my system, results 10x'd.

What's your biggest roadblock with this right now? Let me know below 👇`
    });

  } else if (format === 'hashtags') {
    const cleanTopic = topic.replace(/[^a-zA-Z0-9]/g, '');
    items.push({
      title: '🏷️ Optimized Multi-Tier Hashtag Set',
      content: `#${cleanTopic} #${cleanTopic}Tips #${cleanTopic}Hacks #DigitalCreator #ContentStrategy #CreatorEconomy #GrowthHacks #BuildInPublic #CreatorTools #ContentCreation #OnlineBusiness`
    });

  } else if (format === 'threads') {
    items.push({
      title: '🧵 Twitter / X Viral Thread',
      content: `1/7
${topic} is one of the highest leverage skills in 2026.

Yet 95% of people do it completely backwards.

A breakdown of the modern playbook (and how to apply it): 🧵👇

2/7
Rule 1: Focus on distribution as much as creation.
If nobody sees your work, it doesn't matter how good it is.

3/7
Rule 2: Build feedback loops early.
Measure what resonates, discard the rest, double down on what works.

4/7
Rule 3: Automate the repetitive tasks so you can focus purely on creativity.

5/7
If you found this valuable:
1. Retweet the first tweet to share with other creators 🔁
2. Follow along for daily creator breakdowns! 🚀`
    });
  }

  return items;
}

function renderResults(items) {
  const container = document.getElementById('copyResults');
  if (!container) return;

  container.innerHTML = '';
  items.forEach((item, idx) => {
    const card = document.createElement('div');
    card.className = 'card';
    card.style.marginBottom = '1.25rem';
    card.style.background = 'rgba(255, 255, 255, 0.02)';

    const wordCount = item.content.split(/\s+/).filter(Boolean).length;
    const charCount = item.content.length;

    card.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
        <h4 style="font-size: 0.95rem; color: #a5b4fc;">${item.title}</h4>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
          <span style="font-size: 0.72rem; color: var(--text-muted);">${wordCount} words • ${charCount} chars</span>
          <button type="button" class="btn btn-sm btn-secondary copy-snippet-btn" data-copy="${escapeHtml(item.content)}">
            📋 Copy
          </button>
        </div>
      </div>
      <div style="background: rgba(15, 23, 42, 0.6); padding: 1rem; border-radius: var(--radius-md); font-family: inherit; font-size: 0.9rem; white-space: pre-wrap; line-height: 1.6; color: var(--text-primary); border: 1px solid var(--border-glass);">
${escapeHtml(item.content)}
      </div>
    `;

    card.querySelector('.copy-snippet-btn').addEventListener('click', () => {
      window.copyToClipboard(item.content, 'Content copied to clipboard!');
    });

    container.appendChild(card);
  });
}

function escapeHtml(str) {
  return str.replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
}
