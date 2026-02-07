-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 24, 2025 at 05:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flexzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_type` (`badge_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `badge_type`, `name`, `icon`, `description`) VALUES
(1, 'first_workout', 'First Step', 'bx-run', 'Completed your first workout.'),
(2, 'streak_3', 'On Fire', 'bxs-bolt', 'Maintained a 3-day workout streak.'),
(3, 'workouts_100', 'Century Club', 'bx-crown', 'Completed 100 total workouts.'),
(4, 'early_bird', 'Early Bird', 'bx-sun', 'Completed a workout before 8 AM.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `activity_level` varchar(50) DEFAULT NULL,
  `fitness_goal` varchar(255) DEFAULT NULL,
  `streak_current` int(11) DEFAULT 0,
  `streak_max` int(11) DEFAULT 0,
  `total_workouts` int(11) DEFAULT 0,
  `garage` text DEFAULT NULL,
  `settings` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `layout_preferences` text DEFAULT NULL,
  `privacy_settings` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `height_cm`, `weight_kg`, `dob`, `gender`, `activity_level`, `fitness_goal`, `streak_current`, `streak_max`, `total_workouts`, `garage`, `settings`, `created_at`, `updated_at`, `layout_preferences`, `privacy_settings`) VALUES
(1, 'Mihir', 'mim@gmail.com', '$2y$12$gnKLX2xziV8JqdOf4eEYV.ngeZMt10DByrA1nO8zm4LRIxQdjuoeG', 'default_avatar.png', 178.00, 70.00, '2004-01-01', 'male', 'moderate', 'general_fitness', 2, 2, 3, NULL, '{"units":"kg","notif_workouts":true,"notif_weekly":true}', '2025-12-20 06:38:50', '2025-12-24 10:44:21', NULL, NULL),
(3, 'Maharsh', 'mah@gmail.com', '$2y$12$QBQ/150DYBkIZRn2165oOuOB5ooUK28eADDMJVVlNwkjhFgMFvGRW', 'default_avatar.png', 182.00, 80.00, '2005-01-01', 'male', 'active', 'muscle_gain', 0, 0, 0, NULL, NULL, '2025-12-24 10:49:07', '2025-12-24 10:50:43', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `body_measurements`
--

CREATE TABLE `body_measurements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `chest_cm` decimal(5,2) DEFAULT NULL,
  `waist_cm` decimal(5,2) DEFAULT NULL,
  `arms_cm` decimal(5,2) DEFAULT NULL,
  `thighs_cm` decimal(5,2) DEFAULT NULL,
  `photo_front` varchar(255) DEFAULT NULL,
  `photo_side` varchar(255) DEFAULT NULL,
  `photo_back` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `body_measurements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `body_measurements`
--

INSERT INTO `body_measurements` (`id`, `user_id`, `log_date`, `weight_kg`, `chest_cm`, `waist_cm`, `arms_cm`, `thighs_cm`, `photo_front`, `photo_side`, `photo_back`, `notes`, `is_private`) VALUES
(1, 1, '2025-12-24', 70.00, 34.00, 31.00, 10.00, 17.00, NULL, NULL, NULL, NULL, 1);



-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exercise_name` varchar(255) NOT NULL,
  `type` enum('Strength','Cardio','Warm-up','Cool-down','Flexibility') DEFAULT 'Strength',
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `met_value` decimal(4,2) DEFAULT NULL,
  `muscle_group` varchar(100) DEFAULT NULL,
  `equipment` varchar(255) DEFAULT 'None',
  `difficulty` enum('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  `default_reps` int(11) DEFAULT NULL COMMENT 'Default repetition count for strength',
  `default_duration` int(11) DEFAULT NULL COMMENT 'Default duration in seconds',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `exercise_name`, `type`, `description`, `image_url`, `met_value`, `muscle_group`, `equipment`, `difficulty`, `default_reps`, `default_duration`) VALUES
(1, 'Jumping Jacks', 'Warm-up', 'A classic full-body cardio exercise to get your heart rate up.', 'assets/exercises/jumping_jacks.gif', 8.00, 'Full Body', 'None', 'Beginner', NULL, 60),
(2, 'Arm Circles', 'Warm-up', 'Simple dynamic stretch for shoulders.', 'assets/exercises/arm_circles.gif', 3.00, 'Shoulders', 'None', 'Beginner', NULL, 30),
(3, 'High Knees', 'Warm-up', 'Running in place while lifting knees high to engage core and legs.', 'assets/exercises/high_knees.gif', 8.00, 'Legs', 'None', 'Beginner', NULL, 45),
(4, 'Butt Kicks', 'Warm-up', 'Jogging in place kicking heels towards glutes to warm up hamstrings.', 'assets/exercises/butt_kicks.gif', 7.00, 'Legs', 'None', 'Beginner', NULL, 45),
(5, 'Torso Twists', 'Warm-up', 'Standing rotation to warm up the spine and obliques.', 'assets/exercises/torso_twists.gif', 2.50, 'Core', 'None', 'Beginner', NULL, 30),
(6, 'Inchworms', 'Warm-up', 'Walk out from standing to plank position to warm up hamstrings and shoulders.', 'assets/exercises/inchworms.gif', 4.00, 'Full Body', 'None', 'Intermediate', 8, NULL),
(7, 'Leg Swings', 'Warm-up', 'Dynamic stretching for hips and hamstrings.', 'assets/exercises/leg_swings.gif', 3.00, 'Legs', 'None', 'Beginner', NULL, 30),
(8, 'Band Pull Aparts', 'Warm-up', 'Rear delt and posture correction exercise.', 'assets/exercises/band_pull_aparts.gif', 3.00, 'Shoulders', 'Resistance Band', 'Beginner', 15, NULL),
(9, 'Shoulder Dislocations', 'Warm-up', 'Mobility exercise using a band or stick to rotate shoulders.', 'assets/exercises/shoulder_dislocations.gif', 3.00, 'Shoulders', 'Resistance Band', 'Intermediate', 10, NULL),
(10, 'Pushups', 'Strength', 'Classic bodyweight exercise for chest, shoulders, and triceps.', 'assets/exercises/pushups.gif', 3.80, 'Chest', 'None', 'Beginner', 12, NULL),
(11, 'Squats', 'Strength', 'Full body exercise primarily targeting legs and glutes.', 'assets/exercises/squats.gif', 5.00, 'Legs', 'None', 'Beginner', 15, NULL),
(12, 'Plank', 'Strength', 'Isometric core strengthening exercise.', 'assets/exercises/plank.gif', 2.80, 'Core', 'None', 'Beginner', NULL, 45),
(13, 'Lunges', 'Strength', 'Unilateral leg exercise for balance and strength.', 'assets/exercises/lunges.gif', 4.00, 'Legs', 'None', 'Beginner', 10, NULL),
(14, 'Dumbbell Rows', 'Strength', 'Compound back exercise focusing on lats and biceps.', 'assets/exercises/dumbbell_rows.gif', 4.50, 'Back', 'Dumbbells', 'Intermediate', 12, NULL),
(15, 'Overhead Press', 'Strength', 'Vertical pushing movement for shoulders.', 'assets/exercises/overhead_press.gif', 4.00, 'Shoulders', 'Dumbbells', 'Intermediate', 10, NULL),
(16, 'Bicep Curls', 'Strength', 'Isolation exercise for biceps.', 'assets/exercises/bicep_curls.gif', 3.50, 'Arms', 'Dumbbells', 'Beginner', 12, NULL),
(17, 'Goblet Squat', 'Strength', 'Squat variation holding a dumbbell at chest height.', 'assets/exercises/goblet_squat.gif', 5.50, 'Legs', 'Dumbbells', 'Intermediate', 12, NULL),
(18, 'Lateral Raises', 'Strength', 'Isolation exercise for lateral deltoids.', 'assets/exercises/lateral_raises.gif', 3.50, 'Shoulders', 'Dumbbells', 'Intermediate', 12, NULL),
(19, 'Barbell Deadlift', 'Strength', 'Full-body compound movement focusing on the posterior chain.', 'assets/exercises/bb_deadlift.gif', 6.00, 'Back', 'Barbell', 'Advanced', 8, NULL),
(20, 'Barbell Bench Press', 'Strength', 'Primary compound pushing exercise for chest.', 'assets/exercises/bb_bench_press.gif', 5.00, 'Chest', 'Barbell, Bench', 'Intermediate', 10, NULL),
(21, 'Barbell Squat', 'Strength', 'The king of leg exercises using a barbell.', 'assets/exercises/bb_squat.gif', 6.00, 'Legs', 'Barbell', 'Advanced', 8, NULL),
(22, 'Barbell Rows', 'Strength', 'Compound pull exercise for back thickness.', 'assets/exercises/bb_rows.gif', 5.00, 'Back', 'Barbell', 'Intermediate', 10, NULL),
(23, 'Kettlebell Swing', 'Cardio', 'Explosive hip-hinge movement for conditioning and power.', 'assets/exercises/kb_swing.gif', 8.00, 'Full Body', 'Kettlebell', 'Intermediate', 20, NULL),
(24, 'Turkish Getup', 'Strength', 'Complex full-body movement for stability and strength.', 'assets/exercises/turkish_getup.gif', 6.50, 'Full Body', 'Kettlebell', 'Advanced', 5, NULL),
(25, 'Kettlebell Squat', 'Strength', 'Deep squat holding kettlebell at chest.', 'assets/exercises/kb_squat.gif', 5.50, 'Legs', 'Kettlebell', 'Beginner', 12, NULL),
(26, 'Pullups', 'Strength', 'Bodyweight vertical pull for back width.', 'assets/exercises/pullups.gif', 5.00, 'Back', 'Pull-up Bar', 'Advanced', 5, NULL),
(27, 'Chinups', 'Strength', 'Vertical pull with supinated grip, hitting biceps more.', 'assets/exercises/chinups.gif', 5.00, 'Back', 'Pull-up Bar', 'Intermediate', 6, NULL),
(28, 'Hanging Leg Raise', 'Strength', 'Core exercise targeting lower abs.', 'assets/exercises/hanging_leg_raise.gif', 4.00, 'Core', 'Pull-up Bar', 'Advanced', 10, NULL),
(29, 'Dumbbell Bench Press', 'Strength', 'Chest press performed on a flat bench.', 'assets/exercises/db_bench_press.gif', 4.50, 'Chest', 'Dumbbells, Bench', 'Intermediate', 12, NULL),
(30, 'Bench Dips', 'Strength', 'Tricep isolation using the edge of a bench.', 'assets/exercises/bench_dips.gif', 4.00, 'Arms', 'Bench', 'Beginner', 15, NULL),
(31, 'Step Ups', 'Strength', 'Unilateral leg exercise stepping onto the bench.', 'assets/exercises/step_ups.gif', 5.00, 'Legs', 'Bench', 'Beginner', 12, NULL),
(32, 'Face Pulls', 'Strength', 'Shoulder health and rear delt exercise.', 'assets/exercises/face_pulls.gif', 3.50, 'Shoulders', 'Resistance Band', 'Intermediate', 15, NULL),
(33, 'Banded Squats', 'Strength', 'Squats with added resistance from the band.', 'assets/exercises/banded_squats.gif', 5.00, 'Legs', 'Resistance Band', 'Beginner', 15, NULL),
(34, 'Treadmill Run', 'Cardio', 'Steady state running or jogging.', 'assets/exercises/treadmill_run.gif', 9.00, 'Legs', 'Treadmill', 'Beginner', NULL, 600),
(35, 'Treadmill Incline', 'Cardio', 'Low impact high intensity walking.', 'assets/exercises/treadmill_incline.gif', 6.00, 'Legs', 'Treadmill', 'Beginner', NULL, 900),
(36, 'Bike Ride', 'Cardio', 'Cycling for cardiovascular endurance.', 'assets/exercises/bike_ride.gif', 7.50, 'Legs', 'Bike', 'Beginner', NULL, 900),
(37, 'Bike Sprints', 'Cardio', 'High intensity interval sprinting on bike.', 'assets/exercises/bike_sprints.gif', 10.00, 'Legs', 'Bike', 'Intermediate', NULL, 300),
(38, 'Cobra', 'Cool-down', 'Stretches the abdominal muscles and lower back.', 'assets/exercises/cobra.gif', 2.00, 'Core', 'None', 'Beginner', NULL, 30),
(39, 'Child Pose', 'Cool-down', 'Resting pose that stretches the back and hips.', 'assets/exercises/child_pose.gif', 1.50, 'Back', 'None', 'Beginner', NULL, 30),
(40, 'Hamstring Stretch', 'Cool-down', 'Static stretch for the back of the leg.', 'assets/exercises/hamstring_stretch.gif', 1.50, 'Legs', 'None', 'Beginner', NULL, 30),
(41, 'Quad Stretch', 'Cool-down', 'Standing one-leg stretch for the front of the thigh.', 'assets/exercises/quad_stretch.gif', 1.50, 'Legs', 'None', 'Beginner', NULL, 30),
(42, 'Butterfly', 'Cool-down', 'Seated stretch for hips and groin.', 'assets/exercises/butterfly.gif', 1.50, 'Legs', 'None', 'Beginner', NULL, 45),
(43, 'Cat Cow', 'Cool-down', 'Gentle flow between arching and rounding the back.', 'assets/exercises/cat_cow.gif', 2.00, 'Back', 'None', 'Beginner', NULL, 60),
(44, 'Pigeon Pose', 'Cool-down', 'Deep glute and hip opener.', 'assets/exercises/pigeon_pose.gif', 1.80, 'Legs', 'None', 'Intermediate', NULL, 45),
(45, 'Tricep Stretch', 'Cool-down', 'Overhead arm stretch for triceps.', 'assets/exercises/tricep_stretch.gif', 1.50, 'Arms', 'None', 'Beginner', NULL, 30),
(46, 'Doorway Stretch', 'Cool-down', 'Opens up the chest using a doorframe.', 'assets/exercises/doorway_stretch.gif', 1.50, 'Chest', 'None', 'Beginner', NULL, 30);

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `badge_type` varchar(50) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weight_log`
--

CREATE TABLE `weight_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `weight_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weight_log`
--

INSERT INTO `weight_log` (`log_id`, `user_id`, `weight_kg`, `log_date`) VALUES
(1, 1, 70.00, '2025-12-22 18:30:00'),
(2, 1, 70.50, '2025-12-16 10:55:23'),
(3, 1, 70.20, '2025-12-18 10:55:23'),
(4, 1, 69.80, '2025-12-20 10:55:23'),
(5, 1, 70.00, '2025-12-22 10:55:23'),
(7, 1, 70.00, '2025-12-23 18:30:00'),
(8, 1, 70.00, '2025-12-24 10:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_min` int(11) DEFAULT NULL,
  `difficulty` enum('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
  `equipment` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `title`, `description`, `duration_min`, `difficulty`, `equipment`, `image_path`, `created_by`, `created_at`) VALUES
(1, 'Beginner Full Body', 'A great starting point for full body strength.', 30, 'Beginner', 'None', NULL, NULL, '2025-12-20 06:37:26'),
(2, 'Intermediate Dumbbell', 'Build strength with this dumbbell-focused routine.', 45, 'Intermediate', 'Dumbbells', NULL, NULL, '2025-12-20 06:37:26'),
(3, 'Advanced Bodyweight', 'Challenge yourself with advanced bodyweight movements.', 60, 'Advanced', 'None', NULL, NULL, '2025-12-20 06:37:26');

-- --------------------------------------------------------

--
-- Table structure for table `workout_exercises`
--

CREATE TABLE `workout_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workout_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `rest_seconds` int(11) DEFAULT NULL,
  `order_index` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workout_id` (`workout_id`),
  KEY `exercise_id` (`exercise_id`),
  CONSTRAINT `workout_exercises_ibfk_1` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_exercises`
--

INSERT INTO `workout_exercises` (`id`, `workout_id`, `exercise_id`, `sets`, `reps`, `rest_seconds`, `order_index`) VALUES
(1, 1, 1, 3, 10, 60, 1),
(2, 1, 2, 3, 12, 60, 2),
(3, 1, 3, 3, NULL, 60, 3),
(4, 1, 7, 3, 10, 60, 4),
(5, 2, 4, 3, 10, 60, 1),
(6, 2, 5, 3, 10, 60, 2),
(7, 2, 6, 3, 12, 45, 3),
(8, 2, 2, 3, 12, 60, 4);

-- --------------------------------------------------------

--
-- Table structure for table `workout_log`
--

CREATE TABLE `workout_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `workout_id` int(11) DEFAULT NULL,
  `workout_name` varchar(255) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `calories_burned` decimal(7,2) DEFAULT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `workout_id` (`workout_id`),
  CONSTRAINT `workout_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_log_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_log`
--

INSERT INTO `workout_log` (`log_id`, `user_id`, `workout_id`, `workout_name`, `duration_seconds`, `calories_burned`, `log_date`) VALUES
(1, 1, NULL, 'Beginner Workout', 195, 0.00, '2025-12-20 07:05:52'),
(2, 1, NULL, 'Beginner Workout', 30, 2.00, '2025-12-22 10:56:32'),
(3, 1, NULL, 'Beginner Workout', 34, 2.00, '2025-12-23 10:12:38');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;