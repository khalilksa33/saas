<?php
$pageTitle = 'Sponsorship Rates & Brand Invoices';
$currentRoute = 'rate_calculator';
$extraJs = ['rate-calculator.js'];
require_once __DIR__ . '/../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Creator Sponsorship Rates & Brand Invoicing</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Calculate fair market pricing for brand integrations, and generate print-ready PDF invoices
        </p>
    </div>
</div>

<!-- Section 1: Sponsorship Calculator -->
<div class="card" style="margin-bottom: 2.5rem; background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 60%), var(--bg-card);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h2 style="font-size: 1.35rem; font-weight: 700;">📊 Fair Market Sponsorship Rate Calculator</h2>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Algorithm based on 2026 industry CPMs and creator niche multiples</p>
        </div>
        <span class="badge badge-emerald">UPDATED Q4 2026</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="form-group">
            <label class="form-label" for="calcPlatform">Primary Platform</label>
            <select id="calcPlatform" class="form-control">
                <option value="youtube">YouTube</option>
                <option value="instagram">Instagram</option>
                <option value="tiktok">TikTok</option>
                <option value="newsletter">Email Newsletter</option>
                <option value="podcast">Podcast</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="calcFollowers">Total Followers / Subs</label>
            <input type="number" id="calcFollowers" class="form-control" value="50000" step="5000">
        </div>

        <div class="form-group">
            <label class="form-label" for="calcViews">Average Views per Post</label>
            <input type="number" id="calcViews" class="form-control" value="25000" step="1000">
        </div>

        <div class="form-group">
            <label class="form-label" for="calcEngagement">Engagement Rate (%)</label>
            <input type="number" id="calcEngagement" class="form-control" value="3.8" step="0.1">
        </div>

        <div class="form-group">
            <label class="form-label" for="calcFormat">Deliverable Format</label>
            <select id="calcFormat" class="form-control">
                <option value="1.0">YouTube 60s Integration</option>
                <option value="2.2">YouTube Dedicated Video</option>
                <option value="0.75">Instagram Reel / TikTok</option>
                <option value="0.5">Instagram Story (3 frames)</option>
                <option value="1.2">Dedicated Newsletter Blast</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="calcNiche">Creator Niche / Category</label>
            <select id="calcNiche" class="form-control">
                <option value="2.2">Finance / Crypto / B2B SaaS (2.2x)</option>
                <option value="1.7" selected>Tech / AI / Productivity (1.7x)</option>
                <option value="1.3">Fitness / Health / Wellness (1.3x)</option>
                <option value="1.1">Lifestyle / Travel / Fashion (1.1x)</option>
                <option value="0.9">Gaming / Memes / Entertainment (0.9x)</option>
            </select>
        </div>
    </div>

    <!-- Outputs Summary -->
    <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Recommended Rate</div>
                <div style="font-size: 2.2rem; font-weight: 800; font-family: 'Outfit'; color: #6ee7b7;" id="outRate">$1,250</div>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Acceptable Range</div>
                <div style="font-size: 1.4rem; font-weight: 700; font-family: 'Outfit'; color: var(--text-primary); margin-top: 0.4rem;" id="outRange">$1,050 – $1,675</div>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Effective CPM</div>
                <div style="font-size: 1.4rem; font-weight: 700; font-family: 'Outfit'; color: #a5b4fc; margin-top: 0.4rem;" id="outCpm">$50.00</div>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">3-Post Retainer Bundle</div>
                <div style="font-size: 1.4rem; font-weight: 700; font-family: 'Outfit'; color: #f472b6; margin-top: 0.4rem;" id="outBundle">$3,175 (Save 15%)</div>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="button" id="calculateRateBtn" class="btn btn-secondary">
                🔄 Recalculate
            </button>
            <button type="button" id="addToInvoiceBtn" class="btn btn-primary">
                + Add To Invoice Below
            </button>
        </div>
    </div>
</div>

<!-- Section 2: Creator Invoice Generator -->
<div id="invoiceSection" class="card" style="padding: 2.5rem 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-glass); padding-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.4rem; font-weight: 800;">Brand Deal Invoice Builder</h2>
            <p style="color: var(--text-secondary); font-size: 0.88rem;">Professional, client-ready invoice for sponsorships & deliverables</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" id="addInvoiceItemBtn" class="btn btn-secondary">
                + Add Line Item
            </button>
            <button type="button" id="printInvoiceBtn" class="btn btn-primary">
                🖨️ Print / Download PDF
            </button>
        </div>
    </div>

    <!-- Invoice Sheet (Printable Area) -->
    <div id="printableInvoiceSheet" style="background: rgba(13, 19, 33, 0.7); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); padding: 2.5rem; margin-bottom: 1.5rem;">
        <!-- Header Info -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.75rem;">FROM: CREATOR</span>
                <div class="form-group">
                    <input type="text" class="form-control" value="<?= e($user['name']) ?>" placeholder="Creator / Business Name" style="font-weight: 700;">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" value="<?= e($user['email']) ?>" placeholder="Email Address">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Payment Details (e.g. PayPal, Stripe or Wire routing)">
                </div>
            </div>

            <div>
                <span class="badge badge-emerald" style="margin-bottom: 0.75rem;">BILL TO: BRAND SPONSOR</span>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Sponsor Brand Name (e.g. Acme Tech Inc.)" style="font-weight: 700;">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Contact Person / Agency Rep">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Brand Billing Address / VAT ID">
                </div>
            </div>
        </div>

        <!-- Invoice Meta Details -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: var(--radius-md);">
            <div>
                <label class="form-label" style="font-size: 0.75rem;">Invoice Number</label>
                <input type="text" class="form-control" value="INV-<?= date('Y') ?>-0042" style="font-size: 0.85rem;">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem;">Issue Date</label>
                <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" style="font-size: 0.85rem;">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem;">Payment Terms</label>
                <select class="form-control" style="font-size: 0.85rem;">
                    <option>Net 15 Days</option>
                    <option selected>Net 30 Days</option>
                    <option>50% Deposit / 50% on Air Date</option>
                    <option>Due Upon Receipt</option>
                </select>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="table-wrapper" style="margin-bottom: 1.5rem;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Deliverable Description</th>
                        <th style="width: 90px;">Qty</th>
                        <th style="width: 140px;">Rate ($)</th>
                        <th style="width: 120px; text-align: right;">Total ($)</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="invoiceItemsBody">
                    <tr class="invoice-row">
                        <td><input type="text" class="form-control item-desc" value="YouTube 60s Dedicated Mid-Roll Integration"></td>
                        <td style="width: 90px;"><input type="number" class="form-control item-qty" value="1" min="1"></td>
                        <td style="width: 140px;"><input type="number" class="form-control item-rate" value="1250" min="0"></td>
                        <td style="width: 120px; font-weight: 700; text-align: right;" class="item-total">$1,250</td>
                        <td style="width: 50px; text-align: center;"><button type="button" class="btn btn-sm btn-danger remove-item-btn">×</button></td>
                    </tr>
                    <tr class="invoice-row">
                        <td><input type="text" class="form-control item-desc" value="Social Amplification: 1x Instagram Reel Repost"></td>
                        <td style="width: 90px;"><input type="number" class="form-control item-qty" value="1" min="1"></td>
                        <td style="width: 140px;"><input type="number" class="form-control item-rate" value="350" min="0"></td>
                        <td style="width: 120px; font-weight: 700; text-align: right;" class="item-total">$350</td>
                        <td style="width: 50px; text-align: center;"><button type="button" class="btn btn-sm btn-danger remove-item-btn">×</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals Calculation -->
        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 320px; display: flex; flex-direction: column; gap: 0.75rem; background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: var(--radius-md);">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Subtotal:</span>
                    <span style="font-weight: 700;" id="invoiceSubtotal">$1,600</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Tax Rate (%):</span>
                    <input type="number" id="invoiceTaxRate" value="0" min="0" max="100" class="form-control" style="width: 80px; padding: 0.3rem; text-align: right;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Tax Amount:</span>
                    <span id="invoiceTaxAmount">$0</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Discount ($):</span>
                    <input type="number" id="invoiceDiscount" value="0" min="0" class="form-control" style="width: 90px; padding: 0.3rem; text-align: right;">
                </div>
                <div style="border-top: 1px solid var(--border-glass); padding-top: 0.75rem; display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800; color: #6ee7b7;">
                    <span>Total Due:</span>
                    <span id="invoiceGrandTotal">$1,600</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #fff !important; color: #000 !important; }
    .app-sidebar, .app-topbar, .navbar, .footer, .btn, #calculateRateBtn, #addToInvoiceBtn, .metric-card {
        display: none !important;
    }
    .app-wrapper, .app-main, .app-content {
        margin: 0 !important; padding: 0 !important;
    }
    #printableInvoiceSheet {
        border: none !important; background: #fff !important; color: #000 !important; box-shadow: none !important;
    }
    .form-control {
        border: none !important; background: transparent !important; color: #000 !important; font-size: 1rem !important;
    }
    .data-table th, .data-table td {
        color: #000 !important; border-bottom: 1px solid #ddd !important;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
