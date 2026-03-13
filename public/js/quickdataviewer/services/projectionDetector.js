// ===========================
// Projection Detection
// ===========================
import { findFirstCoordPair } from '../utils/coordinates.js';

// Detects coordinate system by checking PRJ file keywords and coordinate ranges
export const detectDataProjection = (fc, prjText) => {
  const first = findFirstCoordPair(fc); // Get first coordinate from data
  // Check if PRJ file mentions Dutch coordinate system keywords
  const prjSaysRD = (prjText || "").toLowerCase().match(/rd_new|amersfoort|double_stereographic|bessel_1841|sterea/);
  
  if (first) {
    const [x, y] = first;
    // RD: x: 0-300000, y: 250000-700000 (Dutch bounds)
    if ((x > 0 && x < 300000 && y > 250000 && y < 700000) || prjSaysRD) return "EPSG:28992";
    // WGS84: x: -180 to 180, y: -90 to 90 (lat/lon)
    if (x >= -180 && x <= 180 && y >= -90 && y <= 90) return "EPSG:4326";
  }
  return prjSaysRD ? "EPSG:28992" : "EPSG:4326";
};
