# Velofreundliches Wetzikon — Design-System (Zusammenfassung für Claude Code)

Dieses Dokument fasst das komplette Design-System der Website **velofreundliches-wetzikon.ch** in textueller Form zusammen. Es ist so geschrieben, dass es als Kontext für Claude Code (oder ein anderes LLM) in eine `CLAUDE.md` oder ein Skill-File kopiert werden kann.

---

## 1. Kontext & Haltung

**Velofreundliches Wetzikon** ist eine zivilgesellschaftliche Interessengemeinschaft in Wetzikon (Kanton Zürich, Schweiz). Sie setzt sich für eine velofreundliche Verkehrsplanung, sichere Schulwege und eine durchgängige Veloinfrastruktur ein.

**Zielgruppe:** Erwachsene Wohnbevölkerung, Stimmberechtigte, lokale Politik, Stadtplanung.

**Haltung:**

- Professionell, aber leichtgewichtig.
- Bürgerlich-sachlich, nicht korporativ.
- **Kein Öko-Lifestyle**, kein Sport-Cycling, kein NGO-Pathos. Velo ist hier **Verkehrsmittel und Stadtpolitik**, kein Lebensgefühl.
- Tonalität: gut informierter, engagierter Bürger — irgendwo zwischen Quartierverein und Policy Brief.

---

## 2. Sprache & Copy

### Sprache

- **Schweizer Hochdeutsch.**
- **Niemals „ß"** — immer „ss". `Strasse`, nicht `Straße`. Nicht verhandelbar.
- **„Velo"** statt „Fahrrad" oder „Bike". Schweizerischer Standardbegriff, im Vereinsnamen verankert.
- Allgemeines Publikum mit **„Sie"** ansprechen; **„Du"** nur in klar informellen Kontexten (Mitglieder-Event-Einladungen).
- Schweizer Konventionen für Zahlen und Daten:
  - Tausendertrennzeichen: `1'500`
  - Prozent mit geschütztem Leerzeichen: `27.5 %`
  - Datum: `15.03.2026` oder ausgeschrieben `15. März 2026`
  - Währung: `CHF 30`

### Voice

- **Konkret und faktisch.** Strassen, Knoten, Pläne, Zahlen — keine abstrakten „Lösungen", „Erlebnisse", „Visionen".
- **Sachlich-pointiert.** Es werden Positionen vertreten, also dürfen Absätze mit 3–5 Sätzen Substanz haben.
- **„Wir"** für den Verein. **„Sie" / „die Stadt"** für die Stadtverwaltung. Niemals werbliches „uns" („joine uns") — Mitgliedschaft heisst, eine Sache zu unterstützen, nicht eine Marke.
- Headlines: Satzanfang gross, keine durchgehenden Versalien. (Deutsche Substantive werden ohnehin gross geschrieben.)
- Buttons: Satzanfang gross. „Mitglied werden", nicht „MITGLIED WERDEN".

### Was vermieden wird

- Korporativer Slang: „Solutions", „Experience", „Leveraging", „Partner werden mit uns".
- Öko-Sprache: „Planet", „grün", „nachhaltige Zukunft", Blatt-/Erde-Metaphorik.
- Sport-Sprache: „Performance", „Power", „Ride", Mountainbike-Vokabular.
- ALL CAPS zur Betonung.

### Emoji

**Keine Emoji.** UI-Affordances werden mit Icons (Lucide) gelöst.

### Beispielsätze

> **Sichere Velowege für Wetzikon.**
> Wir setzen uns dafür ein, dass das Velo in Wetzikon ein selbstverständliches und sicheres Verkehrsmittel wird — für alle Altersgruppen, auf jedem Schulweg, auf jeder Einkaufsroute.

> **Stellungnahme zum Verkehrsrichtplan 2026.**
> Der vorliegende Entwurf greift in zentralen Punkten zu kurz. Wir fordern eine durchgehende Veloachse zwischen Bahnhof und Kempten sowie eine verbindliche Velo-Vorrangregelung an den Knoten Bahnhofstrasse / Usterstrasse.

> **Mitglied werden — 30 Franken im Jahr.**
> Mit Ihrem Beitrag finanzieren wir Stellungnahmen, Veranstaltungen und die Arbeit vor Ort.

---

## 3. Farben

Eine bewusst schmale Palette (**Palette B**). `#dc3406` ist **die** Markenfarbe und wird selbstbewusst eingesetzt — Links, primäre Buttons, zentrale Callouts. `#000000` ist die Standard-Textfarbe — in Palette B bewusst reines Schwarz für maximalen Kontrast. `#296310` ist **ausschliesslich für positive Zustände** reserviert (Bestätigungen, „eingereicht", abgeschlossene Routenabschnitte). Niemals dekorativ. `#fdd003` ist ein sparsamer Sekundärakzent (warmes Gelb) — nur als Eyebrow/Marker auf dunklen Flächen.

### Markenfarben

| Token | Hex | Verwendung |
|---|---|---|
| `--vw-primary` | `#dc3406` | Links, Primär-Button, Akzent |
| `--vw-primary-hover` | `#c32d05` | Primär-Button im Hover |
| `--vw-primary-press` | `#a82704` | Primär-Button gedrückt |
| `--vw-primary-tint` | `#fbe7df` | Sehr blasser Wasch-Hintergrund |
| `--vw-dark` | `#000000` | Dunkles Neutral / Body Text |
| `--vw-dark-hover` | `#1a1a1a` | Hover auf Dark |
| `--vw-success` | `#296310` | Positive Signale |
| `--vw-success-dark` | `#225210` | Text auf success-tint |
| `--vw-success-tint` | `#e6efe0` | Hintergrund für „eingereicht" etc. |
| `--vw-accent` | `#fdd003` | Sekundärakzent (Gelb) — Eyebrow/Marker nur auf Dark |

### Neutrale Skala (warm-neutral)

| Token | Hex | Verwendung |
|---|---|---|
| `--vw-bg` | `#fafaf8` | Seitenhintergrund |
| `--vw-surface` | `#ffffff` | Karten, Navigation |
| `--vw-surface-2` | `#f4f3ef` | Vertiefte Sektionen |
| `--vw-border` | `#e8e6e0` | Haarfeine Trennlinien |
| `--vw-border-strong` | `#cfcdc6` | Input-Borders |
| `--vw-muted` | `#8a8a86` | Sekundärtext |
| `--vw-muted-strong` | `#5e5e5a` | Lead, Captions |

### Hintergrundregeln

- Überwiegend Off-White (`#fafaf8`), gelegentlich dunkle Sektionen in `#000000` für visuellen Rhythmus.
- **Keine Verläufe, kein Glasmorphismus, keine Muster.** Ausnahme: ein subtiles 1px-Hairline-Muster ist auf dunklen Sektionen als Textur erlaubt.
- Full-Bleed-Fotografie auf der Startseite und einzelnen Kampagnenseiten.

---

## 4. Typografie

- **Jost** (Gewichte 500/600/700) — alle Headlines, Display, Logo.
- **Roboto** (400/500/700) — Body, UI-Labels, Captions, Zahlen.
- Beide via Google Fonts:
  ```html
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  ```

### Skala

| Token | px | Verwendung |
|---|---|---|
| `--vw-text-xs` | 12 | Eyebrow, Caption |
| `--vw-text-sm` | 14 | Small Body, Meta |
| `--vw-text-base` | 16 | Body |
| `--vw-text-lg` | 18 | Lead |
| `--vw-text-xl` | 22 | H4 |
| `--vw-text-2xl` | 28 | H3 |
| `--vw-text-3xl` | 36 | H2 (mobil) |
| `--vw-text-4xl` | 48 | H2 / H1 (mobil) |
| `--vw-text-5xl` | 64 | H1 |
| `--vw-text-6xl` | 80 | Hero-Display |

### Heading-Styles

```css
.vw-h1  { font: 700 clamp(48px,5vw,80px)/1.05 'Jost'; letter-spacing: -0.01em; }
.vw-h2  { font: 600 clamp(36px,3.5vw,48px)/1.05 'Jost'; letter-spacing: -0.01em; }
.vw-h3  { font: 600 28px/1.2 'Jost'; }
.vw-h4  { font: 600 22px/1.2 'Jost'; }
.vw-lead { font: 400 22px/1.6 'Roboto'; color: #5e5e5a; }
.vw-body { font: 400 16px/1.6 'Roboto'; color: #000000; }
.vw-small{ font: 400 14px/1.5 'Roboto'; color: #5e5e5a; }
.vw-eyebrow { font: 700 12px/1.4 'Roboto'; letter-spacing: 0.08em; text-transform: uppercase; color: #dc3406; }
.vw-caption { font: 500 12px/1.4 'Roboto'; letter-spacing: 0.02em; color: #8a8a86; }
```

- Headings sind **eng gesetzt** (`line-height: 1.05–1.2`, `letter-spacing: -0.01em`).
- Body ist **luftig** (`line-height: 1.55–1.6`).
- Eyebrow in **Primary**, uppercase, getrackt.

---

## 5. Spacing, Radien, Schatten

### Spacing (8px-Grid)

`4 · 8 · 12 · 16 · 24 · 32 · 48 · 64 · 96 · 128`

Tokens: `--vw-s-1` bis `--vw-s-10`.

- Section-Padding vertikal: **64–96 px** auf Desktop.
- Container max: **1180 px**.
- Prose-Container (Artikel): **680 px**.
- Sticky Top-Nav: **72 px** hoch.

### Radien

| Token | Wert | Verwendung |
|---|---|---|
| `--vw-radius-sm` | `4px` | Buttons, Inputs, kleine Tags |
| `--vw-radius-md` | `8px` | Karten |
| `--vw-radius-lg` | `12px` | Panels |
| `--vw-radius-full` | `999px` | Pills, Avatare |
| (kein Token) | `0` | Full-Bleed-Bilder, Section-Divider |

Sharp Corners (0) auf grossen Bildflächen sind Teil der editorialen Note.

### Schatten

```css
--vw-shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
--vw-shadow-md: 0 1px 2px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
--vw-shadow-lg: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
```

- Karten ruhen flach mit **1 px Border**, Schatten erscheint höchstens dezent im Hover.
- **Keine** harten Drop-Shadows.

---

## 6. Komponenten

### Buttons

**Primary** (Hauptaktion: Mitglied werden, Stellungnahme einreichen):

```css
background: #dc3406; color: #fff; border: 1px solid transparent;
border-radius: 4px; padding: 11px 20px;
font: 500 15px/1 'Roboto'; letter-spacing: 0.01em;
transition: all 150ms cubic-bezier(.2,.7,.3,1);
/* hover */  background: #c32d05;
/* press */  background: #a82704; transform: translateY(1px);
```

**Secondary** (Outline): weisser Hintergrund, `1px solid #000000`, Text `#000000`. Im Hover füllt sich der Button mit `#000000`, Text wird weiss.

**Ghost**: transparent, Text `#000000`. Hover-Hintergrund `#f4f3ef`.

**On-Dark** (für Buttons auf dunklem Hintergrund): transparenter Hintergrund, `1px solid #fff`, Text weiss. Hover invertiert.

**Grössen:**

- `sm`: `padding: 7px 14px; font-size: 13px`
- `md`: `padding: 11px 20px; font-size: 15px` (Default)
- `lg`: `padding: 14px 26px; font-size: 17px`

Icons (16 px) sitzen mit `gap: 10px` rechts vom Label. Pfeil-Icon (`→`) für Forward-Aktionen.

### Form-Inputs

```css
input, textarea, select {
  font: 400 15px 'Roboto'; color: #000000;
  background: #fff; border: 1px solid #cfcdc6; border-radius: 4px;
  padding: 11px 13px; outline: none;
}
input:focus {
  border-color: #dc3406;
  box-shadow: 0 0 0 3px rgba(220,52,6,0.15);
}
/* error */
input.error { border-color: #a82704; }
.error-text { font: 400 12px 'Roboto'; color: #a82704; }
```

- Labels: `font: 500 13px 'Roboto'; color: #000000`.
- Checkbox: 18×18, Border `1.5px #cfcdc6`, im aktiven Zustand `background: #dc3406` mit weissem Häkchen.
- Radio-Cards (für „Mitgliedschaftsart") sind klickbare Boxen mit `border: 1px solid #cfcdc6`; aktiv → Border `#dc3406`, Hintergrund `#fbe7df`.

### Tags & Badges

Status-Pills (`border-radius: 999px; padding: 4px 10px; font: 500 12px 'Roboto'`):

| Variante | Hintergrund | Text |
|---|---|---|
| Neutral | `#f4f3ef` | `#000000` |
| Primary | `#fbe7df` | `#a82704` |
| Success | `#e6efe0` | `#225210` |
| Dark | `#000000` | `#fff` |
| Outline | `#fff` + `1px #e8e6e0` | `#000000` |

Eyebrow-Badge (eckig, `border-radius: 3px; padding: 3px 8px; font: 700 11px uppercase; letter-spacing: 0.06em`).

### Karten

```css
.card {
  background: #fff; border: 1px solid #e8e6e0; border-radius: 8px;
  padding: 24px 28px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
}
.card--campaign { border-top: 3px solid #dc3406; border-radius: 6px 6px 8px 8px; }
```

- **Campaign-Card** hat einen 3 px breiten orangen Top-Border als Marker für aktive Anliegen.
- Hover: Schatten wechselt zu `--vw-shadow-lg`.

### Navigation

- Sticky Top-Nav, weisser Hintergrund (`rgba(255,255,255,0.96)` + leichter Blur), `1px` Bottom-Border.
- Höhe 72 px.
- Links: `font: 500 14px 'Roboto'; color: #000000; text-decoration: none`.
- Aktiver Link: `color: #dc3406`.
- Hover: Unterstreichung erscheint, `text-underline-offset: 5px`.
- **Keine** Mega-Menüs.
- Rechts: Primary-Button „Mitglied werden" in Grösse `sm`.

### Footer

- Hintergrund `#000000`, Text weiss.
- Vier Spalten: Logo-Block (mit Beschreibung + Kontakt-Mail), Themen, Engagement, Verein.
- Spalten-Überschriften: `font: 600 14px 'Jost'`, mit 1 px Unter-Border in `rgba(255,255,255,0.12)`.
- Link-Farbe: `rgba(255,255,255,0.75)`, ohne Underline.
- Bottom-Bar mit Copyright + Impressum/Datenschutz, in `rgba(255,255,255,0.5)`.

### Callouts

Vier Tönungen, alle mit `3px` linkem Border:

| Ton | Hintergrund | Border-Left | Text | Icon |
|---|---|---|---|---|
| Info | `#fff` + 1px Border | `#000000` | `#000000` | `info` |
| Action | `#fbe7df` | `#dc3406` | `#9a2604` | `alert` |
| Success | `#e6efe0` | `#296310` | `#225210` | `check` |

Verwendung: laufende Vernehmlassungen, eingereichte Stellungnahmen, Hinweise zu Veranstaltungen.

### Links (inline)

- Im Fliesstext: `color: #dc3406; text-decoration: underline; text-underline-offset: 2px`.
- Im Hover: `color: #a82704`.
- In Navigation: standardmässig **ohne** Underline, Underline erscheint im Hover.

---

## 7. Iconography

- **Lucide-Icons** ([https://lucide.dev](https://lucide.dev)) via CDN oder NPM.
- Stroke-Stil: **1.5 px**, `stroke-linecap: round`, `stroke-linejoin: round`, `fill: none`, Farbe via `currentColor`.
- Grössen: **16 / 20 / 24 / 32 px**.
- Icons folgen der Textfarbe; Akzent in Primary nur bei isolierten Bedeutungsträgern (Pfeil, Erfolg).
- **Keine Emoji.**
- Custom Velo-/Infrastruktur-Icons müssen denselben Stil halten: `24×24 viewBox`, `1.5px stroke`, round caps & joins.

Häufig verwendete Icons im Projekt: `bike`, `map-pin`, `calendar`, `file-text`, `message-square`, `users`, `arrow-right`, `clock`, `mail`, `check`, `info`, `alert-circle`, `download`.

---

## 8. Logo

- Wortmarke in **Jost**: „velofreundliches" in Gewicht 500, darunter **„WETZIKON"** in Gewicht 700, getrackt (`letter-spacing` ca. `0.16em`).
- Links davon ein **6 px breiter, vertikaler Akzentbalken in `#dc3406`**.
- Drei Anwendungen: auf hellem Hintergrund, auf Surface (weiss), auf Dark (`#000000` — Text wird weiss, Balken bleibt orange).
- ⚠️ Im jetzigen System ist die Wortmarke ein **Platzhalter**. Sollte ein echtes Logo vorliegen, ersetzt es die Wortmarke direkt.

---

## 9. Imagery

- **Dokumentarisch, Tageslicht, real lokal.**
- Motive: Menschen auf dem Velo (Erwachsene mit Helm, Familien, Pendler — keine Stock-Athleten), Strasseninfrastruktur (Knoten, Markierungen, Schilder), zivile Settings (Stadtratssitzung, Begehung).
- **Vermeiden:** Drohnenaufnahmen, Mountainbike, Rennrad, Produktshots, alles was wie Velohersteller-Werbung wirkt.
- Behandlung: natürlich, ohne starke Filter; ein leichter warmer Ton zur Vereinheitlichung ist erlaubt.

---

## 10. Motion

- Sparsam.
- Easing: `cubic-bezier(.2, .7, .3, 1)` (`--vw-ease`).
- Dauern: `120ms` (fast), `180ms` (default), `280ms` (slow).
- Eintritts-Animationen: Fade + kleine Translate (`8–12 px`).
- Hover-Übergänge: `120–180ms`.
- **Kein** Bounce, **kein** Parallax, **kein** Scroll-Jacking.

---

## 11. Hover & Press (zusammengefasst)

- **Primary-Button:** hover → `#c32d05`; press → `#a82704` + `translateY(1px)`.
- **Secondary-Button:** hover → Hintergrund füllt sich mit `#000000`, Text wird weiss.
- **Ghost-Button:** hover → `#f4f3ef` als Hintergrund.
- **Nav-Link:** hover → Unterstreichung erscheint.
- **Inline-Link:** standardmässig unterstrichen; hover wechselt Farbe von `#dc3406` zu `#a82704`.
- **Card:** hover → grösserer Schatten (`--vw-shadow-lg`).

---

## 12. Layout-Regeln

- **Container** zentriert, `max-width: 1180px`, horizontales Padding `24 px`.
- **Prose-Container** (Artikel, Stellungnahmen-Text) `max-width: 680px`.
- **Sticky Top-Nav** (72 px), darunter ggf. **Sticky Sub-Nav / Tabs** (auf Themen-Seiten).
- **Hero**: 64 px Top-Padding, links Text (mit Eyebrow → H1 → Lead → Buttons → Stat-Zeile), rechts Full-Bleed-Bild (Höhe ~460 px, `border-radius: 0`).
- **Sektions-Heading** (Eyebrow + H2 + optional Lead) leitet jede Hauptsektion ein.
- **Footer** beginnt nach `margin-top: 96px`.

---

## 13. Direkte Übertragung in Code

### CSS-Variablen (Auszug — vollständig in `colors_and_type.css`)

```css
:root {
  /* Brand */
  --vw-primary:        #dc3406;
  --vw-primary-hover:  #c32d05;
  --vw-primary-press:  #a82704;
  --vw-primary-tint:   #fbe7df;
  --vw-dark:           #000000;
  --vw-success:        #296310;
  --vw-success-tint:   #e6efe0;

  /* Neutrals */
  --vw-bg:             #fafaf8;
  --vw-surface:        #ffffff;
  --vw-surface-2:      #f4f3ef;
  --vw-border:         #e8e6e0;
  --vw-muted:          #8a8a86;
  --vw-muted-strong:   #5e5e5a;

  /* Type */
  --vw-font-display:   'Jost', system-ui, sans-serif;
  --vw-font-body:      'Roboto', system-ui, sans-serif;

  /* Motion */
  --vw-ease:           cubic-bezier(.2,.7,.3,1);
  --vw-dur:            180ms;
}
```

### Tailwind-Mapping (falls genutzt)

```js
// tailwind.config.js (Auszug)
theme: {
  extend: {
    colors: {
      primary:  { DEFAULT: '#dc3406', hover: '#c32d05', press: '#a82704', tint: '#fbe7df' },
      dark:     '#000000',
      success:  { DEFAULT: '#296310', dark: '#225210', tint: '#e6efe0' },
      surface:  { DEFAULT: '#ffffff', sunken: '#f4f3ef', bg: '#fafaf8' },
      border:   { DEFAULT: '#e8e6e0', strong: '#cfcdc6' },
      muted:    { DEFAULT: '#8a8a86', strong: '#5e5e5a' },
    },
    fontFamily: {
      display: ['Jost', 'system-ui', 'sans-serif'],
      body:    ['Roboto', 'system-ui', 'sans-serif'],
    },
    borderRadius: { sm: '4px', md: '8px', lg: '12px' },
    boxShadow: {
      card:      '0 1px 2px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04)',
      cardHover: '0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06)',
    },
  }
}
```

---

## 14. Don'ts (Schnellreferenz)

- ❌ Kein „ß".
- ❌ Kein „Fahrrad" / „Bike" — immer „Velo".
- ❌ Kein Emoji.
- ❌ Keine Verläufe, kein Glasmorphismus.
- ❌ Keine Blatt-/Erd-/Öko-Ikonografie.
- ❌ Keine reinen ALL-CAPS-Headlines.
- ❌ Kein bouncy/spring-y Motion.
- ❌ Kein Drop-Shadow „heavy".
- ❌ Kein Mountainbike-/Sport-Vokabular.
- ❌ Keine ikonografischen SVGs für Hero-Bilder — echte Fotografie verwenden.

---

## 15. Do's (Schnellreferenz)

- ✅ Eine Akzentfarbe (`#dc3406`), selbstbewusst eingesetzt.
- ✅ Jost für Headlines, Roboto für Body.
- ✅ Warm-neutrale Off-White-Hintergründe.
- ✅ Lucide-Icons mit 1.5 px Stroke.
- ✅ Klare Editorial-Hierarchie: Eyebrow → H2 → Lead → Body.
- ✅ Campaign-Cards mit 3 px orangem Top-Border.
- ✅ Status-Pills mit kleinem farbigem Dot.
- ✅ Swiss-formatierte Zahlen, Daten, Währungen.
- ✅ „Sie"-Form, ausser bei expliziten Mitglieder-Kontexten.
- ✅ Foto realistisch, dokumentarisch, lokal.

---


---

# Erweiterung — Modal & Formular-Komponenten

*Hinzugefügt: 27. Mai 2026. Diese Sektion erweitert das Design-System um Komponenten, die im Rahmen der **VeloMelde-Karte** entstanden sind. Sie folgt denselben Konventionen wie der Rest des Systems (Schweizer Hochdeutsch, Jost/Roboto, VW-Tokens, 8 px-Grid).*

Dieses Dokument ergänzt das bestehende Design-System (`design-system-summary.md`) um Komponenten, die im Rahmen der VeloMelde-Karte entwickelt wurden. Es folgt denselben Konventionen: Schweizer Hochdeutsch, Jost/Roboto, VW-Tokens, 8px-Grid.

---

## 1. Modal

### Struktur

Das Modal ist zweispaltig auf Desktop (≥ 900px) und einspaltig auf Mobile/Tablet. Auf Mobile öffnet es als **Bottom Sheet** von unten.

```html
<div class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal">
    <div class="modal-left">
      <!-- Formular-Inhalt -->
    </div>
    <div class="modal-right" aria-hidden="true">
      <!-- Karten-Vorschau (nur Desktop) -->
    </div>
  </div>
</div>
```

### CSS

```css
/* Overlay */
.modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(3px);
  z-index: 2000;
  display: flex;
  align-items: flex-end;   /* Bottom Sheet auf Mobile */
  justify-content: center;
  overflow: hidden;
  opacity: 0; pointer-events: none;
  transition: opacity 180ms cubic-bezier(.2,.7,.3,1);
}
.modal-overlay.open { opacity: 1; pointer-events: all; }

/* Modal-Box */
.modal {
  background: #ffffff;
  border: 1px solid #e8e6e0;
  border-radius: 12px;
  width: 100%;
  max-width: 100vw;
  max-height: 92dvh;
  box-sizing: border-box;
  box-shadow: 0 2px 4px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transform: translateY(100%);   /* Bottom Sheet: startet ausserhalb */
  transition: transform 180ms cubic-bezier(.2,.7,.3,1);
}
.modal-overlay.open .modal { transform: translateY(0); }

/* Tablet (≥ 600px): zentriertes Modal, kein Bottom Sheet */
@media (min-width: 600px) {
  .modal-overlay { align-items: center; }
  .modal {
    width: 520px;
    max-width: 94vw;
    border-radius: 12px;
    transform: translateY(12px);
  }
}

/* Desktop (≥ 900px): zweispaltig */
@media (min-width: 900px) {
  .modal {
    width: 820px;
    max-width: 92vw;
    flex-direction: row;
  }
  .modal-right { display: block; flex: 0 0 340px; border-left: 1px solid #e8e6e0; position: relative; }
}
```

### Linke Spalte (Formular)

```css
.modal-left {
  flex: 1;
  padding: 24px 20px 20px;
  min-width: 0;
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
  position: relative;
  -webkit-overflow-scrolling: touch;
}
```

### Schliessen-Button

Runder Button oben rechts in `.modal-left`. Symbol: SVG (zwei diagonale Linien, `stroke-linecap: round`, 1.75px).

```css
.modal-close {
  position: absolute;
  top: 12px; right: 12px;
  width: 32px; height: 32px;
  border-radius: 999px;
  border: 1px solid #e8e6e0;
  background: #ffffff;
  color: #5e5e5a;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 180ms cubic-bezier(.2,.7,.3,1);
  z-index: 10;
}
.modal-close:hover {
  background: #f4f3ef;
  color: #000000;
  border-color: #cfcdc6;
}
```

```html
<button class="modal-close" type="button" aria-label="Modal schliessen">
  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <line x1="1" y1="1" x2="13" y2="13" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
    <line x1="13" y1="1" x2="1" y2="13" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
  </svg>
</button>
```

### Titel-Bereich

```css
.modal-title {
  font-family: 'Jost', system-ui, sans-serif;
  font-size: 20px; font-weight: 600;
  color: #000000;
  margin-bottom: 4px;
  letter-spacing: -0.01em;
  line-height: 1.2;
  padding-right: 40px; /* Abstand zum Schliessen-Button */
}
```

---

## 2. Formular-Felder im Modal

### Field Label (Eyebrow-Stil)

```css
.field-label {
  display: block;
  font-family: 'Roboto', system-ui, sans-serif;
  font-size: 12px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.08em;
  color: #6b6b67;  /* Kontrast-geprüft: 4.6:1 auf Weiss */
  margin-bottom: 8px;
}
.field-label .required { color: #dc3406; margin-left: 2px; }
.field-optional { font-weight: 400; text-transform: none; letter-spacing: 0; font-size: 13px; color: #6b6b67; }
```

### Text-Inputs und Textarea

```css
input[type=text], input[type=email], textarea {
  width: 100%;
  background: #ffffff;
  border: 1px solid #cfcdc6;
  border-radius: 4px;
  padding: 10px 12px;
  color: #000000;
  font-family: 'Roboto', system-ui, sans-serif;
  font-size: 15px; line-height: 1.55;
  min-height: 44px;  /* Touch-Minimum */
  outline: none;
  transition: border-color 180ms cubic-bezier(.2,.7,.3,1), box-shadow 180ms;
}
input:focus, textarea:focus {
  border-color: #dc3406;
  box-shadow: 0 0 0 3px rgba(220,52,6,0.15);
}
/* Inline-Validierung: erst nach blur zeigen */
input.field-valid, textarea.field-valid {
  border-color: #225210;
  box-shadow: 0 0 0 3px rgba(41,99,16,0.12);
}
input.field-invalid, textarea.field-invalid {
  border-color: #a82704;
  box-shadow: 0 0 0 3px rgba(220,52,6,0.12);
}
```

### Validierungshinweis

```css
.validation-hint {
  font-size: 12px; color: #dc3406;
  margin-top: 4px; display: none;
  font-family: 'Roboto', system-ui, sans-serif;
}
.validation-hint.visible { display: block; }
```

**Verhalten:** Fehler werden erst nach dem ersten `blur`-Event angezeigt, nicht beim Tippen. Beim Absenden werden alle Fehler gleichzeitig aufgedeckt, der Fokus springt auf das erste fehlerhafte Element.

### Zeichenzähler

```css
.char-counter {
  font-size: 12px;
  font-family: 'Roboto', system-ui, sans-serif;
  color: #6b6b67; text-align: right; margin-top: 3px;
}
.char-counter.ok { color: #225210; }
```

### Formular-Abstände

```css
.form-section { margin-top: 20px; }
```

---

## 3. Bewertungsskala (5 Stufen)

Fünf zusammenhängende Farbbalken ohne Icons. Auswahl über Outline-Ring.

### Farbskala

| Stufe | Label | Farbe |
|---|---|---|
| 1 | Gefährlich | `#b91c1c` |
| 2 | Schlecht | `#ea580c` |
| 3 | Akzeptabel | `#ca8a04` |
| 4 | Gut | `#65a30d` |
| 5 | Ausgezeichnet | `#15803d` |

⚠️ Diese Farben sind bewusst **unabhängig von `--vw-primary`** -- sie codieren einen semantischen Zustand (Gefahr bis Sicherheit) und dürfen nicht mit der Markenfarbe vermischt werden.

### HTML

```html
<fieldset class="rating-fieldset">
  <legend class="field-label">
    <span class="required" aria-label="Pflichtfeld">*</span>&nbsp;Bewertung
  </legend>
  <div class="rating-scale" id="rating-scale" role="group" aria-label="Bewertung der Velostelle">
    <button class="rating-btn" data-r="1" type="button" aria-pressed="false">
      <div class="r-bar"></div>
      <span class="r-label">Gefährlich</span>
    </button>
    <!-- ... weitere Stufen ... -->
  </div>
  <div class="validation-hint" role="alert">Bitte eine Bewertung auswählen.</div>
</fieldset>
```

### CSS

```css
/* Fieldset-Reset */
.rating-fieldset { border: none; padding: 0; margin: 0; min-width: 0; }

.rating-scale { display: flex; gap: 6px; margin-bottom: 6px; }

.rating-btn {
  flex: 1 1 0;        /* Gleiche Breite erzwingen */
  min-width: 0;       /* Verhindert min-content-Überlauf */
  min-height: 44px;   /* Touch-Minimum */
  display: flex; flex-direction: column; align-items: center;
  border: none; background: transparent; cursor: pointer; padding: 0 0 4px;
}

.rating-btn .r-bar {
  width: 100%; height: 36px;
  border-radius: 4px;
  outline: 3px solid transparent;
  outline-offset: 2px;
  transition: outline-color 180ms cubic-bezier(.2,.7,.3,1);
}

/* Farben */
.rating-btn[data-r="1"] .r-bar { background: #b91c1c; }
.rating-btn[data-r="2"] .r-bar { background: #ea580c; }
.rating-btn[data-r="3"] .r-bar { background: #ca8a04; }
.rating-btn[data-r="4"] .r-bar { background: #65a30d; }
.rating-btn[data-r="5"] .r-bar { background: #15803d; }

.rating-btn .r-label {
  font-family: 'Roboto', system-ui, sans-serif;
  font-size: 12px; font-weight: 400;
  color: #6b6b67; padding: 5px 2px 0;
  text-align: center; line-height: 1.2;
  width: 100%;
  overflow: hidden; word-break: break-word; hyphens: auto;
  transition: color 180ms, font-weight 180ms;
}

/* Ausgewählt */
.rating-btn[data-r="1"].selected .r-bar { outline-color: #b91c1c; }
/* ... weitere Stufen analog ... */
.rating-btn[data-r="1"].selected .r-label { color: #b91c1c; font-weight: 500; }
/* ... */

/* Hover */
.rating-btn:hover .r-label { color: #000000; }
```

### JavaScript-Verhalten

```javascript
// aria-pressed synchron mit visueller Auswahl halten
function selectRating(r) {
  document.querySelectorAll('.rating-btn').forEach(function(b) {
    b.classList.remove('selected');
    b.setAttribute('aria-pressed', 'false');
  });
  var btn = document.querySelector('.rating-btn[data-r="' + r + '"]');
  btn.classList.add('selected');
  btn.setAttribute('aria-pressed', 'true');
}
```

---

## 4. Foto-Upload

```css
.upload-area {
  border: 1px dashed #cfcdc6;
  border-radius: 4px;
  padding: 14px 10px;
  text-align: center; cursor: pointer;
  background: #f4f3ef;
  min-height: 52px;
  display: flex; align-items: center; justify-content: center;
  position: relative;
  transition: border-color 180ms cubic-bezier(.2,.7,.3,1), background 180ms;
}
.upload-area:hover, .upload-area.drag-over {
  border-color: #cfcdc6;   /* Neutral -- kein Rot, kein Fehler-Signal */
  background: #f4f3ef;
}
.upload-area input[type=file] {
  position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.upload-hint { font-size: 13px; color: #5e5e5a; line-height: 1.5; }
.upload-hint strong { color: #000000; font-weight: 500; }

/* Thumbnails */
.photo-previews { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
.photo-thumb {
  position: relative; width: 60px; height: 60px;
  border-radius: 4px; overflow: hidden; border: 1px solid #e8e6e0; flex-shrink: 0;
}
.photo-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.photo-thumb-remove {
  position: absolute; top: 3px; right: 3px;
  width: 18px; height: 18px; border-radius: 50%;
  background: rgba(0,0,0,0.5); color: #fff; border: none; cursor: pointer;
  font-size: 11px; display: flex; align-items: center; justify-content: center;
}
```

**Wichtig:** Hover-Farbe des Upload-Bereichs ist bewusst neutral (`#cfcdc6`, nicht `#dc3406`). Rot würde einen Fehler suggerieren. Der Upload ist optional.

---

## 5. Aktionsbuttons im Modal

```css
.modal-actions { display: flex; gap: 8px; margin-top: 20px; }

/* Primär -- aktiviert sich wenn Formular valide */
.btn-primary {
  background: #f4f3ef; color: #6b6b67;  /* Inaktiv: gedimmt */
  border: 1px solid #e8e6e0; flex: 1;
  transition: background 200ms cubic-bezier(.2,.7,.3,1), color 200ms, border-color 200ms;
}
.btn-primary.ready {
  background: #dc3406; color: #fff; border-color: transparent;
}
.btn-primary.ready:hover { background: #c32d05; }
.btn-primary.ready:active { background: #a82704; transform: translateY(1px); }
/* Kein disabled -- Button ist immer klickbar, zeigt aber Zustand */
```

**UX-Prinzip:** Der Primary-Button ist nie `disabled`. Er ist immer klickbar und wechselt visuell von gedimmt (`#f4f3ef`) zu aktivem Rot (`#dc3406`), sobald alle Pflichtfelder erfüllt sind. Beim Klick auf den gedimmten Button werden alle Fehler gleichzeitig angezeigt.

---

## 6. Sicherheitsfrage (Captcha-Ersatz)

Einfache Rechenaufgabe als Bot-Schutz -- kein API-Key, kein Backend, funktioniert offline.

```css
.captcha-row {
  display: flex; align-items: center; gap: 12px; margin-top: 2px;
}
.captcha-question {
  font-family: 'Roboto', system-ui, sans-serif;
  font-size: 14px; color: #000000;
  white-space: nowrap; flex-shrink: 0;
}
.captcha-input { width: 80px; text-align: center; }
```

```javascript
// Neue Aufgabe generieren (bei jedem Modal-Öffnen und bei falscher Antwort)
function generateCaptcha() {
  var a = Math.floor(Math.random() * 9) + 1;
  var b = Math.floor(Math.random() * 9) + 1;
  captchaAnswer = a + b;
  document.getElementById('captcha-question').textContent = 'Was ist ' + a + ' + ' + b + '?';
}
```

Eingabefeld: `type="text"`, `inputmode="numeric"` (Mobile: Zahlentastatur), `pattern="[0-9]*"`, `maxlength="3"`, `autocomplete="off"`.

---

## 7. Hinweistext / Field Note

Für erklärende Texte unterhalb eines Eingabefeldes (z.B. E-Mail-Bestätigung):

```css
.field-note {
  font-size: 12px; color: #6b6b67;
  margin-top: 5px; line-height: 1.45;
  font-family: 'Roboto', system-ui, sans-serif;
}
```

---

## 8. UX-Richtlinien für Modals

### Touch-Flächen

- Alle interaktiven Elemente: `min-height: 44px` (Apple HIG).
- Ausnahmen: Karten-interne Toggles (Layer-Switch), die UI-Chrome sind: `min-height: 36px` akzeptabel.
- Schliessen-Button: 32×32px ist ausreichend, da er oben rechts platziert ist und gut erreichbar.

### Barrierefreiheit

- `role="dialog"` und `aria-modal="true"` auf dem Modal-Container.
- `aria-labelledby` verweist auf die `id` des Modal-Titels.
- Bewertungsgruppe in `<fieldset>` + `<legend>` (nicht `<label>`).
- Rating-Buttons: `aria-pressed="true/false"` synchron halten.
- Fehlermeldungen: `role="alert"` für sofortige Screenreader-Ankündigung.
- Live-Bereiche (Adresse, Zeichenzähler): `aria-live="polite"`.
- `aria-required="true"` auf Pflichtfeldern.
- Schliessen per ESC-Taste implementieren.

### Validierung

- **Nie** `disabled` auf dem Absende-Button -- immer klickbar.
- Inline-Validierung erst nach `blur`, nicht bei `input`.
- Beim Absenden: alle Fehler gleichzeitig, Fokus auf erstes fehlerhaftes Element.
- `aria-describedby` verbindet Input mit Zeichenzähler und Fehlermeldung.

### Mobile-spezifisch

- Bottom Sheet mit `transform: translateY(100%)` → `translateY(0)`.
- Scrollbarer Formular-Container mit `-webkit-overflow-scrolling: touch`.
- Bei Focus: `scrollIntoView` auf dem Container-Element (nicht `window`), mit 120ms Verzögerung für Keyboard-Animation.
- `visualViewport.resize` abfangen für Nachkorrektur.
- `inputmode="email"` für E-Mail-Felder, `inputmode="numeric"` für Zahlen.

---

*Erstellt: Mai 2026 — Ergänzung zu design-system-summary.md (Stand: 26. Mai 2026)*

---

*Letzte Aktualisierung: 27. Mai 2026.*
