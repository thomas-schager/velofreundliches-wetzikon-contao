# VeloMelder — Data Contract for the New Backend/API

Status: **prepared for a not-yet-created backend repo** (PHP/Symfony or Laravel). This document
describes what `public/additions/velo-melder.html` currently does **client-side only**, as
precisely as the code allows, so the new backend can reproduce/replace it with a real API. It is
not a description of an existing API — no API exists yet; the static page currently reads
`velo-routes.geojson` and `velo-meldungen.json` as flat files and holds new submissions only in
page memory (nothing persists on submit).

Source read for this document: `public/additions/velo-melder.html`,
`public/additions/velo-meldungen.json`, `public/additions/velo-routes.geojson`.

---

## 1. The two resources

1. **Meldungen** ("reports") — point feedback submitted by visitors about a specific spot
   (rating + comment + optional photos + optional name). Today: a static JSON array, loaded
   once, extended only in memory when the form is submitted (lost on page reload).
2. **Velonetz routes** — the cycling-network line data (existing infra + planned "Vorschlag
   gelbes Band" proposals + cantonal/leisure routes), edited today via a hand-rolled Leaflet
   Draw editor (`?edit` mode) and persisted as one static `velo-routes.geojson` file, downloaded
   by hand and re-uploaded to the server.

The new backend's job is to become the read/write source for both, replacing the two static
files with real endpoints.

---

## 2. Meldung (report) — submission shape

This is the exact object built by `submitEntry()` when the form passes validation
(`public/additions/velo-melder.html`, function `submitEntry`):

```js
{
  id: Date.now(),                       // client-generated, NOT a real ID — see §2.2
  lat: pendingLatLng.lat,
  lng: pendingLatLng.lng,
  rating: selectedRating,               // 1-5, see §2.3
  comment: comment,                     // trimmed string, validated >= 10 chars client-side
  photos: pendingPhotos.map(p => p.dataUrl), // array of base64 data: URLs — see §2.4, must change
  name: nameVal,                        // trimmed string, OPTIONAL, may be ''
  anonymous: !nameVal,                  // true when name was left empty
  address: lastGeocodedAddress,         // reverse-geocoded street + city, or null — see §2.5
  addressDistanceM: lastGeocodedDistanceM, // meters between the pin and the geocoded address point, or null
  createdAt: new Date().toISOString()
  // no email on the entry — see §2.6
}
```

This is also, field-for-field, the shape of the **display/read** data the page fetches from
`velo-meldungen.json` (used as the "future API response" stand-in) — see the 10-item sample in
that file for realistic values, e.g.:

```json
{
  "id": "m-1001",
  "lat": 47.3241,
  "lng": 8.7971,
  "rating": 1,
  "comment": "Velostreifen endet abrupt vor der Kreuzung ohne Übergang zur Strasse. …",
  "photos": ["https://picsum.photos/seed/velo-1001-1/480/360"],
  "address": "Usterstrasse 12, Wetzikon",
  "addressDistanceM": 8,
  "name": "Michael Frei",
  "anonymous": false,
  "createdAt": "2026-06-14T09:32:00+02:00"
}
```

Note the sample file's `photos` are plain HTTPS image URLs, not data URLs — that's the intended
**future** shape once photos go through real upload/storage (§2.4); the *current, live* client
code still produces base64 `dataUrl`s because there's nowhere else to put them.

### 2.1 Field reference

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | string/number | server-assigned | Client sends none; backend must assign (e.g. UUID or DB auto-increment cast to string, matching the sample file's `"m-1001"` style). |
| `lat`, `lng` | float | yes | WGS84 decimal degrees. Set by a map click (default mode) or by "Standort anpassen" drag-to-reposition. No bounding-box validation exists client-side — worth adding server-side (Wetzikon-area sanity check) to reject stray taps. |
| `rating` | integer 1–5 | yes | See §2.3 for the fixed scale. |
| `comment` | string | yes | Client enforces **min 10 characters** (trimmed) before enabling submit; no max length is enforced anywhere today — the backend should pick and enforce one (e.g. 1000–2000 chars) since nothing currently stops an unbounded paste. |
| `photos` | array of string | no | 0–N, no client-side count or file-size cap exists today (a real gap — see §2.4). |
| `name` | string | no | Optional as of the most recent iteration of the form (label reads "Name (optional)", no validation). Empty string is valid. |
| `anonymous` | boolean | derived | `true` whenever `name` is empty. The frontend never displays raw email — see §2.6. Display logic elsewhere (`entry.anonymous ? 'Anonym' : (entry.name || 'Anonym')`) is defensive: even if `anonymous` were ever `false` with an empty `name`, it still falls back to "Anonym". |
| `address` | string or null | derived | Reverse-geocoded label (street + housenumber + city), see §2.5. `null` when geocoding failed or returned nothing usable. |
| `addressDistanceM` | number or null | derived | Distance in meters between the submitted point and the geocoded address's own coordinate. Used only to decide *whether* to show the address (see §2.5) — not otherwise meaningful to consumers. |
| `createdAt` | ISO 8601 string | yes | Client sets this at submit time (`new Date().toISOString()`); a real backend should set this server-side instead and ignore/overwrite any client-supplied value. |
| `email` | string | yes, but **never persisted on the entry** | Collected in the form (required, validated as `^[^\s@]+@[^\s@]+\.[^\s@]+$`) but intentionally excluded from the object that becomes an entry — see §2.6. |

### 2.2 IDs

Today `id: Date.now()` (a millisecond timestamp) — a placeholder, not collision-safe, and not
how the sample display data is keyed (`"m-1001"`, `"m-1002"`, …, a simple sequential slug). The
backend should assign real IDs on insert; the client should never need to invent one.

### 2.3 Rating scale (fixed, 5 steps — not user-configurable)

Defined once in JS (`var RATINGS = {...}`) and duplicated as an equivalent CSS-token-driven scale
in `colors_and_type.css` (`--color-rate-1` … `--color-rate-5`, same hex values). Whichever side
"owns" this scale going forward, both must stay in sync — see the design-system-sharing doc for
how.

| `rating` | Label (de) | Hex |
|---|---|---|
| 1 | Gefährlich | `#b91c1c` |
| 2 | Schlecht | `#ea580c` |
| 3 | Akzeptabel | `#ca8a04` |
| 4 | Gut | `#65a30d` |
| 5 | Ausgezeichnet | `#15803d` |

The header's 5 count pills (`updateHeader()`) are a simple `count-by-rating` aggregate computed
client-side over every loaded entry. The new API can either keep serving the full list and let
the client aggregate (fine at current volumes) or expose a small aggregate endpoint later if the
dataset grows.

### 2.4 Photos — the part that most needs to change

**Current (must not carry over as-is):** each selected/dropped image is read client-side via
`FileReader.readAsDataURL()` and kept in memory as a base64 `data:` URL
(`pendingPhotos.push({ id, dataUrl })`); on submit, the array of raw base64 strings becomes
`entry.photos`. There is:
- **no file count limit**,
- **no file size limit**,
- **no server-side type check** (only a client-side `file.type.startsWith('image/')`, trivially
  bypassable, and the `accept="image/*"` input hint, which is only a UI filter).

This works only because nothing is actually persisted server-side today. **For the real API,
`photos` must become real file uploads** (`multipart/form-data`, stored on disk/object storage,
served back as URLs — matching the shape already used in `velo-meldungen.json`'s sample data,
i.e. `photos: ["https://.../photo.jpg", ...]`). Recommend the new backend define and enforce:
a max photo count (the demo data tops out at 3 per entry, majority have 0–1), a max file size per
photo, and a real server-side MIME/content sniff (not just the extension/declared type).

### 2.5 Address — the "next-best-address" rule

The form reverse-geocodes the dropped/dragged pin via **photon.komoot.io** (`GET
https://photon.komoot.io/reverse?lon=…&lat=…&lang=de`, no key required) and keeps two values:
- `rawAddress` — `"<street> <housenumber>, <city>"` built from Photon's response properties
  (falls back to `p.name` if there's no street), or `null` if the API returned nothing usable.
- `distanceM` — great-circle distance (haversine, `distanceMeters()`) between the *submitted
  point* and the *geocoded address's own coordinate* (not a road-snap distance — just how far the
  matched address point is from where the user actually placed the pin).

Both are stored as-is on the entry (`address`, `addressDistanceM`). **Display** (both in the pin
detail view and, identically, while the form's live preview updates) applies a shared tiered rule
(`formatAddressLabel(address, distanceM)`), which the backend/API consumers should replicate
exactly rather than re-derive differently:

| `addressDistanceM` | What's shown |
|---|---|
| `<= 20` | the address as-is |
| `20 < d <= 50` | `"In der Nähe von " + address"` |
| `> 50` (or `address`/`distanceM` is `null`) | nothing — no address shown at all |

Because this is a *display* rule, the backend should keep serving the raw `address` +
`addressDistanceM` pair (not a pre-formatted label) so any future client can apply the same
tiering — or apply it consistently, since two different frontends (this repo's static page and
whatever the new backend's admin UI shows) must not silently diverge here.

If the new backend does its own reverse geocoding (recommended, so it isn't dependent on the
frontend calling a third-party API at submit time and self-reporting the result), Photon's
`/reverse` endpoint is free/keyless and is what's used today; any equivalent geocoder is fine as
long as the two output fields (`address`, `addressDistanceM`) keep the same meaning.

### 2.6 Email — collected, never shown, never stored on the entry

The form has a required, validated email field (`^[^\s@]+@[^\s@]+\.[^\s@]+$`, no further
verification), with copy telling the user *"Wird nicht angezeigt. Die Meldung wird erst nach
Bestätigung per E-Mail veröffentlicht."* ("Not shown. Your report is only published after email
confirmation.") — i.e. the **intended** design is a double-opt-in flow: submit → confirmation
email → entry only goes live once confirmed. **None of that confirmation flow actually exists
today** — the static page has no backend to send anything, so the "email" is collected and
silently discarded; every submission goes "live" (into in-memory `entries`) immediately.

For the real API this means:
- `POST` accepts `email` as an input field for the confirmation step.
- The backend **must** store the email somewhere (moderation, spam/abuse handling, resending the
  confirmation link) but **must never expose it** through any endpoint or admin view that isn't
  explicitly the internal moderation/admin surface — the product requirement, established
  earlier in this project, is "never show an email address" on anything public-facing.
- A submitted Meldung should probably not be publicly visible/returned by the public
  read-endpoint until the confirmation link is clicked (matching the copy's promise), which is a
  real behavior change from today's "instant, unmoderated" demo.

### 2.7 Anti-spam

Today: a trivial client-side arithmetic captcha (`"Was ist 4 + 7?"`, single-digit + single-digit,
answer compared in JS). **This provides no real protection** — there is no server round-trip to
verify against, so it only stops the laziest scripted submissions. The real API needs actual
server-side abuse protection (rate limiting per IP, a real captcha/challenge like hCaptcha or
Cloudflare Turnstile, and/or a honeypot field) — the arithmetic-question UI can stay as the
*visible* affordance if you like the low-friction feel, but it must be backed by something real.

### 2.8 Suggested endpoints (framework-agnostic; adapt to Symfony/Laravel conventions)

| Method | Path (suggested) | Purpose | Notes |
|---|---|---|---|
| `GET` | `/api/meldungen` | List confirmed/published Meldungen | Replaces the static `velo-meldungen.json` fetch. Should exclude `email` entirely from the response shape. |
| `POST` | `/api/meldungen` | Create a new Meldung (starts confirmation flow) | Body ≈ §2.1 minus `id`/`createdAt`/`anonymous` (server-derived), plus `email`; photos as `multipart/form-data`, not base64. Returns 202/201 with no public entry visible yet if double opt-in is implemented. |
| `GET` | `/api/meldungen/confirm/{token}` | Confirm via emailed link | Publishes the entry; invalidates the token. |
| `GET` | `/api/routes` | Current Velonetz GeoJSON | Replaces the static `velo-routes.geojson` fetch — see §3. Response is a plain `FeatureCollection`, same shape as today's file. |
| `POST`/`PUT` | `/api/routes` (admin-only) | Replace/update route data | Replaces the "download GeoJSON, re-upload by hand" workflow from the `?edit` editor. |

Admin-only endpoints (route editing, Meldung moderation/list-with-email, deleting a Meldung)
require the login from the "Admin auth" decision (simple email+password, own user table) and
should sit behind auth middleware distinct from the public read endpoints above.

---

## 3. Velonetz routes — GeoJSON format

`public/additions/velo-routes.geojson` today: a single static file, a plain
`FeatureCollection` of `LineString` features, **WGS84 lon/lat pairs** (no CRS member — GeoJSON's
implicit default), no altitude. Read directly from the live file for this document
(35 features total).

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "LineString",
        "coordinates": [[8.792517, 47.319754], [8.792517, 47.319755], ...]
      },
      "properties": { "type": "vorschlag-gelbes-band" }
    }
  ]
}
```

### 3.1 `properties`

Every feature in the live file today has **exactly one** property key: `type` (a string key into
the `ROUTE_TYPES` registry below). The editor's own export function (`buildGeoJSON()`) also
writes a second, **conditional** property:

- `direction`: `"one-way"` or `"both-ways"` — written only for route types where
  `!band && !noDirection` (see table below). No feature in the current live file actually has
  this key (the live data happens to be 100% band-type routes), but the schema allows it and the
  editor UI has a direction radio-toggle for applicable types, so the backend's GeoJSON
  read/write must preserve it when present and default it to `"one-way"` when absent, matching
  client behavior (`layer.routeDirection = (f.properties && f.properties.direction) || 'one-way'`).

No feature carries an ID, a name/label, a timestamp, or an author — the current model is
anonymous, un-versioned line geometry keyed only by `type`. If the new backend wants edit
history/attribution for routes (who drew/changed a segment, when), that's new scope beyond what
exists today — worth deciding explicitly rather than assuming.

### 3.2 `ROUTE_TYPES` registry (the full set; a given frontend view may only show a subset)

This exact array is the single source of truth for every route type's key, display label,
line color, weight, and rendering style, duplicated verbatim inside
`public/additions/velo-melder.html` (and copied into `velo-velonetz-embed-3.html` per the
`compile-velo-views` skill) — **this registry itself should probably move server-side** once a
backend exists, so both frontends and any admin UI read it from one place instead of a hand-kept
JS array.

| `key` | Label (de) | Color | Weight | Band? | Band style | Notes |
|---|---|---|---|---|---|---|
| `veloweg` | Veloweg | `#15803d` | 5 | — | — | Plain line, direction-aware |
| `velotrottoir` | Velotrottoir | `#65a30d` | 5 | — | — | Plain line, direction-aware |
| `velostreifen` | Velostreifen | `#ca8a04` | 5 | — | — | Plain line, direction-aware |
| `veloroute` | Veloroute | `#ea580c` | 3 | — | — | Dashed (`8 5`), direction-aware |
| `vorschlag-gelbes-band` | Vorschlag gelbes Band | `#ca8a04` | 3 (base) | yes | plain | Rendered as a wide, ~10m-real-world-width translucent band (opacity 0.65), not a thin line — this is the project's own "yellow band" campaign proposal overlay, present in **28 of the 35** live features |
| `kantonale-veloroute` | Kantonale Veloroute | `#64449b` | 3 | yes | outlined | Colored band with a white center stripe, opacity 0.85 |
| `kantonale-hauptverbindung` | Kantonale Hauptverbindung | `#64449b` | 3 | yes | plain | Opacity 0.65 |
| `kantonale-nebenverbindung` | Kantonale Nebenverbindung | `#64449b` | 3 | yes | narrow | Thinner band variant, opacity 0.75 |
| `erschliessungsnetz` | Erschliessungsnetz | `#111111` | 4 | — | — | `noDirection: true` — never gets a `direction` property |
| `freizeitverbindung` | Freizeitverbindung Veloland Schweiz | `#38bdf8` | 3 | yes | plain, `bandScale: 0.75` | Opacity 0.65, scaled band width |

"Band" types are the visually-thick, semi-transparent corridor overlays (used for planning
proposals / cantonal network hierarchy); non-band types render as normal thin route lines. This
distinction is purely a rendering concern client-side today — the backend only needs to store
`type` (and optionally `direction`) per feature; it does not need to know about band styling at
all unless it starts generating map previews itself.

### 3.3 What a real backend changes here

- Replace "download GeoJSON from the editor, FTP the file up" with `GET`/`POST` `/api/routes`
  (§2.8) — the editor UI (`?edit` mode) can keep its exact current drawing/editing behavior and
  just call the API instead of `localStorage` + manual file export.
- Decide whether route edits need history/undo server-side (today: none — the editor's
  "Zurücksetzen" button just clears `localStorage` and re-fetches the static file).
- Keep serving plain WGS84 `FeatureCollection` JSON — every current and planned frontend consumer
  (the public map, the embeddable mini-view, this data-contract's readers) expects exactly that
  shape; there's no reason to introduce a different wire format server-side.

---

## 4. Summary of gaps between "what the demo does" and "what a real backend needs"

1. **Photos**: base64-in-JSON → real file uploads with size/count/type limits.
2. **Email confirmation**: collected and discarded → must actually gate publication (double
   opt-in), and must never leak into any public/read response.
3. **Anti-spam**: fake client-side arithmetic → real server-side protection.
4. **IDs**: `Date.now()` placeholder → server-assigned, stable identifiers.
5. **`createdAt`**: client-supplied → server-assigned, client value ignored.
6. **No length/size limits anywhere** on comment, name, photo count/size — the backend is the
   first place any of these will actually be enforced.
7. **Route edit history**: none today; explicitly out of scope unless you decide otherwise.
8. **`ROUTE_TYPES` registry**: currently a hand-duplicated JS array per frontend file; should
   likely become backend-owned/served data once there's a backend to own it.
