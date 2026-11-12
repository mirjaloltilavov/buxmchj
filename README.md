# Buxgalter MChJ — sayt va admin panel

Sayt manzili: **https://new.buxmchj.uz**, admin panel: **https://new.buxmchj.uz/admin**

- **Texnologiya:** Laravel 12, Filament 3 (admin panel), MySQL, PHP 8.3
- **Tillar:** o‘zbekcha (`/`) va ruscha (`/ru/...`)
- **Dizayn:** Figma — *Buxgalter / Web Design*

`vendor/` papkasi repoga qo‘shilgan. Shuning uchun serverda `composer` ham, SSH ham kerak emas.

## Admin panelda nimalar boshqariladi

| Bo‘lim | Nima qiladi |
|---|---|
| Arizalar | Saytdagi formalardan kelgan arizalar: holat, izoh, mas’ul xodim, manba (UTM), Excel (CSV) eksport |
| Statistika | Davr bo‘yicha arizalar soni, konversiya, javob vaqti, qiziqish/holat/manba/sahifa kesimlari, xodimlar natijasi |
| Kurslar | Kurs kartochkalari, kurs sahifasi, dastur, tariflar, SEO |
| Xizmatlar / Xizmat tariflari | Buxgalteriya xizmati bloklari va narxlari |
| Maqolalar / Kategoriyalar | Blog, matn muharriri, rasm yuklash |
| Ustozlar, Savol-javoblar, Filiallar | Tegishli bloklar |
| Hero slaydlar | Bosh sahifadagi slayder, suzuvchi kartochkalar |
| Instagram postlar | Bosh sahifa va Aloqa sahifasidagi lenta |
| Sahifa matnlari | Barcha sarlavha va matnlar (bo‘sh qolsa dizayndagi matn chiqadi) |
| Sayt sozlamalari | Telefon, ijtimoiy tarmoqlar, arizalar xabarnomasi (Telegram + email), SEO, OG rasm, favicon, Google Analytics / Search Console, huquqiy matnlar, popuplar |
| Foydalanuvchilar | Xodimlar, rollar (Bosh admin / Admin / Kontent muharriri / Menejer), bloklash, parol |
| Faollik jurnali | Kim nimani qachon o‘zgartirgani |

### Arizalar xabarnomasi

*Sayt sozlamalari → Arizalar xabarnomasi* bo‘limida ikki kanal bor. Ikkalasini ham kartochkadagi tugma bilan yoqish yoki o‘chirish mumkin, “Sinov” tugmasi esa sozlama to‘g‘riligini darhol tekshiradi.

- **Telegram bot:** @BotFather'da bot yarating, uni guruhga qo‘shing, bot token va chat ID ni kiriting. Bir nechta chat bo‘lsa vergul bilan ajratiladi.
- **Email:** qabul qiluvchi manzillar (vergul bilan). Yuborish usuli — *Server pochtasi (Plesk)* yoki *SMTP*: Gmail uchun `smtp.gmail.com`, 587, TLS va ilova paroli; Plesk pochtasi uchun `buxmchj.uz`, 465, SSL.

- **Google Sheets:** kartochkadagi yo‘riqnoma bo‘yicha jadvalga Apps Script qo‘yiladi (kod admin panelda tayyor, “Nusxalash” tugmasi bor) va “Veb-ilova” havolasi kiritiladi. Har bir ariza jadvalga yangi qator bo‘lib tushadi.
- **amoCRM:** manzil (`sizning.amocrm.ru`) va uzoq muddatli token kiritiladi, so‘ng voronka, bosqich va mas’ul xodim ro‘yxatdan tanlanadi. Har bir arizadan sdelka, telefonli kontakt va izoh yaratiladi.
- Eski arizalarni yuborish: *Arizalar → Integratsiyalar* tugmasi (yuborilmaganlarni) yoki belgilangan arizalar uchun ommaviy amal.

Xabarnoma va integratsiyalar ariza saqlangandan keyin, mijozga javob qaytgach ishlaydi — biror xizmat ishlamay qolsa ham ariza saytda saqlanadi.

Arizalar statistikasi va manbasi (Google, Instagram, reklama kampaniyasi va h.k.) saytga birinchi kirishdagi UTM belgilar va qaysi saytdan kelgani bo‘yicha avtomatik aniqlanadi.

## SEO

Avtomatik ishlaydi, qo‘shimcha sozlash shart emas:

- **`/sitemap.xml`** — barcha sahifalar, faol kurslar va chop etilgan maqolalar (uz + ru, hreflang bilan, kurs/maqolada `lastmod`).
- **`/robots.txt`** — `/admin` yopiq, sitemap ko‘rsatilgan, Yandex uchun `Clean-param` (utm belgilari bilan kelgan manzillar takror sahifa hisoblanmaydi).
- **Canonical** — faqat ma’noli parametrlar qoladi (`page`, `category`); `utm_*` va boshqalar tushib qoladi. Blog qidiruvi (`?q=`) — `noindex, follow`.
- **Schema.org (JSON-LD)** — bosh sahifa va Aloqa: tashkilot (`AccountingService`: manzil, telefon, koordinata — *Filiallar* dan, ijtimoiy tarmoqlar — sozlamalardan) + `WebSite`; kurs: `Course`; maqola: `BlogPosting`; ichki sahifalar: `BreadcrumbList`.
- **404** — sayt dizaynidagi sahifa, asosiy bo‘limlarga havolalar bilan, `noindex`.

Admin’dan: *Sayt sozlamalari → SEO va brending* (sarlavha, tavsif, OG rasm, favicon) va *Analytics va saytni tasdiqlash* (Google Analytics, Search Console, **Yandex Webmaster**, **Bing** meta-teglari va tasdiqlash fayllari).

Ishga tushirilgach: Search Console va Yandex Webmaster’da saytni tasdiqlang va `https://buxmchj.uz/sitemap.xml` ni yuboring. `.env` da `APP_URL=https://buxmchj.uz` bo‘lishi kerak (havolalar https bilan chiqadi). Plesk’da *Hosting Settings → Preferred domain* ni bitta variantga (`buxmchj.uz`) qo‘ying — `www` bilan takror sahifa bo‘lmasin.

## Foydalanuvchilar va rollar

Admin panel → **Tizim → Foydalanuvchilar** (faqat *Bosh admin* ko‘radi). Har bir xodimga alohida **login** (masalan, `OxunjonAsrorov`) va parol beriladi; email ixtiyoriy. Panelga login yoki email bilan kirish mumkin. Eski foydalanuvchilarga login avtomatik beriladi: email’ning @ dan oldingi qismi (masalan, `admin@...` → `admin`).

| Rol | Nimaga kira oladi |
|---|---|
| Bosh admin | Hammasi: kontent, arizalar, sozlamalar, foydalanuvchilar, faollik jurnali |
| Admin | Kontent, arizalar, sozlamalar, jurnal. Foydalanuvchilarni boshqarmaydi |
| Kontent muharriri | Faqat sayt kontenti (kurslar, xizmatlar, maqolalar, sahifa matnlari va h.k.) |
| Menejer | Faqat arizalar: ko‘rish, holat, izoh, “O‘zimga olish” |

- **Bloklash:** bloklangan foydalanuvchi panelga kira olmaydi. O‘zingizni va oxirgi bosh adminni bloklab/o‘chirib bo‘lmaydi.
- **Parolni tiklash:** foydalanuvchi menyusidagi “Parolni tiklash” yangi parolni bir marta ko‘rsatadi.
- **Profil:** har kim o‘ng yuqoridagi menyudan o‘z ismi, rasmi, telefoni va parolini o‘zgartiradi.
- **Arizalar:** yangi ariza kelganda arizalar bilan ishlaydigan hamma xodimga panel ichida bildirishnoma (qo‘ng‘iroqcha) keladi. Arizani xodimga biriktirish mumkin (“Menga biriktirilgan” tab).
- **Faollik jurnali:** kim, qachon, nimani qo‘shdi/o‘zgartirdi/o‘chirdi va kim tizimga kirdi.

Yangilanishdan keyin birinchi `site:install` mavjud birinchi foydalanuvchini *Bosh admin* qiladi.

## Serverga birinchi o‘rnatish (Plesk, pscloud)

1. **PHP versiyasi:** *new.buxmchj.uz → PHP Settings* bo‘limida **PHP 8.3** ni tanlang.
2. **Hujjat ildizi:** *Hosting Settings → Document root* ni `/new.buxmchj.uz/public` ga o‘zgartiring. O‘zgartirilmasa ham ildizdagi `.htaccess` so‘rovlarni `public/` ga yo‘naltiradi, lekin `/public` qilgan ma’qul.
3. **Ma’lumotlar bazasi:** *Databases → Add Database* bo‘limida MySQL baza va foydalanuvchi yarating.
4. **`.env` fayli:** File Manager'da `new.buxmchj.uz` papkasida `.env.example` dan nusxa olib, uni `.env` deb nomlang. Ichida `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `ADMIN_EMAIL` va `ADMIN_PASSWORD` ni to‘ldiring.
5. **O‘rnatish buyrug‘i:** *Scheduled Tasks → Add Task → Run a PHP script* bo‘limini oching:
   - Script path: `new.buxmchj.uz/artisan`
   - Arguments: `site:install`
   - PHP: 8.3
   - **Run Now** ni bosing. Birinchi marta APP_KEY yaratiladi. **Run Now** ni yana bir marta bosing: migratsiyalar, boshlang‘ich kontent va admin yaratiladi.
6. `https://buxmchj.uz/admin` ga `.env` dagi email (yoki `admin` logini) va parol bilan kiring.

## Yangilash

GitHub'ga `git push` qilinganda webhook orqali Plesk kodni avtomatik tortib oladi. Yangi migratsiya yoki o‘zgartirilgan sozlama bo‘lsa, Scheduled Tasks'dagi `site:install` vazifasini **Run Now** bilan qayta ishga tushiring. Takroriy ishga tushirish xavfsiz: mavjud kontent o‘chirilmaydi.

Admin yuklagan fayllar `public/uploads/` papkasida saqlanadi. Bu papka git'ga kirmaydi va deploy paytida o‘chmaydi.

## Mahalliy ishga tushirish

```bash
cp .env.example .env   # DB_CONNECTION=sqlite qilib, database/database.sqlite yarating
php artisan site:install
php artisan serve
```
