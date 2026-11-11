@php
    $base = \Illuminate\Support\Str::beforeLast($getStatePath(), '.');
@endphp
{{-- Filial joyini xaritada tanlash: bosing yoki pinni suring — lat/lng maydonlari o'zi to'ladi --}}
<div
    wire:ignore
    x-data="{
        lat: $wire.$entangle('{{ $base }}.lat'),
        lng: $wire.$entangle('{{ $base }}.lng'),
        map: null, marker: null,
        load(cb) {
            if (window.L) return cb();
            const css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css'; document.head.appendChild(css);
            const s = document.createElement('script'); s.src = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js'; s.onload = cb; document.head.appendChild(s);
        },
        set(ll) {
            this.lat = +ll.lat.toFixed(6); this.lng = +ll.lng.toFixed(6);
            this.marker.setLatLng(ll);
        },
        init() {
            this.load(() => {
                const has = this.lat && this.lng;
                const start = has ? [+this.lat, +this.lng] : [41.3111, 69.2797];
                this.map = L.map(this.$refs.map, { scrollWheelZoom: true }).setView(start, has ? 16 : 12);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(this.map);
                const icon = L.divIcon({ className: '', html: '<span class=\'bx-mp-pin\'></span>', iconSize: [36, 36], iconAnchor: [18, 36] });
                this.marker = L.marker(start, { draggable: true, icon }).addTo(this.map);
                if (!has) this.marker.setOpacity(.45);
                this.marker.on('dragend', () => { this.marker.setOpacity(1); this.set(this.marker.getLatLng()); });
                this.map.on('click', (e) => { this.marker.setOpacity(1); this.set(e.latlng); });
                this.$watch('lat', () => this.sync()); this.$watch('lng', () => this.sync());
                setTimeout(() => this.map.invalidateSize(), 350);
            });
        },
        sync() {
            if (!this.map || !this.lat || !this.lng) return;
            const ll = L.latLng(+this.lat, +this.lng);
            if (this.marker.getLatLng().distanceTo(ll) > 1) { this.marker.setLatLng(ll).setOpacity(1); this.map.panTo(ll); }
        },
    }"
>
    <div x-ref="map" style="height:340px;border-radius:12px;overflow:hidden;border:1px solid rgba(120,120,130,.25);z-index:0"></div>
    <p style="margin-top:.5rem;font-size:.8rem;color:#868b94">Xaritani bosing yoki pinni sudrab, aniq joyga qo‘ying. Kenglik va uzunlik o‘zi to‘ladi.</p>
    <style>.bx-mp-pin{display:block;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);background:linear-gradient(135deg,#ffd877,#f5a524);border:3px solid #fff;box-shadow:0 6px 14px rgba(0,0,0,.25)}</style>
</div>
