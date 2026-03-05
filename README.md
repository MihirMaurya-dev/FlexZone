# FlexZone 🏋️‍♂️ 
### Track. Train. Transform.

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-777bb4.svg?style=flat-square&logo=php)](https://www.php.net/)
[![JS Standard](https://img.shields.io/badge/javascript-ES6%2B-f7df1e.svg?style=flat-square&logo=javascript)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

FlexZone is a high-performance, minimalist fitness ecosystem. It's built for those who want a zero-friction training experience—no bloat, just pure progress. Designed with a **Mobile-First** approach and a clean **Neumorphic/Glassmorphism** aesthetic.

![FlexZone Banner](assets/about.png)

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

## 🗺️ Roadmap
- [x] **Dynamic Units**: Instant kg/lbs conversion across all charts.
- [x] **Theme Persistence**: Zero-flash theme loading.
- [ ] **PWA Support**: Offline workout logging and home-screen installation.
- [ ] **Push Notifications**: Service Worker integration for workout reminders.
- [ ] **Social API**: Share custom workout "blueprints" with a single link.

## 🤝 Contributing
Contributions are what make the open-source community an amazing place to learn, inspire, and create.
1. Fork the Project.
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`).
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`).
4. Push to the Branch (`git push origin feature/AmazingFeature`).
5. Open a Pull Request.

---
*Built with 💪 by [Mihir Maurya](https://github.com/MihirMaurya-dev)*
