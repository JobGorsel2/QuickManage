// ===========================
// Layer Management & Display
// ===========================
import { layerVisibility } from '../core/state.js';

// Display layer list with checkboxes for visibility toggle
export const displayLayerInfo = (layers, vectorSource, vectorLayer) => {
  const div = document.getElementById("layerNames");
  if (!layers?.length) { div.style.display = "none"; return; }
  
  // Build HTML for layer list with checkboxes
  div.innerHTML = "<strong>Lagen geladen:</strong><br/>" + layers.map(l => {
    const id = `layer-${l.name}`;
    return `<div style="padding: 6px 0; font-size: 12px; display: flex; align-items: flex-start; gap: 6px; min-width: 0;"><input type="checkbox" id="${id}" data-layer="${l.name}" ${layerVisibility[l.name] !== false ? 'checked' : ''} style="cursor: pointer; flex-shrink: 0; margin-top: 2px;" /><label for="${id}" class="layer-label">📍 <strong>${l.name}</strong> (${l.featureCount})</label></div>`;
  }).join("");
  div.style.display = "block";

  // Attach change event listeners to all layer visibility checkboxes
  div.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.addEventListener('change', (e) => {
    layerVisibility[e.target.dataset.layer] = e.target.checked;
    vectorSource.getFeatures().forEach(f => {
      if (f.get('_layerName') === e.target.dataset.layer) f.set('_visible', e.target.checked);
    });
    vectorLayer.changed();
  }));
};
