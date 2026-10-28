---
trigger: always_on
description: Pragmatic Typography Token Usage and Reusable Component Extraction Policy.
---

# Pragmatic Typography & Reusable Component Extraction Policy

## 1. Structure-based Typography Token Usage
- **No Dogmatic Enforcement**: Developers and agents are NOT forced to use custom classes like `.text-heading`, `.text-title`, or `.text-sub` for every single text element.
- **Criteria Based on Structural Scope**:
  - **Isolated / One-off Micro-copy**: Fully acceptable to use standard Tailwind utility classes directly (e.g. `text-sm font-medium text-pine-subtle`, `text-xs text-stone-400`). Do not force custom semantic classes onto unique, one-off snippets.
  - **Major Sections, Page Headings & Repetitive Blocks**: HIGH PRIORITY to use predefined semantic tokens (`.text-display`, `.text-heading`, `.text-subheading`, `.text-lead`, `.text-sub`, `.text-body`, `.text-caption`, `.section-py`) to guarantee visual rhythm and synchronized responsive breakpoints across screens.

## 2. Reusable Component Extraction Priority
- **Component-First for Repetitive UI**: Always prioritize extracting into Blade components or partials (`<x-...>` or `components/...`) whenever a section, UI card, or structural block is repeated across multiple screens or locations (e.g. Story Cards, Scenario Cards, CSI evaluation pillars, Form Controls).
- **Single Source of Truth**: Eliminates dozens of duplicated HTML lines, ensures consistent maintainability, and prevents visual drift.

## 3. Button / CTA Standards & Mobile Touch Targets
- **Mandatory `padding-y` with `min-h`**: Never rely solely on rigid height (`h-10` / `h-12`) without vertical padding. Always pair with `py-*` (e.g., `min-h-[44px] py-2.5 px-5`, `min-h-[56px] py-4.5 px-8`) to prevent text clipping and cramped layouts on mobile.
- **Apple HIG / Google Material Minimum Touch Target**:
  - Hero CTA: `min-h-[56px] sm:min-h-[64px]` with `py-4.5 sm:py-5 px-8 sm:px-10` (use `.btn-cta`).
  - Standard Action: `min-h-[44px] sm:min-h-[48px]` with `py-2.5 sm:py-3 px-5 sm:px-6` (use `.btn-primary`).
  - Small / Icon Button: `min-w-[44px] min-h-[44px]`.
- **Safe Click Margin on Mobile**: Distance between clickable elements must be $\ge 8\text{px}$ (`gap-2`), recommended $12\text{px} - 16\text{px}$ (`gap-3` / `gap-4`) to eliminate accidental taps (fat-finger errors).

