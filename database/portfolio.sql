-- ============================================
-- PROTOFOLIA PORTFOLIO - Supabase Database Setup
-- ============================================
-- Run this SQL in your Supabase SQL Editor
-- Dashboard → SQL Editor → New Query → Paste & Run

-- ============================================
-- 1. PROJECTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS projects (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    category VARCHAR(100),
    tech_stack TEXT, -- Comma-separated: "PHP, JavaScript, Supabase"
    image_url TEXT,
    live_url TEXT,
    github_url TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================
-- 2. MESSAGES TABLE (Contact Form)
-- ============================================
CREATE TABLE IF NOT EXISTS messages (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================
-- 3. SETTINGS TABLE (Site Configuration)
-- ============================================
CREATE TABLE IF NOT EXISTS settings (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    name VARCHAR(255) DEFAULT 'Anuja Kodikara',
    title VARCHAR(255) DEFAULT 'Full Stack Developer',
    bio TEXT DEFAULT 'Welcome to my portfolio!',
    email VARCHAR(255) DEFAULT 'anujakodikara@gmail.com',
    github VARCHAR(255) DEFAULT '#',
    linkedin VARCHAR(255) DEFAULT '#',
    twitter VARCHAR(255) DEFAULT '#',
    avatar_url TEXT,
    resume_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- ============================================
-- 4. INSERT DEFAULT SETTINGS
-- ============================================
INSERT INTO settings (name, title, bio, email, github, linkedin, twitter)
VALUES (
    'Anuja Kodikara',
    'Full Stack Developer',
    'A passionate developer with a love for creating beautiful, functional, and user-friendly web applications. I believe in writing clean code and creating experiences that make people''s lives easier.',
    'anujakodikara@gmail.com',
    'https://github.com/anujakodikara',
    'https://linkedin.com/in/anujakodikara',
    'https://twitter.com/anujakodikara'
);

-- ============================================
-- 5. ROW LEVEL SECURITY (RLS)
-- ============================================

-- Enable RLS on all tables
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;
ALTER TABLE messages ENABLE ROW LEVEL SECURITY;
ALTER TABLE settings ENABLE ROW LEVEL SECURITY;

-- PROJECTS: Anyone can read, only authenticated users can modify
CREATE POLICY "Projects are viewable by everyone" 
    ON projects FOR SELECT 
    USING (true);

CREATE POLICY "Projects can be created by authenticated users" 
    ON projects FOR INSERT 
    TO authenticated 
    WITH CHECK (true);

CREATE POLICY "Projects can be updated by authenticated users" 
    ON projects FOR UPDATE 
    TO authenticated 
    USING (true);

CREATE POLICY "Projects can be deleted by authenticated users" 
    ON projects FOR DELETE 
    TO authenticated 
    USING (true);

-- MESSAGES: Anyone can insert, only authenticated can read/modify
CREATE POLICY "Anyone can send a message" 
    ON messages FOR INSERT 
    WITH CHECK (true);

CREATE POLICY "Messages are viewable by authenticated users" 
    ON messages FOR SELECT 
    TO authenticated 
    USING (true);

CREATE POLICY "Messages can be updated by authenticated users" 
    ON messages FOR UPDATE 
    TO authenticated 
    USING (true);

CREATE POLICY "Messages can be deleted by authenticated users" 
    ON messages FOR DELETE 
    TO authenticated 
    USING (true);

-- SETTINGS: Anyone can read, only authenticated can modify
CREATE POLICY "Settings are viewable by everyone" 
    ON settings FOR SELECT 
    USING (true);

CREATE POLICY "Settings can be updated by authenticated users" 
    ON settings FOR UPDATE 
    TO authenticated 
    USING (true);

-- ============================================
-- 6. SERVICE ROLE POLICIES (for PHP backend)
-- ============================================
-- The service_role key bypasses RLS, so the PHP backend
-- can perform all operations using the service key.
-- The anon key is used for public read operations.

-- ============================================
-- 7. STORAGE BUCKET (Run separately)
-- ============================================
-- Create a 'portfolio' bucket in Supabase Storage:
-- Dashboard → Storage → New Bucket → Name: "portfolio" → Public: Yes

-- ============================================
-- 8. SAMPLE DATA (Optional)
-- ============================================
INSERT INTO projects (title, slug, description, category, tech_stack, is_featured, sort_order) 
VALUES 
(
    'E-Commerce Platform',
    'e-commerce-platform',
    'A full-featured e-commerce platform with product management, shopping cart, payment integration, and order tracking.',
    'Web App',
    'PHP, JavaScript, Supabase, CSS',
    true,
    1
),
(
    'Weather Dashboard',
    'weather-dashboard',
    'Real-time weather dashboard with interactive maps, 7-day forecasts, and location-based alerts.',
    'Web App',
    'React, Node.js, OpenWeather API',
    true,
    2
),
(
    'Task Manager App',
    'task-manager-app',
    'A beautiful task management application with drag-and-drop, categories, and team collaboration features.',
    'Mobile',
    'Flutter, Firebase, Dart',
    true,
    3
);
