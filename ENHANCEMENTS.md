# FlexZone Enhancement Roadmap

## 🎨 UI/UX Enhancements

| Area | Current Issue | Improvement |
|---|---|---|
| **Landing Page** | Basic hero, minimal animations | Parallax scroll, animated stats counter, video/GIF background |
| **Workout Player** | No visual progress bar showing step position | Progress indicator at top (Step 3 of 12) |
| **Dashboard** | Only 7-day chart | Add 30-day and monthly filter toggles |
| **Home Page** | Static daily suggestion | Personalized suggestion based on user's last workout muscle group |
| **History Page** | Plain table | Calendar heatmap view + filter by date/type |
| **Onboarding** | Only 4 steps, no BMI preview | Show live BMI calculation as user types height/weight |

---

## 🚀 Feature Gaps

### High Impact
- **Push Notifications / Workout Reminders** — Currently the toggle is in settings but does nothing
- **BMI & Health Metrics** — Calculate and display BMI, TDEE, and calorie targets from user profile data
- **Workout Rest Timer Sound** — Play a beep/chime when rest is over
- **Progressive Overload Tracker** — Let users track sets/reps/weight on specific exercises over time

### Medium Impact
- **Social Sharing** — "I just completed a 45-min Intermediate Workout 🔥 #FlexZone" share card
- **Workout Plan Scheduling** — Let users schedule recurring workouts for specific days of the week
- **Exercise Demo Videos** — Many exercises have `image_url` pointing to GIFs — ensure all exercises have media
- **Body Measurement Comparison** — Side-by-side before/after measurements in the progress page

### Nice-to-Have
- **Dark Mode OLED theme** — Pure black (`#000`) already exists but add an optional `#0A0A0A` OLED variant
- **Search in History** — Filter workout logs by name or date range
- **AI-based workout suggestion** — Based on streak, last session, and time of day

---

## 🔒 Security & Reliability

| Issue | Fix |
|---|---|
| No CSRF protection on forms | Add CSRF token to all POST forms |
| `sanitizeInput()` uses `htmlspecialchars` which is output encoding, not input validation | Add separate validation layer for types/ranges |
| Session timeout is 30 min but no client-side warning | Show "You'll be logged out in 2 min" toast before expiry |
| Avatar upload doesn't verify actual image content (only MIME type) | Add `getimagesize()` verification to confirm it's a real image file |
| No rate limiting on login endpoint | Add failed login attempt counter with lockout |

---

## ⚡ Performance

| Area | Fix |
|---|---|
| CSS file is 32KB+ single file | Split into page-specific CSS modules and load lazily |
| `ORDER BY RAND()` for exercise generation is slow at scale | Cache daily workout seeds or use keyset pagination |
| No image optimization for uploaded avatars | Compress uploaded images using `imagecopyresized()` in PHP |

---

## 🐛 Code Quality / Bugs

| Issue | Fix |
|---|---|
| Hydration and daily challenge data stored in `localStorage` only | Sync them server-side to persist across devices |
| Leaderboard `total_workouts` from `users` table (cached) vs `workout_log` count (live) — mismatch | Unify to single source of truth |
| Profile page `total_workouts` comes from `users.total_workouts` (cached) | Decide on one source — recommend live `workout_log` count |

---

## 📱 Mobile Experience

- **Swipe Gestures** on workout player — swipe left = next, swipe right = previous exercise
- **PWA Support** — Add a `manifest.json` and service worker so users can install FlexZone on their phone home screen
- **Haptic Feedback** — `navigator.vibrate()` on exercise completion

---

## 💡 Quick Wins

1. Add `<meta name="description">` to all pages for SEO
2. Add `loading="lazy"` to exercise images/videos in the workout preview
3. Show `streak_max` (all-time best streak) on the profile page alongside current streak
4. Add keyboard shortcut (`→` / `←`) for next/prev in the workout player
5. On the History page, show total time and calories **summed at the bottom** of the table

---

## ✅ Already Fixed

- [x] Workout preview and active workout player now use the same exercise list (sessionStorage caching)
- [x] Onboarding page CSS class mismatches resolved
- [x] Age saved as DOB in the database during onboarding
- [x] Custom workout equipment filtering works correctly (pluralization + multi-equipment support)
- [x] Terms, Privacy, FAQ, Contact pages enhanced with modern design
- [x] `save_workout.php` updated to use prepared statements (SQL injection mitigated)
- [x] Database configuration updated to use environment variables for Azure compatibility
- [x] Workout Player Progress Bar implemented (segmented visual track + counter)
- [x] Workout Player Keyboard Shortcuts (Arrows for Next/Prev, Space for Pause)
- [x] Mobile Swipe Gestures and Haptic Feedback added to workout player
- [x] BMI & Health Metrics (TDEE, BMI, Calorie calculations based on profile)
- [x] Global CSRF Token implementation for all API requests
- [x] Rate Limiting implemented on login endpoint (IP-based, 15min lockout)
- [x] Dashboard time filters (Last 7 Days, Last 30 Days, All Time) implemented
- [x] Progressive Overload Tracker (1RM estimation & chart for main lifts)
- [x] Workout Reminders/Notifications (In-app alerts for inactivity)
