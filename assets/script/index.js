const map = L.map("map").setView([-7.3536, 109.6341], 11);

// Basemap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap",
}).addTo(map);

// Control dropdown di peta
const control = L.control({ position: "topright" });
control.onAdd = function (map) {
  const div = L.DomUtil.create("div", "custom-control");
  let html = '<select id="filter-kecamatan"><option value="">-- Pilih Kecamatan --</option>';
  kecamatanData.forEach((k) => {
    html += `<option value="${k.geojson}" data-nama="${k.nama_kecamatan}">${k.nama_kecamatan}</option>`;
  });
  html += "</select>";
  div.innerHTML = html;
  return div;
};
control.addTo(map);

// Simpan marker dan layer geojson
const allMarkers = [];
const geojsonLayers = [];

// Fungsi ambil kecamatan dari alamat
function extractKecamatan(alamat) {
  if (!alamat) return "";
  let match = alamat.match(/Kecamatan\s+([a-zA-Z\s]+)/i);
  if (match) return match[1].trim();
  const parts = alamat.split(",");
  return parts[parts.length - 1].trim();
}

// Fungsi menentukan warna berdasarkan jumlah sekolah
function getColor(jumlah) {
  if (jumlah > 5) return "#005b96";
  if (jumlah >= 3) return "#6497b1";
  return "#b3cde0";
}

// Tampilkan semua geojson kecamatan dengan warna sesuai jumlah sekolah
kecamatanData.forEach((kec) => {
  fetch("assets/geojson/kecamatan/" + kec.geojson)
    .then((res) => res.json())
    .then((data) => {
      const nama = kec.nama_kecamatan;
      const jumlah = jumlahSekolah[nama] || 0;
      const color = getColor(jumlah);

      const layer = L.geoJSON(data, {
        style: {
          color: "	#011f4b",
          weight: 2,
          fillColor: color,
          fillOpacity: 0.7,
        },
        onEachFeature: function (feature, layer) {
          layer.on("click", function () {
            const jumlah = jumlahSekolah[nama] || 0;
            L.popup().setLatLng(layer.getBounds().getCenter()).setContent(`<strong>${nama}</strong><br>Jumlah Sekolah: ${jumlah}`).openOn(map);
          });
        },
      }).addTo(map);
      layer.kecamatan = nama;
      geojsonLayers.push(layer);
    });
});
// Tampilkan semua marker
markersData.forEach((data) => {
  const marker = L.marker([data.latitude, data.longitude]).bindPopup(`
    <strong>${data.nama_sekolah}</strong><br>
    <a href="detail_sekolah.php?id=${data.id}">Profil Sekolah</a>
  `);
  marker.kecamatan = extractKecamatan(data.alamat_sekolah);
  marker.addTo(map);
  allMarkers.push(marker);
});
// Event ketika dropdown berubah
document.addEventListener("change", function (e) {
  if (e.target.id === "filter-kecamatan") {
    const selected = e.target.options[e.target.selectedIndex];
    const namaKecamatan = selected.getAttribute("data-nama");

    // Filter GeoJSON
    geojsonLayers.forEach((layer) => {
      if (!namaKecamatan || layer.kecamatan === namaKecamatan) {
        map.addLayer(layer);
        if (layer.kecamatan === namaKecamatan) {
          map.fitBounds(layer.getBounds());
        }
      } else {
        map.removeLayer(layer);
      }
    });

    // Filter marker
    allMarkers.forEach((marker) => {
      if (!namaKecamatan || marker.kecamatan === namaKecamatan) {
        if (!map.hasLayer(marker)) marker.addTo(map);
      } else {
        if (map.hasLayer(marker)) map.removeLayer(marker);
      }
    });
  }
});

// Legenda
const legend = L.control({
  position: "bottomleft",
});
legend.onAdd = function (map) {
  const div = L.DomUtil.create("div", "info legend");
  div.innerHTML += `
      <h4>Keterangan</h4>
      <img src="assets/image/school-marker.png" width="25"> Lokasi Sekolah<br>
      <strong>Jumlah Sekolah</strong><br>
      <i style="background: #0050b3"></i> > 5<br>
      <i style="background: #3399ff"></i> 3 - 5<br>
      <i style="background: #cce6ff"></i> < 3
    `;
  return div;
};

legend.addTo(map);
