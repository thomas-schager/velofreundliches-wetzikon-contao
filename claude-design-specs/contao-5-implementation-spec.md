# Velofreundliches Wetzikon — Contao 5 Implementation Spec

> Structured build spec for implementing the **Palette B** design as a Contao 5.6+ theme.
> Source of truth for tokens/markup: `colors_and_type.css`, `responsive/site.css`,
> `responsive/index.html`, `design-system-summary.md`. This document maps that design onto
> Contao's page tree, layout sections, frontend modules, content elements and templates.
> Language throughout: **Swiss German (no "ß", always "ss"), "Velo" not "Fahrrad".**

---

## 1. Global Styles

### 1.1 Color palette

| Semantic role | Token | Hex | Notes |
|---|---|---|---|
| **primary** | `--color-primary` | `#dc3406` | Brand orange-red. Links, primary buttons, accents. |
| primary-hover | `--color-primary-hover` | `#c32d05` | Primary button hover. |
| primary-press | `--color-primary-press` | `#a82704` | Primary button pressed / pressed-link / error border. |
| primary-tint | `--color-primary-tint` | `#fbe7df` | Pale wash background, active radio-cards, primary pills. |
| **secondary** | `--color-secondary` | `#000000` | Pure black — used as the "dark" neutral for dark sections (Join, Footer), outline buttons, body text. |
| secondary-hover | `--color-secondary-hover` | `#1a1a1a` | Hover on dark surfaces. |
| **accent** | `--color-accent` | `#fdd003` | Warm yellow. **Only** as eyebrow/marker on dark surfaces. Never decorative on light. |
| **success** | `--color-success` | `#296310` | Positive states only (submitted, confirmed). Never decorative. |
| success-dark | `--color-success-dark` | `#225210` | Text on success-tint. |
| success-tint | `--color-success-tint` | `#e6efe0` | Background for "eingereicht" / confirmations. |
| **background** | `--color-background` | `#fafaf8` | Page background (warm off-white). |
| **surface** | `--color-surface` | `#ffffff` | Cards, nav, inputs. |
| surface-sunken | `--color-surface-2` | `#f4f3ef` | Sunken sections, ghost-button hover. |
| **text** | `--color-text` | `#000000` | Default body text (pure black for max contrast). |
| **text-muted** | `--color-text-muted` | `#5e5e5a` | Lead, captions, secondary text. |
| text-muted-soft | `--color-muted` | `#8a8a86` | Tertiary / meta text. |
| label-muted | `--color-label-muted` | `#6b6b67` | Form eyebrow labels (4.6:1 on white). |
| **border** | `--color-border` | `#e8e6e0` | Hairline borders, dividers. |
| border-strong | `--color-border-strong` | `#cfcdc6` | Input borders. |
| danger | `--color-danger` | `#a82704` | Validation error text/border. |

**Rating scale** (semantic only — never mix with primary): `#b91c1c` Gefährlich · `#ea580c` Schlecht · `#ca8a04` Akzeptabel · `#65a30d` Gut · `#15803d` Ausgezeichnet.

**Background rules:** predominantly off-white `#fafaf8`; occasional `#000000` sections for rhythm (Join, Footer). **No gradients, no glassmorphism, no patterns** (exception: a subtle 1px hairline texture allowed on dark sections). Full-bleed photography on home/campaign pages.

### 1.2 Typography

- **Display / headings / logo:** `Jost` (500, 600, 700).
- **Body / UI / numbers:** `Roboto` (400, 500, 700).
- Headings set tight (`line-height: 1.05–1.2`, `letter-spacing: -0.01em`). Body airy (`line-height: 1.55–1.6`). Eyebrows in primary, uppercase, tracked `0.08em`.

| Element | Family | Weight | Size | Line-height | Tracking |
|---|---|---|---|---|---|
| **h1 / Hero** | Jost | 700 | `clamp(48px, 5vw, 80px)` | 1.05 | -0.01em |
| **h2** | Jost | 600 | `clamp(36px, 3.5vw, 48px)` | 1.05 | -0.01em |
| **h3** | Jost | 600 | 28px | 1.2 | — |
| **h4** | Jost | 600 | 22px | 1.2 | — |
| h5 (eyebrow) | Roboto | 700 | 12px | 1.4 | 0.08em, uppercase |
| h6 / caption | Roboto | 500 | 12px | 1.4 | 0.02em |
| Lead | Roboto | 400 | 22px | 1.6 | — |
| **Body (base)** | Roboto | 400 | **16px** | 1.6 | — |
| Small / meta | Roboto | 400 | 14px | 1.45 | — |

**Base font size:** 16px. **Type scale (px):** 12 · 14 · 16 · 18 · 22 · 28 · 36 · 48 · 64 · 80.

### 1.3 Spacing scale (8px grid)

`4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96 · 128` → tokens `--space-1 … --space-10`.

- Section vertical padding: 40 → 56 → 72 px (mobile → tablet → desktop) — design summary cites 64–96 px on large desktop.
- Container max: **1180px**; prose container: **680px**.
- Sticky nav height: 64px (mobile) / 72px (desktop).
- Touch targets ≥ 44px.

**Radii:** `4px` (sm — buttons/inputs/tags) · `8px` (md — cards) · `12px` (lg — panels/modal) · `999px` (full — pills) · `0` (full-bleed images, intentional sharp corners).

**Shadows:** `sm 0 1px 2px rgba(0,0,0,.04)` · `md 0 1px 2px rgba(0,0,0,.04), 0 2px 8px rgba(0,0,0,.04)` · `lg 0 2px 4px rgba(0,0,0,.04), 0 8px 24px rgba(0,0,0,.06)`. No heavy drop-shadows; cards rest flat on 1px border, shadow only on hover.

**Motion:** ease `cubic-bezier(.2,.7,.3,1)`; durations 120 / 180 / 280 ms. Fade + 8–12px translate entrances. No bounce, no parallax.

### 1.4 External fonts

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
```

> For Swiss data-protection compliance, prefer self-hosting under `files/theme/fonts/` and replacing the `@import`/`<link>` with local `@font-face` declarations.

---

## 2. Site Structure (Page Tree)

Main navigation shows four items (Start is reached via the logo). "Mitglied werden" is a standalone CTA button, **not** part of the nav module. Theme sub-areas (Veloinfrastruktur etc.) are rendered as in-page tabs on the Themen page, so they are **not** separate pages by default (optionally split into child pages if SEO requires).

| Title | Alias | Type | Parent | In main nav |
|---|---|---|---|---|
| **Velofreundliches Wetzikon** | `velofreundliches-wetzikon` | root | — (website root) | — |
| Start | `index` | regular | root | no (= homepage, reached via logo) |
| Themen | `themen` | regular | root | **yes** |
| Stellungnahmen | `stellungnahmen` | regular | root | **yes** |
| Veranstaltungen | `veranstaltungen` | regular | root | **yes** |
| Über uns | `ueber-uns` | regular | root | **yes** |
| Mitglied werden | `mitglied-werden` | regular | root | no (CTA button only) |
| Veranstaltung-Detail (Reader) | `veranstaltung` | regular | Veranstaltungen | no (hidden, event reader) |
| Stellungnahme-Detail (Reader) | `stellungnahme` | regular | Stellungnahmen | no (hidden, news reader) |
| Newsletter | `newsletter` | regular | root | no (footer) |
| Spenden | `spenden` | regular | root | no (footer) |
| Impressum | `impressum` | regular | root | no (footer legal) |
| Datenschutz | `datenschutz` | regular | root | no (footer legal) |
| 404 | `404` | error_404 | root | no |

**Optional child pages of Themen** (if not using tabs): Veloinfrastruktur, Schulweg, Verkehrsplanung, Veloabstellplätze — all `regular`, parent = Themen, not in main nav (sub-nav/tabs instead).

> Set the root page language to `de`, the Swiss timezone `Europe/Zurich`, and date format `d.m.Y` (→ `15.03.2026`).

---

## 3. Page Layout

One Twig layout (`velo`) with three sections. Each section's inner wrapper is `.vw-container` (max 1180px, side padding 20→32px); section wrappers carry `.vw-section` (vertical padding 40→56→72px). Body background `#fafaf8`; dark blocks `#000000`.

| Section | Present | Width | Modules / elements inside |
|---|---|---|---|
| **Header** | yes | full-bleed, sticky, 64/72px tall, `backdrop-filter: blur(6px)`, 1px bottom border | Logo (custom) + Navigation module + "Mitglied werden" CTA button + mobile burger/drawer |
| **Main** | yes | `.vw-container` 1180px | The page article(s) and list modules (events, news) |
| Left column | **no** | — | Not used site-wide. (Mitglied & Themen pages use a 2-col **content grid inside main**, not a layout column — see §5.) |
| Right column | **no** | — | — |
| **Footer** | yes | full-bleed, `#000000` | 4-column footer (logo+about+mail, Themen, Engagement, Verein) + bottom bar (copyright + legal) |

**Internal 2-column patterns** (handled inside the article via CSS grid, not Contao columns):
- Hero: text (eyebrow→h1→lead→buttons→stats) | full-bleed image (~460px).
- Mitglied: form (1.4fr) | sticky summary aside (1fr).
- Themen panel: position text (1fr) | media + callout + sources (1.1fr).

---

## 3a. Responsive Behaviour (mobile / tablet / desktop)

**This is a single mobile-first build — there is no separate "mobile version".** Every Contao
frontend module and content element outputs the **same HTML at every viewport**; all adaptation
happens in `site.css` via shared breakpoints. This is verified against the actual stylesheet, not
assumed. **Do not** build duplicate mobile markup, and **do not** use Contao's
`page.mobile`/user-agent switching — it isn't needed and would break the single-source approach.

### Breakpoints (exactly three, mobile-first `min-width`)

| Name | Range | Trigger |
|---|---|---|
| **Mobile** | ≤ 599px (base styles, no query) | default |
| **Tablet** | ≥ 600px | `@media (min-width: 600px)` |
| **Strip** | ≥ 720px | `@media (min-width: 720px)` — only the Melde strip + Event row |
| **Desktop** | ≥ 960px | `@media (min-width: 960px)` |

> Design width references: Mobile 390px, Tablet 768px (both shown in `Mobile & Tablet Preview.html`),
> Desktop 1180px container. Type scales fluidly via `clamp()` between these, never below 12px.

### How each component reshapes (from `site.css`)

| Component | Mobile ≤ 599 | Tablet ≥ 600 | Desktop ≥ 960 |
|---|---|---|---|
| **Nav** (`.vw-nav`) | burger + drawer, bar 64px | burger + drawer, bar 72px | full link bar + CTA (`.vw-burger` hidden at 960) |
| **Hero** (`.vw-hero__grid`) | 1 col, image above text (`order:-1`), img 220px | 1 col, img 320px | 2 col `1.1fr 1fr`, img 460px, restores order |
| **Hero CTA** | buttons full-width (`flex:1 1 auto`) | buttons auto-width | auto |
| **Melde strip** (`.vw-melde`) | 1 col, button full-width | 1 col | `1fr auto` at **720px**, button auto |
| **Positions** (`.vw-cards-3`) | 1 col | 2 col | 3 col |
| **Events** (`.vw-event`) | 1 col stacked | 1 col | `96px 1fr auto` at **720px** (date col w/ right border) |
| **News** (`.vw-news`) | 1 col | 2 col | 3 col |
| **Join** (`.vw-join`) | 1 col, pad 32/24 | pad 44/40 | 2 col `1.4fr 1fr`, pad 56 |
| **Footer** (`.vw-footer__grid`) | 1 col | 2 col | 4 col `2fr 1fr 1fr 1fr` |
| **Footer bar** | stacked | row at 600 | row |
| **Mitglied** (§5) | form & aside stack 1 col | 1 col | form 1.4fr \| sticky aside 1fr |
| **Themen tabs/panel** (§5) | tabs scroll-x, panel 1 col | 1 col | 2 col, tabs sticky under nav |
| **Modal** (VeloMelde) | bottom sheet (`translateY 100%→0`) | centred 520px at 600 | 2 col 820px at 900 |

### Spacing that scales

- Container side padding: **20px → 32px** at 600.
- Section vertical padding: **40 → 56 → 72px** at 600 / 960.
- Footer top margin: **56 → 80px** at 960.
- Touch targets ≥ **44px** at all sizes (buttons/inputs set `min-height:44px`).

### Build rules to preserve responsiveness

- Load `colors_and_type.css` then `site.css` once, globally — never inline per-module CSS that
  hard-codes a column count.
- Custom content-element templates must emit the grid **container class** (`.vw-cards-3`,
  `.vw-news`, `.vw-event-list`) and let the children flow — don't set `grid-template-columns`
  inline in Twig.
- Contao's News/Event list templates: wrap items in the `vw-*` grid wrapper so the bundle's
  default per-item floats don't override the responsive grid.
- Keep `<meta name="viewport" content="width=device-width, initial-scale=1">` in `fe_page`.
- Test at 390 / 768 / 1280px (matches `Mobile & Tablet Preview.html`).

---

## 4. Frontend Modules needed

| # | Module type | Name | Configuration | Layout section |
|---|---|---|---|---|
| 1 | **Navigation** | `nav-main` | Start level 1, show 1 level, top-level pages only; accessible template (burger on mobile). Active link → `#dc3406`. | Header |
| 2 | **Custom HTML** | `logo` | Word-mark markup (accent bar + "velofreundliches" / "WETZIKON"). Or a Twig partial. | Header |
| 3 | **Custom HTML** | `cta-mitglied` | Primary button (size sm) linking to `mitglied-werden`. | Header |
| 4 | **Custom HTML** | `nav-drawer` | Mobile drawer markup + toggle script (only if not covered by the accessible nav template). | Header |
| 5 | **Event list** | `events-upcoming` | calendar-bundle. Source: "Veranstaltungen" calendar; show next 3; sort ascending; reader = Veranstaltung-Detail. Custom template styles date / tag-pill / location / ghost "Details" button. | Main (home) |
| 6 | **Event list (full)** | `events-all` | Same calendar, paginated full list. | Main (Veranstaltungen page) |
| 7 | **News list** | `news-latest` | news-bundle. Source: "Aktuell" archive; show 3; image + tag-pill + date; reader = Stellungnahme-Detail. | Main (home) |
| 8 | **News list (full)** | `news-all` | Same archive, paginated, with category filter (Stellungnahme / Medienmitteilung / Mitglieder). | Main (Stellungnahmen page) |
| 9 | **Form** | `form-mitglied` | Contao form generator — membership form (see §5 Mitglied). | Main (Mitglied page) |
| 10 | **Form** | `form-melde` | VeloMelde report form (rating scale, photo upload, math-captcha) — custom element/JS may be needed for the rating widget. | Modal / Main |
| 11 | **Breadcrumb** | `breadcrumb` | Standard; show on inner pages (Themen, Stellungnahmen, Über uns), hide on Start. | Main (top of article) |
| 12 | **Custom navigation / HTML** | `footer-cols` | Three link columns (Themen, Engagement, Verein) + logo/about/mail block. | Footer |
| 13 | **Custom navigation** | `footer-legal` | Impressum, Datenschutz. | Footer |

---

## 5. Content Elements per Page

Markup + `vw-*` classes are taken verbatim from `responsive/index.html`. Custom section elements are best built as **custom content elements** (Twig templates under `content_element/`); simple ones can be `headline` + `text` + `image`.

### Start (`index`) — main article
1. **Custom element `velo_hero`** — eyebrow "Interessengemeinschaft · Wetzikon ZH"; h1 "Sichere Velowege. Für ein *alltagstaugliches* Wetzikon." (accent word in `<em>`); lead; 2 buttons (primary "Mitglied werden" + secondary "Unsere Positionen"); 3 stats (412 Mitglieder / 18 Stellungnahmen / 2014 gegründet); full-bleed hero image. Class: `.vw-hero`.
2. **Text (Callout)** — action callout: "Vernehmlassung Verkehrsrichtplan 2026 läuft… bis 30. April… Unsere Eingabe lesen →". Class: `.vw-callout .vw-callout--action`.
3. **Custom element `velo_melde`** — eyebrow "VeloMelde-Karte"; h3 "Gefährliche Stelle entdeckt? Melden Sie sie uns."; body; primary button "Stelle melden" (opens `form-melde` modal). Class: `.vw-melde`.
4. **Custom element `velo_positions`** — section heading (eyebrow "Unsere Positionen" + h2 "Drei Schwerpunkte" + lead) + 3 cards {icon, eyebrow, title, body, "Mehr erfahren →"}: Infrastruktur / Schulweg / Politik. Classes: `.vw-heading`, `.vw-cards-3`, `.vw-card`.
5. **Module `events-upcoming`** (§4.5) — heading row "Agenda / Nächste Veranstaltungen" + "Alle Termine →". Class: `.vw-events`, `.vw-event`.
6. **Module `news-latest`** (§4.7) — heading "Aktuell / Aus dem Verein". Class: `.vw-news`, `.vw-news__card`.
7. **Custom element `velo_join`** (dark) — accent eyebrow "Mitglied werden — 30 Franken im Jahr"; h2; body; 2 buttons (primary "Beitrittsformular" + on-dark "Statuten ansehen"); benefits checklist (4 items). Class: `.vw-join`.

### Themen (`themen`) — main article
- **Headline** (h1) "Konkret. Lokal. Verbindlich." + eyebrow "Themen" + lead.
- **Custom element `velo_tabs`** — sticky sub-nav (top: 72px) with 4 tabs: Veloinfrastruktur / Schulweg / Verkehrsplanung / Abstellplätze. Active underline `2px #dc3406`.
- Per active tab (Infrastruktur shown):
  - **Text** — eyebrow "Position" + h2 + body paragraph.
  - **List** (`.vw-check-list`) — "Unsere Forderungen" (4 items, green check icons).
  - **Hyperlink/Download** — primary "Stellungnahme als PDF" (download element) + ghost "Verkehrsrichtplan ansehen".
  - **Image** — "Karte — Veloachsen Wetzikon (Soll-Ist)".
  - **Text (Callout action)** — "Vernehmlassung läuft."
  - **List (Quellen)** — card with source links + outline tags (Norm / Stadt / Kanton).
  - **Accordion (FAQ)** — `<details>` list "Was wir oft gefragt werden" (3 Q&A). Special class: `.vw-faq`.

### Stellungnahmen (`stellungnahmen`)
- **Headline** + lead, then **Module `news-all`** (§4.8) with category filter. Reader renders full position text in `.vw-prose` (680px). Use `download` elements for attached PDFs.

### Veranstaltungen (`veranstaltungen`)
- **Headline** + lead, then **Module `events-all`** (§4.6). Reader page = single event with `.vw-event__date`, tag-pill, location, register link.

### Über uns (`ueber-uns`)
- **Headline** "Über uns" + lead, **Text** (mission, 3–5 sentence paragraphs), **Image** (board / Begehung photo), board member **list**, links to Statuten/Jahresbericht (`download`).

### Mitglied werden (`mitglied-werden`)
- **Headline** "Beitrittsformular" + eyebrow + lead.
- **Form `form-mitglied`** in a 2-col grid (form 1.4fr | sticky summary aside 1fr):
  - Radio-cards "Mitgliedschaftsart": Einzelperson CHF 30 / Familie CHF 50 / Gönner ab CHF 100. Active → border `#dc3406`, bg `#fbe7df`. Class `.vw-radio-card`.
  - Text fields: Name und Vorname*, E-Mail*, Adresse*, PLZ* (4 digits), Ort (default "Wetzikon").
  - Checkboxes: Newsletter (default on), "aktiv mitarbeiten".
  - Submit "Beitritt einreichen" (primary lg). Validation: errors after blur; never `disabled`.
  - **Aside summary** — live "CHF {fee} / Jahr" + benefits checklist.
- Success view: green check medallion + "Danke, {Vorname}." + success callout "Eingereicht."

### Footer (all pages)
- Logo (on-dark) + about paragraph + contact mail `vorstand@velofreundliches-wetzikon.ch`.
- Columns — **Themen**: Veloinfrastruktur, Schulweg, Verkehrsplanung, Veloabstellplätze · **Engagement**: Mitglied werden, Spenden, Newsletter, Veranstaltungen · **Verein**: Vorstand, Statuten, Jahresbericht, Medienmitteilungen.
- Bottom bar: "© 2026 IG Velofreundliches Wetzikon" + Impressum / Datenschutz.

---

## 6. Templates to Customize

> Contao 5.6: use **global** templates under `/templates/...` (not `/templates/<theme>/...`) — theme-scoped Twig templates can be ignored under the "Twig layout with slots" bug. CSS load order: `colors_and_type.css` **first**, then `site.css`.

| Template | Type | Intended change |
|---|---|---|
| `page/layout/velo.html.twig` | Page layout | Define header/main/footer slots; inject both stylesheets in `{% block head %}`; body classes for `#fafaf8` bg. |
| `fe_page.html.twig` (or `@Contao/page/layout.html.twig` parent) | Page | Add `lang="de"`, font `<link>`/`@font-face`, and the cookie/Datenschutz banner before `</body>`. |
| `mod_navigation_accessible.html5` | Navigation | Accessible burger nav; style via `vw-*` classes; active link `#dc3406`. |
| `content_element/hero/velo_hero.html.twig` | Content element | Hero block markup. |
| `content_element/text/velo_callout.html.twig` | Content element | Callout tones (info / action / success) with 3px left border. |
| `content_element/velo_melde.html.twig` | Content element | VeloMelde strip + modal trigger. |
| `content_element/velo_positions.html.twig` | Content element | 3 position cards. |
| `content_element/velo_join.html.twig` | Content element | Dark join block. |
| `content_element/velo_tabs.html.twig` | Content element | Themen sticky tabs + panels. |
| `news_latest.html.twig` / `news_short.html.twig` | News list/item | `.vw-news__card` markup (image, tag-pill, date, "Weiterlesen →"). |
| `event_list.html.twig` / `event_upcoming.html.twig` | Event list/item | `.vw-event` markup (date col, tag, location, ghost Details button). |
| `form_row.html.twig`, `form_textfield.html.twig`, `form_radio.html.twig`, `form_checkbox.html.twig` | Form widgets | Input border `#cfcdc6`, focus ring `rgba(220,52,6,.15)`, radio-cards, error text `#a82704`. |
| `mod_breadcrumb.html.twig` | Breadcrumb | Minimal `.vw-breadcrumb` styling. |
| `partials/velo_logo.html.twig` | Partial | Word-mark (accent bar + 2 lines). |
| `partials/velo_button.html.twig` | Partial/macro | Button macro (variant / size / icon). |
| `partials/velo_icon.html.twig` | Partial/macro | Lucide inline-SVG macro (`arrow-right`, `map-pin`, `users`, `file-text`, `mail`, `check`, `alert-circle`, `menu`, `x`, `download`). |

---

## 7. Custom CSS

Drop these `:root` custom properties as the theme base (alias of the project's `--vw-*` tokens; keep whichever naming your team prefers — both are listed in §1). Then layer `responsive/site.css` (component + layout CSS) on top, unchanged.

```css
:root {
  /* ---- Brand ---- */
  --color-primary:        #dc3406;
  --color-primary-hover:  #c32d05;
  --color-primary-press:  #a82704;
  --color-primary-tint:   #fbe7df;

  --color-secondary:       #000000;  /* dark neutral */
  --color-secondary-hover: #1a1a1a;

  --color-accent:          #fdd003;  /* yellow — eyebrow on dark only */

  --color-success:        #296310;
  --color-success-dark:   #225210;
  --color-success-tint:   #e6efe0;
  --color-danger:         #a82704;

  /* ---- Neutrals ---- */
  --color-background:     #fafaf8;
  --color-surface:        #ffffff;
  --color-surface-2:      #f4f3ef;
  --color-border:         #e8e6e0;
  --color-border-strong:  #cfcdc6;
  --color-text:           #000000;
  --color-text-muted:     #5e5e5a;
  --color-muted:          #8a8a86;
  --color-label-muted:    #6b6b67;

  /* ---- Rating scale (semantic, never brand) ---- */
  --color-rate-1: #b91c1c;  --color-rate-2: #ea580c;
  --color-rate-3: #ca8a04;  --color-rate-4: #65a30d;
  --color-rate-5: #15803d;

  /* ---- Focus / validation rings ---- */
  --focus-ring:    rgba(220,52,6,0.15);
  --success-ring:  rgba(41,99,16,0.12);
  --danger-tint:   rgba(220,52,6,0.12);

  /* ---- Type ---- */
  --font-display: 'Jost', 'Helvetica Neue', Helvetica, Arial, sans-serif;
  --font-body:    'Roboto', 'Helvetica Neue', Helvetica, Arial, sans-serif;

  --text-xs: 12px;  --text-sm: 14px;  --text-base: 16px; --text-lg: 18px;
  --text-xl: 22px;  --text-2xl: 28px; --text-3xl: 36px;  --text-4xl: 48px;
  --text-5xl: 64px; --text-6xl: 80px;

  --lh-tight: 1.05; --lh-snug: 1.2; --lh-normal: 1.45; --lh-relaxed: 1.6;
  --tracking-tight: -0.01em; --tracking-wide: 0.08em;

  /* ---- Spacing (8px grid) ---- */
  --space-1: 4px;  --space-2: 8px;  --space-3: 12px; --space-4: 16px;
  --space-5: 24px; --space-6: 32px; --space-7: 48px; --space-8: 64px;
  --space-9: 96px; --space-10: 128px;

  /* ---- Radii ---- */
  --radius-sm: 4px; --radius-md: 8px; --radius-lg: 12px; --radius-full: 999px;

  /* ---- Shadows ---- */
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
  --shadow-md: 0 1px 2px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
  --shadow-lg: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);

  /* ---- Layout ---- */
  --container: 1180px; --container-prose: 680px; --nav-h: 72px;

  /* ---- Motion ---- */
  --ease: cubic-bezier(.2,.7,.3,1);
  --dur-fast: 120ms; --dur: 180ms; --dur-slow: 280ms;
}

/* ---- Base ---- */
body {
  font-family: var(--font-body);
  font-size: var(--text-base);
  line-height: var(--lh-relaxed);
  color: var(--color-text);
  background: var(--color-background);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
h1, h2, h3, h4 { font-family: var(--font-display); letter-spacing: var(--tracking-tight); }
h1 { font-weight: 700; font-size: clamp(var(--text-4xl), 5vw, var(--text-6xl));   line-height: var(--lh-tight); }
h2 { font-weight: 600; font-size: clamp(var(--text-3xl), 3.5vw, var(--text-4xl)); line-height: var(--lh-tight); }
h3 { font-weight: 600; font-size: var(--text-2xl); line-height: var(--lh-snug); }
h4 { font-weight: 600; font-size: var(--text-xl);  line-height: var(--lh-snug); }

a { color: var(--color-primary); text-decoration: underline; text-underline-offset: 2px; text-decoration-thickness: 1px; transition: color var(--dur) var(--ease); }
a:hover { color: var(--color-primary-press); }
nav a { text-decoration: none; }
nav a:hover { text-decoration: underline; text-underline-offset: 5px; }
::selection { background: var(--color-primary); color: #fff; }

/* ---- Eyebrow ---- */
.vw-eyebrow { font-family: var(--font-body); font-weight: 700; font-size: var(--text-xs); letter-spacing: var(--tracking-wide); text-transform: uppercase; color: var(--color-primary); }
.vw-eyebrow--accent { color: var(--color-accent); }  /* dark sections only */

/* ---- Container / section ---- */
.vw-container { max-width: var(--container); margin-inline: auto; padding-inline: 20px; }
@media (min-width: 600px) { .vw-container { padding-inline: 32px; } }
.vw-section { padding-block: 40px; }
@media (min-width: 600px) { .vw-section { padding-block: 56px; } }
@media (min-width: 960px) { .vw-section { padding-block: 72px; } }

/* ---- Buttons ---- */
.vw-btn { display: inline-flex; align-items: center; gap: 10px; min-height: 44px;
  border-radius: var(--radius-sm); padding: 11px 20px; font: 500 15px/1 var(--font-body);
  letter-spacing: 0.01em; text-decoration: none; cursor: pointer; border: 1px solid transparent;
  transition: all var(--dur) var(--ease); }
.vw-btn--primary   { background: var(--color-primary); color: #fff; }
.vw-btn--primary:hover  { background: var(--color-primary-hover); }
.vw-btn--primary:active { background: var(--color-primary-press); transform: translateY(1px); }
.vw-btn--secondary { background: var(--color-surface); color: #000; border-color: #000; }
.vw-btn--secondary:hover { background: #000; color: #fff; }
.vw-btn--ghost     { background: transparent; color: #000; }
.vw-btn--ghost:hover { background: var(--color-surface-2); }
.vw-btn--ondark    { background: transparent; color: #fff; border-color: #fff; }
.vw-btn--ondark:hover { background: #fff; color: #000; }
.vw-btn--sm { padding: 7px 14px; font-size: 13px; min-height: 36px; }
.vw-btn--lg { padding: 14px 26px; font-size: 17px; }
.vw-btn--block { width: 100%; justify-content: center; }

/* ---- Inputs ---- */
input, textarea, select { font: 400 15px var(--font-body); color: #000; background: #fff;
  border: 1px solid var(--color-border-strong); border-radius: var(--radius-sm);
  padding: 11px 13px; min-height: 44px; outline: none;
  transition: border-color var(--dur) var(--ease), box-shadow var(--dur); }
input:focus, textarea:focus { border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--focus-ring); }
input.error { border-color: var(--color-danger); }
.error-text { font: 400 12px var(--font-body); color: var(--color-danger); }

/* ---- Cards / tags / callouts ---- */
.vw-card { background: #fff; border: 1px solid var(--color-border); border-radius: var(--radius-md);
  padding: 24px 28px; box-shadow: var(--shadow-md); transition: box-shadow var(--dur) var(--ease); }
.vw-card:hover { box-shadow: var(--shadow-lg); }
.vw-card--campaign { border-top: 3px solid var(--color-primary); border-radius: 6px 6px 8px 8px; }
.vw-tag { display: inline-flex; align-items: center; border-radius: var(--radius-full);
  padding: 4px 10px; font: 500 12px var(--font-body); }
.vw-tag--neutral { background: var(--color-surface-2); color: #000; }
.vw-tag--primary { background: var(--color-primary-tint); color: var(--color-primary-press); }
.vw-tag--success { background: var(--color-success-tint); color: var(--color-success-dark); }
.vw-tag--outline { background: #fff; border: 1px solid var(--color-border); color: #000; }
.vw-callout { display: flex; gap: 12px; padding: 16px 18px; border-left: 3px solid #000; background: #fff; }
.vw-callout--action  { background: var(--color-primary-tint); border-left-color: var(--color-primary); color: #9a2604; }
.vw-callout--success { background: var(--color-success-tint); border-left-color: var(--color-success); color: var(--color-success-dark); }
```

> The full component/layout CSS (nav, hero grid, drawer, events, news, join, footer, modal, rating scale, upload) already exists in `responsive/site.css` and the modal extension in `design-system-summary.md`. Copy those verbatim into `files/themes/velo/css/site.css` and adjust the leading `@import` path to sit beside `colors_and_type.css`.

---

*Compiled 2 June 2026 — Palette B. Pairs with `design-system-summary.md` (full design doc) and `contao-theme-spec.md` (Contao architecture notes).*
