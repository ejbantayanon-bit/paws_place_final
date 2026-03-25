-- 1. Create locations table
CREATE TABLE IF NOT EXISTS locations (
    location_id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 1
);

-- Insert known locations
INSERT IGNORE INTO locations (location_id, slug, name, is_active) VALUES 
(1, 'kennel-main', 'Kennel Main', 1),
(2, 'kennel-north', 'Kennel North', 1),
(3, 'paws-place', 'Paws Place', 1),
(13, 'pup-stop', 'Pup Stop', 1);

-- 2. Add icon to categories
ALTER TABLE categories ADD COLUMN IF NOT EXISTS icon VARCHAR(255) DEFAULT '<i class="ph-duotone ph-fork-knife"></i>';

-- Update categories icons
UPDATE categories SET icon = '<i class="ph-duotone ph-coffee"></i>' WHERE name LIKE '%Coffee%' OR name LIKE '%Milk Tea%' OR name LIKE '%Milktea%';
UPDATE categories SET icon = '<svg viewBox="0 0 256 256" style="width:1em;height:1em;display:inline-block;vertical-align:middle;"><path d="M192,104H64a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8H192a8,8,0,0,1,8,8V96A8,8,0,0,1,192,104Z" fill="currentColor" opacity="0.2"/><path d="M192,104H64a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8H192a8,8,0,0,1,8,8V96A8,8,0,0,1,192,104Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M72,104l16,112.5a16.2,16.2,0,0,0,16,13.8h48a16.2,16.2,0,0,0,16-13.8L184,104" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/><path d="M144,72V56a8,8,0,0,1,8-8h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16"/></svg>' WHERE name LIKE '%Soda%' OR name LIKE '%Fruity Soda%';
UPDATE categories SET icon = '<i class="ph-duotone ph-orange-slice"></i>' WHERE name LIKE '%Fruity%' AND name NOT LIKE '%Soda%';
UPDATE categories SET icon = '<i class="ph-duotone ph-star"></i>' WHERE name LIKE '%Specialty%';
UPDATE categories SET icon = '<i class="ph-duotone ph-plus-circle"></i>' WHERE name LIKE '%Add Ons%' OR name LIKE '%Addon%';
UPDATE categories SET icon = '<i class="ph-duotone ph-ice-cream"></i>' WHERE name LIKE '%Ice Cream%';
UPDATE categories SET icon = '<i class="ph-duotone ph-popsicle"></i>' WHERE name LIKE '%Ice Cream Bar%';
UPDATE categories SET icon = '<i class="ph-duotone ph-beer-bottle"></i>' WHERE name LIKE '%Milk Drink%';

-- 3. Add temperature_type to menu_items
ALTER TABLE menu_items ADD COLUMN IF NOT EXISTS temperature_type ENUM('Hot Brew', 'Cold Brew', 'None') DEFAULT 'None';

-- Update items: If it's in Coffee or Specialty category, guess based on name
UPDATE menu_items mi
JOIN categories c ON mi.category_id = c.category_id
SET mi.temperature_type = 'Cold Brew'
WHERE (c.name LIKE '%Coffee%' OR c.name LIKE '%Specialty%')
  AND (mi.name LIKE '%iced%' OR mi.name LIKE '%cold%' OR mi.name LIKE '%ice%' OR mi.name LIKE '%frappe%' OR mi.name LIKE '%blended%' OR mi.name LIKE '%frozen%');

UPDATE menu_items mi
JOIN categories c ON mi.category_id = c.category_id
SET mi.temperature_type = 'Hot Brew'
WHERE (c.name LIKE '%Coffee%' OR c.name LIKE '%Specialty%')
  AND mi.temperature_type = 'None';

-- 4. Update modifier prices
UPDATE modifiers SET price_add = 10.00 WHERE name IN ('Pearls', 'Coffee', 'Milk', 'Caramel Syrup', 'Coffee Jelly', 'Fruit Jelly');
