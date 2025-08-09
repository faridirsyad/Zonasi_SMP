// Inisialisasi map
const map = L.map("map2").setView([-7.353620352541296, 109.63416651888299], 11);

L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap contributors",
}).addTo(map);

// Marker lokasi pengguna
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

// Buat pane khusus untuk GeoJSON
const geojsonPane = "geojsonPane";
if (!map.getPane(geojsonPane)) {
  map.createPane(geojsonPane);
  map.getPane(geojsonPane).style.pointerEvents = "none";
}

// Simpan semua data Korda (GeoJSON) untuk deteksi titik
const allKordaLayers = [];
const geojsonFiles = ["Korda1.geojson", "Korda2.geojson", "Korda3.geojson", "Korda4.geojson"];
const kordaColors = {
  Korda1: "#f38181",
  Korda2: "#fce38a",
  Korda3: "#95e1d3",
  Korda4: "#78b6c5ff",
};

geojsonFiles.forEach((file) => {
  fetch("assets/geojson/korda/" + file)
    .then((response) => response.json())
    .then((data) => {
      const namaKorda = file.replace(".geojson", "");
      const warna = kordaColors[namaKorda] || "#cccccc";

      const layer = L.geoJSON(data, {
        pane: geojsonPane,
        style: {
          color: "#011f4b",
          weight: 2,
          fillColor: warna,
          fillOpacity: 0.5,
        },
      }).addTo(map);

      allKordaLayers.push({ nama: namaKorda, geojson: data });
    })
    .catch((error) => {
      console.error("Gagal memuat GeoJSON:", file, error);
    });
});

// Event klik pada peta
map.on("click", function (e) {
  const { lat, lng } = e.latlng;
  const point = [lng, lat];

  // Deteksi korda
  let kordaDitemukan = "Tidak diketahui";
  for (const { nama, geojson } of allKordaLayers) {
    for (const feature of geojson.features) {
      if (turf.booleanPointInPolygon(turf.point(point), feature)) {
        kordaDitemukan = nama;
        break;
      }
    }
    if (kordaDitemukan !== "Tidak diketahui") break;
  }

  if (marker) map.removeLayer(marker);
  marker = L.marker([lat, lng], { icon: userIcon }).addTo(map).bindPopup(`Titik Lokasi Anda berada di <strong>${kordaDitemukan}</strong>`).openPopup();

  const sekolahDenganJarak = sekolahData.map((s) => {
    const jarak = getDistance(lat, lng, s.latitude, s.longitude);
    return { ...s, jarak };
  });

  const sekolahKordaSama = sekolahDenganJarak.filter((s) => s.korda === kordaDitemukan).sort((a, b) => a.jarak - b.jarak);

  const sekolahKordaLain = sekolahDenganJarak.filter((s) => s.korda !== kordaDitemukan).sort((a, b) => a.jarak - b.jarak);

  const infoBox = document.getElementById("info-box");
  let content = `
    <p><strong>Lokasi Anda:</strong><br>Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}</p>
    <p><strong>Korda:</strong> ${kordaDitemukan}</p>
    <hr>
  `;

  if (sekolahKordaSama.length > 0) {
    content += `<p><strong>Sekolah di ${kordaDitemukan}:</strong></p><div class="school-list">`;
    sekolahKordaSama.forEach((s) => {
      content += tampilkanCardSekolah(s);
    });
    content += `</div><hr>`;
  } else {
    content += `<p><em>Tidak ada sekolah di ${kordaDitemukan}</em></p><hr>`;
  }

  if (sekolahKordaLain.length > 0) {
    content += `<p><strong>Sekolah di Korda Lain:</strong></p><div class="school-list">`;
    sekolahKordaLain.forEach((s) => {
      content += tampilkanCardSekolah(s);
    });
    content += `</div>`;
  }

  infoBox.innerHTML = content;
});

function tampilkanCardSekolah(s) {
  return `
    <div class="school-card" style="border: 1px solid #ccc; border-radius: 10px; padding: 10px; margin-bottom: 10px; background: #f9f9f9;">
      <h3 style="margin-bottom: 5px; font-size: 1.1em;">${s.nama_sekolah}</h3>
      <p><strong>Alamat:</strong> ${s.alamat_sekolah}</p>
      <p><strong>Daya Tampung:</strong> ${s.daya_tampung}</p>
      <p><strong>Akreditasi:</strong> ${s.akreditasi}</p>
      <p><strong>Korda:</strong> ${s.korda}</p>
      <p><strong>Jarak:</strong> ${s.jarak.toFixed(2)} km</p>
      <a href="detail_sekolah.php?id=${s.id}" class="detail-button"
         style="display:inline-block; padding:6px 12px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">
         Detail Sekolah
      </a>
    </div>
  `;
}

function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = deg2rad(lat2 - lat1);
  const dLon = deg2rad(lon2 - lon1);
  const a = Math.sin(dLat / 2) ** 2 + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) ** 2;
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function deg2rad(deg) {
  return deg * (Math.PI / 180);
}

// Legenda
const legend = L.control({ position: "bottomleft" });
legend.onAdd = function () {
  const div = L.DomUtil.create("div", "info legend");
  div.innerHTML += `
    <h4>Keterangan</h4>
    <img src="assets/image/user-marker.png" width="25"> Lokasi Anda<br>
    <img src="assets/image/school-marker.png" width="25"> Lokasi Sekolah<br><br>
    <i style="background: #f38181; width: 18px; height: 18px; display: inline-block;"></i> Korda1<br>
    <i style="background: #fce38a; width: 18px; height: 18px; display: inline-block;"></i> Korda2<br>
    <i style="background: #95e1d3; width: 18px; height: 18px; display: inline-block;"></i> Korda3<br>
    <i style="background: #78b6c5ff; width: 18px; height: 18px; display: inline-block;"></i> Korda4
  `;
  return div;
};
legend.addTo(map);
