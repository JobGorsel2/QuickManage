// ===========================
// Basemap Configuration
// ===========================
// WMTS (Web Map Tile Service) source for background map tiles in RD projection
import { RD_EXTENT } from './projections.js';

export const createBasemapSource = () => {
  return new ol.source.WMTS({
    url: "https://service.pdok.nl/brt/achtergrondkaart/wmts/v2_0", // PDOK service endpoint
    layer: "standaard", // Standard map variant (options: grijs, pastel, water)
    matrixSet: "EPSG:28992", // Use RD projection for tiles
    format: "image/png", // PNG format for tile images
    style: "default", // Default map styling
    projection: "EPSG:28992", // RD projection
    tileGrid: new ol.tilegrid.WMTS({
      origin: [-285401.92, 903401.92], // Top-left of RD extent
      // Resolution levels from largest (3440.64m) to smallest (0.21m)
      resolutions: [3440.64, 1720.32, 860.16, 430.08, 215.04, 107.52, 53.76, 26.88, 13.44, 6.72, 3.36, 1.68, 0.84, 0.42, 0.21],
      matrixIds: ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14"] // WMTS zoom level IDs
    }),
    wrapX: false // Don't wrap tiles horizontally (RD projection)
  });
};
