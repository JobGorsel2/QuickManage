// ===========================
// Shapefile Loading Service
// ===========================
import { readZipAsRawGeoJSON } from './fileProcessor.js';
import { detectDataProjection } from './projectionDetector.js';
import { displayLayerInfo } from './layerManager.js';
import { vectorSource, vectorLayer, zoomToData } from '../core/map.js';

// Load shapefile from ZIP and render on map with all features
export const loadShapefileZip = async (file, closePopupFn, setLoadingFn) => {
  if (!vectorSource || !vectorLayer) {
    throw new Error("Map not initialized. Please refresh the page.");
  }

  setLoadingFn(true, "Shapefile inlezen…");
  const { layers, prj } = await readZipAsRawGeoJSON(file);
  if (!layers?.length) throw new Error("Geen features gevonden in shapefile.");

  const allFeatures = [];
  let dataProjection = null;

  for (const layer of layers) {
    if (!layer.geojson?.features?.length) continue;
    if (!dataProjection) dataProjection = detectDataProjection(layer.geojson, prj);

    // Tag each feature with its layer name and set visibility, then add to collection
    new ol.format.GeoJSON().readFeatures(layer.geojson, { dataProjection, featureProjection: "EPSG:28992" }).forEach(f => {
      f.set('_layerName', layer.name);
      f.set('_visible', true);
      allFeatures.push(f);
    });
  }

  if (!allFeatures.length) throw new Error("Geen features gevonden in shapefile.");
  vectorSource.clear(true);
  vectorSource.addFeatures(allFeatures);
  displayLayerInfo(layers, vectorSource, vectorLayer);
  closePopupFn();
  zoomToData();
};
