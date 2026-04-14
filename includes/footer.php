</main>

<!-- Footer -->
<footer class="bg-void border-t border-[oklch(100%_0_0/0.06)] pt-16 pb-8">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

      <!-- Brand Column -->
      <div class="lg:col-span-1">
        <a href="/" aria-label="Moksha Construction Home">
          <img src="/assets/images/branding/logo-full-color.png" alt="Moksha Construction" class="h-[76px] w-auto mb-4" width="266" height="76" loading="lazy">
        </a>
        <p class="text-text-3 text-sm leading-relaxed mt-3">Building legacies across the Southeast. Licensed in Tennessee, Texas & North Carolina.</p>
        <!-- Social Links -->
        <div class="flex items-center gap-4 mt-6">
          <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram" class="text-text-3 hover:text-accent-400 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
          </a>
          <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Facebook" class="text-text-3 hover:text-accent-400 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          </a>
          <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow us on LinkedIn" class="text-text-3 hover:text-accent-400 transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </div>

      <!-- Navigation Column -->
      <div>
        <h3 class="text-sm font-semibold text-text uppercase tracking-widest mb-4">Navigation</h3>
        <ul class="space-y-3">
          <li><a href="/" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Home</a></li>
          <li><a href="/about" class="text-sm text-text-2 hover:text-accent-400 transition-colors">About</a></li>
          <li><a href="/projects" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Projects</a></li>
          <li><a href="/subcontractors" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Work With Us</a></li>
          <li><a href="/contact" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Contact</a></li>
        </ul>
      </div>

      <!-- Services Column -->
      <div>
        <h3 class="text-sm font-semibold text-text uppercase tracking-widest mb-4">Services</h3>
        <ul class="space-y-3">
          <li><a href="/services/general-contracting" class="text-sm text-text-2 hover:text-accent-400 transition-colors">General Contracting</a></li>
          <li><a href="/services/construction-management" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Construction Management</a></li>
          <li><a href="/services/design-build" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Design & Build</a></li>
          <li><a href="/services/residential-commercial-industrial" class="text-sm text-text-2 hover:text-accent-400 transition-colors">Residential · Commercial · Industrial</a></li>
        </ul>
      </div>

      <!-- Contact Column -->
      <div>
        <h3 class="text-sm font-semibold text-text uppercase tracking-widest mb-4">Contact</h3>
        <ul class="space-y-3">
          <li>
            <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-sm text-text-2 hover:text-accent-400 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
              <?= SITE_PHONE ?>
            </a>
          </li>
          <li>
            <a href="mailto:<?= SITE_EMAIL ?>" class="text-sm text-text-2 hover:text-accent-400 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4 text-accent-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              <?= SITE_EMAIL ?>
            </a>
          </li>
          <li class="text-sm text-text-3 pt-2">
            <strong class="text-text-2">Nashville</strong><br>
            <?= OFFICE_NASHVILLE['street'] ?><br>
            <?= OFFICE_NASHVILLE['city'] ?>, <?= OFFICE_NASHVILLE['state'] ?> <?= OFFICE_NASHVILLE['zip'] ?>
          </li>
          <li class="text-sm text-text-3">
            <strong class="text-text-2">Atlanta</strong><br>
            <?= OFFICE_ATLANTA['street'] ?><br>
            <?= OFFICE_ATLANTA['city'] ?>, <?= OFFICE_ATLANTA['state'] ?> <?= OFFICE_ATLANTA['zip'] ?>
          </li>
        </ul>
      </div>

    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-[oklch(100%_0_0/0.06)] pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
      <p class="text-xs text-text-4">
        Licensed in TN · TX · NC &nbsp;|&nbsp; Expanding to GA · SC · FL
      </p>
      <p class="text-xs text-text-4">
        &copy; <?= SITE_YEAR ?> <?= SITE_NAME ?> · Website by <a href="https://apete.ch" target="_blank" rel="noopener" class="hover:text-accent-400 transition-colors">APETech</a>
      </p>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="/assets/js/app.js?v=<?= ASSET_VERSION ?>" defer></script>

<!-- Leaflet (lazy-loaded only when a .leaflet-map element exists) -->
<script>
  (function() {
    if (!document.querySelector('.leaflet-map')) return;

    // Inject Leaflet CSS
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    link.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
    link.crossOrigin = '';
    document.head.appendChild(link);

    // Inject Leaflet JS
    var script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
    script.crossOrigin = '';
    script.onload = initMoksaMaps;
    document.head.appendChild(script);

    function initMoksaMaps() {
      var offices = [
        { name: 'Nashville Office', address: '315 Deaderick Street, Suite 1550, Nashville, TN 37238', coords: [36.1659, -86.7844], primary: true },
        { name: 'Atlanta Office', address: '1 W Court Square, Decatur, GA 30030', coords: [33.7748, -84.2963], primary: true }
      ];

      document.querySelectorAll('.leaflet-map').forEach(function(el) {
        var map = L.map(el, {
          scrollWheelZoom: false,
          zoomControl: true,
          attributionControl: false
        });

        // Carto Dark Matter — free dark tiles
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
          subdomains: 'abcd',
          maxZoom: 19
        }).addTo(map);

        // Custom branded marker (gold accent dot with glow)
        var goldIcon = L.divIcon({
          className: 'moksha-pin',
          html: '<span class="moksha-pin-dot"></span><span class="moksha-pin-pulse"></span>',
          iconSize: [16, 16],
          iconAnchor: [8, 8]
        });
        var primaryIcon = L.divIcon({
          className: 'moksha-pin moksha-pin-primary',
          html: '<span class="moksha-pin-dot"></span><span class="moksha-pin-pulse"></span>',
          iconSize: [22, 22],
          iconAnchor: [11, 11]
        });

        var bounds = [];
        offices.forEach(function(o) {
          var marker = L.marker(o.coords, { icon: o.primary ? primaryIcon : goldIcon }).addTo(map);
          marker.bindPopup(
            '<div class="moksha-popup"><strong>' + o.name + '</strong><br>' + o.address + '</div>'
          );
          bounds.push(o.coords);
        });

        map.fitBounds(bounds, { padding: [50, 50] });

        // Tiny tap-to-enable scroll zoom hint
        map.on('click', function() { map.scrollWheelZoom.enable(); });
      });
    }
  })();
</script>
</body>
</html>
