# Motion system (Figma: "Motion system" + sahifa "Motion notes")

Barcha animatsiyalar `public/js/site.js` va `public/css/site.css` (MOTION bo‘limi) da.

## Global
- Easing: smooth `cubic-bezier(.22,1,.36,1)`; spring (hover/press/toggle) `cubic-bezier(.34,1.56,.64,1)`; progress/marquee linear.
- Davomiylik: 150 ms rang/opacity · 250 ms tugma · 500 ms karta · 700 ms section · 900 ms hero.
- Reveal (default): opacity 0→1, y 32→0, blur 8→0; element 20 % ko‘ringanda, bir marta. Tartib: sarlavha → tavsif → kontent. Reduced motion: faqat opacity. Mobil: y 16.
- Stagger: kartalar 80 ms, ro‘yxat/FAQ/chiplar 60 ms, hero 120 ms.
- Button: hover — o‘qli doira −45°→0° va x+4, label x−2; yellow gradient siljiydi (400 ms); ink — o‘q chiqib, qarshi tomondan kiradi; press scale .97; loading — spinner → ✓.
- Icon button hover scale 1.06, ikonka y−2; Close hover 90°. Nav link hover pill 150 ms; aktiv pill bosilgan havolaga siljiydi.
- Header: ochilganda y −20→0 (500 ms); scroll > 80 px — Fixed (oq, blur, soya, kichik logo); pastga — yashirinadi, yuqoriga — qaytadi (300 ms).
- Kartalar: hover y−6, soya, rasm scale 1.05 (600 ms); o‘q −45°→0°; maqola sarlavhasi ostida chiziq o‘sadi; press .99.
- Badge nuqtasi pulse (1→2.2, 1.6 s loop); stat count-up (1.2 s).
- Input focus — sariq chegara 150 ms; xato — x ±4 silkinish 300 ms; segmented pill spring; FAQ — height 300 ms, + 45°, javob fade + y8, bittasi ochiq.
- Sahifa o‘tishi: fade 300 ms + kontent y16.
- Popup: overlay fade + blur 16 (300 ms), modal scale .96→1 y24 (spring); mobil — pastdan sheet.

## Breakpointlar
Desktop 1440 (asosiy), Laptop 1200, Tablet 810, Phone 390. Kontent max 1440.
