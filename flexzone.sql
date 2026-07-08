-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: flexzone
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_type` (`badge_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `body_measurements`
--

DROP TABLE IF EXISTS `body_measurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exercises`
--

DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_badges`
--

DROP TABLE IF EXISTS `user_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `badge_type` varchar(50) NOT NULL,
  `unlocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_user_badge` (`user_id`,`badge_type`),
  CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `hydration_data` text DEFAULT NULL,
  `challenge_data` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weight_log`
--

DROP TABLE IF EXISTS `weight_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weight_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_user_date` (`user_id`,`log_date`),
  CONSTRAINT `weight_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workout_exercises`
--

DROP TABLE IF EXISTS `workout_exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workout_log`
--

DROP TABLE IF EXISTS `workout_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `idx_user_date` (`user_id`,`log_date`),
  CONSTRAINT `workout_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_log_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `workouts`
--

DROP TABLE IF EXISTS `workouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-08 13:18:36
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: flexzone
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,'Jumping Jacks','Warm-up','A classic full-body cardio exercise to get your heart rate up.','assets/exercises/jumping_jacks.gif',8.00,'Full Body','None','Beginner',NULL,60),(2,'Arm Circles','Warm-up','Simple dynamic stretch for shoulders.','assets/exercises/arm_circles.gif',3.00,'Shoulders','None','Beginner',NULL,30),(3,'High Knees','Warm-up','Running in place while lifting knees high to engage core and legs.','assets/exercises/high_knees.gif',8.00,'Legs','None','Beginner',NULL,45),(4,'Butt Kicks','Warm-up','Jogging in place kicking heels towards glutes to warm up hamstrings.','assets/exercises/butt_kicks.gif',7.00,'Legs','None','Beginner',NULL,45),(5,'Torso Twists','Warm-up','Standing rotation to warm up the spine and obliques.','assets/exercises/torso_twists.gif',2.50,'Core','None','Beginner',NULL,30),(6,'Inchworms','Warm-up','Walk out from standing to plank position to warm up hamstrings and shoulders.','assets/exercises/inchworms.gif',4.00,'Full Body','None','Intermediate',8,NULL),(7,'Leg Swings','Warm-up','Dynamic stretching for hips and hamstrings.','assets/exercises/leg_swings.gif',3.00,'Legs','None','Beginner',NULL,30),(8,'Band Pull-Aparts','Warm-up','Rear delt and posture correction exercise.','assets/exercises/band_pull_aparts.gif',3.00,'Shoulders','Resistance Band','Beginner',15,NULL),(9,'Shoulder Dislocations','Warm-up','Mobility exercise using a band or stick to rotate shoulders.','assets/exercises/shoulder_dislocations.gif',3.00,'Shoulders','Resistance Band','Intermediate',10,NULL),(10,'Push-ups','Strength','Classic bodyweight exercise for chest, shoulders, and triceps.','assets/exercises/pushups.gif',3.80,'Chest','None','Beginner',12,NULL),(11,'Squats','Strength','Full body exercise primarily targeting legs and glutes.','assets/exercises/squats.gif',5.00,'Legs','None','Beginner',15,NULL),(12,'Plank','Strength','Isometric core strengthening exercise.','assets/exercises/plank.gif',2.80,'Core','None','Beginner',NULL,45),(13,'Lunges','Strength','Unilateral leg exercise for balance and strength.','assets/exercises/lunges.gif',4.00,'Legs','None','Beginner',10,NULL),(14,'Dumbbell Rows','Strength','Compound back exercise focusing on lats and biceps.','assets/exercises/dumbbell_rows.gif',4.50,'Back','Dumbbells','Intermediate',12,NULL),(15,'Overhead Press','Strength','Vertical pushing movement for shoulders.','assets/exercises/overhead_press.gif',4.00,'Shoulders','Dumbbells','Intermediate',10,NULL),(16,'Bicep Curls','Strength','Isolation exercise for biceps.','assets/exercises/bicep_curls.gif',3.50,'Arms','Dumbbells','Beginner',12,NULL),(17,'Goblet Squats','Strength','Squat variation holding a dumbbell at chest height.','assets/exercises/goblet_squat.gif',5.50,'Legs','Dumbbells','Intermediate',12,NULL),(18,'Lateral Raises','Strength','Isolation exercise for lateral deltoids.','assets/exercises/lateral_raises.gif',3.50,'Shoulders','Dumbbells','Intermediate',12,NULL),(19,'Barbell Deadlift','Strength','Full-body compound movement focusing on the posterior chain.','assets/exercises/bb_deadlift.gif',6.00,'Back','Barbell','Advanced',8,NULL),(20,'Barbell Bench Press','Strength','Primary compound pushing exercise for chest.','assets/exercises/bb_bench_press.gif',5.00,'Chest','Barbell, Bench','Intermediate',10,NULL),(21,'Barbell Squat','Strength','The king of leg exercises using a barbell.','assets/exercises/bb_squat.gif',6.00,'Legs','Barbell','Advanced',8,NULL),(22,'Bent Over Rows','Strength','Compound pull exercise for back thickness.','assets/exercises/bb_rows.gif',5.00,'Back','Barbell','Intermediate',10,NULL),(23,'Kettlebell Swing','Cardio','Explosive hip-hinge movement for conditioning and power.','assets/exercises/kb_swing.gif',8.00,'Full Body','Kettlebell','Intermediate',20,NULL),(24,'Turkish Get-Up','Strength','Complex full-body movement for stability and strength.','assets/exercises/turkish_getup.gif',6.50,'Full Body','Kettlebell','Advanced',5,NULL),(25,'Goblet Squat (KB)','Strength','Deep squat holding kettlebell at chest.','assets/exercises/kb_squat.gif',5.50,'Legs','Kettlebell','Beginner',12,NULL),(26,'Pull-ups','Strength','Bodyweight vertical pull for back width.','assets/exercises/pullups.gif',5.00,'Back','Pull-up Bar','Advanced',5,NULL),(27,'Chin-ups','Strength','Vertical pull with supinated grip, hitting biceps more.','assets/exercises/chinups.gif',5.00,'Back','Pull-up Bar','Intermediate',6,NULL),(28,'Hanging Leg Raises','Strength','Core exercise targeting lower abs.','assets/exercises/hanging_leg_raise.gif',4.00,'Core','Pull-up Bar','Advanced',10,NULL),(29,'Dumbbell Chest Press','Strength','Chest press performed on a flat bench.','assets/exercises/db_bench_press.gif',4.50,'Chest','Dumbbells, Bench','Intermediate',12,NULL),(30,'Bench Dips','Strength','Tricep isolation using the edge of a bench.','assets/exercises/bench_dips.gif',4.00,'Arms','Bench','Beginner',15,NULL),(31,'Step-ups','Strength','Unilateral leg exercise stepping onto the bench.','assets/exercises/step_ups.gif',5.00,'Legs','Bench','Beginner',12,NULL),(32,'Banded Face Pulls','Strength','Shoulder health and rear delt exercise.','assets/exercises/face_pulls.gif',3.50,'Shoulders','Resistance Band','Intermediate',15,NULL),(33,'Banded Squats','Strength','Squats with added resistance from the band.','assets/exercises/banded_squats.gif',5.00,'Legs','Resistance Band','Beginner',15,NULL),(34,'Treadmill Run','Cardio','Steady state running or jogging.','assets/exercises/treadmill_run.gif',9.00,'Legs','Treadmill','Beginner',NULL,600),(35,'Treadmill Incline Walk','Cardio','Low impact high intensity walking.','assets/exercises/treadmill_incline.gif',6.00,'Legs','Treadmill','Beginner',NULL,900),(36,'Stationary Bike','Cardio','Cycling for cardiovascular endurance.','assets/exercises/bike_ride.gif',7.50,'Legs','Bike','Beginner',NULL,900),(37,'HIIT Bike Sprints','Cardio','High intensity interval sprinting on bike.','assets/exercises/bike_sprints.gif',10.00,'Legs','Bike','Intermediate',NULL,300),(38,'Cobra Stretch','Cool-down','Stretches the abdominal muscles and lower back.','assets/exercises/cobra.gif',2.00,'Core','None','Beginner',NULL,30),(39,'Child Pose','Cool-down','Resting pose that stretches the back and hips.','assets/exercises/child_pose.gif',1.50,'Back','None','Beginner',NULL,30),(40,'Standing Hamstring Stretch','Cool-down','Static stretch for the back of the leg.','assets/exercises/hamstring_stretch.gif',1.50,'Legs','None','Beginner',NULL,30),(41,'Quad Stretch','Cool-down','Standing one-leg stretch for the front of the thigh.','assets/exercises/quad_stretch.gif',1.50,'Legs','None','Beginner',NULL,30),(42,'Butterfly Stretch','Cool-down','Seated stretch for hips and groin.','assets/exercises/butterfly.gif',1.50,'Legs','None','Beginner',NULL,45),(43,'Cat-Cow Stretch','Cool-down','Gentle flow between arching and rounding the back.','assets/exercises/cat_cow.gif',2.00,'Back','None','Beginner',NULL,60),(44,'Pigeon Pose','Cool-down','Deep glute and hip opener.','assets/exercises/pigeon_pose.gif',1.80,'Legs','None','Intermediate',NULL,45),(45,'Triceps Stretch','Cool-down','Overhead arm stretch for triceps.','assets/exercises/tricep_stretch.gif',1.50,'Arms','None','Beginner',NULL,30),(46,'Doorway Chest Stretch','Cool-down','Opens up the chest using a doorframe.','assets/exercises/doorway_stretch.gif',1.50,'Chest','None','Beginner',NULL,30),(47,'Dumbbell Romanian Deadlift','Strength','Targeting hamstrings and glutes with controlled descent.','assets/exercises/placeholder.png',5.00,'Legs','Dumbbells','Intermediate',12,NULL),(48,'Dumbbell Tricep Extension','Strength','Overhead extension to isolate the triceps.','assets/exercises/placeholder.png',3.00,'Arms','Dumbbells','Beginner',12,NULL),(49,'Dumbbell Hammer Curls','Strength','Neutral grip curls for brachialis and biceps.','assets/exercises/placeholder.png',3.00,'Arms','Dumbbells','Beginner',12,NULL),(50,'Weighted Lunges','Strength','Stepping lunges holding dumbbells for added resistance.','assets/exercises/placeholder.png',5.50,'Legs','Dumbbells','Intermediate',20,NULL),(51,'Barbell Skull Crushers','Strength','Lying tricep extension using an EZ bar or barbell.','assets/exercises/placeholder.png',3.50,'Arms','Barbell, Bench','Intermediate',10,NULL),(52,'Pendlay Rows','Strength','Strict horizontal pull from the floor to build back power.','assets/exercises/placeholder.png',5.50,'Back','Barbell','Advanced',8,NULL),(53,'Barbell Military Press','Strength','Strict overhead press performed while standing.','assets/exercises/placeholder.png',5.00,'Shoulders','Barbell','Intermediate',8,NULL),(54,'Barbell Front Squat','Strength','Squat variation with the bar resting on front deltoids.','assets/exercises/placeholder.png',6.50,'Legs','Barbell','Advanced',8,NULL),(55,'Kettlebell Snatch','Cardio','Explosive overhead movement using one arm.','assets/exercises/placeholder.png',9.00,'Full Body','Kettlebell','Advanced',10,NULL),(56,'Kettlebell Windmill','Strength','Core and shoulder stability exercise.','assets/exercises/placeholder.png',4.50,'Core','Kettlebell','Advanced',8,NULL),(57,'Kettlebell Clean','Strength','Pulling the kettlebell from floor to rack position.','assets/exercises/placeholder.png',6.00,'Full Body','Kettlebell','Intermediate',10,NULL),(58,'Kettlebell Sumo Deadlift','Strength','Wide-stance deadlift focusing on inner thighs and glutes.','assets/exercises/placeholder.png',5.00,'Legs','Kettlebell','Beginner',15,NULL),(59,'Banded Clamshells','Strength','Isolation exercise for gluteus medius.','assets/exercises/placeholder.png',2.50,'Legs','Resistance Band','Beginner',20,NULL),(60,'Banded Lateral Walk','Strength','Side-stepping with a band to activate hips.','assets/exercises/placeholder.png',3.50,'Legs','Resistance Band','Beginner',20,NULL),(61,'Banded Glute Bridge','Strength','Glute bridge with external resistance from a band.','assets/exercises/placeholder.png',3.00,'Legs','Resistance Band','Beginner',20,NULL),(62,'Banded Rows','Strength','Seated or standing rows using a resistance band.','assets/exercises/placeholder.png',3.50,'Back','Resistance Band','Beginner',15,NULL),(63,'Diamond Push-ups','Strength','Push-up variation with hands close together for triceps.','assets/exercises/placeholder.png',4.50,'Chest','None','Intermediate',12,NULL),(64,'Mountain Climbers','Cardio','High-intensity core and cardio movement.','assets/exercises/placeholder.png',8.00,'Core','None','Beginner',NULL,45),(65,'Burpees','Cardio','Full-body explosive movement for conditioning.','assets/exercises/placeholder.png',10.00,'Full Body','None','Intermediate',15,NULL),(66,'Hollow Body Hold','Strength','Isometric core hold for abdominal strength.','assets/exercises/placeholder.png',3.00,'Core','None','Intermediate',NULL,45),(67,'Barbell Deadlift','Strength','A powerlifting staple. Lift a loaded barbell off the ground to the hips, then lower it.','exercises/placeholder.png',6.00,'Full Body','Barbell','Advanced',5,NULL),(68,'Barbell Back Squat','Strength','A powerlifting staple. Squat down with a barbell across your upper back.','exercises/placeholder.png',5.50,'Legs','Barbell','Advanced',5,NULL),(69,'Barbell Bench Press','Strength','A powerlifting staple. Lie on a bench and press a barbell upwards.','exercises/placeholder.png',5.00,'Chest','Barbell','Intermediate',5,NULL),(70,'Burpees','Cardio','A full body HIIT movement. Drop to a push-up, jump up, and reach for the ceiling.','exercises/placeholder.png',8.00,'Full Body','None','Advanced',NULL,45),(71,'Mountain Climbers','Cardio','A core-intensive HIIT exercise. Start in a plank and bring knees to chest rapidly.','exercises/placeholder.png',7.50,'Core','None','Intermediate',NULL,30),(72,'Jump Squats','Cardio','An explosive lower body HIIT exercise.','exercises/placeholder.png',7.00,'Legs','None','Intermediate',NULL,30),(73,'Knee Push-ups (Alternate)','Strength','A beginner friendly alternate for standard push-ups.','exercises/placeholder.png',3.80,'Chest','None','Beginner',10,NULL),(74,'Band-Assisted Pull-ups (Alternate)','Strength','A beginner friendly alternate for pull-ups using a resistance band.','exercises/placeholder.png',4.00,'Back','Resistance Band','Beginner',8,NULL),(75,'Goblet Squat (Alternate)','Strength','A beginner friendly alternate to barbell squats.','exercises/placeholder.png',4.50,'Legs','Dumbbell','Beginner',12,NULL),(76,'Bicycle Crunches','Strength','An intense ab exercise targeting the obliques.','exercises/placeholder.png',5.00,'Core','None','Intermediate',20,NULL),(77,'Dumbbell Lateral Raise','Strength','Isolate the shoulders with dumbbells.','exercises/placeholder.png',3.50,'Shoulders','Dumbbell','Beginner',15,NULL),(78,'Dumbbell Bicep Curl','Strength','Isolate the biceps.','exercises/placeholder.png',3.00,'Arms','Dumbbell','Beginner',12,NULL),(79,'Tricep Dips','Strength','Bodyweight tricep exercise using a bench or chair.','exercises/placeholder.png',4.00,'Arms','None','Intermediate',12,NULL);
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
INSERT INTO `badges` VALUES (1,'first_workout','First Step','bx-run','Completed your first workout.'),(2,'streak_3','On Fire','bxs-flame','Maintained a 3-day workout streak.'),(3,'workouts_100','Century Club','bx-crown','Completed 100 total workouts.'),(4,'early_bird','Early Bird','bx-sun','Completed a workout before 8 AM.');
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-08 13:18:36
