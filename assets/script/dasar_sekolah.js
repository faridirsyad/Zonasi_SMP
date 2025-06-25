// Inisialisasi peta
const map = L.map("map2").setView([-7.353620352541296, 109.63416651888299], 11);

// Tambahkan tile layer dari OpenStreetMap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// Layer untuk marker sekolah dan lingkaran radius
let sekolahMarker = null;
let circle5km = null;
let circle10km = null;
let desaMarkers = [];

// Fungsi untuk menghitung jarak dengan rumus Haversine
function hitungJarak(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const toRad = (deg) => deg * (Math.PI / 180);

  const dLat = toRad(lat2 - lat1);
  const dLon = toRad(lon2 - lon1);

  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return R * c;
}

// Tampilkan marker sekolah
sekolahData.forEach((s) => {
  const markerSekolah = L.marker([s.latitude, s.longitude]).bindPopup(`
    <strong>${s.nama_sekolah}</strong><br>
    ${s.alamat_sekolah}<br>
    <a href="detail_sekolah.php?id=${s.id}">Profil Sekolah</a>
  `);
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

// Fungsi utama saat memilih sekolah dari dropdown
function fokusKeSekolahDropdown(idSekolah) {
  if (!idSekolah) return;

  const sekolah = sekolahData.find((s) => s.id == idSekolah);
  if (!sekolah) return;

  const lat = parseFloat(sekolah.latitude);
  const lng = parseFloat(sekolah.longitude);

  // Reset marker dan circle sebelumnya
  if (sekolahMarker) map.removeLayer(sekolahMarker);
  if (circle5km) map.removeLayer(circle5km);
  if (circle10km) map.removeLayer(circle10km);
  desaMarkers.forEach((marker) => map.removeLayer(marker));
  desaMarkers = [];

  // Tambahkan marker sekolah
  sekolahMarker = L.marker([lat, lng]).addTo(map).bindPopup(`<b>${sekolah.nama_sekolah}</b><br><a href="detail_sekolah.php?id=${sekolah.id}">Profil Sekolah</a>`).openPopup();

  // Zoom dan center ke sekolah
  map.setView([lat, lng], 12);

  // Tambahkan lingkaran radius
  circle5km = L.circle([lat, lng], { radius: 5000, color: "green", fillOpacity: 0.1 }).addTo(map);
  circle10km = L.circle([lat, lng], { radius: 10000, color: "orange", fillOpacity: 0.1 }).addTo(map);

  // Hitung jarak setiap desa ke sekolah
  const desaDenganJarak = desaData.map((desa) => {
    const jarak = hitungJarak(lat, lng, parseFloat(desa.latitude), parseFloat(desa.longitude));
    return { ...desa, jarak };
  });

  // Urutkan desa berdasarkan jarak
  desaDenganJarak.sort((a, b) => a.jarak - b.jarak);

  // Tampilkan informasi desa di info box
  const infoBox = document.getElementById("info-box");
  infoBox.innerHTML = `<h4>Desa dalam Radius Sekolah</h4>`;

  desaDenganJarak.forEach((desa) => {
    let warna;
    if (desa.jarak <= 5) warna = "green";
    else if (desa.jarak <= 10) warna = "orange";
    else warna = "red";

    // Tambahkan marker desa ke peta
    const marker = L.circleMarker([desa.latitude, desa.longitude], {
      radius: 6,
      color: warna,
      fillOpacity: 0.9,
    })
      .addTo(map)
      .bindPopup(`<b>${desa.nama_desakel}</b><br>Kec. ${desa.kecamatan}<br>Jarak: ${desa.jarak.toFixed(2)} km`);

    desaMarkers.push(marker);

    // Tambahkan info ke dalam box
    const desaInfo = document.createElement("div");
    desaInfo.classList.add("school-card");
    desaInfo.innerHTML = `
      <h3>${desa.nama_desakel}</h3>
      <p>Kecamatan: ${desa.kecamatan}</p>
      <p>Jarak: ${desa.jarak.toFixed(2)} km</p>
    `;
    infoBox.appendChild(desaInfo);
  });
}

// Isi dropdown sekolah saat load
window.addEventListener("DOMContentLoaded", () => {
  const dropdown = document.getElementById("dropdown-sekolah");
  sekolahData.forEach((sekolah) => {
    const option = document.createElement("option");
    option.value = sekolah.id;
    option.textContent = sekolah.nama_sekolah;
    dropdown.appendChild(option);
  });
});

// Legenda
const legend = L.control({
  position: "bottomleft",
});
legend.onAdd = function (map) {
  const div = L.DomUtil.create("div", "info legend");
  div.innerHTML += `
        <h4>Keterangan</h4>
        <img src="assets/image/school-marker.png" width="25"> Lokasi Sekolah
        <h4>Radius Jarak Desa</h4>
        <i style="background: green; width: 12px; height: 12px; display: inline-block; border-radius: 50%; margin-right: 5px;"></i> ≤ 5 km<br>
        <i style="background: orange; width: 12px; height: 12px; display: inline-block; border-radius: 50%; margin-right: 5px;"></i> ≤ 10 km<br>
        <i style="background: red; width: 12px; height: 12px; display: inline-block; border-radius: 50%; margin-right: 5px;"></i> > 10 km
    `;
  return div;
};
legend.addTo(map);

console.log(`Jarak ke desa ${desa.nama_desakel}: ${desa.jarak.toFixed(2)} km`);
