const map = L.map("map2").setView([-7.353620352541296, 109.63416651888299], 11); // Titik tengah Banjarnegara

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap contributors",
}).addTo(map);

let marker = null;

const userIcon = L.icon({
  iconUrl: "assets/image/user-marker.png",
  iconSize: [30, 30],
  iconAnchor: [15, 30],
  popupAnchor: [0, -30],
});

// Tampilkan marker sekolah
sekolahData.forEach((s) => {
  const markerSekolah = L.marker([s.latitude, s.longitude]).bindPopup(`<strong>${s.nama_sekolah}</strong><br>${s.alamat_sekolah}`);
  markerSekolah.addTo(map);
});

// Tampilkan batas wilayah dari GeoJSON
fetch("assets/geojson/Banjarnegara.geojson")
  .then((response) => response.json())
  .then((data) => {
    L.geoJSON(data, {
      style: {
        color: "blue",
        weight: 2,
        fillOpacity: 0.1,
      },
    }).addTo(map);
  })
  .catch((error) => {
    console.error("Gagal memuat GeoJSON:", error);
  });

map.on("click", function (e) {
  const { lat, lng } = e.latlng;

  if (marker) map.removeLayer(marker);
  marker = L.marker([lat, lng], {
    icon: userIcon,
  })
    .addTo(map)
    .bindPopup("Titik Lokasi Anda")
    .openPopup();

  const infoBox = document.getElementById("info-box");

  // Hitung jarak & urutkan sekolah
  let sekolahDenganJarak = sekolahData.map((s) => {
    const jarak = getDistance(lat, lng, s.latitude, s.longitude);
    return { ...s, jarak };
  });

  sekolahDenganJarak.sort((a, b) => a.jarak - b.jarak); // Urutkan berdasarkan jarak

  // Tampilkan info ke info-box
  let content = `
      <p><strong>Lokasi Anda:</strong><br>Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}</p>
      <hr>
      <p><strong>Sekolah Terdekat:</strong></p>
      <div class="school-list">
    `;

  sekolahDenganJarak.forEach((s) => {
    content += `
        <div class="school-card" style="border: 1px solid #ccc; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #f9f9f9;">
          <h3 style="margin-bottom: 5px; font-size: 1.1em;">${s.nama_sekolah}</h3>
          <p><strong>Alamat:</strong> ${s.alamat_sekolah}</p>
          <p><strong>Daya Tampung:</strong> ${s.daya_tampung}</p>
          <p><strong>Akreditasi:</strong> ${s.akreditasi}</p>
          <p><strong>Jarak:</strong> ${s.jarak.toFixed(2)} km</p>
          <a href="detail_sekolah.php?id=${s.id}" class="detail-button"
             style="display:inline-block; padding:6px 12px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">
             Detail Sekolah
          </a>
        </div>
      `;
  });

  content += `</div>`;
  infoBox.innerHTML = content;
});

// Fungsi hitung jarak Haversine
function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = deg2rad(lat2 - lat1);
  const dLon = deg2rad(lon2 - lon1);
  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function deg2rad(deg) {
  return deg * (Math.PI / 180);
}

// Legenda
const legend = L.control({
  position: "bottomleft",
});
legend.onAdd = function (map) {
  const div = L.DomUtil.create("div", "info legend");
  div.innerHTML += `
        <h4>Keterangan</h4>
        <img src="assets/image/user-marker.png" width="25"> Lokasi Anda<br>
        <img src="assets/image/school-marker.png" width="25"> Lokasi Sekolah
    `;
  return div;
};
legend.addTo(map);
