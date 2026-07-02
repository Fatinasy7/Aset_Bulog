import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({
  iconRetinaUrl: iconRetinaUrl,
  iconUrl: iconUrl,
  shadowUrl: shadowUrl,
});

document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('asset-map');
  if (!el) return;

  const lat = parseFloat(el.dataset.lat);
  const lng = parseFloat(el.dataset.lng);
  const popup = el.dataset.popup || '';

  if (isNaN(lat) || isNaN(lng)) return;

  try {
    const map = L.map(el).setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map).bindPopup(popup);
  } catch (e) {
    console.error('Leaflet init error', e);
  }
});
