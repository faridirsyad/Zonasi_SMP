const map = L.map("map").setView([-7.3536, 109.6341], 11);

// Basemap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "&copy; OpenStreetMap",
}).addTo(map);

// Control dropdown untuk filter Korda
const control = L.control({ position: "topright" });
control.onAdd = function (map) {
  const div = L.DomUtil.create("div", "custom-control");
  let html = '<select id="filter-korda"><option value="">-- Pilih Korda --</option>';
  kordaData.forEach((k) => {
    html += `<option value="${k.geojson}" data-nama="${k.nama_korda}">${k.nama_korda}</option>`;
  });
  html += "</select>";
  div.innerHTML = html;
  return div;
};
control.addTo(map);

// Simpan marker dan geojson layer
const allMarkers = [];
const geojsonLayers = [];

// Fungsi ekstrak dan rapikan nama Korda
function extractKorda(korda) {
  return (korda || "").trim().toLowerCase();
}

// Warna berdasarkan nama korda (lowercase)
const kordaColors = {
  korda1: "#1631cb",
  korda2: "#33a02c",
  korda3: "#f05255",
  korda4: "#fffb21",
};

// Buat mapping ID Korda ke nama Korda
const idToNamaKorda = {};
kordaData.forEach((k) => {
  idToNamaKorda[k.id] = k.nama_korda;
});

// Hitung jumlah daya tampung per Korda
const jumlahtampung = {};
markersData.forEach((sekolah) => {
  const namaKorda = idToNamaKorda[sekolah.korda];
  if (!namaKorda) return;
  if (!jumlahtampung[namaKorda]) {
    jumlahtampung[namaKorda] = 0;
  }
  jumlahtampung[namaKorda] += sekolah.daya_tampung || 0;
});

// Tampilkan semua geojson Korda
kordaData.forEach((korda) => {
  fetch("assets/geojson/korda/" + korda.geojson)
    .then((res) => res.json())
    .then((data) => {
      const nama = extractKorda(korda.nama_korda); // lowercase
      const color = kordaColors[nama] || "#cccccc";

      let jumlahSekolah = 0;
      let totalDayaTampung = 0;

      markersData.forEach((sekolah) => {
        if (sekolah.korda === korda.id) {
          jumlahSekolah++;
          totalDayaTampung += parseInt(sekolah.daya_tampung) || 0;
        }
      });

      const layer = L.geoJSON(data, {
        style: {
          color: "#011f4b",
          weight: 2,
          fillColor: color,
          fillOpacity: 0.7,
        },
        onEachFeature: function (feature, layer) {
          layer.on("click", function () {
            L.popup()
              .setLatLng(layer.getBounds().getCenter())
              .setContent(
                `
              <strong>${korda.nama_korda}</strong><br>
              Jumlah Sekolah: ${jumlahSekolah}<br>
              Jumlah Daya Tampung: ${totalDayaTampung}
            `
              )
              .openOn(map);
          });
        },
      }).addTo(map);

      layer.korda = nama;
      geojsonLayers.push(layer);
    });
});

// Tampilkan semua marker sekolah
markersData.forEach((data) => {
  const namaKorda = idToNamaKorda[data.korda];
  const marker = L.marker([data.latitude, data.longitude]).bindPopup(`
    <strong>${data.nama_sekolah}</strong><br>
    Daya Tampung: ${data.daya_tampung || 0}
  `);

  marker.korda = extractKorda(namaKorda);
  marker.addTo(map);
  allMarkers.push(marker);
});

// Event filter dropdown Korda
document.addEventListener("change", function (e) {
  if (e.target.id === "filter-korda") {
    const selected = e.target.options[e.target.selectedIndex];
    const namaKorda = extractKorda(selected.getAttribute("data-nama"));

    // Filter geojson layer
    geojsonLayers.forEach((layer) => {
      if (!namaKorda || layer.korda === namaKorda) {
        map.addLayer(layer);
        if (layer.korda === namaKorda) {
          map.fitBounds(layer.getBounds());
        }
      } else {
        map.removeLayer(layer);
      }
    });

    // Filter marker
    allMarkers.forEach((marker) => {
      if (!namaKorda || marker.korda === namaKorda) {
        if (!map.hasLayer(marker)) marker.addTo(map);
      } else {
        if (map.hasLayer(marker)) map.removeLayer(marker);
      }
    });
  }
});

// Legenda warna
const legend = L.control({ position: "bottomleft" });
legend.onAdd = function (map) {
  const div = L.DomUtil.create("div", "info legend");
  div.innerHTML += `
    <strong>Keterangan</strong><br>
    <i style="background: #1631cb; width: 18px; height: 18px; display: inline-block;"></i> Korda1<br>
    <i style="background: #33a02c; width: 18px; height: 18px; display: inline-block;"></i> Korda2<br>
    <i style="background: #f05255; width: 18px; height: 18px; display: inline-block;"></i> Korda3<br>
    <i style="background: #fffb21; width: 18px; height: 18px; display: inline-block;"></i> Korda4
  `;
  return div;
};
legend.addTo(map);
