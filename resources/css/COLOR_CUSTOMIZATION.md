# Color Customization Guide

This project uses TailwindCSS v4.1 with a centralized color system for easy customization.

## Changing Colors

All colors are defined in `resources/css/app.css` under the `@theme` block. To change the color scheme, simply update the CSS custom properties:

### Primary Colors
```css
--color-primary-50: #eff6ff;   /* Lightest */
--color-primary-100: #dbeafe;
--color-primary-200: #bfdbfe;
--color-primary-300: #93c5fd;
--color-primary-400: #60a5fa;
--color-primary-500: #3b82f6;   /* Base */
--color-primary-600: #2563eb;   /* Hover states */
--color-primary-700: #1d4ed8;
--color-primary-800: #1e40af;
--color-primary-900: #1e3a8a;
--color-primary-950: #172554;  /* Darkest */
```

### Status Colors
- **Success**: `--color-success-*` (green tones)
- **Error**: `--color-error-*` (red tones)
- **Warning**: `--color-warning-*` (yellow/amber tones)
- **Info**: `--color-info-*` (blue tones)

## Component Usage

All components automatically use these colors:

- **Buttons**: Use `variant="primary"` for primary actions
- **Inputs**: Focus states use `primary-500` and `primary-600`
- **Alerts**: Use `type="success|error|warning|info"`
- **Links**: Use `text-primary-600 hover:text-primary-500`

## Example: Change to Purple Theme

Replace the primary color values in `app.css`:

```css
--color-primary-500: #8b5cf6;  /* Purple-500 */
--color-primary-600: #7c3aed;  /* Purple-600 */
--color-primary-700: #6d28d9;  /* Purple-700 */
```

All components will automatically update to use the new colors!

