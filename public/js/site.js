/* Buxgalter MChJ — sayt skriptlari. Animatsiyalar Figma "Motion system" va sahifa "Motion notes" bo'yicha. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isMobile = function () { return window.matchMedia('(max-width: 810px)').matches; };
  var ru = document.documentElement.lang === 'ru';
  var raf = window.requestAnimationFrame || function (f) { return setTimeout(f, 16); };

  /* =====================================================================
     1. Reveal + stagger (Motion system: Reveal / Stagger)
     Shablonda: data-reveal="up|scale|card|pop|left|right|clip|clip-up|fade"
     Konteynerda: data-stagger="80" — ichidagi [data-reveal] lar ketma-ket chiqadi.
     Quyidagi xarita eski bloklarga avtomatik qo'llaydi (sarlavha → tavsif → kontent).
  ===================================================================== */
  var AUTO = [
    // [selector, variant, stagger(ms), baseDelay(ms)]
    ['.section-head > *, .articles-head .t > *, .articles-head > .btn-pill, .insta-head > *', 'up', 120, 0],
    ['.directions > *', 'up40', 100, 120],
    ['.bento-row1 > .bento-photo, .bento-row1 > .tile, .tile-stack > .tile, .bento-row2 > *', 'scale', 80, 0],
    ['.courses-grid > .course-card, .course-stack > .course-card', 'up', 80, 0],
    ['.services-ov .left > *', 'up', 120, 0],
    ['.services-media', 'card', 0, 0],
    ['.service-cards > *', 'up', 80, 0],
    ['.faq .left > *', 'up', 120, 0],
    ['.accordion > *', 'up', 60, 120],
    ['.article-feature', 'left', 0, 0],
    ['.article-list > *', 'right', 80, 0],
    ['.consult', 'card', 0, 0],
    ['.consult .left > *', 'up', 80, 150],
    ['.steps > *', 'up', 80, 300],
    ['.lead-form .form-fields > *', 'up', 60, 200],
    ['.f-top .h > *', 'up', 80, 0],
    ['.f-contacts > *', 'up', 80, 120],
    ['.f-brand > *, .f-col', 'up', 60, 0]
  ];
  AUTO.forEach(function (rule) {
    var groups = new Map();
    $$(rule[0]).forEach(function (el) {
      if (el.hasAttribute('data-reveal')) return;
      el.setAttribute('data-reveal', rule[1]);
      var p = el.parentElement;
      var i = groups.get(p) || 0;
      el.style.setProperty('--d', (rule[3] + i * rule[2]) + 'ms');
      groups.set(p, i + 1);
    });
  });
  $$('[data-stagger]').forEach(function (box) {
    var step = parseInt(box.getAttribute('data-stagger'), 10) || 80;
    var base = parseInt(box.getAttribute('data-stagger-base'), 10) || 0;
    $$('[data-reveal]', box).forEach(function (el, i) {
      if (!el.style.getPropertyValue('--d')) el.style.setProperty('--d', (base + i * step) + 'ms');
    });
  });
  // ✓ belgilarni chizish (stroke draw, 60 ms stagger)
  $$('.checks').forEach(function (list) {
    list.classList.add('draw-list');
    $$('li', list).forEach(function (li, i) { li.classList.add('draw'); li.style.setProperty('--d', (200 + i * 60) + 'ms'); });
  });

  var revealEls = $$('[data-reveal], .draw');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.2, rootMargin: '0px 0px -40px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
    // juda baland elementlar 20 % ga yetmasligi mumkin
    setTimeout(function () {
      revealEls.forEach(function (el) {
        var r = el.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0 && r.height > window.innerHeight * 3) el.classList.add('is-in');
      });
    }, 300);
  } else {
    revealEls.forEach(function (el) { el.classList.add('is-in'); });
  }

  /* =====================================================================
     2. Stat count-up (1.2 s, 40 % ko'ringanda)
  ===================================================================== */
  var countEls = $$('[data-count], .tile--accent .big, .stats b');
  var runCount = function (el) {
    var text = el.getAttribute('data-count-text') || el.textContent;
    el.setAttribute('data-count-text', text);
    var m = text.match(/(\d+(?:[ \u00a0]\d{3})*)/);
    if (!m) return;
    var target = parseInt(m[1].replace(/\s/g, ''), 10);
    var before = text.slice(0, m.index), after = text.slice(m.index + m[1].length);
    var hasSpace = /[ \u00a0]/.test(m[1]);
    var t0 = null, dur = 1200;
    var fmt = function (n) { return hasSpace ? String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') : String(n); };
    var step = function (t) {
      if (!t0) t0 = t;
      var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
      el.textContent = before + fmt(Math.round(target * e)) + after;
      if (p < 1) raf(step);
    };
    raf(step);
  };
  if ('IntersectionObserver' in window && !reduce) {
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { runCount(en.target); cio.unobserve(en.target); } });
    }, { threshold: 0.4 });
    countEls.forEach(function (el) { cio.observe(el); });
  }

  /* =====================================================================
     3. Header: Fixed holat (> 80 px), pastga — yashirish, yuqoriga — qaytarish (300 ms)
  ===================================================================== */
  var header = $('[data-header]');
  var menuOpen = false;
  if (header) {
    var lastY = window.scrollY, ticking = false;
    var onScroll = function () {
      var y = window.scrollY;
      header.classList.toggle('is-fixed', y > 80);
      if (!menuOpen && !header.querySelector('details[open]')) {
        if (y > 160 && y > lastY + 4) header.classList.add('is-hidden');
        else if (y < lastY - 4 || y <= 160) header.classList.remove('is-hidden');
      }
      lastY = y; ticking = false;
    };
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; raf(onScroll); } }, { passive: true });
    onScroll();

    // Nav: aktiv pill bosilgan havolaga siljiydi
    var nav = $('[data-nav]', header), pill = nav && $('.nav-pill', nav);
    var placePill = function (a, instant) {
      if (!pill) return;
      if (!a) { pill.style.opacity = '0'; return; }
      if (instant) pill.style.transition = 'none';
      pill.style.opacity = '1';
      pill.style.width = a.offsetWidth + 'px';
      pill.style.transform = 'translateX(' + a.offsetLeft + 'px)';
      if (instant) { void pill.offsetWidth; pill.style.transition = ''; }
    };
    if (nav) {
      var active = $('a.is-active', nav);
      placePill(active, true);
      document.fonts && document.fonts.ready.then(function () { placePill($('a.is-active', nav), true); });
      window.addEventListener('resize', function () { placePill($('a.is-active', nav), true); });
      $$('a', nav).forEach(function (a) {
        a.addEventListener('click', function () {
          $$('a', nav).forEach(function (x) { x.classList.remove('is-active'); });
          a.classList.add('is-active');
          placePill(a);
        });
      });
    }
  }

  /* Mobil menyu */
  var menu = $('[data-mobile-menu]');
  var setMenu = function (open) {
    if (!menu) return;
    menuOpen = open;
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    $$('[data-burger]').forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
    document.documentElement.style.overflow = open ? 'hidden' : '';
  };
  $$('[data-burger]').forEach(function (b) { b.addEventListener('click', function () { setMenu(true); }); });
  $$('[data-burger-close]').forEach(function (b) { b.addEventListener('click', function () { setMenu(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); closePopup(); } });

  /* Til menyusi: tashqariga bosilganda yopish */
  document.addEventListener('click', function (e) {
    $$('details.lang[open]').forEach(function (d) { if (!d.contains(e.target)) d.removeAttribute('open'); });
  });

  /* =====================================================================
     4. Hero slider (Motion: Hero slider)
     - Kirish: badge → sarlavha → tavsif → tugmalar 120 ms stagger; rasm 1.06 → 1 (900 ms);
       suzuvchi kartalar navbat bilan pop (200 ms oraliq), so'ng ±6 px suzish (4 s)
     - Autoplay 6 s, tab progress linear; hover — pauza
     - Almashganda: matn cross-fade + y16, rasm clip-reveal chapdan o'ngga (700 ms)
     - Fon chiziqlari parallax 0.2
  ===================================================================== */
  var hero = $('[data-hero]');
  if (hero) {
    var slides = $$('[data-slide]', hero);
    var tabs = $$('[data-tab]', hero);
    var delay = parseInt(hero.getAttribute('data-delay'), 10) || 6000;
    var current = 0, timer = null, started = 0, remaining = delay, paused = false;
    slides.forEach(function (s) {
      $$('.float-card', s).forEach(function (c, i) { c.style.setProperty('--d', (700 + i * 200) + 'ms'); c.classList.add('floating'); });
    });
    var setActive = function (i, first) {
      current = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) {
        var on = k === current;
        s.classList.toggle('is-active', on);
        s.classList.toggle('is-ready', on);
        s.setAttribute('aria-hidden', on ? 'false' : 'true');
        if (!first && on) s.classList.add('is-switch');
      });
      tabs.forEach(function (t, k) {
        t.classList.remove('is-active');
        t.setAttribute('aria-selected', k === current ? 'true' : 'false');
        if (k === current) { void t.offsetWidth; t.classList.add('is-active'); }
      });
      remaining = delay; start();
    };
    var start = function () {
      clearTimeout(timer);
      if (slides.length < 2 || paused) return;
      started = Date.now();
      timer = setTimeout(function () { setActive(current + 1); }, remaining);
    };
    var pause = function () {
      if (paused) return; paused = true; clearTimeout(timer);
      remaining = Math.max(0, remaining - (Date.now() - started));
      hero.classList.add('is-paused');
    };
    var resume = function () { if (!paused) return; paused = false; hero.classList.remove('is-paused'); start(); };
    tabs.forEach(function (t) { t.addEventListener('click', function () { setActive(parseInt(t.getAttribute('data-tab'), 10)); }); });
    var prev = $('[data-prev]', hero), next = $('[data-next]', hero);
    if (prev) prev.addEventListener('click', function () { setActive(current - 1); });
    if (next) next.addEventListener('click', function () { setActive(current + 1); });
    hero.addEventListener('mouseenter', pause);
    hero.addEventListener('mouseleave', resume);
    document.addEventListener('visibilitychange', function () { document.hidden ? pause() : resume(); });
    // Kirish animatsiyasi
    raf(function () { hero.classList.add('is-entered'); setActive(0, true); });
    // Parallax (0.2)
    var deco = $('.hero-deco', hero);
    if (deco && !reduce) {
      window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (y < hero.offsetHeight) deco.style.transform = 'translateY(calc(-50% + ' + (y * 0.2) + 'px))';
      }, { passive: true });
    }
  }
  // Ichki sahifa hero'lari: kirish ketma-ketligi
  $$('[data-page-hero]').forEach(function (h) { raf(function () { h.classList.add('is-entered'); }); });

  /* =====================================================================
     5. FAQ: height 300 ms, + belgisi 45°, javob fade + y8, bittasi ochiq
  ===================================================================== */
  $$('[data-accordion]').forEach(function (acc) {
    var items = $$('details', acc);
    var animate = function (d, open) {
      var body = $('.acc-body', d);
      if (!body || reduce) { if (open) d.setAttribute('open', ''); else d.removeAttribute('open'); return; }
      if (open) {
        d.setAttribute('open', '');
        var h = body.scrollHeight;
        body.style.height = '0px'; body.style.opacity = '0'; body.style.transform = 'translateY(8px)';
        raf(function () {
          body.style.transition = 'height .3s cubic-bezier(.22,1,.36,1), opacity .3s, transform .3s cubic-bezier(.22,1,.36,1)';
          body.style.height = h + 'px'; body.style.opacity = '1'; body.style.transform = 'none';
        });
        setTimeout(function () { body.style.height = ''; body.style.transition = ''; }, 320);
      } else {
        body.style.height = body.scrollHeight + 'px';
        raf(function () {
          body.style.transition = 'height .3s cubic-bezier(.22,1,.36,1), opacity .2s';
          body.style.height = '0px'; body.style.opacity = '0';
        });
        d.classList.add('is-closing');
        setTimeout(function () { d.removeAttribute('open'); d.classList.remove('is-closing'); body.style.cssText = ''; }, 300);
      }
    };
    items.forEach(function (d) {
      $('summary', d).addEventListener('click', function (e) {
        e.preventDefault();
        var willOpen = !d.hasAttribute('open') || d.classList.contains('is-closing');
        items.forEach(function (o) { if (o !== d && o.hasAttribute('open')) animate(o, false); });
        animate(d, willOpen);
      });
    });
  });

  /* =====================================================================
     6. Segmented: tanlov pill'i spring bilan siljiydi
  ===================================================================== */
  $$('.segmented').forEach(function (seg) {
    var pill = document.createElement('span');
    pill.className = 'seg-pill';
    seg.insertBefore(pill, seg.firstChild);
    var place = function () {
      var checked = $('input:checked', seg);
      if (!checked) { pill.style.opacity = '0'; return; }
      var lab = checked.closest('label');
      pill.style.opacity = '1';
      pill.style.width = lab.offsetWidth + 'px';
      pill.style.height = lab.offsetHeight + 'px';
      pill.style.transform = 'translate(' + lab.offsetLeft + 'px,' + lab.offsetTop + 'px)';
    };
    seg.classList.add('has-pill');
    $$('input', seg).forEach(function (i) { i.addEventListener('change', place); });
    window.addEventListener('resize', place);
    setTimeout(place, 50);
    document.fonts && document.fonts.ready.then(place);
  });

  /* =====================================================================
     7. "Konsultatsiya" tugmalari: forma shu sahifada bo'lmasa — Aloqa sahifasiga
  ===================================================================== */
  $$('[data-consult]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var target = $('#consultation') || $('#form') || $('[data-lead-form]');
      if (target) {
        e.preventDefault();
        setMenu(false);
        target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
        var input = $('input[name="name"]', target);
        if (input) setTimeout(function () { input.focus({ preventScroll: true }); }, 700);
      } else {
        a.setAttribute('href', ru ? '/ru/contact#form' : '/contact#form');
      }
    });
  });

  /* Yuqoriga */
  $$('[data-to-top]').forEach(function (b) {
    b.addEventListener('click', function (e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); });
  });

  /* =====================================================================
     8. Telefon maskasi va ariza formasi
     Yuborish: tugmada spinner → ✓, forma "Rahmat" holatiga cross-fade; xato — silkinish
  ===================================================================== */
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
  var msg = {
    name: ru ? 'Введите ваше имя.' : 'Ismingizni kiriting.',
    phone: ru ? 'Введите номер телефона полностью.' : 'Telefon raqamni to‘liq kiriting.',
    fail: ru ? 'Не удалось отправить. Попробуйте ещё раз или позвоните нам.' : 'Yuborib bo‘lmadi. Qayta urinib ko‘ring yoki bizga qo‘ng‘iroq qiling.'
  };
  var shake = function (el) { var box = el.closest('.input') || el; box.classList.remove('shake'); void box.offsetWidth; box.classList.add('shake'); };
  $$('[data-lead-form]').forEach(function (form) {
    var err = $('[data-form-error]', form);
    var showError = function (t) { if (err) { err.textContent = t; err.hidden = !t; } };
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = form.elements.name.value.trim();
      var phone = form.elements.phone.value.replace(/\D/g, '');
      if (!name) { showError(msg.name); shake(form.elements.name); form.elements.name.focus(); return; }
      if (phone.length < 12) { showError(msg.phone); shake(form.elements.phone); form.elements.phone.focus(); return; }
      showError('');
      var btn = $('button[type="submit"]', form);
      if (btn) { btn.disabled = true; btn.classList.add('is-loading'); }
      fetch(form.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
        credentials: 'same-origin'
      }).then(function (r) {
        if (!r.ok) throw new Error(r.status);
        if (btn) { btn.classList.remove('is-loading'); btn.classList.add('is-done'); }
        setTimeout(function () { form.classList.add('is-sent'); form.reset(); }, 450);
        if (window.__popupSent) window.__popupSent(form);
      }).catch(function () {
        if (btn) btn.classList.remove('is-loading');
        showError(msg.fail);
      }).then(function () { if (btn) btn.disabled = false; });
    });
  });

  /* =====================================================================
     9. Footer: katta "Buxgalter" yozuvi scroll bilan y 80 → 0
  ===================================================================== */
  var word = $('.wordmark span');
  if (word && !reduce) {
    var updWord = function () {
      var r = word.getBoundingClientRect();
      var p = Math.min(1, Math.max(0, (window.innerHeight - r.top) / (r.height + window.innerHeight * 0.4)));
      word.style.transform = 'translateY(' + (80 * (1 - p)) + 'px)';
    };
    window.addEventListener('scroll', function () { raf(updWord); }, { passive: true });
    updWord();
  }

  /* =====================================================================
     10. Karusellar (Bitiruvchilar, mobil swipe + snap + nuqtalar)
  ===================================================================== */
  $$('[data-carousel]').forEach(function (wrap) {
    var track = $('[data-track]', wrap);
    if (!track) return;
    var stepBy = function (dir) {
      var item = track.firstElementChild;
      var w = item ? item.getBoundingClientRect().width + 24 : 300;
      track.scrollBy({ left: dir * w, behavior: reduce ? 'auto' : 'smooth' });
    };
    var p = $('[data-car-prev]', wrap), n = $('[data-car-next]', wrap);
    if (p) p.addEventListener('click', function () { stepBy(-1); });
    if (n) n.addEventListener('click', function () { stepBy(1); });
    // Sichqoncha bilan tortish
    var down = false, sx = 0, sl = 0, moved = false;
    track.addEventListener('pointerdown', function (e) { if (e.pointerType !== 'mouse') return; down = true; moved = false; sx = e.clientX; sl = track.scrollLeft; track.classList.add('is-drag'); });
    window.addEventListener('pointermove', function (e) { if (!down) return; var dx = e.clientX - sx; if (Math.abs(dx) > 4) moved = true; track.scrollLeft = sl - dx; });
    window.addEventListener('pointerup', function () { if (!down) return; down = false; track.classList.remove('is-drag'); });
    track.addEventListener('click', function (e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
  });
  // Mobil: gorizontal qator nuqtalari
  $$('[data-snap]').forEach(function (row) {
    var dots = document.createElement('div');
    dots.className = 'snap-dots';
    var items = Array.prototype.slice.call(row.children);
    items.forEach(function (_, i) { var d = document.createElement('i'); if (!i) d.className = 'on'; dots.appendChild(d); });
    row.parentNode.insertBefore(dots, row.nextSibling);
    row.addEventListener('scroll', function () {
      var i = Math.round(row.scrollLeft / Math.max(1, row.scrollWidth / items.length));
      $$('i', dots).forEach(function (d, k) { d.className = k === i ? 'on' : ''; });
    }, { passive: true });
  });

  /* Kursdan keyingi yo'l: chiziq scroll bilan chapdan o'ngga chiziladi, yetgan bosqich nuqtasi sariq */
  $$('[data-career]').forEach(function (box) {
    var line = $('.career__line i', box), lvls = $$('.career__lvl', box);
    var upd = function () {
      var r = box.getBoundingClientRect();
      var p = Math.min(1, Math.max(0, (window.innerHeight * 0.85 - r.top) / (r.height + window.innerHeight * 0.35)));
      if (line) line.style.width = (p * 100) + '%';
      lvls.forEach(function (l, i) { l.classList.toggle('is-reached', p >= i / Math.max(1, lvls.length - 1) - 0.02); });
    };
    window.addEventListener('scroll', function () { raf(upd); }, { passive: true });
    upd();
  });

  /* =====================================================================
     11. Video modal (bitiruvchilar)
  ===================================================================== */
  var modal = null;
  var closePopup = function () {
    $$('.popup.is-open').forEach(function (p) { p.classList.remove('is-open'); setTimeout(function () { if (p.dataset.temp) p.remove(); }, 320); });
    document.documentElement.style.overflow = '';
  };
  window.__closePopup = closePopup;
  $$('[data-video]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      var url = b.getAttribute('data-video');
      if (!url) return;
      e.preventDefault();
      var m = url.match(/(?:youtu\.be\/|v=|shorts\/)([\w-]{11})/);
      var src = m ? 'https://www.youtube.com/embed/' + m[1] + '?autoplay=1&rel=0' : url;
      modal = document.createElement('div');
      modal.className = 'popup popup--video';
      modal.dataset.temp = '1';
      modal.innerHTML = '<div class="popup-overlay" data-popup-close></div><div class="popup-box"><button class="icon-btn popup-close" type="button" data-popup-close aria-label="Close">×</button><div class="video-frame"><iframe src="' + src + '" allow="autoplay; encrypted-media; fullscreen" allowfullscreen></iframe></div></div>';
      document.body.appendChild(modal);
      raf(function () { modal.classList.add('is-open'); });
      document.documentElement.style.overflow = 'hidden';
    });
  });
  document.addEventListener('click', function (e) { if (e.target.closest('[data-popup-close]')) { e.preventDefault(); closePopup(); } });

  /* =====================================================================
     12. Sahifa o'tishi: fade 300 ms
  ===================================================================== */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
    var href = a.getAttribute('href');
    if (!href || href.charAt(0) === '#' || a.target === '_blank' || a.hasAttribute('download') || /^(tel:|mailto:|javascript:)/.test(href)) return;
    var url = new URL(a.href, location.href);
    if (url.origin !== location.origin || (url.pathname === location.pathname && url.hash)) return;
    if (reduce) return;
    e.preventDefault();
    document.body.classList.add('is-leaving');
    setTimeout(function () { location.href = a.href; }, 200);
  });
  window.addEventListener('pageshow', function (e) { if (e.persisted) document.body.classList.remove('is-leaving'); });
})();
