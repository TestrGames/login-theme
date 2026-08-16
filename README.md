# Login Theme

A [Pelican Panel](https://pelican.dev) plugin that restyles just the login
screen — a split layout with a decorative gradient panel on one side and a
clean, borderless login form on the other — without touching the rest of
the panel's look.

[![Latest release](https://img.shields.io/github/v/release/TestrGames/login-theme?label=release)](https://github.com/TestrGames/login-theme/releases)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## What it does

Out of the box, Pelican's login screen is a plain card on a flat black
background. This plugin replaces that with a two-column layout:

- **Left** — a decorative panel: a soft, slowly-drifting animated gradient
  (or your own background image), a heading, and a tagline
- **Right** — the login form itself, flat and borderless on a clean dark
  background, with a gradient-tinted heading

On narrower screens the decorative panel hides itself and the form takes
the full width, so it still works fine on mobile.

Nothing about the login *form* changes — same fields, same passkey/2FA
support, same validation. This is styling only.

It applies to **all three panels** (admin, app, server) at once, since
Pelican renders the same login page class for all of them.

## Customizing

Admin → Plugins → Login Theme → Settings:

- **Accent color** — tints the gradient glow, the heading, and the submit
  button
- **Side panel background image URL** — replaces the animated gradient on
  the left panel with your own image; leave blank to keep the gradient
- **Side panel heading** / **tagline** — the text shown on the left panel

No code or redeploy needed for any of these — just save the settings page.

## Install

1. Grab the latest `login-theme.zip` from
   [Releases](https://github.com/TestrGames/login-theme/releases)
2. Admin → Plugins → **Import** → upload the zip
3. `php artisan optimize:clear`
4. Open the login screen (or Admin → Plugins → Login Theme → Settings to
   customize colors/text/image first)

## Updating

The panel checks [`update.json`](update.json) for a newer version and shows
an **Update** button in Admin → Plugins when one exists. Two things can
delay that:

| Layer | Delay | Force it |
|---|---|---|
| Panel's own update check | 10 minutes | `php artisan tinker --execute="cache()->forget('plugins.login-theme.update');"` |
| GitHub's CDN serving `update.json` | a few minutes | just wait |

<details>
<summary>Publishing a new version (maintainers)</summary>

<br>

1. Bump `"version"` in `plugin.json`
2. Commit, then `git tag vX.Y.Z && git push origin vX.Y.Z` (must match
   `plugin.json`'s version exactly)
3. [`.github/workflows/release.yml`](.github/workflows/release.yml) builds
   the zip, creates the GitHub Release, and rewrites `update.json`
   automatically.

</details>

<details>
<summary><strong>How it works, technically</strong></summary>

<br>

- Pelican renders the login page for all three panels through one shared
  class, `App\Filament\Pages\Auth\Login` (its own subclass of Filament's
  base login page — adds OAuth buttons, passkeys, captcha, but no view
  override). This plugin injects HTML/CSS scoped to exactly that class via
  `FilamentView::registerRenderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, ..., scopes: Login::class)`.
- `SIMPLE_LAYOUT_START` fires as the very first thing inside
  `<div class="fi-simple-layout">`, immediately before the column that
  holds the actual form (`.fi-simple-main-ctn`) — confirmed directly
  against Filament's own
  `packages/panels/resources/views/components/layout/simple.blade.php`.
  That means the markup injected here becomes a real DOM sibling *before*
  the form column, which is what makes the CSS flexbox split-screen layout
  possible without touching any Blade view: `.fi-simple-layout` becomes
  `display: flex`, the injected `.lt-side-panel` div is the first
  (left) child, `.fi-simple-main-ctn` is the second (right) child.
- The login form's own card background is made fully transparent
  (`.fi-simple-page`, `.fi-simple-page-content`) rather than styled as a
  translucent "glass" card — an earlier version tried a frosted-glass
  look, but `.fi-simple-page-content` has an opaque background baked into
  Filament's own CSS that peeked out past the rounded corners as a visible
  second box. Making both fully transparent removes that seam instead of
  fighting it.
- Settings (`src/LoginThemePlugin.php`) use the same
  `HasPluginSettings` + `EnvironmentWriterTrait` pattern as other Pelican
  plugins (e.g. `billing`, `subdomains`) — values are written to `.env`
  (`LOGINTHEME_ACCENT_COLOR`, `LOGINTHEME_BACKGROUND_IMAGE_URL`,
  `LOGINTHEME_SIDE_PANEL_HEADING`, `LOGINTHEME_SIDE_PANEL_TAGLINE`), not a
  database table.

</details>

## License

MIT — see [LICENSE](LICENSE).
