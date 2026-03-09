# Photographer SB — Design System Token Spec & Migration Checklist

Date: 2026-03-07  
Prepared by: UX Audit (Codebase-anchored)

## 1) Design System Foundation (Non-breaking)

### 1.1 Core Principles
- Preserve current IA and page structure.
- Standardize visual language (type, spacing, cards, buttons, badges, forms, images).
- Remove ad-hoc hex/arbitrary utilities from page-level markup over time.
- Keep brand identity (`burgundy` / `primary`) unchanged.

### 1.2 Source of Truth
- Primary token source: `tailwind.config.js`
- Global semantic variables: `resources/css/app.css`
- Admin semantic variables: `resources/css/admin-theme.css`
- Rule: new UI styles must map to tokenized values only.

---

## 2) Token Specification (Proposed Standard)

## 2.1 Color Roles
Use semantic role names in component APIs; map internally to Tailwind/theme values.

- `color.text.primary` → `text-gray-900`
- `color.text.secondary` → `text-gray-600`
- `color.text.muted` → `text-gray-500`
- `color.surface.page` → `bg-gray-50`
- `color.surface.card` → `bg-white`
- `color.surface.subtle` → `bg-gray-50`
- `color.brand.primary` → `bg-burgundy` / `text-burgundy`
- `color.brand.primary.hover` → `bg-burgundy-dark`
- `color.border.default` → `border-gray-200`
- `color.border.strong` → `border-gray-300`
- `color.status.success` → `green-600` set
- `color.status.warning` → `amber-600` set
- `color.status.danger` → `red-600` set
- `color.status.info` → `blue-600` set

### 2.2 Typography Scale
Standardize to this ladder only:
- Display: `text-5xl`, `text-4xl`
- H1: `text-3xl` (mobile), `md:text-4xl`
- H2: `text-2xl`
- H3: `text-xl`
- H4: `text-lg`
- Body large: `text-base`
- Body: `text-sm`
- Meta/caption: `text-xs`

Rules:
- Avoid `text-[10px]` and `text-[11px]` except one approved micro-label utility.
- Use consistent heading font strategy:
  - Marketing heroes: display allowed
  - App/dashboards/forms: sans hierarchy preferred

### 2.3 Spacing Scale
Allowed rhythm: `2, 3, 4, 6, 8, 10, 12` (Tailwind spacing units by page context).
- Section spacing: `py-8` desktop baseline
- Card padding: `p-4` (compact), `p-6` (default)
- Grid gaps: `gap-4` default, `gap-6` for feature grids

### 2.4 Radius Scale
Limit to:
- Inputs/buttons: `rounded-lg`
- Cards: `rounded-xl` (default), `rounded-2xl` (feature/hero cards only)
- Pills/badges/avatar chips: `rounded-full`

### 2.5 Elevation Scale
Limit to:
- `shadow-sm` (default card)
- `shadow-md` (interactive hover)
- `shadow-lg` (hero/featured only)

Avoid mixing `shadow`, `shadow-card`, `shadow-xl`, custom shadow literals in same module.

### 2.6 Motion Scale
- Default transition: `transition-colors duration-200`
- Elevated interactive card: `transition-all duration-300 hover:-translate-y-0.5`
- Avoid stacking `scale + translate + heavy shadow` unless feature card.

### 2.7 Breakpoint and Touch Targets
- Interactive control min-height: `min-h-[44px]`
- Ensure icon buttons maintain `w-10 h-10` on mobile where tap-critical.

---

## 3) Unified Component Contracts

## 3.1 Button (single shared primitive)
Variants:
- `primary`, `secondary`, `outline`, `danger`, `ghost`

Sizes:
- `sm`, `md`, `lg`

Rules:
- No raw hex in button classes.
- No per-page custom gradients for standard CTAs.
- Disabled/loading behavior standardized.

## 3.2 Card
Variants:
- `default` (white + border + shadow-sm)
- `interactive` (+hover border + shadow-md)
- `feature` (rounded-2xl, optional subtle gradient)

## 3.3 Badge/Tag
Types:
- `neutral`, `success`, `warning`, `danger`, `info`, `brand`

Rules:
- Unified text size (`text-xs`), padding (`px-2.5 py-1`), radius (`rounded-full`)
- Uppercase only for status microchips where needed.

## 3.4 Form Controls
- Input/select/textarea base class shared.
- Focus ring style consistent across app.
- Error state: red border + helper text + optional `FormError` component.

## 3.5 Tabs
- Base tab button style and active indicator standardized.
- Mobile: horizontal scroll allowed but same active treatment everywhere.

## 3.6 Alerts/Toasts/Empty/Skeleton
- Consolidate to shared `Toast`, `Alert`, `EmptyState`, `LoadingSkeleton` usage.
- Remove custom ad-hoc empty/loading blocks gradually.

## 3.7 Avatar & Image Containers
- Avatar sizes: `sm 32`, `md 40`, `lg 56`, `xl 72`.
- Avatar shape: `rounded-full` platform-wide.
- Card media aspect presets:
  - list cards: `16:9`
  - profile highlights: `4:5`
  - hero cover: `16:9`+ responsive crop

---

## 4) Image System Standardization

## 4.1 Fallback Rules
- Single helper/composable for image fallback behavior.
- Fallback priority:
  1) actual asset URL
  2) local placeholder asset
  3) generic icon state
- Eliminate inline DOM-manipulation fallback logic in templates.

## 4.2 Placeholder Policy
- Use local placeholders only (avoid external `placehold.co` in production UI).

## 4.3 Crop & Fit Rules
- Use `object-cover` for content imagery.
- Use `object-contain` for logos/icons.
- Do not mix arbitrary fixed heights with no ratio policy on similar cards.

---

## 5) Page-by-Page Migration Checklist

Legend:
- P0 = critical consistency risk
- P1 = high-visibility consistency polish
- P2 = standardization/cleanup

## 5.1 Global Shell (P0)
- [ ] `resources/js/App.vue` — unify nav/footer button/link tokens and spacing
- [ ] `resources/css/app.css` — publish public component utility classes (`sb-btn`, `sb-card`, `sb-input`)
- [ ] `resources/css/admin-theme.css` — align admin semantic token naming to shared conventions

## 5.2 Public Discovery & Listing (P0/P1)
- [ ] `resources/js/components/PhotographerSearch.vue` (P0)
  - [ ] replace custom hero/CTA one-off classes with tokenized variants
  - [ ] normalize card hover behavior and badge style
- [ ] `resources/js/Pages/Events.vue` (P0)
  - [ ] unify event card badge, CTA, and metadata text sizing
  - [ ] standardize pagination control style
- [ ] `resources/js/Pages/Competitions.vue` (P0)
  - [ ] remove raw hex style drift, use brand tokens
  - [ ] align filter controls with global form control spec
- [ ] `resources/js/Pages/CategoryPhotographers.vue` + `LocationPhotographers.vue` (P1)
  - [ ] normalize avatar fallback and card header hierarchy

## 5.3 Profile & Account Surfaces (P1)
- [ ] `resources/js/components/PhotographerProfile.vue`
  - [ ] reduce ad-hoc color literals
  - [ ] normalize badge and profile snapshot card styles
- [ ] `resources/js/Pages/PhotographerSettings.vue`
  - [ ] migrate to shared form field/button classes
  - [ ] unify validation/help text styles
- [ ] `resources/js/components/Auth.vue` and legacy auth pages
  - [ ] standardize input/button/feedback states
  - [ ] retire deprecated auth page styles from active routes

## 5.4 Dashboards (P0)
- [ ] `resources/js/components/PhotographerDashboard.vue`
  - [ ] map all cards to unified card variants
  - [ ] map all CTA buttons to shared button variants
  - [ ] normalize small text (`text-[11px]` -> approved meta token)
- [ ] `resources/js/Pages/Admin/Dashboard.vue`
  - [ ] align KPI card hierarchy with shared card/elevation system
  - [ ] ensure admin quick actions use shared button primitives
- [ ] `resources/js/Pages/Client/*.vue` (P1)
  - [ ] remove inline style usage where class tokens exist

## 5.5 Forms (Create/Edit flows) (P0)
- [ ] `resources/js/Pages/Admin/Events/Create.vue`
- [ ] `resources/js/Pages/Admin/Events/Edit.vue`
- [ ] `resources/js/Pages/CompetitionSubmit.vue`
- [ ] `resources/js/Pages/Verification/Create.vue`

For each:
- [ ] use shared `FormField`, `FormError`, `Button`
- [ ] standardize required indicators, helper text, and validation states
- [ ] enforce touch target + spacing consistency

## 5.6 Admin Modules (P1/P2)
- [ ] Events, Competitions, Users, Reviews, Payments index pages
- [ ] standardize table/action row controls
- [ ] normalize status chips and action buttons

## 5.7 Legacy Component Cleanup (P2)
- [ ] `resources/js/components/EventsList.vue`
- [ ] `resources/js/components/CompetitionsList.vue`

Actions:
- [ ] either remove if unused or restyle to match current pages
- [ ] avoid parallel visual systems in active codebase

---

## 6) Responsive & Accessibility Checklist

- [ ] Ensure all clickable controls are >=44px height where needed
- [ ] Replace micro text (<12px equivalent) in interactive contexts
- [ ] Check overflow and fixed elements on mobile (dropdown widths, sticky bars)
- [ ] Ensure focus-visible states are present and consistent
- [ ] Confirm color contrast for badge/text combinations

---

## 7) Execution Phases (Production-safe)

## Phase A — Foundation Lock (Week 1)
- Publish token spec and shared utility classes.
- Freeze new raw hex/arbitrary class usage in PR review.

## Phase B — Core Components (Week 2)
- Standardize Button/Card/Badge/Input/Tab components.
- Document usage examples.

## Phase C — High Traffic Surfaces (Weeks 3–4)
- Home/discovery/events/competitions/profile/dashboard pages first.

## Phase D — Forms and Admin Rollout (Weeks 5–6)
- Migrate create/edit flows and admin index/detail pages.

## Phase E — Cleanup & Governance (Week 7)
- Remove legacy visual paths.
- Add lint/checklist guardrails for consistency.

---

## 8) PR Acceptance Criteria Template

Every UI PR must pass:
- [ ] Uses shared component or approved tokenized classes
- [ ] No new raw hex literals in templates
- [ ] No new arbitrary text sizes unless approved
- [ ] Button/card/badge/form styles match spec
- [ ] Mobile/touch target verified
- [ ] Image fallback behavior uses shared utility

---

## 9) Immediate Next Step

Start with a pilot migration on:
1. `resources/js/Pages/Events.vue`
2. `resources/js/Pages/Competitions.vue`
3. `resources/js/components/PhotographerDashboard.vue`

These three surfaces provide the highest visible consistency impact with low architecture risk.
