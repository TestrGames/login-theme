# Login Theme

A [Pelican Panel](https://pelican.dev) plugin that restyles just the login
screen — modern glassmorphism card, animated gradient background — without
touching the rest of the panel's look.

[![Latest release](https://img.shields.io/github/v/release/TestrGames/login-theme?label=release)](https://github.com/TestrGames/login-theme/releases)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## What it does

Out of the box, Pelican's login screen is a plain card on a flat black
background. This plugin swaps that for:

- A soft, slowly-drifting animated gradient background
- A frosted-glass login card (blurred, translucent, subtle border and glow)
- A gradient-tinted heading

Nothing about the login *form* changes — same fields, same passkey/2FA
support, same validation. This is styling only.

It applies to **all three panels** (admin, app, server) at once, since
Pelican renders the same login page class for all of them.

## Customizing

Admin → Plugins → Login Theme → Settings:

- **Accent color** — tints the background glow, the heading, and the submit
  button
- **Background image URL** — set this to replace the animated gradient with
  your own image entirely (a full-bleed cover background instead); leave it
  blank to keep the gradient

No code or redeploy needed for either — just save the settings page.

## Install

1. Grab the latest `login-theme.zip` from
   [Releases](https://github.com/TestrGames/login-theme/releases)
2. Admin → Plugins → **Import** → upload the zip
3. `php artisan optimize:clear`
4. Open the login screen (or Admin → Plugins → Login Theme → Settings to
   customize the accent color / background image first)

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
  override). This plugin injects a `<style>` block scoped to exactly that
  class via
  `FilamentView::registerRenderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, ..., scopes: Login::class)`,
  so the CSS is present on every login route (`/login`, `/admin/login`,
  `/server/login`) and nowhere else.
- Selectors target Filament's own simple-page layout classes:
  `.fi-simple-layout` (full-page background), `.fi-simple-page` (the
  card), `.fi-simple-header-heading` (the heading text). These come from
  Filament's package views, not anything Pelican-specific, so a Filament
  major-version upgrade is the main thing that could change them.
- Settings (`src/LoginThemePlugin.php`) use the same
  `HasPluginSettings` + `EnvironmentWriterTrait` pattern as other Pelican
  plugins (e.g. `billing`, `subdomains`) — values are written to `.env` as
  `LOGINTHEME_ACCENT_COLOR` / `LOGINTHEME_BACKGROUND_IMAGE_URL`, not a
  database table.

</details>

## License

MIT — see [LICENSE](LICENSE).
