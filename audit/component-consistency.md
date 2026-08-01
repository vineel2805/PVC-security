# Component Styling Analysis

## Buttons (6 Versions)
1. **Primary CTA (Gold):** `background: #b8860b; color: #fff;`
2. **Primary Gradient:** `background: linear-gradient(...)` (Used in Brand Hero)
3. **Secondary Outline:** `border: 1px solid #ddd;` (Clear filters)
4. **Cart Add Button:** `background: #c09853;` (Slightly different gold)
5. **Floating Actions (WhatsApp/Call):** Fixed rounded floating buttons.
6. **Disabled State:** `background: #e9e9e9;`

## Cards (4 Versions)
1. **Product Card (Grid):** `box-shadow: 0 4px 12px rgba(0,0,0,0.03)`
2. **Category Tile:** Different padding and shadow structure.
3. **Brand Banner Card:** `aspect-ratio: 2/1`
4. **Search Suggestion Card:** Inline, horizontal list item card.

## Inputs (3 Versions)
1. **Header Search Input:** Fully rounded (`border-radius: 999px`), gray background.
2. **Dedicated Search Input:** Large, white background, bottom border.
3. **Contact Form Input:** Standard rectangular inputs.

## Typography
- Fonts: Mixing `Montserrat`, `Outfit`, and `Inter`.
- Font Sizes: `11px`, `11.5px`, `12px`, `13px`, `13.5px`, `14px`, `15px`, `16px`, `18px`, `20px`, `22px`, `28px`.

## Recommendation
Implement a strict Design System (e.g., using CSS variables in a `tokens.css` file):
- 2 Primary Button variants (Solid / Outline).
- 2 Card variants (Interactive / Static).
- 1 Unified Input style.
- Standardized Typography Scale (Base 8 system).
