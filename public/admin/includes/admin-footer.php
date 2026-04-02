    </div><!-- /.admin-content -->

    <!-- Admin Footer -->
    <footer class="admin-footer">
      <span>&copy; <?= date('Y') ?> Moksha Construction</span>
    </footer>

  </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<!-- Alpine.js — for toggle interactions -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Admin JS -->
<script>
// Mobile sidebar
function openSidebar() {
  document.getElementById('adminSidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('visible');
  document.body.style.overflow = 'hidden';
}

function closeSidebar() {
  document.getElementById('adminSidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('visible');
  document.body.style.overflow = '';
}

// Confirm-before-delete helper (called via onclick)
function confirmDelete(form) {
  if (confirm('Delete this project? This cannot be undone.')) {
    form.submit();
  }
}

// Auto-dismiss flash messages after 5 s
document.addEventListener('DOMContentLoaded', function () {
  const flashes = document.querySelectorAll('.flash[data-auto-dismiss]');
  flashes.forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity 0.4s, max-height 0.4s';
      el.style.opacity = '0';
      el.style.maxHeight = '0';
      el.style.overflow = 'hidden';
      el.style.padding = '0';
      el.style.margin = '0';
    }, 5000);
  });
});
</script>

</body>
</html>
