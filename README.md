# FlexZone 🏋️‍♂️
### Track. Train. Transform.

FlexZone is a minimalist, all-in-one fitness application designed to help users log workouts, visualize progress, and achieve their physical potential through a clean and intuitive interface.

![FlexZone Banner](assets/about.png)

## ✨ Features

- **🎯 Personalized Onboarding**: Tailors your experience based on age, gender, activity level, and fitness goals.
- **⚡ Smart Workout Generator**: Generate routines instantly based on your available equipment (The Garage), target muscle groups, and desired duration.
- **📊 Dynamic Insights**: Visualize your journey with interactive charts (Chart.js) showing weekly activity levels and weight trends.
- **⏱️ Live Exercise Player**: Interactive workout mode with timers, rest intervals, exercise visuals, and real-time calorie tracking.
- **📸 Progress Tracking**: Log body measurements and upload progress photos (Front/Side/Back) to see your transformation over time.
- **🥇 Competitive Leaderboard**: Compete with other users based on total training time, calories burned, and workout consistency.
- **🏅 Achievement System**: Earn unique badges like "Early Bird," "Century Club," and "On Fire" as you hit milestones.
- **🌗 Dark Mode**: Full support for system-preferred or manual dark/light mode switching.
- **📥 Data Portability**: Export your entire workout history to CSV at any time.

## 🛠️ Tech Stack

- **Backend:** PHP 8.x
- **Database:** MySQL / MariaDB
- **Frontend:** Vanilla JavaScript (ES6+), Vanilla CSS3
- **Libraries:** 
  - [Chart.js](https://www.chartjs.org/) (Data Visualization)
  - [Boxicons](https://boxicons.com/) (Iconography)
  - [Intro.js](https://introjs.com/) (User Onboarding Tour)
  - [Google Fonts (Poppins)](https://fonts.google.com/)

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or any WAMP/LEMP stack.
- PHP 8.0 or higher.
- MySQL/MariaDB.

### Steps
1. **Clone the repository:**
   ```bash
   git clone https://github.com/MihirMaurya-dev/FlexZone.git
   ```
2. **Move to web directory:**
   Place the folder in your `htdocs` (XAMPP) or `/var/www/html` folder.
3. **Database Setup:**
   - Open phpMyAdmin.
   - Create a new database named `flexzone`.
   - Import the provided `flexzone.sql` file.
4. **Configuration:**
   - Ensure your database connection settings in `php/config/db_connection.php` match your local environment:
     ```php
     define('DB_HOST', '127.0.0.1');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'flexzone');
     define('DB_PORT', 3307); // Default is usually 3306
     ```
5. **Run:**
   Navigate to `http://localhost/FlexZone` in your browser.

## 📁 Project Structure

```text
FlexZone/
├── assets/             # Images, Exercise GIFs, and Progress Photos
├── css/                # Component-specific and Global styles
├── includes/           # Reusable PHP components (Header, Navbar, Footer)
├── js/                 # Frontend logic and API integration
├── pages/              # Main application views
├── php/
│   ├── api/            # RESTful API endpoints (User, Workouts)
│   ├── auth/           # Login, Signup, and Session management
│   └── config/         # Database connection and Utilities
└── flexzone.sql        # Database Schema & Initial Data
```

## 📜 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
*Built with 💪 by [Mihir Maurya](https://github.com/MihirMaurya-dev)*
