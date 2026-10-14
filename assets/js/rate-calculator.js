/**
 * Creator Sponsorship Rate Calculator & Printable Invoice Studio
 */

document.addEventListener('DOMContentLoaded', () => {
  // Elements for Calculator
  const calcPlatform = document.getElementById('calcPlatform');
  const calcFollowers = document.getElementById('calcFollowers');
  const calcViews = document.getElementById('calcViews');
  const calcEngagement = document.getElementById('calcEngagement');
  const calcFormat = document.getElementById('calcFormat');
  const calcNiche = document.getElementById('calcNiche');
  const calculateBtn = document.getElementById('calculateRateBtn');
  const addToInvoiceBtn = document.getElementById('addToInvoiceBtn');

  // Outputs
  const outRate = document.getElementById('outRate');
  const outRange = document.getElementById('outRange');
  const outCpm = document.getElementById('outCpm');
  const outBundle = document.getElementById('outBundle');

  let currentCalculatedRate = 1250;
  let currentDeliverableName = 'YouTube 60s Integration';

  function calculateRates() {
    const views = parseFloat(calcViews?.value || 25000);
    const followers = parseFloat(calcFollowers?.value || 50000);
    const engagement = parseFloat(calcEngagement?.value || 3.5);
    const nicheMult = parseFloat(calcNiche?.value || 1.7);
    const formatMult = parseFloat(calcFormat?.value || 1.0);

    // Base CPM calculation based on industry averages
    let baseCpm = 25; // $25 base CPM
    if (engagement > 5.0) baseCpm += 8;
    if (engagement > 8.0) baseCpm += 15;

    // Computed Rate
    const rawRate = (views / 1000) * baseCpm * nicheMult * formatMult;
    const finalRate = Math.max(150, Math.round(rawRate / 25) * 25);
    const minRange = Math.round((finalRate * 0.85) / 25) * 25;
    const maxRange = Math.round((finalRate * 1.35) / 25) * 25;
    const effectiveCpm = ((finalRate / views) * 1000).toFixed(2);
    const bundleRate = Math.round((finalRate * 3 * 0.85) / 50) * 50;

    currentCalculatedRate = finalRate;
    const formatSelectEl = document.getElementById('calcFormat');
    currentDeliverableName = formatSelectEl ? formatSelectEl.options[formatSelectEl.selectedIndex].text : 'Brand Sponsorship';

    if (outRate) outRate.textContent = '$' + finalRate.toLocaleString();
    if (outRange) outRange.textContent = `$${minRange.toLocaleString()} – $${maxRange.toLocaleString()}`;
    if (outCpm) outCpm.textContent = '$' + effectiveCpm;
    if (outBundle) outBundle.textContent = '$' + bundleRate.toLocaleString() + ' (Save 15%)';
  }

  if (calculateBtn) {
    calculateBtn.addEventListener('click', () => {
      calculateRates();
      window.showToast('Updated fair market sponsorship rate!', 'success');
    });
  }

  // Trigger initial calculation
  calculateRates();

  // Add calculated rate to invoice
  if (addToInvoiceBtn) {
    addToInvoiceBtn.addEventListener('click', () => {
      addInvoiceRow(currentDeliverableName, 1, currentCalculatedRate);
      window.showToast('Added deliverable to Invoice Builder below!', 'success');
      document.getElementById('invoiceSection')?.scrollIntoView({ behavior: 'smooth' });
    });
  }

  // Invoice Engine
  initInvoiceStudio();
});

function initInvoiceStudio() {
  const addRowBtn = document.getElementById('addInvoiceItemBtn');
  const printBtn = document.getElementById('printInvoiceBtn');

  if (addRowBtn) {
    addRowBtn.addEventListener('click', () => {
      addInvoiceRow('Custom Content Deliverable', 1, 500);
      recalculateInvoice();
    });
  }

  if (printBtn) {
    printBtn.addEventListener('click', () => {
      window.print();
    });
  }

  // Initial calculation
  recalculateInvoice();
}

function addInvoiceRow(desc = '', qty = 1, rate = 0) {
  const tbody = document.getElementById('invoiceItemsBody');
  if (!tbody) return;

  const tr = document.createElement('tr');
  tr.className = 'invoice-row';
  tr.innerHTML = `
    <td><input type="text" class="form-control item-desc" value="${desc}" placeholder="Deliverable details..."></td>
    <td style="width: 90px;"><input type="number" class="form-control item-qty" value="${qty}" min="1"></td>
    <td style="width: 140px;"><input type="number" class="form-control item-rate" value="${rate}" min="0"></td>
    <td style="width: 120px; font-weight: 700; text-align: right;" class="item-total">$${(qty * rate).toLocaleString()}</td>
    <td style="width: 50px; text-align: center;"><button type="button" class="btn btn-sm btn-danger remove-item-btn">×</button></td>
  `;

  tbody.appendChild(tr);

  // Bind events
  tr.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', recalculateInvoice);
  });

  tr.querySelector('.remove-item-btn').onclick = () => {
    tr.remove();
    recalculateInvoice();
  };

  recalculateInvoice();
}

function recalculateInvoice() {
  let subtotal = 0;
  document.querySelectorAll('.invoice-row').forEach(row => {
    const qty = parseFloat(row.querySelector('.item-qty')?.value || 1);
    const rate = parseFloat(row.querySelector('.item-rate')?.value || 0);
    const lineTotal = qty * rate;
    const totalCell = row.querySelector('.item-total');
    if (totalCell) totalCell.textContent = '$' + lineTotal.toLocaleString();
    subtotal += lineTotal;
  });

  const taxPct = parseFloat(document.getElementById('invoiceTaxRate')?.value || 0);
  const discountAmt = parseFloat(document.getElementById('invoiceDiscount')?.value || 0);

  const taxAmount = (subtotal * taxPct) / 100;
  const finalTotal = Math.max(0, subtotal + taxAmount - discountAmt);

  const subtotalEl = document.getElementById('invoiceSubtotal');
  const taxEl = document.getElementById('invoiceTaxAmount');
  const totalEl = document.getElementById('invoiceGrandTotal');

  if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toLocaleString();
  if (taxEl) taxEl.textContent = '$' + taxAmount.toLocaleString();
  if (totalEl) totalEl.textContent = '$' + finalTotal.toLocaleString();
}
