# Protofilia - Portfolio Website

A modern, premium portfolio website built with PHP, CSS, and JavaScript with Supabase as the backend.

## Features
- 🎨 Premium dark-themed UI with glassmorphism effects
- 📱 Fully responsive design
- 🔐 Admin panel with project management
- 📧 Contact form with message management
- ⚙️ Profile settings editable from admin dashboard
- 🗄️ Supabase backend for data storage

## Tech Stack
- **Frontend:** HTML, CSS (vanilla), JavaScript
- **Backend:** PHP
- **Database:** Supabase (PostgreSQL)
- **Icons:** Lucide Icons
- **Fonts:** Inter, JetBrains Mono

## Setup

1. Clone this repository
2. Copy `includes/config.example.php` to `includes/config.php`
3. Update the Supabase credentials in `config.php`
4. Set up your Supabase database with the required tables
5. Run with XAMPP/WAMP or any PHP server

## Database Tables

### `settings`
| Column | Type |
|--------|------|
| id | uuid |
| name | text |
| title | text |
| bio | text |
| email | text |
| phone | text |
| location | text |
| github | text |
| linkedin | text |
| twitter | text |
| avatar_url | text |
| resume_url | text |

### `projects`
| Column | Type |
|--------|------|
| id | uuid |
| title | text |
| slug | text |
| description | text |
| category | text |
| tech_stack | text |
| image_url | text |
| live_url | text |
| github_url | text |
| is_featured | boolean |
| sort_order | integer |

### `messages`
| Column | Type |
|--------|------|
| id | uuid |
| name | text |
| email | text |
| subject | text |
| message | text |
| is_read | boolean |

## License
MIT
