# FlexZone Changelog

## [Latest Update] - UI/UX, Feature Gaps & DB Expansion

### 🎨 UI & Dashboard Enhancements
- **Targeted Workouts Section:** Added a completely new grid to the Home Dashboard with vibrant gradient cards.
- **New Quick-Start Categories:** Added 1-click generators for:
  - 🔥 **HIIT** (High-Intensity Interval Training)
  - 🏋️‍♂️ **Powerlifting** (Heavy Barbell Lifts)
  - 🤸 **Bodyweight** (No Equipment)
  - 🏃 **Cardio** (Endurance)
  - 💪 **Muscle Splits:** Arms, Back, Abs/Core, Chest, and Legs.
- **Workout Player:** Added a segmented visual progress bar indicating the current step.
- **Dashboard Filters:** Added 7-day, 30-day, and All-Time toggles for charts.
- **Onboarding:** Live BMI calculates and updates in real-time as users type.

### 🚀 New Features & Capabilities
- **Rest Timer Audio Cues:** Implemented the Web Audio API to play short warning beeps at the 3, 2, and 1-second marks, and a high-pitched GO beep when the rest period concludes.
- **History Search:** Added a debounced live search bar to the History page to instantly filter past workouts by name.
- **Social Sharing:** Integrated the native mobile `navigator.share()` API on the History page, allowing users to broadcast completed workouts to social apps (with a clipboard fallback for desktop).
- **History Summaries:** Added a dynamic footer to the History table that automatically sums total workout duration and calories burned for the visible page.
- **Keyboard Shortcuts:** Added `→` (Next), `←` (Previous), and `Space` (Pause) keyboard controls to the active Workout Player.
- **Profile Streaks:** The profile page now displays the all-time `streak_max` alongside the current streak.

### 💾 Database & Content Expansion
- **13 New Exercises Added** to support targeted workouts:
  - *Powerlifting:* Barbell Deadlift, Barbell Back Squat, Barbell Bench Press.
  - *HIIT:* Burpees, Mountain Climbers, Jump Squats.
  - *Alternates (Beginner):* Knee Push-ups, Band-Assisted Pull-ups, Goblet Squats.
  - *Isolation:* Bicycle Crunches, Lateral Raises, Bicep Curls, Tricep Dips.

### ⚡ Performance & SEO
- **SEO Optimization:** Added `<meta name="description">` tags globally.
- **Lazy Loading:** Implemented `loading="lazy"` on all workout preview images and videos to drastically improve page load speeds.
- **Avatar Compression:** Avatar uploads are intercepted via the GD library, resized to max 400x400, and converted to the highly efficient `.webp` format.
- **Query Optimization:** Removed `ORDER BY RAND()` from the workout generation engine, replacing it with an $O(N)$ PHP array `shuffle()` to prevent database bottlenecks at scale.

### 🔒 Security & Bug Fixes
- **Global CSRF Protection:** Middleware integrated for all API endpoints.
- **Rate Limiting:** Implemented a 15-minute lockout for excessive failed login attempts.
- **Data Persistence:** Hydration and Daily Challenge widgets now sync with the backend to persist data across multiple devices.
- **Code Quality:** Resolved all discrepancies in `ENHANCEMENTS.md` and officially marked outstanding bugs as fixed.
