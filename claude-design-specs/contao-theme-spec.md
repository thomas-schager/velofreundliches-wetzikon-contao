# Velofreundliches Wetzikon — Contao-Theme-Spezifikation

> Handoff-Dokument für **Claude Code**. Ziel: das hier vorliegende Design-System (Palette B)
> als **Contao-Theme** umsetzen. Dieses Dokument beschreibt *wie* das System in Contao 5.6+
> abgebildet wird. Die *Inhalte* des Systems (Farben, Typografie, Komponenten, Copy-Regeln)
> stehen vollständig in **`design-system-summary.md`** — dieses File hier ergänzt nur die
> Contao-spezifische Umsetzung. Beide zusammen lesen.

---

## 0. Was schon fertig ist (verbatim übernehmen)

Diese Dateien im Projekt sind die **Quelle der Wahrheit** und sollen möglichst 1:1 ins Theme
wandern — nicht neu erfinden:

| Datei | Rolle im Theme |
|---|---|
| `colors_and_type.css` | **Design-Tokens** (alle `--vw-*` Custom Properties) + Typo-Primitives. Wird zur Basis-Stylesheet des Themes. |
| `responsive/site.css` | **Komponenten- & Layout-CSS** (mobile-first, fertige Breakpoints). Wird zum Haupt-Stylesheet des Themes. |
| `responsive/index.html` | **Referenz-Markup** für die Startseite. Jede Sektion = ein Contao-Baustein (siehe §4). Die Klassennamen (`vw-*`) sind bewusst Template-tauglich. |
| `design-system-summary.md` | Vollständige Design-Doku: Copy-Ton, Farb-Bedeutung, Komponenten-Verhalten, Don'ts. |
| `Mobile & Tablet Preview.html` | Visuelle Referenz, wie sich die Seite auf 390 px (Mobile) und 768 px (Tablet) verhält. |

**Wichtig:** Die Palette ist **Palette B** — `--vw-primary: #dc3406`, Text `#000000`, dunkle Flächen
`#000000`, Sekundärakzent Gelb `#fdd003`. Die alten Werte (`#ff5544` / `#3a3a3a`) sind ersetzt und
werden **nicht** mehr verwendet.

---

## 1. Tech-Entscheidungen

- **Contao 5.6 LTS-Linie (≥ 5.6, kompatibel bis 5.7).** PHP 8.2+.
- **Twig-Templates** (`.html.twig`), *keine* Legacy-`.html5`-Templates ausser wo Contao noch kein
  Twig-Pendant hat. Ausnahme: das Navigations-Template (siehe §5).
- **Page-Layout:** „**Twig layout with slots**" (`page/layout/*.html.twig` mit `{% slot %}`).
  Die HTML5-Layouts bleiben zwar verfügbar, das neue Slot-Layout ist aber der Zielzustand.
- **Globale Templates statt theme-spezifischer Templates.** In Contao 5.6.9 gibt es einen
  bekannten Bug, bei dem theme-spezifische Twig-Templates unter „Twig layout with slots"
  ignoriert werden und auf das globale Template zurückfallen. Darum: Templates **global** unter
  `/templates/...` ablegen, nicht unter `/templates/<themeFolder>/...`.
- **CSS:** plain CSS (kein SASS nötig — die `--vw-*` Custom Properties erledigen das Theming).
  Einbinden über das Layout („Externe CSS-Dateien" aus `files/`) **oder** per `{{ asset() }}` im
  Page-Template. Reihenfolge: `colors_and_type.css` **zuerst**, dann `site.css`.
- **Fonts:** Jost + Roboto via Google Fonts (`@import` steht bereits in `colors_and_type.css`).
  Für DSGVO/Schweizer Datenschutz optional self-hosten unter `files/theme/fonts/` und den
  `@import` durch `@font-face` ersetzen.
- **Keine** Build-Pipeline zwingend nötig. Wenn gewünscht: Webpack Encore unter
  `files/themes/velo/assets/` → `public/`. Sonst CSS direkt in `files/themes/velo/css/`.

---

## 2. Verzeichnis-Struktur des Themes

```
templates/
  page/
    layout/
      velo.html.twig          # Page-Layout mit Slots (header / main / footer)
  content_element/
    hero/velo_hero.html.twig   # Hero (Eyebrow→H1→Lead→Buttons→Stats + Bild)
    text/velo_callout.html.twig# Action-Callout (laufende Vernehmlassung)
    velo_melde.html.twig       # VeloMelde-Strip
    velo_positions.html.twig   # 3 Schwerpunkt-Karten
    velo_news.html.twig        # News-Karten (3er-Raster)
    velo_join.html.twig        # Dunkler Mitglied-werden-Block
  frontend_module/
    velo_events.html.twig      # Veranstaltungs-Liste (oder Eventlist-Modul stylen)
  mod_navigation_accessible.html5  # Burger-fähige Navigation (siehe §5)
  partials/
    velo_logo.html.twig        # Wortmarke (Akzentbalken + 2 Zeilen)
    velo_button.html.twig      # Button-Makro (variant/size/icon)
    velo_icon.html.twig        # Lucide-Inline-SVG-Makro
files/
  themes/velo/
    css/
      colors_and_type.css      # ← aus Projekt-Root kopieren
      site.css                 # ← aus responsive/ kopieren (Pfad zum @import anpassen!)
    img/                       # echte Fotos (Hero, News) ersetzen die Platzhalter
```

> Beim Kopieren von `responsive/site.css`: die erste Zeile
> `@import url('../colors_and_type.css');` auf den neuen relativen Pfad anpassen
> (`@import url('colors_and_type.css');` wenn beide im selben `css/`-Ordner liegen).

---

## 3. Page-Layout (`page/layout/velo.html.twig`)

Slots definieren, in die im Backend Module/Inhaltselemente einsortiert werden:

```twig
{% extends "@Contao/page/layout.html.twig" %}

{% block head %}
  {{ parent() }}
  {{ asset('themes/velo/css/colors_and_type.css', 'contao.assets.files') }}
  {{ asset('themes/velo/css/site.css', 'contao.assets.files') }}
{% endblock %}

{% block body %}
  <header class="vw-nav">{% slot header %}{% endslot %}</header>
  <main>{% slot main %}{% endslot %}</main>
  <footer class="vw-footer">{% slot footer %}{% endslot %}</footer>
{% endblock %}
```

- **Container-Regel:** Jede Sektion rendert innen ein `.vw-container` (max 1180 px, seitliches
  Padding 20→32 px). Die Section-Wrapper tragen `.vw-section` (vertikales Padding 40→56→72 px).
- Body-Hintergrund = `--vw-bg` (`#fafaf8`); dunkle Flächen (Join, Footer) = `--vw-dark` (`#000`).

---

## 4. Sektionen → Contao-Bausteine

Reihenfolge der Startseite wie in `responsive/index.html`. Jede Sektion ist ein eigenes
**Inhaltselement** (Content-Element, ab 5.6 direkt im Theme verwaltbar) bzw. Modul. Markup +
Klassen exakt aus `responsive/index.html` übernehmen.

| # | Sektion | Contao-Baustein | Template | Redaktionell pflegbar |
|---|---|---|---|---|
| 1 | **Hero** | Custom Content-Element `velo_hero` | `content_element/hero/velo_hero.html.twig` | Eyebrow, H1 (mit `<em>` für Akzentwort), Lead, 2 Buttons, 3 Stats, Bild |
| 2 | **Action-Callout** | Content-Element (Variante von `text`) | `content_element/text/velo_callout.html.twig` | Titel + Fliesstext + Link |
| 3 | **VeloMelde-Strip** | Custom Content-Element `velo_melde` | `content_element/velo_melde.html.twig` | Eyebrow, Titel, Text, Button-Ziel |
| 4 | **Positionen** (3 Karten) | Custom Content-Element `velo_positions` | `content_element/velo_positions.html.twig` | Liste aus {Icon, Eyebrow, Titel, Text, Link} |
| 5 | **Veranstaltungen** | **Event-Liste** (Calendar-Bundle) ODER Custom-Modul | `frontend_module/velo_events.html.twig` | aus Kalender; Template stylt Datum/Tag/Ort/Tag-Pill |
| 6 | **News** (3 Karten) | **Nachrichtenliste** (News-Bundle) | `content_element/velo_news.html.twig` bzw. `news_latest`-Template | aus News-Archiv |
| 7 | **Mitglied-Block** (dunkel) | Custom Content-Element `velo_join` | `content_element/velo_join.html.twig` | Eyebrow (gelb), H2, Text, 2 Buttons, Vorteils-Liste |
| 8 | **Footer** | Custom-Modul / Navigation + Custom-HTML | im Footer-Slot | Logo, 3 Link-Spalten, Kontakt, Bottom-Bar |

**Hinweis Events/News:** Wenn die offiziellen Contao-Bundles (`contao/calendar-bundle`,
`contao/news-bundle`) genutzt werden, nur deren Item-Templates überschreiben und die `vw-*`-Klassen
aus `responsive/index.html` einsetzen (`.vw-event`, `.vw-news__card`, `.vw-tag` …). Nicht die
Listenlogik neu bauen.

---

## 5. Navigation (Burger auf Mobile)

Contao 5.6 bringt eine **barrierefreie Navigation**, die auf Mobile automatisch zum Burger-Menü
wird — exakt das Verhalten aus `responsive/index.html`.

1. Frontend-Modul **Navigation** anlegen, Template **`mod_navigation_accessible.html5`** wählen.
2. Styling ausschliesslich über die `vw-*`-Klassen / Custom Properties anpassen — die Optik aus
   `site.css` (`.vw-nav`, `.vw-nav__link`, `.vw-burger`, `.vw-drawer`) übernehmen.
3. Aktiver Link → `--vw-primary` (`#dc3406`). Sticky-Header, Höhe 64 px (Mobile) / 72 px (Desktop),
   `backdrop-filter: blur(6px)`, 1 px Bottom-Border `--vw-border`.
4. Rechts der Primary-Button „Mitglied werden" (Grösse `sm`) — als zweites Element in den
   Header-Slot oder ins Navigations-Template integrieren.
5. Logo (`partials/velo_logo.html.twig`): 5 px Akzentbalken `--vw-primary` + „velofreundliches"
   (Jost 500) über „WETZIKON" (Jost 700, `letter-spacing: 0.16em`).

> Falls eine ältere Contao-Version ohne `mod_navigation_accessible` läuft: das Drawer-Markup +
> das kleine Toggle-Script aus `responsive/index.html` (unten, `data-open-drawer` /
> `data-close-drawer`) direkt ins Navigations-Template übernehmen.

---

## 6. Responsive-Verhalten (verbindlich)

Mobile-first. Breakpoints sind in `site.css` bereits gesetzt:

| Bereich | Mobile ≤ 599 | Tablet ≥ 600 | Desktop ≥ 960 |
|---|---|---|---|
| Navigation | Burger-Drawer | Burger-Drawer | volle Link-Leiste + CTA |
| Hero | gestapelt, Bild oben | gestapelt | 2-spaltig (Text \| Bild) |
| Positionen | 1 Spalte | 2 Spalten | 3 Spalten |
| News | 1 Spalte | 2 Spalten | 3 Spalten |
| Events | Datum über Inhalt | 96 px Datum-Spalte | 3-spaltig (Datum \| Inhalt \| Button) |
| Join / Footer | 1 Spalte | Footer 2 Spalten | Join 2-spaltig, Footer 4 Spalten |
| Buttons (Hero/Join/Melde) | vollbreit | auto | auto |

- Container-Padding: 20 px (Mobile) → 32 px (ab Tablet).
- Section-Padding vertikal: 40 → 56 → 72 px.
- Touch-Ziele ≥ 44 px (Buttons haben `min-height: 44px`).
- Schrift skaliert über `clamp()` (Hero-H1 `clamp(34px, 8vw, 68px)`), nie unter 12 px.

---

## 7. Komponenten-Mapping (CSS-Klassen schon vorhanden)

Alle Klassen liegen in `site.css` — Claude Code muss nur das Twig-Markup mit diesen Klassen
ausgeben:

- **Buttons:** `.vw-btn` + `--primary` / `--secondary` / `--ghost` / `--ondark`,
  Grössen `--sm` / `--lg`, `--block` (vollbreit). Icon (16 px Lucide) als letztes Kind.
- **Tags/Pills:** `.vw-tag` + `--neutral` / `--primary` / `--success` / `--outline`.
- **Callout:** `.vw-callout--action` (Aktion). Weitere Töne analog `design-system-summary.md`
  (Info, Success) ergänzen.
- **Karten:** `.vw-card` (Positionen), `.vw-news__card` (News), `.vw-event` (Events).
- **Eyebrow:** `.vw-eyebrow` (primary) / `.vw-eyebrow--accent` (gelb, nur auf Dark).
- **Bild-Platzhalter:** `.vw-img` + `.vw-img__cap` — im echten Theme durch `<img>`/`figure`
  ersetzen (echte, dokumentarische Fotos; siehe Imagery-Regeln in der Summary).
- **Icons:** Lucide, 1.5 px Stroke, `currentColor`. Als Twig-Makro `velo_icon(name, size)`.
  Benötigt mind.: `arrow-right`, `map-pin`, `users`, `file-text`, `mail`, `check`, `alert-circle`,
  `menu`, `x`.

---

## 8. Modal & Formulare (VeloMelde-Karte)

Die Erweiterung in `design-system-summary.md` (Abschnitt „Erweiterung — Modal & Formular-
Komponenten") beschreibt das vollständige Melde-Modal inkl. Bewertungsskala (5 Stufen,
`--vw-rate-1…5`), Foto-Upload, Captcha-Ersatz und Validierungsregeln. In Contao:

- Als **Formular** (Contao-Formgenerator) abbilden oder als Custom-Content-Element mit eigenem JS.
- Bewertungsskala-Farben (`#b91c1c … #15803d`) sind **semantisch** und dürfen **nicht** mit
  `--vw-primary` vermischt werden.
- Validierung: Fehler erst nach `blur`, Submit deckt alle Fehler auf, Fokus aufs erste Fehlerfeld.
  Submit-Button nie `disabled` — gedimmt → aktiv (`--vw-primary`).
- Mobile: Bottom-Sheet (`translateY(100%)`→`0`); ab 600 px zentriertes Modal; ab 900 px zweispaltig
  mit Karten-Vorschau rechts.

---

## 9. Copy- & Sprachregeln (kurz; Details in der Summary)

- **Schweizer Hochdeutsch, nie „ß"** → immer „ss". **„Velo"**, nicht „Fahrrad"/„Bike".
- „Sie"-Form; „Du" nur in Mitglieder-Event-Kontexten.
- Schweizer Zahlen/Daten/Währung: `1'500` · `27.5 %` · `15.03.2026` · `CHF 30`.
- **Keine Emoji.** Affordances über Lucide-Icons.
- Headlines im Satzanfang, **keine** ALL-CAPS-Betonung (Logo „WETZIKON" ist die einzige Ausnahme).
- Keine Öko-/Sport-/Korporativ-Sprache.

---

## 10. Definition of Done

- [ ] Theme im Backend angelegt, Layout „velo" mit Slots header/main/footer.
- [ ] `colors_and_type.css` + `site.css` eingebunden (in dieser Reihenfolge), Fonts laden.
- [ ] Startseite rendert alle 8 Sektionen aus `responsive/index.html` pixelnah.
- [ ] Navigation klappt auf Mobile in den Burger-Drawer; aktiver Link in `#dc3406`.
- [ ] Breakpoints 600 / 960 verhalten sich wie in §6 (mit echtem Mobile-Gerät + Tablet prüfen).
- [ ] Events/News kommen aus den Contao-Bundles, nicht hartkodiert.
- [ ] Alle Buttons ≥ 44 px Touch-Höhe; Fokuszustände sichtbar (`--vw-focus-ring`).
- [ ] Keine alten Farbwerte (`#ff5544`, `#3a3a3a`) mehr im Code.
- [ ] Platzhalter-`.vw-img` durch echte Fotos ersetzt.

---

*Erstellt: 2. Juni 2026 — Ergänzung zu `design-system-summary.md` für die Contao-Umsetzung (Palette B).*
