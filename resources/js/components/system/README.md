# Photographer SB System Components

This folder contains reusable UI primitives aligned with the platform design tokens.

## Usage

```vue
<script setup>
import { SbButton, SbCard, SbInput, SbBadge } from '@/components/system'
</script>

<template>
  <SbCard kind="dashboard">
    <h3 class="sb-type-h4">Card Title</h3>
    <SbInput label="Name" />
    <SbButton variant="primary">Save</SbButton>
  </SbCard>
</template>
```

## Rules
- Use tokenized classes (`sb-ui-*`, `sb-type-*`) for consistency.
- Do not hard-code new colors, radii, or shadows.
- Keep interactions subtle and consistent.
