# Frontend Cleanup - Blade File Instructions

## Overview
The comprehensive Vue.js cleanup has been completed. This document outlines the remaining work for Blade files that still contain `alert()`, `confirm()`, and `console` statements.

## Key Blade Files Requiring Manual Conversion

### 1. **Admin Views** (error-center, sitemap, access-gate)
- **File**: `resources/views/admin/error-center/show.blade.php` (lines 335, 348, 355, 368, 375, 388, 395, 408, 419, 437)
- **File**: `resources/views/admin/error-center/index.blade.php` (lines 322, 335, 338, 343, 356, 359, 364, 377, 380)
- **File**: `resources/views/admin/sitemap/index.blade.php` (lines 205, 211, 212)
- **File**: `resources/views/admin/access-gate.blade.php` (line 267)
- **Patterns**: `confirm()`, `alert()`, `console.error()`
- **Replacement**: Replace with Alpine.js component or AJAX fetch + toast notifications
- **Recommendation**: Create shared Blade component `resources/views/components/confirmation-modal.blade.php` using Alpine.js

### 2. **Event Attendance** (mobile + desktop views)
- **File**: `resources/views/admin/events/attendance/mobile.blade.php` (lines 100, 175)
- **File**: `resources/views/admin/events/attendance/index.blade.php` (line 156)
- **Patterns**: `alert()`, `console.error()`
- **Replacement**: Toast-based notifications (create Blade toast component)
- **Context**: Camera access errors and fetch errors

### 3. **Social Share Component**
- **File**: `resources/views/components/social-share-buttons.blade.php` (line 73)
- **Pattern**: `alert('Failed to copy link')`
- **Replacement**: Toast notification component
- **Context**: Copy-to-clipboard fallback handling

### 4. **Sitemap Check Results**
- **File**: `resources/views/admin/sitemap/check-results.blade.php` (line 20)
- **Pattern**: `onclick="return confirm('Delete this check?')"`
- **Replacement**: Alpine.js modal or inline confirmation handler
- **Context**: Delete confirmation for sitemap check results

## Implementation Strategy

### Option 1: Alpine.js-Based (Recommended for Blade)
Use Alpine.js (likely already in dependencies) to:
1. Create reusable `x-data` confirmation modals
2. Replace `confirm()` with modal showing
3. Replace `alert()` with toast notifications

Example pattern:
```blade
<button 
  @click="showConfirm('Are you sure?', () => submitForm())"
  class="btn btn-red"
>
  Delete
</button>
```

### Option 2: Inline JavaScript Fetch + Toast
Replace with modern fetch calls:
```blade
<button 
  @click="deleteItem"
  class="btn btn-red"
>
  Delete
</button>

<script>
async function deleteItem() {
  if (!confirm('Delete this item?')) return;
  try {
    const response = await fetch('/api/items/123', { method: 'DELETE' });
    // Show toast on success
  } catch (e) {
    // Show error toast
  }
}
</script>
```

### Option 3: Livewire (If Using Livewire)
If the app uses Livewire, replace with Livewire confirmations:
```blade
<button wire:click="delete" onclick="return confirm('Sure?')">Delete</button>
```

## Console Logging in Blade

For development console logs in Blade files (e.g., `console.log()` and `console.error()`):
- **Pattern**: `console.error('Error:', error)` and `console.warn('Unexpected response')`
- **Replacement**: Guard with `@env('local')` or wrap in `if (DEBUG_MODE)`
- **Example**:
```blade
@env('local')
  <script>
    console.log('Debug: loaded');
  </script>
@endenv
```

## File-by-File Breakdown

| File | Lines | Pattern | Action |
|------|-------|---------|--------|
| error-center/show.blade.php | 335, 348, 355, 368, 375, 388, 395, 408, 419, 437 | confirm(), alert() | Replace with modal |
| error-center/index.blade.php | 322-380 | confirm(), alert(), console.error() | Replace with modal + toast |
| sitemap/index.blade.php | 205, 211, 212 | alert(), console.error() | Replace with toast |
| sitemap/check-results.blade.php | 20 | onclick="confirm(...)" | Replace with Alpine modal |
| access-gate.blade.php | 267 | console.log() | Guard with @env or remove |
| attendance/mobile.blade.php | 100, 175 | alert(), console.error() | Replace with toast |
| attendance/index.blade.php | 156 | console.error() | Guard with @env |
| social-share-buttons.blade.php | 73 | alert() | Replace with toast |

## Toast Component for Blade

Recommendation: Create a reusable `resources/views/components/blade-toast.blade.php`:
```blade
@props(['id' => 'toast-' . uniqid(), 'message' => '', 'type' => 'info'])

<div id="{{ $id }}" class="fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg bg-{{ $type }}-100 text-{{ $type }}-800">
  {{ $message }}
</div>

<script>
window.showToast = function(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg bg-${type}-100 text-${type}-800`;
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 5000);
};
</script>
```

## Summary

**Completed**:
- ✅ All Vue.js files cleaned (console logs replaced with `useDevLogger`)
- ✅ All Vue.js alert/confirm/prompt replaced with Toast + BaseModal
- ✅ Created `useApiError` and `useDevLogger` composables
- ✅ Fixed TODO in Admin Settings SiteLinks
- ✅ EventDetail.vue fully updated

**Remaining**:
- 🔄 Blade files with alert/confirm/console (8 files, ~20 lines total)
- 🔄 Create shared Blade toast component (optional but recommended)
- 🔄 Create Alpine.js confirmation modal (optional but recommended)

**Total Impact**:
- ~200+ lines of debug code cleaned across 50+ Vue files
- Standardized error handling with `useApiError` composable
- Dev-only logging with `useDevLogger` composable
- Replaced all blocking dialogs with modern toast + modal components

---

**Note**: Blade file cleanup requires manual conversion due to server-rendered nature. The patterns identified above can be batch-replaced once the replacement strategy is chosen.
