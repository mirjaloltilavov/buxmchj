/* Buxgalter MChJ — sayt skriptlari. Animatsiyalar Figma "Motion system" va sahifa "Motion notes" bo'yicha. */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isMobile = function () { return window.matchMedia('(max-width: 810px)').matches; };
  var ru = document.documentElement.lang === 'ru';
  var raf = window.requestAnimationFrame || function (f) { return setTimeout(f, 16); };

  /* Tugma yozuvi: hover'da tepaga chiqib, pastdan qaytib kiradi (matn .roll ichiga o'raladi) */
  var rollify = function (btn) {
    if (btn.hasAttribute('data-rolled')) return;
    btn.setAttribute('data-rolled', '');
    var box = btn.querySelector(':scope > .label') || btn;
    Array.prototype.slice.call(box.childNodes).forEach(function (n) {
      if (n.nodeType !== 3 || !n.textContent.trim()) return;
      var w = document.createElement('span'), inner = document.createElement('span');
      w.className = 'roll'; inner.textContent = n.textContent.trim(); w.appendChild(inner);
      box.replaceChild(w, n);
    });
  };
  var rollAll = function (root) { $$('.btn-pill, .btn', root).forEach(rollify); };
  rollAll(document);

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
      el.style.setProperty('--d', Math.round((rule[3] + i * rule[2]) * 1.4) + 'ms');
      groups.set(p, i + 1);
    });
  });
  // Umumiy qoida: bo'limlardagi appear berilmagan har bir blok ham chiqadi (hech narsa "qotib" turmasin)
  var SKIP = 'input,.sr-only,[data-page-hero],[data-a-hero],[data-c-hero],.hero,.p-hero,.a-hero,.c-hero,#instagram,[data-bfeat],.snap-dots,script,style,template';
  $$('main > section, main > div > section').forEach(function (sec) {
    if (sec.matches(SKIP)) return;
    var walk = function (el, depth) {
      var i = 0;
      Array.prototype.slice.call(el.children).forEach(function (ch) {
        if (ch.hasAttribute('data-reveal') || ch.matches(SKIP) || ch.hidden) return;
        var pos = getComputedStyle(ch).position;
        if (pos === 'absolute' || pos === 'fixed') return;
        if (ch.querySelector('[data-reveal]')) { if (depth < 5) walk(ch, depth + 1); return; }
        if (!ch.textContent.trim() && !ch.querySelector('img,svg,iframe,video,input,button')) return;
        ch.setAttribute('data-reveal', 'up');
        if (!ch.style.getPropertyValue('--d')) ch.style.setProperty('--d', (i * 110) + 'ms');
        i++;
      });
    };
    walk(sec, 0);
  });
  $$('[data-stagger]').forEach(function (box) {
    var step = parseInt(box.getAttribute('data-stagger'), 10) || 80;
    var base = parseInt(box.getAttribute('data-stagger-base'), 10) || 0;
    $$('[data-reveal]', box).forEach(function (el, i) {
      if (!el.style.getPropertyValue('--d')) el.style.setProperty('--d', Math.round((base + i * step) * 1.4) + 'ms');
    });
  });
  // ✓ belgilarni chizish (stroke draw, 60 ms stagger)
  $$('.checks').forEach(function (list) {
    list.classList.add('draw-list');
    $$('li', list).forEach(function (li, i) { li.classList.add('draw'); li.style.setProperty('--d', (200 + i * 60) + 'ms'); });
  });

  var revealEls = $$('[data-reveal], .draw');
  var io = null;
  if ('IntersectionObserver' in window && !reduce) {
    io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.2, rootMargin: '0px 0px -40px 0px' });
    // clip-path bilan yashirilgan element brauzer uchun "ko'rinmaydi" — uning o'rniga ota-elementi kuzatiladi
    var ioClip = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        (en.target._clipKids || []).forEach(function (k) { k.classList.add('is-in'); });
        ioClip.unobserve(en.target);
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    var baseObserve = io.observe.bind(io);
    io.observe = function (el) {
      if (/^clip/.test(el.getAttribute('data-reveal') || '') && el.parentElement) {
        var par = el.parentElement;
        (par._clipKids = par._clipKids || []).push(el);
        ioClip.observe(par);
      } else baseObserve(el);
    };
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
  // Keyin qo'shilgan (AJAX) elementlar uchun
  function observeReveal(root) {
    $$('[data-reveal]', root).forEach(function (el) {
      if (io) io.observe(el); else el.classList.add('is-in');
    });
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
     - Autoplay 6 s, tab progress linear (hover autoplay'ni to'xtatmaydi; faqat tab yashirin bo'lsa pauza)
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
      var target = $('#consultation') || $('#form') || $('[data-lead-form]:not(.promo__form)');
      if (window.__closePopup && a.closest('.popup')) window.__closePopup();
      if (target) {
        e.preventDefault();
        setMenu(false);
        var want = a.getAttribute('data-interest');
        if (want) {
          var r = $('input[name="interest"][value="' + want + '"]', target);
          if (r && !r.checked) { r.checked = true; r.dispatchEvent(new Event('change', { bubbles: true })); }
        }
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
      var nameEl = form.querySelector('[name="name"]');
      var name = nameEl ? nameEl.value.trim() : '-';
      var phone = form.elements.phone.value.replace(/\D/g, '');
      if (!name) { showError(msg.name); shake(nameEl); nameEl.focus(); return; }
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

  /* Kurs dasturi — sticky: ko'rinib turgan modulga mos menyu bandi aktiv, indikator siljiydi; modul rasmi parallax ±20 */
  $$('[data-program]').forEach(function (box) {
    var links = $$('[data-module-link]', box), mods = $$('[data-module]', box), ind = $('.program__ind', box);
    var setCur = function (i) {
      links.forEach(function (a, k) { a.classList.toggle('is-active', k === i); });
      mods.forEach(function (m, k) { m.classList.toggle('is-current', k === i); });
      var a = links[i];
      if (ind && a) ind.style.transform = 'translateY(' + (a.offsetTop + (a.offsetHeight - 24) / 2) + 'px)';
    };
    var upd = function () {
      var mid = window.innerHeight * 0.45, cur = 0;
      mods.forEach(function (m, k) {
        var r = m.getBoundingClientRect();
        if (r.top < mid) cur = k;
        var img = $('[data-parallax]', m);
        if (img && !reduce) {
          var p = (r.top + r.height / 2 - window.innerHeight / 2) / window.innerHeight;
          img.style.transform = 'translateY(' + Math.max(-20, Math.min(20, -p * 40)) + 'px)';
        }
      });
      setCur(cur);
    };
    links.forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        var t = mods[parseInt(a.getAttribute('data-module-link'), 10)];
        if (t) window.scrollTo({ top: t.getBoundingClientRect().top + window.scrollY - 140, behavior: reduce ? 'auto' : 'smooth' });
      });
    });
    window.addEventListener('scroll', function () { raf(upd); }, { passive: true });
    window.addEventListener('resize', upd);
    setTimeout(upd, 60);
  });

  /* Tariflar: "Bir martada / Bo'lib to'lash" — narx raqami aylanib almashadi, eski narx ustidan chiziq chiziladi */
  $$('[data-pay-toggle]').forEach(function (tg) {
    var section = tg.closest('section') || document;
    var fmt = function (n) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
    var apply = function (mode) {
      $$('[data-price]', section).forEach(function (pr) {
        var b = $('b', pr), val = $('[data-price-value]', pr), lbl = $('[data-price-label]', pr), full = $('.pcard__full', pr);
        var v = parseInt(pr.getAttribute(mode === 'split' ? 'data-split' : 'data-once'), 10) || 0;
        b.classList.add('is-rolling');
        setTimeout(function () {
          val.textContent = fmt(v);
          lbl.textContent = lbl.getAttribute(mode === 'split' ? 'data-split-label' : 'data-once-label');
          b.classList.remove('is-rolling');
        }, 200);
        if (full) full.style.visibility = mode === 'split' ? 'hidden' : 'visible';
      });
    };
    $$('input', tg).forEach(function (i) { i.addEventListener('change', function () { apply(i.value); }); });
  });
  // eski narx ustidan chiziq — ko'ringanda
  if ('IntersectionObserver' in window) {
    var sio = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-strike'); sio.unobserve(e.target); } }); }, { threshold: 0.6 });
    $$('.pcard__full').forEach(function (el) { sio.observe(el); });
  }

  /* Narx kalkulyatori: baza narxlar tariflardan, slayder qo'shimchasi taxminiy.
     Natija raqam aylanishi bilan yangilanadi (400 ms), o'zgargan qator sariq yonib o'chadi (300 ms). */
  $$('[data-calc]').forEach(function (calc) {
    var base = {};
    try { base = JSON.parse(calc.getAttribute('data-base')); } catch (e) {}
    // Qo'shimcha: 10 kishidan ortiq har bir xodim va 150 dan ortiq har 10 operatsiya uchun (taxminiy)
    var EMP_FREE = 10, EMP_STEP = 60000, OPS_FREE = 150, OPS_STEP = 60000;
    var T = ru
      ? { yatt: 'ИП', llc: 'ООО', yes: 'Да', no: 'Нет', vat: 'плательщик НДС', novat: 'без НДС', local: 'Внутренняя торговля и услуги', imp: 'Импорт-экспорт', ppl: 'чел.', ops: 'в мес.' }
      : { yatt: 'YaTT', llc: 'MChJ', yes: 'Ha', no: 'Yo‘q', vat: 'QQS to‘lovchisi', novat: 'QQSsiz', local: 'Ichki savdo va xizmat', imp: 'Import-eksport', ppl: 'kishi', ops: 'ta / oy' };
    var fmt = function (n) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
    var val = function (n) { var i = $('input[name="' + n + '"]:checked', calc) || $('input[name="' + n + '"]', calc); return i ? i.value : ''; };
    var res = function (k) { return $('[data-res="' + k + '"]', calc); };
    var last = {}, first = true;
    var flash = function (k) { var row = $('[data-row="' + k + '"]', calc); if (!row || first) return; row.classList.remove('flash'); void row.offsetWidth; row.classList.add('flash'); setTimeout(function () { row.classList.remove('flash'); }, 300); };
    var setText = function (k, t) { var el = res(k); if (el && el.textContent !== t) { el.textContent = t; flash(k); } };
    var upd = function () {
      var org = val('org'), vat = val('vat') === '1', kind = val('kind');
      var emp = parseInt(val('emp'), 10) || 0, ops = parseInt(val('ops'), 10) || 0;
      var sum = kind === 'import' ? base.import : (org === 'yatt' ? base.yatt : (vat ? base.llc_vat : base.llc));
      if (org === 'yatt' && vat) sum = Math.max(sum, base.llc); // QQS to'lovchi YaTT
      sum += Math.max(0, emp - EMP_FREE) * EMP_STEP + Math.ceil(Math.max(0, ops - OPS_FREE) / 10) * OPS_STEP;
      sum = Math.round(sum / 100000) * 100000;
      // slayder to'ldirilishi va qiymatlari
      $$('input[type="range"]', calc).forEach(function (r) { r.style.setProperty('--p', ((r.value - r.min) / (r.max - r.min) * 100) + '%'); });
      var empTxt = emp + (emp >= 50 ? '+' : '') + ' ' + T.ppl, opsTxt = '~' + ops + (ops >= 500 ? '+' : '') + ' ' + (ru ? 'шт' : 'ta');
      var oe = $('[data-out="emp"]', calc), oo = $('[data-out="ops"]', calc);
      if (oe) oe.textContent = empTxt; if (oo) oo.textContent = opsTxt;
      setText('org', org === 'yatt' ? T.yatt : T.llc);
      setText('vat', vat ? T.yes : T.no);
      setText('kind', kind === 'import' ? T.imp : T.local);
      setText('emp', empTxt);
      setText('ops', '~' + ops + ' ' + T.ops);
      var badge = res('badge'); if (badge) badge.textContent = (org === 'yatt' ? T.yatt : T.llc) + ' · ' + (vat ? T.vat : T.novat);
      var s = res('sum');
      if (s && last.sum !== sum) {
        var box = s.parentNode;
        if (first) s.textContent = fmt(sum);
        else { box.classList.add('is-rolling'); setTimeout(function () { s.textContent = fmt(sum); box.classList.remove('is-rolling'); }, 200); }
        last.sum = sum;
      }
      first = false;
    };
    $$('input', calc).forEach(function (i) {
      i.addEventListener('input', upd);
      i.addEventListener('change', upd);
      if (i.type === 'range') i.addEventListener('input', function () { var o = $('[data-out="' + i.name + '"]', calc); if (o) { o.classList.add('pop'); clearTimeout(o._t); o._t = setTimeout(function () { o.classList.remove('pop'); }, 200); } });
    });
    upd();
  });

  /* Ishni qanday boshlaymiz — sticky: progress chizig'i scroll bilan to'ladi, aktiv qadam scale 1.02 + sariq, o'tganlari ✓ */
  $$('[data-steps]').forEach(function (box) {
    var steps = $$('[data-step]', box), line = $('.steps-line i', box);
    var upd = function () {
      var mid = window.innerHeight * 0.5, cur = -1;
      steps.forEach(function (s, k) { if (s.getBoundingClientRect().top < mid) cur = k; });
      steps.forEach(function (s, k) { s.classList.toggle('is-active', k === cur); s.classList.toggle('is-done', k < cur); });
      if (line) {
        var wrap = line.parentNode.getBoundingClientRect();
        var p = Math.min(1, Math.max(0, (mid - wrap.top) / wrap.height));
        line.style.height = (p * 100) + '%';
      }
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
     13. Maqolalar (Figma Motion notes):
     - Qidiruv placeholder'i har 3 s da almashadi (yozilayotgandek); fokusda soya kattalashadi
     - Tanlangan maqola: muqova chapdan o'ngga clip-reveal 800 ms, matn 120 ms stagger
     - Filtr: sticky top 96 + soya; chip almashganda kartalar 150 ms fade-out, yangilari 60 ms stagger;
       son "roll" bo'ladi. "Ko'proq" — skeleton shimmer, keyin kartalar stagger bilan.
     - Telegram CTA: qog'oz samolyot uchib kiradi (x−40, y40 → 0)
  ===================================================================== */
  var bs = $('[data-bsearch]');
  if (bs) {
    var bInput = $('input[type=search]', bs), bPh = $('.bsearch__ph', bs);
    var hints = [];
    try { hints = JSON.parse(bInput.getAttribute('data-hints') || '[]'); } catch (e) {}
    if (hints.length && bPh && !reduce) {
      bs.classList.add('has-hints');
      var hi = 0, typer;
      var typeHint = function (text) {
        clearInterval(typer);
        var n = 0;
        typer = setInterval(function () {
          n++; bPh.textContent = text.slice(0, n);
          if (n >= text.length) clearInterval(typer);
        }, 45);
      };
      typeHint(bInput.getAttribute('placeholder'));
      setInterval(function () {
        if (document.activeElement === bInput || bInput.value) return;
        typeHint(hints[hi++ % hints.length]);
      }, 3000);
    }
  }

  $$('[data-bfeat], .tg-cta').forEach(function (el) {
    if (!io) { el.classList.add('is-in'); return; }
    if (!el.hasAttribute('data-reveal')) io.observe(el);
  });

  var blog = $('[data-blog]');
  if (blog) {
    var bf = $('[data-bfilter]', blog), hdr = $('.site-header');
    var onBScroll = function () {
      var hidden = hdr && hdr.classList.contains('is-hidden');
      var t = hidden ? (isMobile() ? 8 : 16) : (isMobile() ? 76 : 96);
      bf.style.top = t + 'px';
      bf.classList.toggle('is-stuck', bf.getBoundingClientRect().top <= t + 1 && window.scrollY > 200);
    };
    window.addEventListener('scroll', onBScroll, { passive: true });
    onBScroll();

    var results = $('[data-bresults]', blog);
    var countEl = $('[data-bcount]', blog);
    var drop = function (el) { if (el && el.parentNode) el.parentNode.removeChild(el); };
    var swap = function (html, mode) {
      var doc = new DOMParser().parseFromString(html, 'text/html');
      var nr = $('[data-bresults]', doc), nc = $('[data-bcount]', doc), nf = $('[data-bfilter] .bfilter__chips', doc);
      if (!nr) return false;
      if (mode === 'append') {
        var grid = $('[data-bgrid]', results);
        $$('.acard--sk', grid).forEach(drop);
        $$('[data-bgrid] > .acard', nr).forEach(function (c, i) {
          c.style.setProperty('--d', (i % 3) * 60 + 'ms'); grid.appendChild(c);
        });
        drop($('.bmore', results));
        var newMore = $('.bmore', nr);
        if (newMore) results.appendChild(newMore);
        observeReveal(grid); rollAll(results);
      } else {
        results.innerHTML = nr.innerHTML;
        if (nf) $('.bfilter__chips', bf).innerHTML = nf.innerHTML;
        if (nc && countEl && countEl.textContent !== nc.textContent) {
          countEl.classList.remove('is-roll'); void countEl.offsetWidth; countEl.classList.add('is-roll');
          setTimeout(function () { countEl.textContent = nc.textContent; }, 160);
        }
        observeReveal(results); rollAll(results);
      }
      return true;
    };
    var getHtml = function (url) {
      return fetch(url, { headers: { 'X-Requested-With': 'fetch' } }).then(function (r) {
        if (!r.ok) throw new Error(r.status); return r.text();
      });
    };

    blog.addEventListener('click', function (e) {
      var chip = e.target.closest('[data-fchip]');
      var more = e.target.closest('[data-bmore]');
      if (!chip && !more) return;
      if (e.metaKey || e.ctrlKey || e.shiftKey) return;
      e.preventDefault();
      e.stopPropagation();
      var url = (chip || more).href.split('#')[0];
      if (chip) {
        if (chip.classList.contains('is-active')) return;
        $$('[data-fchip]', bf).forEach(function (c) { c.classList.toggle('is-active', c === chip); });
        results.classList.add('is-out');
        var wait = new Promise(function (r) { setTimeout(r, 150); });
        Promise.all([getHtml(url), wait]).then(function (res) {
          if (!swap(res[0], 'replace')) { location.href = url; return; }
          results.classList.remove('is-out');
          history.pushState(null, '', url + '#all');
          var y = blog.getBoundingClientRect().top + window.scrollY - 120;
          if (window.scrollY > y) window.scrollTo({ top: y, behavior: reduce ? 'auto' : 'smooth' });
        }).catch(function () { location.href = url; });
      } else {
        if (more.classList.contains('is-loading')) return;
        more.classList.add('is-loading');
        var grid = $('[data-bgrid]', results);
        if (grid) {
          var sk = '<article class="acard acard--sk" aria-hidden="true"><span class="acard__img"></span><div class="acard__body"><span class="sk" style="width:40%"></span><span class="sk" style="height:22px"></span><span class="sk" style="width:80%"></span></div></article>';
          grid.insertAdjacentHTML('beforeend', sk + sk + sk);
        }
        Promise.all([getHtml(url), new Promise(function (r) { setTimeout(r, 400); })]).then(function (res) {
          if (!swap(res[0], 'append')) location.href = url;
        }).catch(function () { location.href = url; });
      }
    });
    window.addEventListener('popstate', function () { if ($('[data-blog]')) location.reload(); });
  }

  /* =====================================================================
     14. Maqola (Figma Motion notes):
     - Yuqorida o'qish progress chizig'i (sariq)
     - Sarlavha so'zma-so'z stagger, muqova pastdan chiqadi va scroll'da parallax
     - Paragraflar ko'ringanda fade-up; iqtibos chizig'i yuqoridan chiziladi
     - Chapda sticky ulashish: havola nusxalanganda "Nusxalandi" tooltip
  ===================================================================== */
  var aHero = $('[data-a-hero]');
  if (aHero) {
    raf(function () { aHero.classList.add('is-entered'); });
    var cov = $('[data-a-parallax]', aHero);
    var prog = $('[data-read-progress]');
    var prose = $('[data-prose]');
    var onA = function () {
      if (cov && !reduce) {
        var r = cov.parentNode.getBoundingClientRect();
        if (r.bottom > 0 && r.top < window.innerHeight) {
          var p = Math.max(0, Math.min(1, (window.innerHeight - r.top) / (window.innerHeight + r.height)));
          cov.style.transform = 'translateY(' + (-p * 13) + '%)';
        }
      }
      if (prog && prose) {
        var pr = prose.getBoundingClientRect();
        var total = pr.height - window.innerHeight * 0.5;
        var done = Math.max(0, Math.min(1, (window.innerHeight * 0.5 - pr.top) / Math.max(1, total)));
        prog.style.setProperty('--p', done.toFixed(4));
      }
    };
    window.addEventListener('scroll', function () { raf(onA); }, { passive: true });
    window.addEventListener('resize', onA);
    onA();
    if (prose) {
      $$(':scope > *', prose).forEach(function (el, i) {
        el.setAttribute('data-reveal', el.tagName === 'FIGURE' ? 'card' : 'up');
        if (i === 0) el.style.setProperty('--d', '120ms');
      });
      observeReveal(prose);
    }
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy]');
    if (btn) {
      e.preventDefault();
      var text = btn.getAttribute('data-copy');
      var ok = function () {
        btn.classList.add('is-copied');
        clearTimeout(btn._t);
        btn._t = setTimeout(function () { btn.classList.remove('is-copied'); }, 1800);
      };
      if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(text).then(ok, ok);
      else {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch (err) {}
        document.body.removeChild(ta); ok();
      }
      return;
    }
    var nat = e.target.closest('[data-share-native]');
    if (nat && navigator.share) {
      e.preventDefault();
      navigator.share({ title: document.title, url: location.href }).catch(function () {});
    }
  });

  /* =====================================================================
     15. Aloqa (Figma Motion notes):
     - Sarlavha 120 ms stagger; 4 ta kontakt plitkasi 80 ms stagger (y 24 → 0, scale .98 → 1)
     - Xarita: pin yuqoridan tushadi (bounce) + to'lqin halqa; filial almashganda karta matni x 16 → 0
  ===================================================================== */
  $$('[data-c-hero]').forEach(function (h) { raf(function () { h.classList.add('is-entered'); }); });

  var br = $('[data-branches]');
  if (br) {
    var list = [];
    try { list = JSON.parse(br.getAttribute('data-branches') || '[]'); } catch (e) {}
    var frame = $('[data-map-frame]', br), body = $('[data-branch-body]', br), pin = $('[data-pin]', br);
    var canvas = $('[data-leaflet]', br), lmap = null, lmark = null;
    var loadLeaflet = function (cb) {
      if (window.L) return cb();
      var css = document.createElement('link'); css.rel = 'stylesheet'; css.href = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css'; document.head.appendChild(css);
      var sc = document.createElement('script'); sc.src = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js'; sc.onload = cb;
      sc.onerror = function () { // Leaflet yuklanmasa — oddiy Google xaritasi (pinsiz emas, o'zining belgisi bilan)
        var b0 = list[0]; if (!b0 || !canvas) return;
        canvas.outerHTML = '<iframe class="c-map__frame" src="' + b0.map + '" loading="lazy" title="Map"></iframe><span class="c-pin" aria-hidden="true" data-pin><i></i></span>';
        canvas = null;
      };
      document.head.appendChild(sc);
    };
    // Karta kartasi chapda turgani uchun nuqta ko'rinadigan (o'ng) qismning o'rtasiga qo'yiladi
    var offsetX = function () { return isMobile() ? 0 : Math.round(br.querySelector('.c-branch').offsetWidth / 2 + 16); };
    var focusAt = function (lat, lng, animate) {
      var z = 16, pt = lmap.project([lat, lng], z).subtract([offsetX(), 0]);
      lmap.setView(lmap.unproject(pt, z), z, { animate: !!animate && !reduce });
    };
    var initMap = function () {
      if (lmap || !canvas) return;
      loadLeaflet(function () {
        var lat = +canvas.getAttribute('data-lat'), lng = +canvas.getAttribute('data-lng');
        lmap = L.map(canvas, { zoomControl: true, scrollWheelZoom: false, attributionControl: true });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19, subdomains: 'abcd', attribution: '© OpenStreetMap © CARTO' }).addTo(lmap);
        var icon = L.divIcon({ className: 'c-marker', html: '<span class="c-pin"><i></i>' + (document.querySelector('template.c-pin-src') || { innerHTML: '' }).innerHTML + '</span>', iconSize: [60, 60], iconAnchor: [30, 60] });
        lmark = L.marker([lat, lng], { icon: icon, keyboard: false }).addTo(lmap);
        focusAt(lat, lng, false);
        // sichqoncha g'ildiragi faqat xaritaga bosilgandan keyin yaqinlashtiradi (sahifa scrolli buzilmasin)
        lmap.on('click focus', function () { lmap.scrollWheelZoom.enable(); });
        lmap.on('mouseout', function () { lmap.scrollWheelZoom.disable(); });
        canvas.closest('.c-map').classList.add('is-in');
        window.addEventListener('resize', function () { lmap.invalidateSize(); });
      });
    };
    if (canvas) {
      if ('IntersectionObserver' in window) {
        var mio = new IntersectionObserver(function (en) { if (en[0].isIntersecting) { mio.disconnect(); initMap(); } }, { rootMargin: '300px' });
        mio.observe(canvas);
      } else initMap();
    }
    $$('[data-branch-tab]', br).forEach(function (tab) {
      tab.addEventListener('click', function () {
        var b = list[+tab.getAttribute('data-branch-tab')];
        if (!b || tab.classList.contains('is-active')) return;
        $$('[data-branch-tab]', br).forEach(function (t) { t.classList.toggle('is-active', t === tab); });
        ['name', 'address', 'hours', 'phone'].forEach(function (k) {
          var el = $('[data-b="' + k + '"]', body);
          if (!el) return;
          el.textContent = b[k] || '';
          var line = el.closest('.c-line');
          if (line) line.hidden = !b[k];
        });
        var r = $('[data-b-href="route"]', body), t = $('[data-b-href="tel"]', body);
        if (r) r.href = b.route;
        if (t) t.href = b.tel;
        body.classList.remove('is-swap'); void body.offsetWidth; body.classList.add('is-swap');
        if (frame) frame.src = b.map;
        if (lmap && b.lat && b.lng) { lmark.setLatLng([b.lat, b.lng]); focusAt(b.lat, b.lng, true); }
        if (pin && !reduce) {
          var m = pin.closest('.c-map');
          m.classList.remove('is-in'); void m.offsetWidth; m.classList.add('is-in');
        }
      });
    });
  }

  /* =====================================================================
     16. Promo popuplar (Figma Motion notes — Popups):
     - Trigger: N s dan keyin yoki exit-intent, sessiyada 1 marta
     - Kurs: rasm clip-reveal, '−20%' stiker −30° → −8° (spring), eski narx chiziladi, yangi narx count-up
     - Countdown har soniya almashadi; Close hover 90°
     - Mobil: pastdan sheet, pastga tortib yopiladi
  ===================================================================== */
  var pad = function (n) { return (n < 10 ? '0' : '') + n; };
  $$('[data-countdown]').forEach(function (cd) {
    var end = Date.parse(cd.getAttribute('data-countdown'));
    var cells = {}; $$('[data-cd]', cd).forEach(function (b) { cells[b.getAttribute('data-cd')] = b; });
    var tick = function () {
      var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
      var v = { d: Math.floor(left / 86400), h: Math.floor(left % 86400 / 3600), m: Math.floor(left % 3600 / 60), s: left % 60 };
      Object.keys(v).forEach(function (k) {
        var el = cells[k], txt = pad(v[k]);
        if (el && el.textContent !== txt) {
          el.textContent = txt;
          if (!reduce && cd.offsetParent) { el.classList.remove('tick'); void el.offsetWidth; el.classList.add('tick'); }
        }
      });
    };
    tick(); setInterval(tick, 1000);
  });

  var promo = $('[data-promo]');
  if (promo) {
    var key = 'promo_seen_' + promo.getAttribute('data-promo');
    var seen = function () { try { return sessionStorage.getItem(key) === '1'; } catch (e) { return false; } };
    var mark = function () { try { sessionStorage.setItem(key, '1'); } catch (e) {} };
    var busy = function () {
      var a = document.activeElement;
      return $('.popup.is-open') || $('.mobile-menu.is-open') || (a && /INPUT|TEXTAREA|SELECT/.test(a.tagName));
    };
    var counted = false;
    var openPromo = function () {
      if (seen() || busy()) return false;
      mark();
      promo.setAttribute('aria-hidden', 'false');
      promo.classList.add('is-open');
      document.documentElement.style.overflow = 'hidden';
      var cnt = $('[data-promo-count]', promo);
      if (cnt && !counted && !reduce) {
        counted = true;
        var to = +cnt.getAttribute('data-promo-count'), from = Math.round(to * 1.25), t0 = null;
        var fmt = function (n) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
        var step = function (ts) {
          if (!t0) t0 = ts + 700;
          var p = Math.max(0, Math.min(1, (ts - t0) / 1000));
          var e = 1 - Math.pow(1 - p, 3);
          cnt.textContent = fmt(Math.round((from + (to - from) * e) / 1000) * 1000);
          if (p < 1) raf(step);
        };
        cnt.textContent = fmt(from);
        raf(step);
      }
      return true;
    };
    var promoDelay = (parseInt(promo.getAttribute('data-delay'), 10) || 15) * 1000;
    if (!seen()) {
      var promoTimer = setTimeout(function retry() { if (!openPromo() && !seen()) promoTimer = setTimeout(retry, 5000); }, promoDelay);
      document.addEventListener('mouseout', function onOut(e) {
        if (e.relatedTarget || e.clientY > 0 || isMobile()) return;
        if (performance.now() < 4000) return;
        if (openPromo()) { clearTimeout(promoTimer); document.removeEventListener('mouseout', onOut); }
      });
    }
    promo.addEventListener('transitionend', function () {
      if (!promo.classList.contains('is-open')) promo.setAttribute('aria-hidden', 'true');
    });

    // Mobil sheet: pastga tortib yopish
    var sheet = $('[data-sheet]', promo), sy = null, dy = 0;
    sheet.addEventListener('touchstart', function (e) {
      if (!isMobile() || sheet.scrollTop > 0) return;
      sy = e.touches[0].clientY; dy = 0; sheet.classList.add('is-dragging');
    }, { passive: true });
    sheet.addEventListener('touchmove', function (e) {
      if (sy === null) return;
      dy = Math.max(0, e.touches[0].clientY - sy);
      sheet.style.setProperty('--drag', dy + 'px');
    }, { passive: true });
    sheet.addEventListener('touchend', function () {
      if (sy === null) return;
      sheet.classList.remove('is-dragging');
      sy = null;
      if (dy > 110) { closePopup(); setTimeout(function () { sheet.style.removeProperty('--drag'); }, 450); }
      else sheet.style.removeProperty('--drag');
    });
  }

})();
