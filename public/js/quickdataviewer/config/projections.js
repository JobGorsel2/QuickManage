// ===========================
// Projection Configuration
// ===========================
// Register EPSG:28992 (RD - Dutch coordinate system) projection using Proj4.js
export const initializeProjections = () => {
  proj4.defs("EPSG:28992", "+proj=sterea +lat_0=52.1561605555556 +lon_0=5.38763888888889 +k=0.9999079 +x_0=155000 +y_0=463000 +ellps=bessel +towgs84=565.4171,50.3319,465.5524,1.9342,-1.6677,9.1019,4.0725 +units=m +no_defs");
  ol.proj.proj4.register(proj4);
  ol.proj.get("EPSG:28992").setExtent(RD_EXTENT);
};

// Set map extent boundaries (Netherlands RD bounds in meters)
export const RD_EXTENT = [-285401.92, 22598.08, 595401.92, 903401.92];
