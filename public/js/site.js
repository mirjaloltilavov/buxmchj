/* Buxgalter MChJ — sayt skriptlari (kutubxonasiz) */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- Hero slider ---------- */
  var hero = $('[data-hero]');
  if (hero) {
    var slides = $$('[data-slide]', hero);
    var tabs = $$('[data-tab]', hero);
    var delay = parseInt(hero.getAttribute('data-delay'), 10) || 6000;
    var current = 0, timer = null;
    var go = function (i) {
      if (!slides.length) return;
      current = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) {
        s.classList.toggle('is-active', k === current);
        s.setAttribute('aria-hidden', k === current ? 'false' : 'true');
      });
      tabs.forEach(function (t, k) {
        t.classList.remove('is-active');
        t.setAttribute('aria-selected', k === current ? 'true' : 'false');
        if (k === current) { void t.offsetWidth; t.classList.add('is-active'); }
      });
      restart();
    };
    var restart = function () {
      clearTimeout(timer);
      if (slides.length > 1) timer = setTimeout(function () { go(current + 1); }, delay);
    };
    tabs.forEach(function (t) { t.addEventListener('click', function () { go(parseInt(t.getAttribute('data-tab'), 10)); }); });
    var prev = $('[data-prev]', hero), next = $('[data-next]', hero);
    if (prev) prev.addEventListener('click', function () { go(current - 1); });
    if (next) next.addEventListener('click', function () { go(current + 1); });
    document.addEventListener('visibilitychange', function () { document.hidden ? clearTimeout(timer) : restart(); });
    restart();
  }

  /* ---------- Mobile menu ---------- */
  var menu = $('[data-mobile-menu]');
  var burger = $('[data-burger]');
  var setMenu = function (open) {
    if (!menu) return;
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (burger) burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.documentElement.style.overflow = open ? 'hidden' : '';
  };
  if (burger) burger.addEventListener('click', function () { setMenu(true); });
  $$('[data-burger-close]').forEach(function (b) { b.addEventListener('click', function () { setMenu(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });

  /* ---------- Language dropdown: tashqariga bosilganda yopish ---------- */
  document.addEventListener('click', function (e) {
    $$('details.lang[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
  });

  /* ---------- "Konsultatsiya" tugmalari: forma shu sahifada bo'lmasa — Aloqa sahifasiga ---------- */
  $$('[data-consult]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var target = $('#consultation') || $('[data-lead-form]');
      if (target) {
        e.preventDefault();
        setMenu(false);
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        var input = $('input[name="name"]', target);
        if (input) setTimeout(function () { input.focus({ preventScroll: true }); }, 600);
      } else {
        var contact = document.documentElement.lang === 'ru' ? '/ru/contact#form' : '/contact#form';
        a.setAttribute('href', contact);
      }
    });
  });

  /* ---------- Accordion: bittasi ochiq ---------- */
  $$('[data-accordion]').forEach(function (acc) {
    var items = $$('details', acc);
    items.forEach(function (d) {
      d.addEventListener('toggle', function () {
        if (d.open) items.forEach(function (o) { if (o !== d) o.removeAttribute('open'); });
      });
    });
  });

  /* ---------- Scroll reveal ---------- */
  var reveals = $$('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  }

  /* ---------- Yuqoriga ---------- */
  $$('[data-to-top]').forEach(function (b) {
    b.addEventListener('click', function (e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
  });

  /* ---------- Telefon maskasi: +998 XX XXX XX XX ---------- */
  $$('[data-phone]').forEach(function (input) {
    var format = function () {
      var d = input.value.replace(/\D/g, '');
      if (d.indexOf('998') === 0) d = d.slice(3);
      d = d.slice(0, 9);
      var out = '+998';
      if (d.length) out += ' ' + d.slice(0, 2);
      if (d.length > 2) out += ' ' + d.slice(2, 5);
      if (d.length > 5) out += ' ' + d.slice(5, 7);
      if (d.length > 7) out += ' ' + d.slice(7, 9);
      input.value = out;
    };
    input.addEventListener('focus', function () { if (!input.value) input.value = '+998 '; });
    input.addEventListener('input', format);
    input.addEventListener('blur', function () { if (input.value.replace(/\D/g, '').length <= 3) input.value = ''; });
  });

  /* ---------- Ariza formasi (AJAX) ---------- */
  var ru = document.documentElement.lang === 'ru';
  var msg = {
    name: ru ? 'Введите ваше имя.' : 'Ismingizni kiriting.',
    phone: ru ? 'Введите номер телефона полностью.' : 'Telefon raqamni to‘liq kiriting.',
    fail: ru ? 'Не удалось отправить. Попробуйте ещё раз или позвоните нам.' : 'Yuborib bo‘lmadi. Qayta urinib ko‘ring yoki bizga qo‘ng‘iroq qiling.'
  };
  $$('[data-lead-form]').forEach(function (form) {
    var err = $('[data-form-error]', form);
    var showError = function (t) { if (err) { err.textContent = t; err.hidden = !t; } };
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = form.elements.name.value.trim();
      var phone = form.elements.phone.value.replace(/\D/g, '');
      if (!name) { showError(msg.name); form.elements.name.focus(); return; }
      if (phone.length < 12) { showError(msg.phone); form.elements.phone.focus(); return; }
      showError('');
      var btn = $('button[type="submit"]', form);
      if (btn) btn.disabled = true;
      fetch(form.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
        credentials: 'same-origin'
      }).then(function (r) {
        if (!r.ok) throw new Error(r.status);
        form.classList.add('is-sent');
        form.reset();
      }).catch(function () {
        showError(msg.fail);
      }).then(function () { if (btn) btn.disabled = false; });
    });
  });
})();
