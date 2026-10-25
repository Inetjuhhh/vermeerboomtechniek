# Vermeer Boomtechniek: rebuild plan

New Laravel version of vermeerboomtechniek.nl (currently WordPress with Divi + WPForms).
Same stack as inetjuhhh: Laravel 13, Filament 5, Tailwind 4, MySQL.

## Pages (content from the current site)

| New URL | Old URL | Content |
|---|---|---|
| `/` | `/` | Hero, 8 services, photos, reviews, contact block |
| `/diensten` + `/diensten/{slug}` | `/aanbod-boomverzorging/` | Snoeien, Rooien, Planten, Veiligheid controleren (VTA), Stormschade, Boomverankering |
| `/advies` | `/advies/` | Boomtechnisch advies (ETT), list of topics |
| `/boom-effect-analyse` | `/boom-effect-analyse/` | BEA explanation |
| `/het-bedrijf` | `/het-bedrijf/` | Bas, history, certificates, 4 core values |
| `/tarieven` | new | A few fixed prices (from Filament) |
| `/bereken-je-offerte` | new | Quote calculator (prototype: claude.ai artifact 1z4FeJdEeRC6QpuBzHTKYB) |
| `/projecten` | new, optional | Photo/project portfolio |
| `/contact` | `/contact/` | Form + details + map link |

Old URLs get 301 redirects (keeps Google rankings).

## Filament admin (for Ine and Bas)

- **Diensten**: title, slug, intro, body, photo, order, shown on home y/n
- **Tarieven**: fixed prices per service (label, price, unit, "vanaf" y/n)
- **Calculator**: per service the price per tree per height class, factors, surcharges, on/off
- **Aanvragen**: contact form and calculator leads (with answers + shown estimate), mail to info@
- **Reviews**, **Certificaten** (logo + name), **Foto's/projecten**
- **Instellingen**: phone, email, address, KvK, opening hours, work area

## Steps (each one a commit moment)

1. Base: Laravel + MySQL + Tailwind + Filament login ✅ (step 1 started)
2. Design choice: 4 directions as a comparison artifact, then layout + home
3. Content pages + models/seeders with the current texts
4. Contact form (spam protection with Turnstile, mail, saved in admin)
5. Tarieven + calculator (rates editable in Filament)
   - ✅ Admin part: Offerte calculator → Rekensom testen / Diensten en factoren / Overige kosten / Instellingen.
     The sum is in `app/Calculator/QuoteCalculator.php`, starting data (EXAMPLE prices) in `CalculatorSeeder`.
   - BEA has three methods (vaste prijs + per boom, uurbasis, vaste projectprijs): choose one with Bas.
   - Still to do: the public page `/bereken-je-offerte` (after the design choice) and saving requests as leads.
6. Photos: low-resolution copies of the current site are in `public/images/tijdelijk/` (see LEESMIJ.md there).
   **TODO: upload the real photos in high resolution** and then delete that folder.
7. SEO: meta/OG, sitemap, redirects from old URLs, LocalBusiness schema
8. Go-live (same Hetzner server as inetjuhhh? decide later)

## From the WordPress export (25-10-2026)

- Texts are the same as on the live site (already used in the design proposals).
- YouTube video "Vermeer Boomtechniek - Werkzaamheden": https://youtu.be/WZhOGdUF91s (home + Het bedrijf)
- Contact form fields: Naam, E-mailadres, Telefoonnummer, Onderwerp, Bericht; mails to info@vermeerboomtechniek.nl
- Old menu: Home, Aanbod Boomverzorging, Boomtechnisch advies (submenu: Advies, BEA), Het Bedrijf, Contact
- Design proposals (4 directions): claude.ai artifact CJm5M6zJDRWCGrsKx4zagE

## Needed from Ine/Bas

- Logo in high resolution (SVG preferably); the site now only has PNG up to 300px
- KvK number, BTW number (also needed for the footer and terms)
- Real prices for the calculator and the tarieven page
- Real reviews (are the 3 on the site real? Google reviews?)
- **The real photos in high resolution** (the ones in `public/images/tijdelijk/` are max. 800px)
- Which photos are Bas's own (the `gardener-*.jpg` look like stock photos)
- Boomverankering and Planten: more text, they now only have one line

## Status 25-10-2026 (evening)

- Design: **A "Schors"** (dark bark, moss green, Barlow Condensed) with the tree rows from H
  (Kroon / Stam / Wortels, two green and one brown). Chosen by Bas; B was built first and dropped.
  All colours/fonts are role-named tokens in `resources/css/app.css` (bg, surface, text, accent, crown/stem/roots),
  so another design later = mainly new token values + the few views in `resources/views`.
- Built: home, /diensten, /diensten/{slug}, /het-bedrijf, /contact (form + mail + spam trap), /bereken-je-offerte
  (Livewire, same sum as the admin), redirects from the old WordPress URLs.
- Admin: Berichten, Website (diensten incl. "deel van de boom", pagina's, reviews, certificaten, instellingen), Offerte calculator.
- Local login: inetjuhhh@gmail.com / password (local only! choose a strong password on the server).
- Privacy page /privacy (editable under Website → Pagina's). Contact messages are removed after 12 months (model:prune, daily): the server needs the scheduler cron.
- Optional page /houtbewerking (epoxy side gig): switch off with "Zichtbaar" under Website → Pagina's, or delete it. Example photos are CC BY 2.0 (see public/images/tijdelijk/houtbewerking/BRONNEN.md); no lamp photo yet.
- Still to do: real photos, Bas's prices, Turnstile on the contact form, SEO (sitemap, schema), go-live.
