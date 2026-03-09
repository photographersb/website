# Photographer SB — Platform Design System Master Spec

Date: 2026-03-07
Scope: Public pages, Photographer profiles, Events, Competitions, Search, User dashboard, Photographer dashboard, Admin panel
Principles: Keep brand identity, no full redesign, consistency first, reusable components

---

## 1) Design Token System

### 1.1 Color Tokens
Primary (brand burgundy)
- `primary-50` `#FDF2F5`
- `primary-100` `#FCE7ED`
- `primary-200` `#F9CFD9`
- `primary-300` `#F4A8BA`
- `primary-400` `#EC7393`
- `primary-500` `#DF4A6D`
- `primary-600` `#C62F51`
- `primary-700` `#8B1538`
- `primary-800` `#6F112D`
- `primary-900` `#530D22`

Secondary (slate/system)
- `secondary-50` `#F8FAFC` … `secondary-900` `#0F172A`

Accent (gold)
- `accent-50` `#FFFBEB` … `accent-900` `#78350F`

Semantic
- Success: `success-50` … `success-900`
- Warning: `warning-50` … `warning-900`
- Error: `error-50` … `error-900`

Neutral
- `neutral-0`, `neutral-50` … `neutral-950`

Implementation source of truth:
- `tailwind.config.js` color scales
- `resources/css/app.css` CSS variables (`--sb-*`)

### 1.2 Typography Scale
- H1: `2.25rem` / 700 / display
- H2: `1.875rem` / 700 / display
- H3: `1.5rem` / 700 / display
- H4: `1.25rem` / 600 / display
- H5: `1.125rem` / 600 / display
- Body: `1rem`
- Small: `0.875rem`
- Caption: `0.75rem`

Utilities:
- `sb-type-h1` … `sb-type-h5`
- `sb-type-body`, `sb-type-small`, `sb-type-caption`

### 1.3 Spacing System
Canonical spacing tokens:
- 4px (`--sb-space-xs`)
- 8px (`--sb-space-sm`)
- 12px (`--sb-space-sm-md`)
- 16px (`--sb-space-md`)
- 24px (`--sb-space-lg`)
- 32px (`--sb-space-xl`)
- 40px (`--sb-space-xl-2`)
- 48px (`--sb-space-2xl`)

Stack helpers:
- `sb-space-stack-xs|sm|sm-md|md|lg|xl|xl-2|2xl`

---

## 2) UI Component Library

Location:
- `resources/js/components/system/`

### Buttons
- `SbButton.vue`
- Variants: `primary`, `secondary`, `outline`, `ghost`, `danger`
- Sizes: `sm`, `md`, `lg`, `icon`

### Cards
- `SbCard.vue`
- `DashboardCard.vue`, `PhotographerCard.vue`, `EventCard.vue`, `CompetitionCard.vue`

### Badges
- `SbBadge.vue`
- `VerifiedBadge.vue`, `FeaturedBadge.vue`, `CategoryBadge.vue`, `StatusBadge.vue`

### Avatars
- `SbAvatar.vue` (sm/md/lg/xl, with fallback initials)

### Forms
- `SbInput.vue`
- `SbTextarea.vue`
- `SbSelect.vue`
- `SbCheckbox.vue`
- `SbFileUpload.vue`

### Navigation
- `TopNavShell.vue`
- `SidebarNavShell.vue`
- `SbBreadcrumbs.vue`

### Alerts
- `SbAlert.vue` (success/warning/error)

Exports:
- `resources/js/components/system/index.js`

---

## 3) Card Design Standard

Card base contract (`sb-ui-card`):
- Padding: default `16px`, dashboard `24px`
- Radius: `12px` (`--sb-radius-lg`)
- Shadow: `--sb-shadow-sm`
- Hover: `sb-ui-card--interactive` (raises to `--sb-shadow-md`)

Structural zones:
1. Image area (`sb-ui-card__image`, 16:9)
2. Badge area (`sb-ui-card__badges`)
3. Content area (`sb-ui-card__content`)
4. Metadata area (`sb-ui-card__meta`)
5. CTA area (`sb-ui-card__actions`)

Applies to:
- Photographer cards
- Event cards
- Competition cards

---

## 4) Image System

Rules:
- Photographer/User avatar: circle (`sb-ui-avatar`)
- Card image ratio: 16:9 (`sb-ui-card__image`)
- Profile hero ratio: 4:3 (`sb-ui-card__image--hero`)
- Missing image fallback: neutral background + initials placeholder

---

## 5) Badge Standard

Base:
- `sb-ui-badge`
- Padding: `0.2rem 0.55rem`
- Radius: pill (`9999px`)
- Font: `0.75rem`, 600

Standard variants:
- Verified, Featured, Category, Status
- Free, Paid, Available
- Success, Warning, Danger, Info

---

## 6) Form Design System

Contract:
- Inputs/selects/textareas use `sb-ui-input|select|textarea`
- Error state: `sb-ui-*-error`
- Focus ring: burgundy-based consistent ring
- Helper text: `sb-ui-form-help`
- Error text: `sb-ui-form-error`
- Counter text: `sb-ui-form-counter`
- Upload preview: `sb-ui-upload-preview`

Targeted parity areas:
- User profile forms
- Event create/edit forms
- Competition create/edit forms
- Admin CRUD forms

---

## 7) Dashboard Design Rules

Common dashboard pattern:
- Header block
- KPI cards (`DashboardCard` + `sb-ui-card--dashboard`)
- Main data region (charts/tables/activity)
- Action row (`SbButton` variants)

Cohesion rules:
- Same spacing rhythm: 16/24/32
- Same title hierarchy (H2/H3 + small captions)
- Same card and table edge treatment

---

## 8) Responsive Rules

Breakpoints:
- Mobile: `<640px`
- Tablet: `640–1023px`
- Laptop: `1024–1279px`
- Desktop: `>=1280px`

Requirements:
- Cards stack to one column on mobile
- Controls maintain minimum 44px touch target
- Typography never below `0.75rem`
- Dense tables switch to horizontal-scroll containers

---

## 9) Animation & Interaction

Motion principles:
- Subtle and fast (`150ms–300ms`)
- Use existing token transitions (`--sb-transition-fast`, `--sb-transition`)

Standard behaviors:
- Buttons: gentle hover/background shift
- Cards: small elevation increase on hover
- Loading: spinner/disabled opacity only
- Avoid large motion or decorative-only animation

---

## 10) Implementation Structure & Rollout

### 10.1 Folder Structure
```
resources/js/components/system/
  buttons/
  cards/
  badges/
  forms/
  navigation/
  avatars/
  alerts/
  index.js
```

### 10.2 Adoption Order (Roadmap)
1. Foundations (tokens + utility primitives)
2. Form-heavy pages (highest consistency gain)
3. Dashboard surfaces
4. Public listing cards (photographer/event/competition)
5. Remaining admin utilities

### 10.3 PR Quality Gate
- No hard-coded new colors/shadows/radii in migrated files
- Prefer `Sb*` components or `sb-ui-*` classes
- Preserve business logic/routes/api behavior
- Build must pass

### 10.4 Migration KPI Targets
- 90%+ form controls use `sb-ui-*` or `Sb*` primitives
- 100% new pages must use component library
- 0 new arbitrary color values outside token system

---

## Final Notes
This system is intentionally evolutionary: it standardizes and polishes the existing product without replacing core layouts or brand identity. It is designed to scale safely in a live Laravel + Vue platform with incremental, low-risk adoption.
