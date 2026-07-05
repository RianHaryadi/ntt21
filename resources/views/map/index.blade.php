@extends('layouts.app')

@section('title', 'Peta Destinasi NTT')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: calc(100vh - 72px); width: 100%; }
    .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .leaflet-popup-content { margin: 0; }
    .leaflet-popup-tip-container { display: none; }
    .map-popup-img { width: 220px; height: 120px; object-fit: cover; }
    .map-popup-body { padding: 12px 14px; }
    .map-popup-name { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 14px; color: #001a33; }
    .map-popup-loc  { font-size: 11px; color: #999; margin: 2px 0 8px; }
    .map-popup-foot { display: flex; justify-content: space-between; align-items: center; }
    .map-popup-price { font-weight: 700; font-size: 13px; color: #0F6E63; }
    .map-popup-btn {
        background: #0F6E63; color: white; font-size: 11px; font-weight: 700;
        padding: 5px 12px; border-radius: 999px; text-decoration: none;
    }
    .filter-panel {
        position: absolute; top: 88px; left: 16px; z-index: 1000;
        background: white; border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 14px 16px; min-width: 200px;
    }
    .filter-btn {
        display: block; width: 100%; text-align: left;
        padding: 8px 12px; border-radius: 10px; font-size: 13px;
        font-weight: 600; color: #001a33; transition: all 0.2s;
        cursor: pointer; border: none; background: transparent;
    }
    .filter-btn:hover, .filter-btn.active { background: #fff3ef; color: #0F6E63; }
    .map-counter {
        position: absolute; bottom: 24px; left: 16px; z-index: 1000;
        background: rgba(0,26,51,0.9); color: white; backdrop-filter: blur(10px);
        border-radius: 12px; padding: 8px 16px; font-size: 13px; font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="relative" style="margin-top: 72px;">

    {{-- Filter Panel --}}
    <div class="filter-panel">
        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Filter Kategori</p>
        <button class="filter-btn active" onclick="filterMap('all', this)">
            <i class="fas fa-globe-asia w-5 text-laut"></i> Semua
        </button>
        <button class="filter-btn" onclick="filterMap('Beach', this)">
            <i class="fas fa-umbrella-beach w-5 text-blue-400"></i> Pantai
        </button>
        <button class="filter-btn" onclick="filterMap('Mountain', this)">
            <i class="fas fa-mountain w-5 text-green-500"></i> Gunung
        </button>
        <button class="filter-btn" onclick="filterMap('Culture', this)">
            <i class="fas fa-masks-theater w-5 text-purple-500"></i> Budaya
        </button>
        <button class="filter-btn" onclick="filterMap('Nature', this)">
            <i class="fas fa-leaf w-5 text-emerald-500"></i> Alam
        </button>
        <hr class="my-3 border-line">
        <a href="{{ route('destinations.index') }}" class="filter-btn text-center text-laut">
            <i class="fas fa-list w-5"></i> Lihat Semua List
        </a>
    </div>

    {{-- Counter --}}
    <div class="map-counter" id="map-counter">Memuat destinasi...</div>

    {{-- Map --}}
    <div id="map"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map', {
    center: [-8.6573, 121.0794],
    zoom: 8,
    zoomControl: false,
});

L.control.zoom({ position: 'bottomright' }).addTo(map);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18,
}).addTo(map);

const categoryColors = {
    Beach:   '#3b82f6',
    Mountain:'#22c55e',
    Culture: '#a855f7',
    Nature:  '#10b981',
    default: '#0F6E63',
};

function makeIcon(color) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:32px;height:32px;border-radius:50% 50% 50% 0;
            background:${color};border:3px solid white;
            transform:rotate(-45deg);
            box-shadow:0 4px 12px rgba(0,0,0,0.25);
        "></div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -36],
    });
}

function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

let allMarkers = [];
let destinations = [];

fetch('{{ route("map.data") }}')
    .then(r => r.json())
    .then(data => {
        destinations = data;
        data.forEach(d => {
            const color = categoryColors[d.category] || categoryColors.default;
            const marker = L.marker([d.latitude, d.longitude], { icon: makeIcon(color) });

            const img = d.image
                ? `<img src="${d.image}" class="map-popup-img" alt="${d.name}">`
                : `<div class="map-popup-img" style="background:#001a33;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-image" style="color:#ffffff44;font-size:2rem;"></i>
                   </div>`;

            const stars = Array.from({length:5}, (_,i) =>
                `<i class="fas fa-star" style="color:${i < Math.round(d.rating) ? '#0F6E63' : '#ddd'};font-size:10px;"></i>`
            ).join('');

            marker.bindPopup(`
                <div style="width:220px;">
                    ${img}
                    <div class="map-popup-body">
                        <div class="map-popup-name">${d.name}</div>
                        <div class="map-popup-loc"><i class="fas fa-map-marker-alt" style="color:#0F6E63;margin-right:4px;"></i>${d.location}</div>
                        <div style="margin-bottom:8px;">${stars}</div>
                        <div class="map-popup-foot">
                            <span class="map-popup-price">${fmt(d.price)}</span>
                            <a href="/destinations/${d.id}" class="map-popup-btn">Lihat →</a>
                        </div>
                    </div>
                </div>
            `, { maxWidth: 220 });

            marker._category = d.category;
            marker.addTo(map);
            allMarkers.push(marker);
        });

        document.getElementById('map-counter').textContent = `${data.length} destinasi ditemukan`;
    });

function filterMap(category, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    let count = 0;
    allMarkers.forEach(m => {
        if (category === 'all' || m._category === category) {
            m.addTo(map);
            count++;
        } else {
            map.removeLayer(m);
        }
    });

    document.getElementById('map-counter').textContent = `${count} destinasi ditemukan`;
}
</script>
@endpush
