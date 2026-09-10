-- ============================================================================
-- COMPLETE BRAND UPDATE: Harmony → Chitrawan Nature Cure Hospital
-- Run this on your database server: mysql -u root wellness < app/scripts/update-brand.sql
-- ============================================================================

USE wellness;

-- ============================================================================
-- 1. Update site/brand section
-- ============================================================================
UPDATE page_sections 
SET heading = 'Chitrawan Nature Cure Hospital'
WHERE page_key = 'site' AND section_key = 'brand' AND heading = 'Harmony Wellness';

-- ============================================================================
-- 2. Update home/hero section
-- ============================================================================
UPDATE page_sections 
SET content = 'Welcome to Chitrawan Nature Cure Hospital, where ancient healing traditions meet modern therapeutic practices. Our team helps you achieve physical, mental, and emotional balance with personalized natural care.'
WHERE page_key = 'home' AND section_key = 'hero' AND content LIKE '%Harmony%';

-- ============================================================================
-- 3. Update home/about_intro section
-- ============================================================================
UPDATE page_sections 
SET kicker = 'About Chitrawan',
    content = 'Chitrawan Nature Cure Hospital is a holistic healthcare destination committed to promoting natural healing and preventive healthcare. Our peaceful environment, expert practitioners, and evidence-based therapies help patients restore balance and improve their quality of life.'
WHERE page_key = 'home' AND section_key = 'about_intro' AND content LIKE '%Harmony%';

-- ============================================================================
-- 4. Update home/story section
-- ============================================================================
UPDATE page_sections 
SET content = 'Chitrawan Nature Cure Hospital was born from a simple belief: true health comes from within. What began as a small clinic with two therapists has grown into a trusted wellness destination, guided every step of the way by the thousands of patients who trusted us with their care.\n\nEvery therapy we offer, every space we design, and every team member we welcome reflects the same promise — to treat you as family and walk beside you on your path to balance.'
WHERE page_key = 'home' AND section_key = 'story' AND content LIKE '%Harmony%';

-- ============================================================================
-- 5. Update about/hero section
-- ============================================================================
UPDATE page_sections 
SET content = 'Meet the people and philosophy behind Chitrawan Nature Cure Hospital — where natural healing is a way of life.'
WHERE page_key = 'about' AND section_key = 'hero' AND content LIKE '%Harmony%';

-- ============================================================================
-- 6. Update about/founder section
-- ============================================================================
UPDATE page_sections 
SET content = 'Dr. Rajesh Sharma established Chitrawan Nature Cure Hospital with the vision of creating a healthcare facility that promotes natural healing and healthy living. With more than twenty years of experience in holistic medicine, he believes prevention and lifestyle changes are the keys to long-term wellness.\n\nHis mission is to help people heal naturally and live healthier lives through personalized care and compassionate treatment.'
WHERE page_key = 'about' AND section_key = 'founder' AND content LIKE '%Harmony%';

-- ============================================================================
-- 7. Update about/group section
-- ============================================================================
UPDATE page_sections 
SET content = 'The people who make Chitrawan Nature Cure Hospital a place of healing.'
WHERE page_key = 'about' AND section_key = 'group' AND content LIKE '%Harmony%';

-- ============================================================================
-- 8. Update contact/info section (extras JSON with address, phone, email)
-- ============================================================================
UPDATE page_sections 
SET extras = '[
  "Our Address|Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal",
  "Phone|+977 56-535213",
  "Email|nchchitwan@gmail.com",
  "Opening Hours|Sunday – Friday: 8:00 AM – 7:00 PM\\nSaturday: 9:00 AM – 4:00 PM"
]'
WHERE page_key = 'contact' AND section_key = 'info';

-- ============================================================================
-- 9. Update contact/map section
-- ============================================================================
UPDATE page_sections 
SET content = 'Chitrawan Nature Cure Hospital · Bharatpur-15, Nepal',
    link = 'Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal'
WHERE page_key = 'contact' AND section_key = 'map' AND (content LIKE '%Harmony%' OR link LIKE '%Harmony%');

-- ============================================================================
-- 10. Update any other content fields with generic REPLACE
-- ============================================================================
UPDATE page_sections 
SET content = REPLACE(content, 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'),
    heading = REPLACE(heading, 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital'),
    kicker = REPLACE(kicker, 'Harmony Wellness Center', 'Chitrawan Nature Cure Hospital')
WHERE content LIKE '%Harmony%' OR heading LIKE '%Harmony%' OR kicker LIKE '%Harmony%';

-- ============================================================================
-- VERIFICATION: Show what changed
-- ============================================================================
SELECT '=== Brand Update Complete ===' AS status;
SELECT 'Brand name: Chitrawan Nature Cure Hospital' AS detail;
SELECT 'Email: nchchitwan@gmail.com' AS detail;
SELECT 'Phone: +977 56-535213' AS detail;
SELECT 'Address: Chitrawan Nature Cure Hospital, Bharatpur-15, Nepal' AS detail;

-- Check for any remaining Harmony references
SELECT COUNT(*) AS remaining_harmony_count FROM page_sections 
WHERE content LIKE '%Harmony%' OR heading LIKE '%Harmony%' OR kicker LIKE '%Harmony%' 
   AND content NOT LIKE '%in harmony%' AND heading NOT LIKE '%in harmony%' AND kicker NOT LIKE '%in harmony%';
