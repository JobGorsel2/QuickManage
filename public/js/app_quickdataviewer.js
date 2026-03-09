// ===========================
// 0) Register projections
// ===========================
proj4.defs(
  "EPSG:28992",
  "+proj=sterea +lat_0=52.1561605555556 +lon_0=5.38763888888889 +k=0.9999079 " +
    "+x_0=155000 +y_0=463000 +ellps=bessel " +
    "+towgs84=565.4171,50.3319,465.5524,1.9342,-1.6677,9.1019,4.0725 " +
    "+units=m +no_defs"
);
ol.proj.proj4.register(proj4);

// Recommended extent for RD (NL bounds)
const RD_EXTENT = [-285401.92, 22598.08, 595401.92, 903401.92];
const rdProj = ol.proj.get("EPSG:28992");
rdProj.setExtent(RD_EXTENT);

// ===========================
// 1) DOM refs
// ===========================
const dropZone = document.getElementById("drop-zone");
const fileInput = document.getElementById("file-input");
const errorBox = document.getElementById("error-box");
const errorMsg = document.getElementById("error-msg");
const loadingOverlay = document.getElementById("loading-overlay");
const loadingMsg = document.getElementById("loading-msg");

const screen1 = document.querySelector(".fileSelectionViewer");
const screen2 = document.querySelector(".quickDataViewer");

// ===========================
// 2) UI helpers
// ===========================
function showError(msg) {
  errorMsg.textContent = msg;
  errorBox.classList.remove("hidden");
}
function clearError() {
  errorMsg.textContent = "";
  errorBox.classList.add("hidden");
}
function setLoading(on, msg = "Bestand verwerken…") {
  loadingMsg.textContent = msg;
  loadingOverlay.classList.toggle("hidden", !on);
}
function switchToMapScreen() {
  screen1.style.display = "none";
  screen2.style.display = "block";
}

// ===========================
// 3) PDOK WMTS basemap (EPSG:28992)
// ===========================
// Note: for PDOK WMTS, style is usually "default". Your previous style string may break.
// If you KNOW your style is correct, you can keep it; this is the safe default:
const pdokWmtsSource = new ol.source.WMTS({
  url: "https://service.pdok.nl/brt/achtergrondkaart/wmts/v2_0",
  layer: "standaard", // options often include: standaard, grijs, pastel, water (depends on service)
  matrixSet: "EPSG:28992",
  format: "image/png",
  style: "default",
  projection: "EPSG:28992",
  tileGrid: new ol.tilegrid.WMTS({
    origin: [-285401.92, 903401.92], // top-left of RD extent
    resolutions: [
      3440.64, 1720.32, 860.16, 430.08, 215.04,
      107.52, 53.76, 26.88, 13.44, 6.72,
      3.36, 1.68, 0.84, 0.42, 0.21
    ],
    matrixIds: ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14"]
  }),
  wrapX: false
});

const baseLayer = new ol.layer.Tile({ source: pdokWmtsSource });

// ===========================
// 4) Vector styling (orange fill, with layer visibility)
// ===========================
function getFeatureStyle(feature, resolution) {
  const isVisible = feature.get('_visible') !== false; // Default to visible
  
  if (!isVisible) {
    return null; // Don't render invisible features
  }
  
  return new ol.style.Style({
    fill: new ol.style.Fill({
      color: "rgba(231, 151, 3, 0.58)" // orange fill
    }),
    stroke: new ol.style.Stroke({
      color: "rgb(173, 81, 1)",
      width: 2
    }),
    image: new ol.style.Circle({
      radius: 6,
      fill: new ol.style.Fill({ color: "rgba(255, 165, 0, 0.75)" }),
      stroke: new ol.style.Stroke({ color: "rgba(255, 120, 0, 1)", width: 2 })
    })
  });
}

// ===========================
// 5) Map init (in RD / EPSG:28992)
// ===========================
const vectorSource = new ol.source.Vector();

const vectorLayer = new ol.layer.Vector({
  source: vectorSource,
  style: getFeatureStyle
});

const map = new ol.Map({
  target: "map",
  layers: [baseLayer, vectorLayer],
  
  view: new ol.View({
    projection: "EPSG:28992",
    center: [170000, 463000],
    zoom: 14,
    extent: RD_EXTENT
  })

  
});

 // ===========================
// Control pane (Upload / Basemap / Zoom / Clear)
// ===========================

// Keep last known extent of uploaded data for "Zoom to layer"
let lastDataExtent = null;

function updateLastExtent() {
  const extent = vectorSource.getExtent();
  if (extent && isFinite(extent[0]) && isFinite(extent[2])) {
    lastDataExtent = extent.slice();
  } else {
    lastDataExtent = null;
  }
}

// Wrap your zoomToData to also save last extent
const _zoomToData = zoomToData;
zoomToData = function () {
  _zoomToData();
  updateLastExtent();
};

// Also update extent after any new features are added
vectorSource.on("addfeature", updateLastExtent);
vectorSource.on("clear", () => (lastDataExtent = null));

function setBasemapVariant(variant) {
  // PDOK BRT WMTS layer names commonly include: standaard, grijs, pastel
  // If your service uses different names, adjust here.
  pdokWmtsSource.setLayer(variant);
  // Refresh tiles
  pdokWmtsSource.refresh();
}

function createControlPane() {
  const container = document.getElementById("controlpanel_dataviewer");
   
  
  // const badge = container.querySelector("#qdv-basemap-badge");

  // Initial state
  let basemap = "standaard";

  container.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;

    const action = btn.getAttribute("data-action");

    if (action === "upload") {
      // open your existing hidden file input
      fileInput.click();
    }
    if (action === 'back') {
      // Switch back to upload screen
      screen1.style.display = 'block';
      screen2.style.display = 'none';
      document.getElementById('layerNames').style.display = 'none';
    }
    if (action === "basemap-toggle") {
      basemap = basemap === "standaard" ? "grijs" : "standaard";
      setBasemapVariant(basemap);
      badge.textContent = basemap;
    }

    if (action === "zoom") {
      // Prefer last extent if available
      if (lastDataExtent) {
        map.getView().fit(lastDataExtent, {
          padding: [40, 40, 40, 40],
          duration: 250,
          maxZoom: 14
        });
      } else {
        showError("Geen layer geladen om naar te zoomen.");
      }
    }

    if (action === "clear") {
      vectorSource.clear(true);
      closePopup?.(); // if you use popup from earlier
      lastDataExtent = null;
      // Reset layer visibility state
      Object.keys(layerVisibility).forEach(key => delete layerVisibility[key]);
      document.getElementById("layerNames").style.display = "none";
    }

    if (action === "export") {
      exportToExcel();
    }
  });

  return new ol.control.Control({ element: container });
}

map.addControl(createControlPane());

function clearMap() {
  vectorSource.clear(true);
}

// When loading data this is the zoom function:
function zoomToData() {
  const extent = vectorSource.getExtent();
  if (extent && isFinite(extent[0]) && isFinite(extent[1]) && isFinite(extent[2]) && isFinite(extent[3])) {
    map.getView().fit(extent, {
      padding: [40, 40, 40, 40],
      duration: 250,
      maxZoom: 10
    });
  }
}

// ===========================
// 6) Popup overlay (attributes on click)
// ===========================
const popupEl = document.createElement("div");
popupEl.style.position = "absolute";
popupEl.style.minWidth = "240px";
popupEl.style.maxWidth = "420px";
popupEl.style.background = "white";
popupEl.style.border = "1px solid rgba(0,0,0,0.25)";
popupEl.style.borderRadius = "10px";
popupEl.style.boxShadow = "0 6px 18px rgba(0,0,0,0.18)";
popupEl.style.padding = "10px 12px";
popupEl.style.fontSize = "13px";
popupEl.style.lineHeight = "1.35";
popupEl.style.pointerEvents = "auto";

const popupCloser = document.createElement("div");
popupCloser.textContent = "×";
popupCloser.style.position = "absolute";
popupCloser.style.top = "6px";
popupCloser.style.right = "10px";
popupCloser.style.cursor = "pointer";
popupCloser.style.fontSize = "18px";
popupCloser.style.opacity = "0.6";
popupCloser.onmouseenter = () => (popupCloser.style.opacity = "1");
popupCloser.onmouseleave = () => (popupCloser.style.opacity = "0.6");
popupEl.appendChild(popupCloser);

const popupTitle = document.createElement("div");
popupTitle.style.fontWeight = "600";
popupTitle.style.marginRight = "18px";
popupTitle.textContent = "Attributes";
popupEl.appendChild(popupTitle);

const popupContent = document.createElement("div");
popupContent.style.marginTop = "8px";
popupContent.style.maxHeight = "240px";
popupContent.style.overflow = "auto";
popupEl.appendChild(popupContent);

document.body.appendChild(popupEl);

const popupOverlay = new ol.Overlay({
  element: popupEl,
  autoPan: true,
  autoPanAnimation: { duration: 200 },
  offset: [0, -10]
});

map.addOverlay(popupOverlay);

function closePopup() {
  popupOverlay.setPosition(undefined);
}
popupCloser.addEventListener("click", closePopup);

// Cursor feedback + close popup on background click
map.on("pointermove", (evt) => {
  const hit = map.hasFeatureAtPixel(evt.pixel);
  map.getTargetElement().style.cursor = hit ? "pointer" : "";
});

map.on("singleclick", (evt) => {
  const feature = map.forEachFeatureAtPixel(evt.pixel, (f) => f);
  if (!feature) {
    closePopup();
    return;
  }

  // Build a simple attributes table
  const props = feature.getProperties();
  delete props.geometry;

  const keys = Object.keys(props);

  popupTitle.textContent = keys.length ? "Attributes" : "No attributes";
  popupContent.innerHTML = "";

  if (!keys.length) {
    popupContent.textContent = "No attributes found on this feature.";
  } else {
    const table = document.createElement("table");
    table.style.width = "100%";
    table.style.borderCollapse = "collapse";

    for (const k of keys) {
      const tr = document.createElement("tr");

      const tdK = document.createElement("td");
      tdK.textContent = k;
      tdK.style.fontWeight = "600";
      tdK.style.padding = "4px 6px";
      tdK.style.borderBottom = "1px solid rgba(0,0,0,0.08)";
      tdK.style.verticalAlign = "top";
      tdK.style.width = "45%";

      const tdV = document.createElement("td");
      const v = props[k];
      tdV.textContent = (v === null || v === undefined) ? "" : String(v);
      tdV.style.padding = "4px 6px";
      tdV.style.borderBottom = "1px solid rgba(0,0,0,0.08)";
      tdV.style.verticalAlign = "top";

      tr.appendChild(tdK);
      tr.appendChild(tdV);
      table.appendChild(tr);
    }

    popupContent.appendChild(table);
  }

  popupOverlay.setPosition(evt.coordinate);
});

// ===========================
// 7) Shapefile ZIP -> RAW GeoJSON (supports multiple layers)
// ===========================
async function readZipAsRawGeoJSON(file) {
  if (!window.JSZip) throw new Error("JSZip ontbreekt (jszip.min.js).");
  if (!window.shp) throw new Error("shpjs ontbreekt (shp.min.js).");

  const zipBuf = await file.arrayBuffer();
  const zip = await JSZip.loadAsync(zipBuf);

  // Find all .shp files (multiple layers)
  const shpNames = Object.keys(zip.files).filter((n) => n.toLowerCase().endsWith(".shp"));
  const prjName = Object.keys(zip.files).find((n) => n.toLowerCase().endsWith(".prj"));

  if (shpNames.length === 0) throw new Error("Zip mist .shp of .dbf.");

  const prjTxt = prjName ? await zip.files[prjName].async("string") : "";
  const layers = [];

  // Process each shapefile layer
  for (const shpName of shpNames) {
    const baseName = shpName.split(/[\\/]/).pop().replace(/\.shp$/i, "");
    const dbfName = Object.keys(zip.files).find(
      (n) => n.toLowerCase().endsWith(".dbf") && 
             n.split(/[\\/]/).pop().replace(/\.dbf$/i, "").toLowerCase() === baseName.toLowerCase()
    );

    if (!dbfName) continue; // Skip if no matching DBF

    const shpBuf = await zip.files[shpName].async("arraybuffer");
    const dbfBuf = await zip.files[dbfName].async("arraybuffer");

    const geometries = window.shp.parseShp(shpBuf);
    const properties = window.shp.parseDbf(dbfBuf);
    const geojson = window.shp.combine([geometries, properties]);

    layers.push({
      name: baseName,
      geojson: geojson,
      featureCount: geojson?.features?.length || 0
    });
  }

  return { layers, prj: prjTxt };
}

function findFirstCoordPair(fc) {
  const f = fc.features?.[0];
  if (!f?.geometry?.coordinates) return null;

  function findPair(coords) {
    if (!Array.isArray(coords)) return null;
    if (coords.length >= 2 && typeof coords[0] === "number" && typeof coords[1] === "number") return coords;
    for (const c of coords) {
      const p = findPair(c);
      if (p) return p;
    }
    return null;
  }

  return findPair(f.geometry.coordinates);
}

function detectDataProjection(fc, prjText) {
  const first = findFirstCoordPair(fc);
  console.log("First coordinate:", first);

  const t = (prjText || "").toLowerCase();
  const prjSaysRD =
    t.includes("rd_new") ||
    t.includes("amersfoort") ||
    t.includes("double_stereographic") ||
    t.includes("bessel_1841") ||
    t.includes("sterea");

  if (first) {
    const [x, y] = first;

    const looksLikeRD = x > 0 && x < 300000 && y > 250000 && y < 700000;
    const looksLikeLonLat = x >= -180 && x <= 180 && y >= -90 && y <= 90;

    if (looksLikeRD) return "EPSG:28992";
    if (looksLikeLonLat) return "EPSG:4326";
  }

  if (prjSaysRD) return "EPSG:28992";
  return "EPSG:4326";
}

// ===========================
// 8) Layer visibility state
// ===========================
const layerVisibility = {}; // Track which layers are visible

// ===========================
// 8b) Display layer information with toggles
// ===========================
function displayLayerInfo(layers) {
  const layerNamesDiv = document.getElementById("layerNames");
  
  if (!layers || layers.length === 0) {
    layerNamesDiv.style.display = "none";
    return;
  }

  let html = "<strong>Lagen geladen:</strong><br/>";
  layers.forEach((layer) => {
    const isVisible = layerVisibility[layer.name] !== false; // Default to visible
    const checkboxId = `layer-${layer.name}`;
    html += `
      <div style="padding: 6px 0; font-size: 12px; display: flex; align-items: center; gap: 6px;">
        <input type="checkbox" id="${checkboxId}" data-layer="${layer.name}" ${isVisible ? 'checked' : ''} style="cursor: pointer;" />
        <label for="${checkboxId}" style="cursor: pointer; margin: 0; flex: 1; text-align: left;">
          📍 <strong>${layer.name}</strong> (${layer.featureCount})
        </label>
      </div>
    `;
  });

  layerNamesDiv.innerHTML = html;
  layerNamesDiv.style.display = "block";

  // Attach toggle event listeners
  layerNamesDiv.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
    checkbox.addEventListener('change', (e) => {
      const layerName = e.target.getAttribute('data-layer');
      toggleLayer(layerName, e.target.checked);
    });
  });
}

// ===========================
// 8c) Toggle layer visibility
// ===========================
function toggleLayer(layerName, isVisible) {
  layerVisibility[layerName] = isVisible;
  
  // Update features visibility
  vectorSource.getFeatures().forEach((feature) => {
    const featureLayer = feature.get('_layerName');
    if (featureLayer === layerName) {
      feature.set('_visible', isVisible);
    }
  });

  // Refresh the vector layer to apply the style changes
  vectorLayer.changed();
}

// ===========================
// 9) Load Shapefile (.zip) and render into RD map
// ===========================
async function loadShapefileZip(file) {
  setLoading(true, "Shapefile inlezen…");

  const { layers, prj } = await readZipAsRawGeoJSON(file);
  if (!layers || layers.length === 0) throw new Error("Geen features gevonden in shapefile.");

  // Collect all features from all layers
  const allFeatures = [];
  let dataProjection = null;

  for (const layer of layers) {
    if (!layer.geojson?.features?.length) continue;
    
    // Detect projection from first layer with features
    if (!dataProjection) {
      dataProjection = detectDataProjection(layer.geojson, prj);
      console.log("Using dataProjection:", dataProjection);
    }

    const features = new ol.format.GeoJSON().readFeatures(layer.geojson, {
      dataProjection,
      featureProjection: "EPSG:28992"
    });

    // Tag features with their layer name
    features.forEach((feature) => {
      feature.set('_layerName', layer.name);
      feature.set('_visible', true);
    });

    allFeatures.push(...features);
  }

  if (allFeatures.length === 0) throw new Error("Geen features gevonden in shapefile.");

  vectorSource.clear(true);
  vectorSource.addFeatures(allFeatures);

  // Display layer information
  displayLayerInfo(layers);

  // Close old popup if any
  closePopup();

  zoomToData();
}

// ===========================
// 10) Main handler
// ===========================
async function handleFile(file) {
  clearError();

  const name = file.name.toLowerCase();
  if (!name.endsWith(".zip")) {
    showError("Upload een .zip shapefile.");
    return;
  }

  try {
    setLoading(true, "Bestand verwerken…");
    clearMap();

    await loadShapefileZip(file);

    setLoading(false);
    switchToMapScreen();
  } catch (err) {
    console.error(err);
    setLoading(false);
    showError(err?.message || String(err));
  }
}

// ===========================
// 11) Export to Excel
// ===========================
function sanitizeSheetName(name) {
  // Excel sheet names cannot exceed 31 characters and cannot contain: [ ] : * ? / \
  let sanitized = name.replace(/[\[\]:*?/\\]/g, '_');
  // Truncate to 31 characters
  return sanitized.substring(0, 31);
}

function exportToExcel() {
  // Check if XLSX library is loaded
  if (typeof XLSX === 'undefined') {
    showError("Excel library is nog niet geladen. Probeer het later.");
    return;
  }

  const features = vectorSource.getFeatures();
  
  if (features.length === 0) {
    showError("Geen data om te exporteren.");
    return;
  }

  // Collect data from visible features grouped by layer
  const layerData = {};

  features.forEach((feature) => {
    const layerName = feature.get('_layerName') || 'Default';
    const isVisible = feature.get('_visible') !== false;

    // Only export visible features
    if (!isVisible) return;

    if (!layerData[layerName]) {
      layerData[layerName] = [];
    }

    const props = feature.getProperties();
    delete props.geometry;
    delete props._layerName;
    delete props._visible;

    layerData[layerName].push(props);
  });

  try {
    // Create workbook with sheets for each layer
    const workbook = XLSX.utils.book_new();
    const usedSheetNames = new Set();

    Object.keys(layerData).forEach((layerName) => {
      const data = layerData[layerName];
      if (data.length > 0) {
        // Sanitize and truncate sheet name
        let sheetName = sanitizeSheetName(layerName);
        
        // Handle duplicate sheet names by appending a number
        let finalName = sheetName;
        let counter = 1;
        while (usedSheetNames.has(finalName)) {
          finalName = sanitizeSheetName(sheetName.slice(0, 28) + '_' + counter);
          counter++;
        }
        usedSheetNames.add(finalName);

        const worksheet = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(workbook, worksheet, finalName);
      }
    });

    // Generate filename with timestamp
    const timestamp = new Date().toISOString().slice(0, 10);
    const filename = `quickdataviewer_export_${timestamp}.xlsx`;

    // Trigger download
    XLSX.writeFile(workbook, filename);
  } catch (err) {
    console.error('Export error:', err);
    showError("Er is een fout opgetreden bij het exporteren naar Excel.");
  }
}

// ===========================
// 12) Drop / input events
// ===========================
dropZone.addEventListener("click", () => fileInput.click());
dropZone.addEventListener("keydown", (e) => {
  if (e.key === "Enter" || e.key === " ") fileInput.click();
});
dropZone.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropZone.classList.add("drag-over");
});
dropZone.addEventListener("dragleave", () => dropZone.classList.remove("drag-over"));
dropZone.addEventListener("drop", (e) => {
  e.preventDefault();
  dropZone.classList.remove("drag-over");
  const file = e.dataTransfer.files[0];
  if (file) handleFile(file);
});
fileInput.addEventListener("change", () => {
  if (fileInput.files[0]) handleFile(fileInput.files[0]);
  fileInput.value = "";
});