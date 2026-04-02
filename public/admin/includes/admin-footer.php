    </div><!-- /.admin-content -->

    <!-- Admin Footer -->
    <footer class="admin-footer">
      <span>&copy; <?= date('Y') ?> Moksha Construction</span>
    </footer>

  </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<!-- Alpine.js — for toggle interactions -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Quill — rich text editor for description fields -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var descEl = document.getElementById('description');
  if (!descEl) return;

  // Hide original textarea, create Quill container
  descEl.style.display = 'none';
  var editorDiv = document.createElement('div');
  editorDiv.id = 'quill-editor';
  editorDiv.innerHTML = descEl.value;
  descEl.parentNode.insertBefore(editorDiv, descEl.nextSibling);

  var quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Describe the project scope, materials, unique challenges, and outcome…',
    modules: {
      toolbar: [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link'],
        ['clean']
      ]
    }
  });

  // Sync Quill content back to hidden textarea on form submit
  var form = descEl.closest('form');
  if (form) {
    form.addEventListener('submit', function() {
      descEl.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
    });
  }
});
</script>
<style>
/* Dark theme for Quill editor */
#quill-editor {
  background: var(--raised, #1e1a30);
  color: var(--text, #f0eef2);
  border: 1px solid rgba(255,255,255,0.12);
  border-top: none;
  border-radius: 0 0 6px 6px;
  min-height: 250px;
  font-family: Inter, system-ui, sans-serif;
  font-size: .9375rem;
  line-height: 1.7;
}
#quill-editor .ql-editor {
  min-height: 250px;
  padding: 1rem 1.25rem;
}
#quill-editor .ql-editor p { margin-bottom: .75rem; }
#quill-editor .ql-editor a { color: #FFE907; }
#quill-editor .ql-editor.ql-blank::before {
  color: rgba(255,255,255,0.25);
  font-style: normal;
}
.ql-toolbar.ql-snow {
  background: var(--surface, #161225);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 6px 6px 0 0;
  padding: .5rem .75rem;
}
.ql-toolbar .ql-stroke { stroke: rgba(255,255,255,0.5); }
.ql-toolbar .ql-fill { fill: rgba(255,255,255,0.5); }
.ql-toolbar .ql-picker-label { color: rgba(255,255,255,0.5); }
.ql-toolbar button:hover .ql-stroke,
.ql-toolbar .ql-active .ql-stroke { stroke: #FFE907; }
.ql-toolbar button:hover .ql-fill,
.ql-toolbar .ql-active .ql-fill { fill: #FFE907; }
.ql-toolbar button:hover,
.ql-toolbar button.ql-active { color: #FFE907; }
.ql-snow .ql-tooltip {
  background: var(--raised, #1e1a30);
  border: 1px solid rgba(255,255,255,0.12);
  color: var(--text, #f0eef2);
  box-shadow: 0 4px 24px rgba(0,0,0,.5);
  border-radius: 6px;
}
.ql-snow .ql-tooltip input[type=text] {
  background: var(--bg, #0d0510);
  border: 1px solid rgba(255,255,255,0.12);
  color: var(--text, #f0eef2);
  border-radius: 4px;
}
.ql-snow .ql-tooltip a { color: #FFE907; }
</style>

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
