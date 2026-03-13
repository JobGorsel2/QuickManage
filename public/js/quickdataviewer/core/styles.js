// ===========================
// Vector Feature Styling
// ===========================
// Returns blue style for selected features, orange for visible, null for hidden ones
export const getFeatureStyle = (feature) => {
  if (feature.get('_selected') === true) {
    // Blue style with higher opacity for selected features
    return new ol.style.Style({
      fill: new ol.style.Fill({ color: "rgba(0, 102, 204, 0.8)" }), // Bright blue fill
      stroke: new ol.style.Stroke({ color: "rgb(0, 51, 153)", width: 3 }), // Dark blue border, thicker
      image: new ol.style.Circle({ radius: 8, fill: new ol.style.Fill({ color: "rgba(0, 102, 204, 0.9)" }), stroke: new ol.style.Stroke({ color: "rgb(0, 51, 153)", width: 3 }) })
    });
  }
  // Orange style for normal visible features
  return feature.get('_visible') !== false ? new ol.style.Style({
    fill: new ol.style.Fill({ color: "rgba(231, 151, 3, 0.58)" }), // Orange fill for polygons
    stroke: new ol.style.Stroke({ color: "rgb(173, 81, 1)", width: 2 }), // Dark orange border
    image: new ol.style.Circle({ radius: 6, fill: new ol.style.Fill({ color: "rgba(255, 165, 0, 0.75)" }), stroke: new ol.style.Stroke({ color: "rgba(255, 120, 0, 1)", width: 2 }) })
  }) : null; // Return null to hide feature on map
};
