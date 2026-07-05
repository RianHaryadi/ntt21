import os
import re

files_to_update = [
    "resources/views/home.blade.php",
    "resources/views/destinations/index.blade.php",
    "resources/views/destinations/show.blade.php",
    "resources/views/hotel/index.blade.php",
    "resources/views/hotel/show.blade.php",
    "resources/views/paket_tour/index.blade.php",
    "resources/views/paket_tour/show.blade.php",
    "resources/views/culture/index.blade.php",
    "resources/views/user/dashboard.blade.php",
    "resources/views/livewire/travel-chat.blade.php",
    "resources/views/booking/create.blade.php",
    "resources/views/transaction/success.blade.php"
]

replacements = [
    (r'bg-ocean-900', 'bg-ink'),
    (r'bg-slate-900', 'bg-ink'),
    (r'bg-slate-800', 'bg-ink/90'),
    (r'bg-slate-50', 'bg-surface'),
    (r'bg-gray-50', 'bg-surface'),
    (r'bg-white', 'bg-paper'),
    (r'text-sunset-500', 'text-clay'),
    (r'text-orange-500', 'text-clay'),
    (r'text-slate-800', 'text-ink'),
    (r'text-gray-800', 'text-ink'),
    (r'text-slate-500', 'text-muted'),
    (r'text-gray-500', 'text-muted'),
    (r'border-slate-200', 'border-line'),
    (r'border-gray-200', 'border-line'),
    (r'font-montserrat', 'font-serif'),
    (r'focus:border-sunset-500', 'focus:border-clay focus:ring-1 focus:ring-clay/30'),
    (r'font-display', 'font-serif'),
    (r'bg-laut', 'bg-clay'),
    (r'text-laut', 'text-clay'),
    (r'border-laut', 'border-clay'),
    (r'from-laut', 'from-clay'),
    (r'to-laut', 'to-clay'),
    (r'ring-laut', 'ring-clay'),
    (r'bg-petrol', 'bg-ink'),
    (r'text-petrol', 'text-ink'),
    (r'from-petrol', 'from-ink'),
    (r'to-petrol', 'to-ink'),
    (r'bg-coral', 'bg-clay'),
    (r'text-coral', 'text-clay'),
    (r'shadow-\[0_0_20px_rgba\(15,110,99,0\.3\)\]', 'shadow-sm shadow-clay/20'),
    (r'shadow-\[0_0_40px_rgba\(15,110,99,0\.6\)\]', 'shadow-md shadow-clay/30'),
    (r'shadow-\[0_0_40px_rgba\(15,110,99,0\.5\)\]', 'shadow-md shadow-clay/30'),
    (r'shadow-\[0_8px_30px_rgba\(15,110,99,0\.5\)\]', 'shadow-md shadow-clay/30'),
    (r'shadow-\[0_8px_40px_rgba\(15,110,99,0\.7\)\]', 'shadow-lg shadow-clay/40'),
    (r'shadow-\[0_8px_30px_rgba\(249,115,22,0\.5\)\]', 'shadow-md shadow-clay/30'),
    (r'shadow-\[0_8px_40px_rgba\(249,115,22,0\.7\)\]', 'shadow-lg shadow-clay/40')
]

for filepath in files_to_update:
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
            
        new_content = content
        for pattern, repl in replacements:
            new_content = re.sub(pattern, repl, new_content)
            
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")
    else:
        print(f"File not found: {filepath}")
