// ===========================
// File Processing - Extract and parse shapefile ZIP
// ===========================

// Extract all shapefiles from ZIP archive and return as GeoJSON with layer info
export const readZipAsRawGeoJSON = async (file) => {
  // Convert file to array buffer and load as ZIP archive
  const zipBuf = await file.arrayBuffer();
  const zip = await JSZip.loadAsync(zipBuf);
  
  // Find all .shp files in ZIP (can be multiple layers)
  const shpNames = Object.keys(zip.files).filter(n => n.toLowerCase().endsWith(".shp"));
  if (shpNames.length === 0) throw new Error("Zip mist .shp of .dbf.");
  
  // Extract projection file if available (helps detect coordinate system)
  const prjFile = Object.keys(zip.files).find(n => n.toLowerCase().endsWith(".prj"));
  const prjTxt = prjFile ? await zip.files[prjFile].async("string") : "";
  const layers = [];

  for (const shpName of shpNames) {
    const baseName = shpName.split(/[\\/]/).pop().replace(/\.shp$/i, "");
    const dbfName = Object.keys(zip.files).find(n => n.toLowerCase().endsWith(".dbf") && n.split(/[\\/]/).pop().replace(/\.dbf$/i, "").toLowerCase() === baseName.toLowerCase());
    if (!dbfName) continue;

    const shpBuf = await zip.files[shpName].async("arraybuffer");
    const dbfBuf = await zip.files[dbfName].async("arraybuffer");
    const geojson = window.shp.combine([window.shp.parseShp(shpBuf), window.shp.parseDbf(dbfBuf)]);

    layers.push({ name: baseName, geojson, featureCount: geojson?.features?.length || 0 });
  }

  return { layers, prj: prjTxt };
};
