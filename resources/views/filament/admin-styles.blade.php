<style>
    /* Yon menyu: scrollbar ko'rinmaydi, lekin g'ildirak/touch bilan aylanadi */
    .fi-sidebar-nav,.fi-sidebar,.fi-sidebar-nav-groups{scrollbar-width:none;-ms-overflow-style:none}
    .fi-sidebar-nav::-webkit-scrollbar,.fi-sidebar::-webkit-scrollbar,.fi-sidebar-nav-groups::-webkit-scrollbar{display:none;width:0;height:0}
    /* Boshqa joylarda — ingichka, kamdan-kam ko'rinadigan */
    *{scrollbar-width:thin;scrollbar-color:rgba(120,120,130,.35) transparent}
    *::-webkit-scrollbar{width:6px;height:6px}
    *::-webkit-scrollbar-track{background:transparent}
    *::-webkit-scrollbar-thumb{background:rgba(120,120,130,.35);border-radius:6px}
    *::-webkit-scrollbar-thumb:hover{background:rgba(120,120,130,.6)}
</style>
<style>
    .bx-ic{display:inline-block;width:1.4rem;height:1.4rem;line-height:0;color:inherit}
    .bx-ic svg{width:100%;height:100%;display:block}
    .bx-picker{position:relative}
    .bx-picker__btn{display:flex;align-items:center;gap:.6rem;width:100%;padding:.5rem .75rem;border-radius:.5rem;border:1px solid rgba(120,120,130,.35);background:transparent;text-align:left;cursor:pointer;color:inherit}
    .bx-picker__btn:hover{border-color:#f5a524}
    .bx-picker__cur{color:#f5a524;width:1.5rem;height:1.5rem}
    .bx-picker__name{flex:1;font-size:.875rem}
    .bx-picker__chev{width:1rem;height:1rem;opacity:.6}
    .bx-picker__panel{position:absolute;z-index:40;left:0;right:0;top:calc(100% + 6px);min-width:340px;max-width:520px;background:#fff;border:1px solid rgba(120,120,130,.3);border-radius:.75rem;box-shadow:0 16px 40px rgba(0,0,0,.18);padding:.75rem}
    .dark .bx-picker__panel{background:#18181b}
    .bx-picker__top{display:flex;gap:.5rem;align-items:center;margin-bottom:.6rem}
    .bx-picker__search{flex:1;padding:.45rem .7rem;border-radius:.5rem;border:1px solid rgba(120,120,130,.35);background:transparent;color:inherit;font-size:.875rem}
    .bx-picker__add{font-size:.75rem;font-weight:600;color:#f5a524;white-space:nowrap}
    .bx-picker__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(44px,1fr));gap:.35rem;max-height:260px;overflow:auto}
    .bx-picker__item{display:grid;place-items:center;height:44px;border-radius:.5rem;border:1px solid transparent;background:rgba(120,120,130,.08);cursor:pointer;color:inherit}
    .bx-picker__item:hover{background:rgba(245,165,36,.18)}
    .bx-picker__item.is-on{border-color:#f5a524;background:rgba(245,165,36,.25);color:#f5a524}
    .bx-picker__empty{grid-column:1/-1;font-size:.8rem;opacity:.7;padding:.5rem}
</style>
<style>
    /* Kartochkalar konstruktori — sayt ko'rinishiga yaqin */
    .bx-cards{display:grid;grid-template-columns:repeat(var(--cols,3),minmax(0,1fr));gap:.9rem}
    @media (max-width:1100px){.bx-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:700px){.bx-cards{grid-template-columns:1fr}}
    .bx-cards--outcome,.bx-cards--feature{--cols:2!important}
    .bx-cards--tag{display:flex;flex-wrap:wrap}
    .bx-cards__empty{grid-column:1/-1;padding:1.25rem;border:1px dashed rgba(120,120,130,.4);border-radius:.75rem;text-align:center;font-size:.875rem;opacity:.7}
    .bx-card{border:1px solid rgba(120,120,130,.25);border-radius:1rem;background:rgba(120,120,130,.05);overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,box-shadow .2s}
    .bx-card:hover{border-color:#f5a524;box-shadow:0 6px 18px rgba(245,165,36,.15)}
    .bx-card__prev{padding:.75rem;flex:1}
    .bx-card__bar{display:flex;align-items:center;justify-content:space-between;padding:.25rem .5rem;border-top:1px solid rgba(120,120,130,.2);background:rgba(120,120,130,.06)}
    .bx-card__n{display:grid;place-items:center;width:1.4rem;height:1.4rem;border-radius:999px;background:#16171a;color:#ffd877;font-size:.7rem;font-weight:700}
    .dark .bx-card__n{background:#ffd877;color:#16171a}
    .bx-card__ctl{display:flex;gap:.1rem}
    .bx-tile{display:grid;place-items:center;width:2.6rem;height:2.6rem;border-radius:.8rem;background:linear-gradient(180deg,#ffd877,#f5a524);color:#16171a;flex:none;--s:1.3rem}
    .bx-tile .icon,.bx-tile .icon svg{width:1.3rem;height:1.3rem;display:block}
    .bx-tile--s{width:2rem;height:2rem;border-radius:.6rem}.bx-tile--s .icon,.bx-tile--s .icon svg{width:1rem;height:1rem}
    .bx-ic--s{display:inline-block;width:1rem;height:1rem;vertical-align:-.15rem}
    .bx-ic--s .icon,.bx-ic--s .icon svg{width:1rem;height:1rem;display:block}
    .bx-aud{display:flex;flex-direction:column;gap:.6rem;padding:.9rem;border-radius:.9rem;border:1px solid rgba(120,120,130,.25);background:#fff;min-height:9rem;color:#16171a}
    .bx-aud b{font-size:.95rem;line-height:1.25}.bx-aud p{font-size:.8rem;opacity:.75;line-height:1.35}
    .bx-aud.is-acc{background:linear-gradient(180deg,#ffd877,#f5a524);border-color:transparent}
    .bx-aud.is-acc .bx-tile{background:#fff}
    .bx-aud.is-dark{background:#16171a;color:#fff}.bx-aud.is-dark .bx-tile{background:linear-gradient(180deg,#8dbbff,#2f6fe4);color:#fff}
    .bx-mod{display:flex;flex-direction:column;gap:.6rem;color:inherit}
    .bx-thumb{width:100%;height:7rem;object-fit:cover;border-radius:.8rem;display:block}
    .bx-thumb--empty{display:grid;place-items:center;background:rgba(120,120,130,.15);font-size:.75rem;opacity:.7}
    .bx-mod__top{display:flex;justify-content:space-between;align-items:center;font-size:.75rem;opacity:.9}
    .bx-mod__b{display:flex;flex-direction:column;gap:.4rem}.bx-mod__b b{font-size:.95rem}.bx-mod__b p{font-size:.8rem;opacity:.7}
    .bx-stat{display:flex;flex-direction:column;gap:.15rem}.bx-stat b{font-size:1.5rem;line-height:1.1}.bx-stat span{font-size:.8rem;opacity:.7}
    .bx-out{display:flex;gap:.6rem;align-items:flex-start;font-size:.9rem}
    .bx-tick{display:grid;place-items:center;flex:none;width:1.4rem;height:1.4rem;border-radius:.4rem;background:linear-gradient(180deg,#7fddaa,#1f9d5c);color:#fff}
    .bx-tick .icon,.bx-tick .icon svg{width:.9rem;height:.9rem;display:block}
    .bx-tag{display:inline-flex;gap:.3rem;align-items:center;padding:.3rem .7rem;border-radius:999px;background:rgba(120,120,130,.18);font-size:.8rem;font-weight:600}
    .bx-tag.is-acc{background:linear-gradient(90deg,#ffe08a,#f8b34d);color:#16171a}
    .bx-plan{display:flex;flex-direction:column;gap:.35rem;padding:.9rem;border-radius:.9rem;border:1px solid rgba(120,120,130,.25);background:#fff;color:#16171a}
    .bx-plan.is-hl{background:#16171a;color:#fff}
    .bx-plan b{font-size:1rem}.bx-plan small{font-size:.75rem;opacity:.7}
    .bx-plan__price{font-size:1.15rem;font-weight:700;margin:.2rem 0}.bx-plan__price s{font-size:.75rem;opacity:.6;font-weight:500}
    .bx-plan ul{display:flex;flex-direction:column;gap:.3rem;font-size:.78rem;list-style:none;padding:0;margin:0}
    .bx-plan li{display:flex;gap:.4rem;align-items:center}
    .bx-plan .bx-tick{width:1.1rem;height:1.1rem;border-radius:.3rem}.bx-plan .bx-tick .icon,.bx-plan .bx-tick .icon svg{width:.7rem;height:.7rem}
</style>
<style>
    /* Sozlamalar — ko'rish kartochkalari */
    .bx-set{margin-bottom:1.75rem}
    .bx-set__group{font-size:.8rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.6;margin-bottom:.75rem}
    .bx-set__grid{display:grid;gap:1rem;grid-template-columns:repeat(auto-fill,minmax(min(100%,520px),1fr))}
    .bx-set__card{border:1px solid rgba(120,120,130,.25);border-radius:1rem;background:#fff;overflow:hidden}
    .dark .bx-set__card{background:rgba(255,255,255,.04)}
    .bx-set__head{display:flex;align-items:center;gap:.75rem;padding:.85rem 1rem;border-bottom:1px solid rgba(120,120,130,.2);flex-wrap:wrap}
    .bx-set__ico{display:grid;place-items:center;width:2.2rem;height:2.2rem;border-radius:.65rem;background:linear-gradient(180deg,#ffd877,#f5a524);color:#16171a;flex:none}
    .bx-set__ttl{flex:1;min-width:8rem;display:flex;flex-direction:column;line-height:1.25}
    .bx-set__ttl b{font-size:.95rem}.bx-set__ttl small{font-size:.75rem;opacity:.6}
    .bx-set__rows{display:grid;grid-template-columns:1fr 1fr;gap:.85rem 1.25rem;padding:1rem;margin:0}
    .bx-set__rows.is-off{opacity:.45}
    .bx-set__row{min-width:0}.bx-set__row.is-wide{grid-column:1/-1}
    .bx-set__row dt{font-size:.72rem;font-weight:600;opacity:.6;margin-bottom:.2rem}
    .bx-set__row dd{margin:0;font-size:.88rem;line-height:1.4;overflow-wrap:anywhere}
    .bx-set__txt{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .bx-set__empty{opacity:.45;font-size:.82rem}
    .bx-set__img{max-height:5.5rem;border-radius:.6rem;display:block}
    .bx-set__chips{display:flex;flex-wrap:wrap;gap:.3rem}
    .bx-pill{display:inline-block;padding:.15rem .6rem;border-radius:999px;background:rgba(120,120,130,.18);font-size:.75rem;font-weight:600}
    .bx-pill.is-yes{background:rgba(31,157,92,.15);color:#1f9d5c}
    .bx-sw{display:inline-flex;align-items:center;gap:.5rem;padding:.2rem .6rem .2rem .25rem;border-radius:999px;border:1px solid rgba(120,120,130,.3);background:transparent;cursor:pointer;font-size:.75rem;font-weight:600;color:inherit}
    .bx-sw i{width:2rem;height:1.15rem;border-radius:999px;background:rgba(120,120,130,.4);position:relative;transition:background .2s}
    .bx-sw i::after{content:"";position:absolute;left:.15rem;top:.15rem;width:.85rem;height:.85rem;border-radius:50%;background:#fff;transition:transform .2s}
    .bx-sw.is-on i{background:#f5a524}.bx-sw.is-on i::after{transform:translateX(.85rem)}
    @media (max-width:700px){.bx-set__rows{grid-template-columns:1fr}}
    .bx-set__card{display:flex;flex-direction:column}.bx-set__card>.bx-set__rows{flex:1 1 auto}
    .bx-set__foot{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.75rem 1.25rem;border-top:1px solid rgba(0,0,0,.06);font-size:.8rem;color:#868b94}
    .dark .bx-set__foot{border-color:rgba(255,255,255,.08)}
    /* Sozlamalar: ichki yon menyu */
    .bx-shell{display:grid;grid-template-columns:15.5rem minmax(0,1fr);gap:1.5rem;align-items:start}
    .bx-nav{position:sticky;top:5rem}
    .bx-nav__inner{border:1px solid rgba(120,120,130,.2);border-radius:1rem;background:#fff;padding:.6rem;max-height:calc(100vh - 6.5rem);overflow:auto}
    .dark .bx-nav__inner{background:rgba(255,255,255,.04)}
    .bx-nav__search{display:flex;align-items:center;gap:.4rem;padding:.45rem .6rem;border-radius:.65rem;background:rgba(120,120,130,.08);margin-bottom:.5rem;color:#868b94}
    .bx-nav__search input{flex:1;min-width:0;border:0;background:transparent;font-size:.85rem;padding:0;outline:none;box-shadow:none;color:inherit}
    .bx-nav__search input:focus{box-shadow:none}
    .bx-nav__list{display:flex;flex-direction:column;gap:.15rem}
    .bx-nav__item{display:flex;align-items:center;gap:.6rem;width:100%;padding:.55rem .6rem;border-radius:.65rem;font-size:.875rem;font-weight:600;text-align:left;color:inherit;transition:background .15s}
    .bx-nav__item span{flex:1;line-height:1.25}
    .bx-nav__item em{font-style:normal;font-size:.7rem;font-weight:700;min-width:1.4rem;text-align:center;padding:.1rem .35rem;border-radius:999px;background:rgba(120,120,130,.12);opacity:.8}
    .bx-nav__item svg{flex:none;opacity:.55}
    .bx-nav__item:hover{background:rgba(120,120,130,.08)}
    .bx-nav__item.is-active{background:rgba(245,165,36,.14);color:#b86e00}
    .dark .bx-nav__item.is-active{color:#ffc24d}
    .bx-nav__item.is-active svg{opacity:1}
    .bx-nav__item.is-active em{background:#f5a524;color:#16171a}
    .bx-nav__sub{margin:.1rem 0 .4rem 1.45rem;padding-left:.75rem;border-left:2px solid rgba(120,120,130,.15);display:flex;flex-direction:column}
    .bx-nav__sub a{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.35rem .5rem;border-radius:.5rem;font-size:.8rem;opacity:.75;line-height:1.3}
    .bx-nav__sub a:hover{opacity:1;background:rgba(120,120,130,.08)}
    .bx-nav__dot{flex:none;width:.45rem;height:.45rem;border-radius:50%;background:rgba(120,120,130,.4)}
    .bx-nav__dot.is-on{background:#1f9d5c}
    .bx-main{min-width:0}
    .bx-set__group{display:flex;align-items:center;gap:.5rem}
    .bx-set__card{scroll-margin-top:5.5rem}
    .bx-set__card.is-flash{animation:bxFlash 1.6s ease}
    @keyframes bxFlash{0%,40%{box-shadow:0 0 0 3px rgba(245,165,36,.65)}100%{box-shadow:0 0 0 0 rgba(245,165,36,0)}}
    .bx-empty{padding:2rem;text-align:center;opacity:.6}
    @media (max-width:1100px){
        .bx-shell{grid-template-columns:minmax(0,1fr);gap:1rem}
        .bx-nav{min-width:0}
        .bx-nav{position:sticky;top:4rem;z-index:5}
        .bx-nav__inner{max-height:none;display:flex;align-items:center;gap:.5rem;overflow-x:auto;padding:.45rem;scrollbar-width:none}
        .bx-nav__search{margin:0;flex:none;width:9.5rem}
        .bx-nav__list{flex-direction:row;gap:.25rem}
        .bx-nav__item{white-space:nowrap;width:auto}
        .bx-nav__sub{display:none!important}
    }
    /* 5 ustunli KPI qatori (Filament 4 tagacha qo'llaydi) */
    @media (min-width: 768px) { .fi-wi-stats-overview-stats-ctn:not([class*="grid-cols"]) { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1280px) { .fi-wi-stats-overview-stats-ctn:not([class*="grid-cols"]) { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
</style>
