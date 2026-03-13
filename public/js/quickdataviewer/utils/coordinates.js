// ===========================
// Coordinate Utilities
// ===========================

// Helper: Extract first [x, y] coordinate pair from GeoJSON features
// Used for coordinate system detection based on coordinate ranges
export const findFirstCoordPair = (fc) => {
  // Recursively search nested coordinate arrays for a [x, y] pair
  const findPair = (coords) => {
    if (!Array.isArray(coords)) return null;
    // Check if this is a [x, y] pair (two numbers)
    if (coords.length >= 2 && typeof coords[0] === "number" && typeof coords[1] === "number") return coords;
    // Recursively search nested arrays
    for (const c of coords) { const p = findPair(c); if (p) return p; }
    return null;
  };
  // Get first feature and extract coordinates from geometry
  return findPair(fc.features?.[0]?.geometry?.coordinates);
};
