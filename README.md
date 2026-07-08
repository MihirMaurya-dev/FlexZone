<div align="center">

# 🏋️‍♂️ FlexZone
### Track. Train. Transform.

![Views](https://komarev.com/ghpvc/?username=MihirMaurya-dev-FlexZone&label=Views&color=777bb4&style=flat)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.0-777bb4.svg?style=flat&logo=php)](https://www.php.net/)
[![JS Standard](https://img.shields.io/badge/JavaScript-ES6%2B-f7df1e.svg?style=flat&logo=javascript)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)


> **A high-performance, minimalist fitness ecosystem.**  
> Built for those who want a zero-friction training experience—no bloat, just pure progress. Designed with a **Mobile-First** approach and a clean **Neumorphic/Glassmorphism** aesthetic.

<br>

<img src="assets/about.png" alt="FlexZone Banner" width="100%">

</div>

## 🚀 Why FlexZone?

Most fitness apps are cluttered with ads and subscriptions. FlexZone is built by developers, for developers:
- **Zero Framework Bloat**: Pure Vanilla JS and CSS for maximum speed.
- **Privacy First**: Local database control and easy CSV data portability.
- **Developer Friendly**: Clean, RESTful API architecture using PHP 8.x.

## ✨ Core Engine

- **🎯 Intelligent Onboarding**: Adaptive profiling (Mifflin-St Jeor) to calculate targets.
- **⚡ The Garage**: A dynamic equipment-aware workout engine. It generates routines based on *exactly* what you have in your kit.
- **📊 Real-time Analytics**: Beautiful, interactive progress visualization powered by Chart.js.
- **⏱️ Live Session Player**: Interactive HUD with timers, rest intervals, and live MET-based calorie tracking.
- **🌗 Instant Theming**: Seamless Dark/Light mode with CSS Variables and system preference syncing.
- **🏅 Gamified Growth**: Automated achievement engine (Streaks, Badges, Leaderboards).

## 🛠️ Tech Stack

- **Core:** PHP 8.x (Secure Session management & RESTful API)
- **Database:** MySQL / MariaDB (Relational schema with Foreign Key integrity)
- **UI/UX:** Vanilla JS (ES6+), CSS3 (Flex/Grid), Boxicons
- **Analytics:** Chart.js
- **Touring:** Intro.js

## 📦 Rapid Setup

### 1. Environment
- PHP 8.0+
- MySQL (Port 3307 default, configurable)
- XAMPP / WAMP / Docker

### 2. Install
```bash
git clone https://github.com/MihirMaurya-dev/FlexZone.git
cd FlexZone
```

### 3. Database
1. Create a DB named `flexzone`.
2. Import `flexzone.sql`.
3. Configure `php/config/db_connection.php`:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'flexzone');
   define('DB_PORT', 3307); 
   ```

## 🚀 Deployment Guide

This project is built on a standard PHP and MySQL stack. Below are the instructions to deploy this application to a live server (such as InfinityFree, cPanel, or a VPS) and secure it using Cloudflare, managed via the **Antigravity CLI**.

### 📋 Prerequisites

Before deploying, ensure you have the following:
*   A registered domain name pointing to Cloudflare nameservers.
*   A hosting environment with PHP and MySQL support.
*   **Antigravity CLI** installed on your local machine.
*   Your exported database schema (`database.sql`).

---

### ⚙️ Step 1: Database Setup

1. Log into your hosting Control Panel.
2. Navigate to **MySQL Databases** and create a new database.
3. Open **phpMyAdmin** and import your local `.sql` file to generate the required tables.
4. Note your newly generated credentials: `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`.

---

### 🌐 Step 2: DNS & Cloudflare Configuration

To route traffic properly and prevent proxy errors, configure your Cloudflare DNS settings:

1. Delete any temporary `CNAME` verification records.
2. Create the following **A Records**:
   * **Type:** `A` | **Name:** `@` | **Target:** `YOUR_SERVER_IP` | **Proxy:** Proxied (Orange Cloud)
   * **Type:** `A` | **Name:** `www` | **Target:** `YOUR_SERVER_IP` | **Proxy:** Proxied (Orange Cloud)
3. Navigate to the **SSL/TLS** tab in Cloudflare and set the encryption mode to **Flexible** (if your origin server does not have a dedicated SSL certificate).

---

### 💻 Step 3: Configure Environment Variables

Before using the CLI to push your files, update your database connection script to match your live server environment. 

> **Warning:** Never commit production passwords directly to public GitHub repositories. Use environment variables or a `.gitignore` configuration file.

```php
<?php
// Example database connection
$host     = "YOUR_LIVE_DB_HOST";
$username = "YOUR_LIVE_DB_USER";
$password = "YOUR_LIVE_DB_PASSWORD";
$database = "YOUR_LIVE_DB_NAME";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## 🗺️ Roadmap
- [x] **Targeted Workouts**: 1-click generators for HIIT, Powerlifting, and specific muscle groups.
- [x] **Rest Timer Audio Cues**: Web Audio API integration for interval beeps.
- [x] **Social Sharing**: Native mobile sharing for completed workout logs.
- [x] **Global CSRF Protection**: Complete API security overhaul.
- [ ] **PWA Support**: Offline workout logging and home-screen installation.
- [ ] **Push Notifications**: Service Worker integration for workout reminders.

## 🤝 Contributing
Contributions are what make the open-source community an amazing place to learn, inspire, and create.
1. Fork the Project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---
*Built with 💪 by [Mihir Maurya](https://github.com/MihirMaurya-dev)*
