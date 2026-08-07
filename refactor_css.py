import tinycss2
import re
import collections

# Desired property order
PROPERTY_ORDER = [
    # Positioning
    "position", "top", "right", "bottom", "left", "z-index",
    # Display & Box Model
    "display", "flex", "flex-direction", "flex-wrap", "flex-flow", "justify-content", "align-items", "align-content", "align-self", "flex-grow", "flex-shrink", "flex-basis", "grid", "grid-template-columns", "grid-template-rows", "gap", "column-gap", "row-gap", "order",
    "width", "min-width", "max-width", "height", "min-height", "max-height",
    "box-sizing", "padding", "padding-top", "padding-right", "padding-bottom", "padding-left",
    "margin", "margin-top", "margin-right", "margin-bottom", "margin-left",
    "overflow", "overflow-x", "overflow-y", "-webkit-overflow-scrolling",
    # Borders & Outline
    "border", "border-top", "border-right", "border-bottom", "border-left", "border-width", "border-style", "border-color", "border-radius",
    "outline", "box-shadow",
    # Background
    "background", "background-color", "background-image", "background-position", "background-repeat", "background-size", "background-clip", "-webkit-background-clip",
    # Typography
    "color", "-webkit-text-fill-color", "font", "font-family", "font-size", "font-weight", "line-height", "text-align", "text-transform", "text-decoration", "white-space", "letter-spacing", "text-overflow",
    # Visual & Misc
    "opacity", "visibility", "filter", "backdrop-filter", "list-style", "appearance", "-webkit-appearance", "cursor", "pointer-events",
    # Animation & Transition
    "transform", "transition", "animation", "view-transition-name"
]

def get_prop_index(prop_name):
    try:
        return PROPERTY_ORDER.index(prop_name)
    except ValueError:
        return 999

# Color mappings to variables
VAR_MAP = {
    "#111111": "var(--pvc-dark-gray)",
    "#111": "var(--pvc-dark-gray)",
    "#000000": "var(--pvc-black)",
    "#000": "var(--pvc-black)",
    "#ffffff": "var(--pvc-white)",
    "#fff": "var(--pvc-white)",
    "#c9a14a": "var(--pvc-gold-primary)",
    "#d4af37": "var(--pvc-gold)",
    "#f2d482": "var(--pvc-gold-light)",
    "#b8860b": "var(--pvc-gold-dark)"
}

def replace_colors(value):
    val = value
    for hex_code, var_name in VAR_MAP.items():
        val = re.sub(f'(?i){hex_code}\\b', var_name, val)
    return val

def parse_declarations(decl_tokens):
    decls = []
    parsed = tinycss2.parse_declaration_list(decl_tokens, skip_comments=True, skip_whitespace=True)
    for d in parsed:
        if d.type == 'declaration':
            val_str = ''.join([t.serialize() for t in d.value if t.type != 'whitespace' or t.value != ' ']).strip()
            # Also re-serialize properly retaining inner spaces if needed, but simple join is ok for CSS values?
            # Wait, tinycss2 tokens include spaces as whitespace tokens. If we skip them in serialization, "1px solid red" becomes "1pxsolidred"!
            # We must serialize all tokens in d.value properly.
            val_str = ''.join([t.serialize() for t in d.value]).strip()
            val_str = replace_colors(val_str)
            decls.append((d.name, val_str, d.important))
    
    decls.sort(key=lambda x: (get_prop_index(x[0]), x[0]))
    return decls

def format_decls(decls, indent="    "):
    lines = []
    for name, val, imp in decls:
        imp_str = " !important" if imp else ""
        lines.append(f"{indent}{name}: {val}{imp_str};")
    return "\n".join(lines)

def process_css_file():
    with open('assets/css/global_header.css', 'r', encoding='utf-8') as f:
        css = f.read()
    
    css = re.sub(r'/\*.*?\*/', '', css, flags=re.DOTALL)
    
    rules = tinycss2.parse_stylesheet(css, skip_comments=True, skip_whitespace=True)
    
    media_queries = collections.defaultdict(list)
    global_rules = []
    imports = []
    
    for rule in rules:
        if rule.type == 'at-rule':
            if rule.lower_at_keyword == 'import':
                imports.append(rule.serialize())
            elif rule.lower_at_keyword == 'media':
                mq_prelude = ''.join(t.serialize() for t in rule.prelude).strip()
                mq_rules = tinycss2.parse_rule_list(rule.content, skip_comments=True, skip_whitespace=True)
                for mqr in mq_rules:
                    if mqr.type == 'qualified-rule':
                        sel = ''.join(t.serialize() for t in mqr.prelude).strip()
                        decls = parse_declarations(mqr.content)
                        if decls:
                            media_queries[mq_prelude].append((sel, decls))
            elif rule.lower_at_keyword == 'view-transition':
                decls = parse_declarations(rule.content)
                global_rules.append(('@view-transition', decls, 'at-rule'))
            elif rule.lower_at_keyword == 'keyframes':
                name = ''.join(t.serialize() for t in rule.prelude).strip()
                global_rules.append((f'@keyframes {name}', rule.content, 'keyframes'))
        elif rule.type == 'qualified-rule':
            sel = ''.join(t.serialize() for t in rule.prelude).strip()
            decls = parse_declarations(rule.content)
            if decls:
                global_rules.append((sel, decls, 'qualified'))
    
    def merge_rules(rules_list):
        merged = {}
        for sel, decls in rules_list:
            if sel not in merged:
                merged[sel] = []
            
            decl_dict = {d[0]: d for d in merged[sel]}
            for d in decls:
                decl_dict[d[0]] = d
            merged[sel] = list(decl_dict.values())
        
        final_rules = []
        for sel, decls in merged.items():
            decls.sort(key=lambda x: (get_prop_index(x[0]), x[0]))
            final_rules.append((sel, decls))
        return final_rules
    
    out = []
    out.append("/* ==========================================================================\n   PVC SECURITY - PREMIUM GLOBAL HEADER STYLES\n   ========================================================================== */\n")
    
    for imp in imports:
        out.append(imp.strip() + "\n")
    
    out.append("\n/* ==========================================================================\n   1. VARIABLES & ROOT\n   ========================================================================== */")
    
    root_decls = []
    other_global = []
    for item in global_rules:
        if item[0] == ':root':
            root_decls = item[1]
        else:
            other_global.append(item)
    
    has_primary = any(d[0] == '--pvc-gold-primary' for d in root_decls)
    if not has_primary:
        root_decls.append(('--pvc-gold-primary', '#c9a14a', False))
    
    if root_decls:
        out.append(f":root {{\n{format_decls(root_decls)}\n}}")
    
    out.append("\n/* ==========================================================================\n   2. GLOBAL HEADER BASE (DESKTOP)\n   ========================================================================== */")
    
    def classify_sel(sel):
        if 'view-transition' in sel or '@view-transition' in sel or '@keyframes' in sel:
            return 5
        elif 'search' in sel.lower():
            return 4
        elif 'mobile' in sel.lower() or 'overlay' in sel.lower():
            return 6
        elif 'cart' in sel.lower() or 'phone' in sel.lower() or 'icon-btn' in sel.lower() or 'utils' in sel.lower():
            return 3
        elif 'nav' in sel.lower() or 'mega' in sel.lower():
            return 2
        else:
            return 1
            
    grouped_global = collections.defaultdict(list)
    for sel, decls, rtype in other_global:
        if rtype == 'qualified':
            grouped_global[classify_sel(sel)].append((sel, decls))
        elif rtype == 'at-rule' or rtype == 'keyframes':
            grouped_global[5].append((sel, decls, rtype))
            
    sections = {
        1: "GLOBAL HEADER BASE",
        2: "NAVIGATION & MEGA MENU",
        3: "HEADER UTILITIES (ICONS, CART, PHONE)",
        4: "SEARCH RESULTS & DROPDOWN",
        5: "VIEW TRANSITIONS",
        6: "MOBILE MENU & OVERLAY"
    }
    
    for sec_id in sorted(sections.keys()):
        if sec_id != 1:
            out.append(f"\n/* ==========================================================================\n   {sec_id + 1}. {sections[sec_id]}\n   ========================================================================== */")
        
        items = grouped_global[sec_id]
        
        std_rules = [i for i in items if len(i) == 2]
        special_rules = [i for i in items if len(i) == 3]
        
        merged = merge_rules(std_rules)
        for sel, decls in merged:
            out.append(f"{sel} {{\n{format_decls(decls)}\n}}")
            
        for sel, decls, rtype in special_rules:
            if rtype == 'at-rule':
                out.append(f"{sel} {{\n{format_decls(decls)}\n}}")
            elif rtype == 'keyframes':
                out.append(f"{sel} {{")
                kf_rules = tinycss2.parse_rule_list(decls, skip_comments=True, skip_whitespace=True)
                for kfr in kf_rules:
                    kf_sel = ''.join(t.serialize() for t in kfr.prelude).strip()
                    kf_decls = parse_declarations(kfr.content)
                    out.append(f"    {kf_sel} {{\n{format_decls(kf_decls, '        ')}\n    }}")
                out.append("}")
    
    out.append("\n/* ==========================================================================\n   8. RESPONSIVE DESIGN (MEDIA QUERIES)\n   ========================================================================== */")
    
    def extract_width(mq):
        m = re.search(r'max-width:\s*(\d+)px', mq)
        return int(m.group(1)) if m else 0
        
    sorted_mq = sorted(media_queries.keys(), key=extract_width, reverse=True)
    
    for mq in sorted_mq:
        out.append(f"\n@media {mq} {{")
        merged = merge_rules(media_queries[mq])
        for sel, decls in merged:
            out.append(f"    {sel} {{\n{format_decls(decls, '        ')}\n    }}")
        out.append("}")
        
    with open('assets/css/global_header.css', 'w', encoding='utf-8') as f:
        f.write("\n".join(out))
    
    print("CSS Refactored successfully.")

if __name__ == "__main__":
    process_css_file()
