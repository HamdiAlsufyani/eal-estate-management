/**
 * Dependency-free inline-SVG chart renderer for the analytics dashboards.
 * Scans [data-chart] elements for a JSON config and renders bar, line, or
 * donut charts. Uses viewBox-based SVG so charts scale responsively and
 * stay correct in both LTR and RTL layouts without a charting library.
 */

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[char]));
}

function defaultPalette() {
    const style = getComputedStyle(document.documentElement);
    const read = (name, fallback) => (style.getPropertyValue(name).trim() || fallback);

    return [
        read('--color-primary', '#153E74'),
        read('--color-secondary', '#E5A72D'),
        read('--color-success', '#16A34A'),
        read('--color-info', '#0EA5E9'),
        read('--color-warning', '#F59E0B'),
        read('--color-danger', '#DC2626'),
    ];
}

function renderBar(labels, data, color) {
    const width = 600;
    const height = 220;
    const padding = 28;
    const max = Math.max(...data, 1);
    const barWidth = (width - padding * 2) / data.length;

    const bars = data.map((value, i) => {
        const barHeight = (value / max) * (height - padding * 2 - 16);
        const x = padding + i * barWidth + barWidth * 0.15;
        const y = height - padding - barHeight;
        const w = barWidth * 0.7;

        return `<rect x="${x.toFixed(1)}" y="${y.toFixed(1)}" width="${w.toFixed(1)}" height="${Math.max(barHeight, 0).toFixed(1)}" rx="3" fill="${color}"><title>${escapeHtml(labels[i] ?? '')}: ${value}</title></rect>`;
    }).join('');

    const showEvery = Math.max(Math.ceil(labels.length / 10), 1);
    const labelEls = labels.map((label, i) => {
        if (i % showEvery !== 0) return '';
        const x = padding + i * barWidth + barWidth / 2;

        return `<text x="${x.toFixed(1)}" y="${height - 8}" font-size="9" text-anchor="middle" fill="currentColor" opacity="0.55">${escapeHtml(label)}</text>`;
    }).join('');

    return `<svg viewBox="0 0 ${width} ${height}" class="h-auto w-full" preserveAspectRatio="xMidYMid meet" role="img">${bars}${labelEls}</svg>`;
}

function renderLine(labels, data, color) {
    const width = 600;
    const height = 220;
    const padding = 28;
    const max = Math.max(...data, 1);
    const stepX = (width - padding * 2) / Math.max(data.length - 1, 1);

    const points = data.map((value, i) => [
        padding + i * stepX,
        height - padding - (value / max) * (height - padding * 2 - 10),
    ]);

    const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'}${p[0].toFixed(1)},${p[1].toFixed(1)}`).join(' ');
    const last = points[points.length - 1];
    const first = points[0];
    const areaPath = `${linePath} L${last[0].toFixed(1)},${height - padding} L${first[0].toFixed(1)},${height - padding} Z`;

    const dots = points.map((p, i) => `<circle cx="${p[0].toFixed(1)}" cy="${p[1].toFixed(1)}" r="2.5" fill="${color}"><title>${escapeHtml(labels[i] ?? '')}: ${data[i]}</title></circle>`).join('');

    const showEvery = Math.max(Math.ceil(labels.length / 8), 1);
    const labelEls = labels.map((label, i) => {
        if (i % showEvery !== 0 && i !== labels.length - 1) return '';

        return `<text x="${points[i][0].toFixed(1)}" y="${height - 8}" font-size="9" text-anchor="middle" fill="currentColor" opacity="0.55">${escapeHtml(label)}</text>`;
    }).join('');

    return `<svg viewBox="0 0 ${width} ${height}" class="h-auto w-full" preserveAspectRatio="xMidYMid meet" role="img">
        <path d="${areaPath}" fill="${color}" opacity="0.12" stroke="none"></path>
        <path d="${linePath}" fill="none" stroke="${color}" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
        ${dots}${labelEls}
    </svg>`;
}

function polarToCartesian(cx, cy, r, angleDeg) {
    const rad = (angleDeg * Math.PI) / 180;

    return { x: cx + r * Math.cos(rad), y: cy + r * Math.sin(rad) };
}

function arcSegment(cx, cy, r, startAngle, endAngle, color, thickness) {
    const start = polarToCartesian(cx, cy, r, endAngle);
    const end = polarToCartesian(cx, cy, r, startAngle);
    const largeArc = endAngle - startAngle <= 180 ? 0 : 1;

    return `<path d="M ${start.x.toFixed(2)} ${start.y.toFixed(2)} A ${r} ${r} 0 ${largeArc} 0 ${end.x.toFixed(2)} ${end.y.toFixed(2)}" fill="none" stroke="${color}" stroke-width="${thickness}"></path>`;
}

function renderDonut(labels, data, palette) {
    const total = data.reduce((sum, value) => sum + value, 0) || 1;
    const size = 180;
    const radius = 70;
    const cx = size / 2;
    const cy = size / 2;
    const thickness = 22;

    let angle = -90;
    let segments = '';

    data.forEach((value, i) => {
        const sweep = (value / total) * 360;
        if (sweep > 0) {
            segments += arcSegment(cx, cy, radius, angle, angle + sweep, palette[i % palette.length], thickness);
        }
        angle += sweep;
    });

    const legend = labels.map((label, i) => `
        <div class="flex items-center gap-2 text-xs text-text-muted">
            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background:${palette[i % palette.length]}"></span>
            <span>${escapeHtml(label)} — ${Math.round((data[i] / total) * 100)}%</span>
        </div>
    `).join('');

    return `<div class="flex flex-wrap items-center gap-6">
        <svg viewBox="0 0 ${size} ${size}" width="${size}" height="${size}" class="shrink-0" role="img">${segments}</svg>
        <div class="flex flex-col gap-1.5">${legend}</div>
    </div>`;
}

function renderChart(el) {
    let config;

    try {
        config = JSON.parse(el.dataset.chart);
    } catch (error) {
        return;
    }

    const { type = 'bar', labels = [], series = [], colors = null } = config;

    if (!series.length || series.every((value) => !value)) {
        return;
    }

    const palette = colors && colors.length ? colors : defaultPalette();

    if (type === 'donut') {
        el.innerHTML = renderDonut(labels, series, palette);
    } else if (type === 'line') {
        el.innerHTML = renderLine(labels, series, palette[0]);
    } else {
        el.innerHTML = renderBar(labels, series, palette[0]);
    }
}

export function initCharts() {
    document.querySelectorAll('[data-chart]').forEach(renderChart);
}
