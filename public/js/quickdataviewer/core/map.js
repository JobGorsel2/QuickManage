// ===========================
// Map Initialization & Core Setup
// ===========================
import { RD_EXTENT } from '../config/projections.js';
import { createBasemapSource } from '../config/basemap.js';
import { getFeatureStyle } from './styles.js';
import { updateLastExtent } from './state.js';

// Vector layer setup for displaying shapefile features
export let vectorSource = null;
export let vectorLayer = null;
export let map = null;

// Initialize map - MUST be called after projections are registered
export const initializeMap = () => {
  console.log("🗺️ Initializing map...");
  
  // Create vector source and layer
  vectorSource = new ol.source.Vector();
  vectorLayer = new ol.layer.Vector({ source: vectorSource, style: getFeatureStyle });

  // Create the map
  map = new ol.Map({
    target: "map",
    layers: [new ol.layer.Tile({ source: createBasemapSource() }), vectorLayer],
    view: new ol.View({ 
      projection: "EPSG:28992", 
      center: [170000, 463000], 
      zoom: 14, 
      extent: RD_EXTENT 
    })
  });

  // Update stored extent when features are added to the map
  const updateLastExtentHandler = () => { 
    const e = vectorSource.getExtent(); 
    updateLastExtent((e && isFinite(e[0]) && isFinite(e[2])) ? e.slice() : null); 
  };

  vectorSource.on("addfeature", updateLastExtentHandler);
  vectorSource.on("clear", () => updateLastExtent(null));

  console.log("✓ Map initialized successfully");
  return map;
};

// Zoom to loaded data extent with padding and max zoom constraint
export const zoomToData = () => { 
  if (!map || !vectorSource) {
    console.error("Map or vectorSource not initialized");
    return;
  }
  const e = vectorSource.getExtent(); 
  if (e && isFinite(e[0]) && isFinite(e[1]) && isFinite(e[2]) && isFinite(e[3])) { 
    map.getView().fit(e, { padding: [40, 40, 40, 40], duration: 250, maxZoom: 10 }); 
  } 
};
