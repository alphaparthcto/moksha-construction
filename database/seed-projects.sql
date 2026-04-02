-- Seed existing projects into the database
-- Run after schema.sql

INSERT INTO projects (title, slug, type, size, location, year, description, featured_image, status, sort_order, created_by) VALUES

('Expansive Exhibition Center', 'expansive-exhibition-center', 'commercial', '280,000 sq ft', 'Clarksville, TN', 2024,
 'A sprawling multi-purpose hub for trade shows, conventions, cultural gatherings, and large-scale events. State-of-the-art amenities with flexible layouts designed for maximum versatility.',
 '/assets/images/projects/exhibition/main.png', 'published', 1, 1),

('Versatile Office Building', 'versatile-office-building', 'commercial', '200,000 sq ft', 'Tennessee', 2024,
 'A landmark office building featuring an integrated live sound studio, multiple presentation theaters, and a two-story cafe — redefining the modern workplace with seamless multi-use functionality.',
 '/assets/images/projects/office/main.png', 'published', 2, 1),

('Luxurious Hotel of Distinction', 'luxurious-hotel-of-distinction', 'hospitality', '90 Room Suites', 'Clarksville, TN', 2024,
 'An upscale hospitality venue featuring 90 luxurious room suites with world-class amenities and personalized services. Every detail crafted to deliver the ultimate guest experience.',
 '/assets/images/projects/hotel/main.png', 'published', 3, 1),

('Lotus Villa Apartments', 'lotus-villa-apartments', 'residential', '64 Units', 'Tennessee', 2024,
 'A ground-up apartment complex featuring contemporary architectural design, spacious units, a fitness center, communal gathering spaces, and landscaped green areas — built for modern community living.',
 '/assets/images/projects/apartments/drone-1.jpg', 'published', 4, 1),

('Commercial Retail Center', 'commercial-retail-center', 'commercial', '10,000 sq ft', 'Tennessee', 2024,
 'A ground-up retail center optimizing every square foot for functionality and visual appeal. Innovative design blended with practical commercial considerations — built to attract and serve customers from day one.',
 '/assets/images/projects/retail/photo-1.jpg', 'published', 5, 1);

-- Seed gallery images for projects that have multiple images
-- Exhibition Center (project_id will be 1 if seeded fresh)
INSERT INTO project_images (project_id, image_path, alt_text, sort_order) VALUES
(1, '/assets/images/projects/exhibition/main.png', 'Exhibition Center main exterior view', 1),
(1, '/assets/images/projects/exhibition/photo-1.jpg', 'Exhibition Center photo 1', 2),
(1, '/assets/images/projects/exhibition/photo-2.jpeg', 'Exhibition Center photo 2', 3),
(1, '/assets/images/projects/exhibition/photo-3.jpg', 'Exhibition Center photo 3', 4),
(1, '/assets/images/projects/exhibition/photo-4.png', 'Exhibition Center photo 4', 5),
(1, '/assets/images/projects/exhibition/photo-5.png', 'Exhibition Center photo 5', 6),
(1, '/assets/images/projects/exhibition/photo-6.jpg', 'Exhibition Center photo 6', 7);

-- Office Building (project_id = 2)
INSERT INTO project_images (project_id, image_path, alt_text, sort_order) VALUES
(2, '/assets/images/projects/office/main.png', 'Office Building main exterior view', 1),
(2, '/assets/images/projects/office/photo-1.jpg', 'Office Building photo 1', 2),
(2, '/assets/images/projects/office/photo-2.jpg', 'Office Building photo 2', 3),
(2, '/assets/images/projects/office/photo-3.jpg', 'Office Building photo 3', 4),
(2, '/assets/images/projects/office/photo-4.jpg', 'Office Building photo 4', 5),
(2, '/assets/images/projects/office/photo-5.jpg', 'Office Building photo 5', 6),
(2, '/assets/images/projects/office/photo-6.jpg', 'Office Building photo 6', 7),
(2, '/assets/images/projects/office/photo-7.jpg', 'Office Building photo 7', 8),
(2, '/assets/images/projects/office/photo-8.jpg', 'Office Building photo 8', 9),
(2, '/assets/images/projects/office/photo-9.jpg', 'Office Building photo 9', 10),
(2, '/assets/images/projects/office/photo-10.jpg', 'Office Building photo 10', 11),
(2, '/assets/images/projects/office/photo-11.jpeg', 'Office Building photo 11', 12),
(2, '/assets/images/projects/office/photo-12.jpeg', 'Office Building photo 12', 13),
(2, '/assets/images/projects/office/photo-13.jpeg', 'Office Building photo 13', 14);

-- Hotel (project_id = 3) — only has main image
INSERT INTO project_images (project_id, image_path, alt_text, sort_order) VALUES
(3, '/assets/images/projects/hotel/main.png', 'Hotel of Distinction main view', 1);

-- Lotus Villa Apartments (project_id = 4)
INSERT INTO project_images (project_id, image_path, alt_text, sort_order) VALUES
(4, '/assets/images/projects/apartments/drone-1.jpg', 'Lotus Villa aerial view 1', 1),
(4, '/assets/images/projects/apartments/drone-2.jpeg', 'Lotus Villa aerial view 2', 2),
(4, '/assets/images/projects/apartments/drone-3.jpeg', 'Lotus Villa aerial view 3', 3),
(4, '/assets/images/projects/apartments/drone-4.jpeg', 'Lotus Villa aerial view 4', 4),
(4, '/assets/images/projects/apartments/drone-5.jpeg', 'Lotus Villa aerial view 5', 5),
(4, '/assets/images/projects/apartments/drone-6.jpeg', 'Lotus Villa aerial view 6', 6),
(4, '/assets/images/projects/apartments/drone-7.jpeg', 'Lotus Villa aerial view 7', 7),
(4, '/assets/images/projects/apartments/drone-8.jpeg', 'Lotus Villa aerial view 8', 8);

-- Retail Center (project_id = 5)
INSERT INTO project_images (project_id, image_path, alt_text, sort_order) VALUES
(5, '/assets/images/projects/retail/photo-1.jpg', 'Retail Center photo 1', 1),
(5, '/assets/images/projects/retail/photo-2.jpg', 'Retail Center photo 2', 2),
(5, '/assets/images/projects/retail/photo-3.jpg', 'Retail Center photo 3', 3),
(5, '/assets/images/projects/retail/photo-4.jpg', 'Retail Center photo 4', 4);
