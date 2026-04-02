import '../css/app.css'
import Alpine from 'alpinejs'
import Lenis from 'lenis'

/* ============================================================
   ALPINE.JS
============================================================ */

window.Alpine = Alpine

Alpine.data('mobileMenu', () => ({
  open: false,
  toggle() {
    this.open = !this.open
    document.body.classList.toggle('overflow-hidden', this.open)
  },
  close() {
    this.open = false
    document.body.classList.remove('overflow-hidden')
  },
}))

Alpine.data('dropdown', () => ({
  open: false,
  toggle() { this.open = !this.open },
  close() { this.open = false },
}))

Alpine.start()

/* ============================================================
   LENIS — Smooth scroll (autoRaf avoids infinite rAF loop)
============================================================ */

new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  touchMultiplier: 2,
  autoRaf: true,
})

/* ============================================================
   SCROLL REVEAL — Single IntersectionObserver for all elements
============================================================ */

const revealObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible')
        revealObserver.unobserve(entry.target)
      }
    })
  },
  { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
)

const counterObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target
        animateCounter(el, parseInt(el.dataset.target) || 0, el.dataset.prefix || '', el.dataset.suffix || '')
        counterObserver.unobserve(el)
      }
    })
  },
  { threshold: 0.5 }
)

function animateCounter(el, target, prefix, suffix) {
  const duration = 2000
  const start = performance.now()
  const step = (now) => {
    const elapsed = now - start
    const progress = Math.min(elapsed / duration, 1)
    const eased = 1 - Math.pow(1 - progress, 3)
    el.textContent = prefix + Math.round(eased * target).toLocaleString() + suffix
    if (progress < 1) requestAnimationFrame(step)
  }
  requestAnimationFrame(step)
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.reveal').forEach((el) => revealObserver.observe(el))
  document.querySelectorAll('[data-counter]').forEach((el) => counterObserver.observe(el))
})

/* ============================================================
   HEADER — Scroll shrink
============================================================ */

const header = document.getElementById('siteHeader')
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 40)
  }, { passive: true })
}
