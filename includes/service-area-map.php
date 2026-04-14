<?php
/**
 * Service Area Interactive Map
 * Renders a Leaflet map with office pins for Nashville, Clarksville, and Atlanta.
 *
 * Usage:
 *   <?php
 *     $map_height = '500px';   // optional override (default: aspect-[16/9] container)
 *     $map_id     = 'home-map'; // unique id required when multiple maps on a page
 *     require __DIR__ . '/../includes/service-area-map.php';
 *   ?>
 */
$map_id = $map_id ?? 'service-area-map';
$map_height = $map_height ?? '460px';
?>
<div
  id="<?= htmlspecialchars($map_id) ?>"
  class="leaflet-map w-full rounded-[var(--radius-xl)] border border-[oklch(100%_0_0/0.08)] overflow-hidden"
  style="height: <?= htmlspecialchars($map_height) ?>;"
  aria-label="Map of Moksha Construction office locations"
  role="img"
></div>
