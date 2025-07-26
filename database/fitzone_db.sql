-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 04:19 AM
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
-- Database: `fitzone_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_bookings`
--

CREATE TABLE `class_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fitness_classes`
--

CREATE TABLE `fitness_classes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(20) NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `trainer` varchar(100) NOT NULL,
  `schedule_days` varchar(100) NOT NULL,
  `schedule_times` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitness_classes`
--

INSERT INTO `fitness_classes` (`id`, `name`, `image`, `description`, `duration`, `difficulty`, `trainer`, `schedule_days`, `schedule_times`, `created_at`, `updated_at`) VALUES
(1, 'HIIT Fusion', 'assets/images/classes/hiit.jpg', 'High-intensity interval training that combines cardio and strength exercises for maximum calorie burn.', '45 min', 'Advanced', 'Sarah Johnes', 'Mon, Wed, Fri', '6:00 AM, 5:30 PM', '2025-04-19 07:18:34', '2025-04-19 07:20:45'),
(11, 'Morning Power Yoga', '1745577831_000524f8-4636-4479-9947-eccc44f4477d_640.jpeg', 'Start your day with a relaxing yet energizing yoga session.', '60 minutes', 'Beginner', 'Nadeesha Perera', 'Mon, Wed, Fri', '6:00 AM - 7:00 AM', '2025-04-25 10:40:20', '2025-04-25 10:43:51'),
(12, 'Cardio Blast', '1745577781_Fitness-Class-Tweaks_2.jpg', 'Burn calories fast with our intense cardio workout.', '45 minutes', 'Intermediate', 'Kavindu Jayasinghe', 'Tue, Thu, Sat', '5:30 PM - 6:15 PM', '2025-04-25 10:40:20', '2025-04-25 10:43:01'),
(13, 'Zumba Dance Fit', '1745577706_zumba-classes-design-template-7519811dfb442a6016da632d450fb6fd_screen.jpg', 'Dance your way to fitness with fun Zumba sessions.', '50 minutes', 'Beginner', 'Shanika Fernando', 'Mon, Wed, Fri', '7:00 PM - 7:50 PM', '2025-04-25 10:40:20', '2025-04-25 10:41:46'),
(14, 'Strength & Tone', '1745577646_647d7273e4b0dbf4b2664217_scaled_cover.jpg', 'Build strength and tone muscles with guided workouts.', '1 hour', 'Advanced', 'Dilan Bandara', 'Tue, Thu', '6:00 PM - 7:00 PM', '2025-04-25 10:40:20', '2025-04-25 10:40:46'),
(15, 'Evening Stretch & Flex', '1745577660_cover+image.jpg', 'Improve your flexibility and reduce stress.', '40 minutes', 'All Levels', 'Ishara Madushani', 'Sat, Sun', '5:00 PM - 5:40 PM', '2025-04-25 10:40:20', '2025-04-25 10:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int(11) NOT NULL,
  `type` enum('Basic','Standard','Premium') NOT NULL,
  `description` text NOT NULL,
  `price_1month` decimal(10,2) NOT NULL,
  `price_6month` decimal(10,2) NOT NULL,
  `price_12month` decimal(10,2) NOT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `type`, `description`, `price_1month`, `price_6month`, `price_12month`, `is_popular`, `is_active`, `created_at`) VALUES
(1, 'Basic', 'Access to basic gym equipment and facilities', 3500.00, 18000.00, 35000.00, 0, 1, '2025-04-21 07:28:29'),
(2, 'Standard', 'Full access to gym equipment, classes, and facilities', 5000.00, 27000.00, 50000.00, 1, 1, '2025-04-21 07:28:29'),
(3, 'Premium', 'Full access including personal trainer sessions and premium amenities', 8000.00, 43200.00, 80000.00, 0, 1, '2025-04-21 07:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `member_subscriptions`
--

CREATE TABLE `member_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `membership_type` enum('Basic','Standard','Premium') NOT NULL,
  `duration` enum('1month','6month','12month') NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_subscriptions`
--

INSERT INTO `member_subscriptions` (`id`, `user_id`, `membership_type`, `duration`, `price`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 27, 'Premium', '1month', 8000.00, '2025-04-21', '2025-05-21', 'active', '2025-04-21 12:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `field_type` varchar(20) NOT NULL DEFAULT 'text',
  `options` text DEFAULT NULL,
  `required` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `display_name`, `field_type`, `options`, `required`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'FitZone Fitness Center', 'general', 'Site Name', 'text', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(2, 'site_description', 'Premium Fitness Center', 'general', 'Site Description', 'text', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(3, 'contact_email', 'infos@fitzone.com', 'general', 'Contact Email', 'email', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:52:25'),
(4, 'contact_phone', '+94 11 123 4567', 'general', 'Contact Phone', 'text', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(5, 'address', '123 Fitness Avenue, Kurunegala, Sri Lank accs ss sda', 'general', 'Address', 'textarea', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:55:32'),
(6, 'opening_hours', 'Mon-Fri: 6:00AM - 10:00PM | Sat: 7:00AM - 8:00PM | Sun: 8:00AM - 6:00PM', 'general', 'Opening Hours', 'textarea', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(7, 'smtp_host', 'smtp.example.com', 'email', 'SMTP Host', 'text', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(8, 'smtp_port', '587', 'email', 'SMTP Port', 'number', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(9, 'smtp_username', 'user@example.com', 'email', 'SMTP Username', 'text', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(10, 'smtp_password', '', 'email', 'SMTP Password', 'password', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(11, 'email_from_name', 'FitZone Fitness Center', 'email', 'Email From Name', 'text', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(12, 'email_from_address', 'norecccply@fitzone.com', 'email', 'Email From Address', 'email', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:52:10'),
(13, 'trial_days', '7', 'membership', 'Free Trial Days', 'number', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(14, 'max_class_bookings', '3', 'membership', 'Max Class Bookings per Week', 'number', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(15, 'cancellation_policy', '24 hours notice required for class cancellation without penalty', 'membership', 'Cancellation Policy', 'textarea', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(16, 'facebook_url', 'https://facebook.com/fitzone', 'social', 'Facebook URL', 'url', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(17, 'instagram_url', 'https://instagram.com/fitzone', 'social', 'Instagram URL', 'url', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(18, 'twitter_url', 'https://twitter.com/fitzone', 'social', 'Twitter URL', 'url', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(19, 'youtube_url', '', 'social', 'YouTube URL', 'url', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(20, 'maintenance_mode', '0', 'system', 'Maintenance Mode', 'boolean', NULL, 0, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(21, 'currency_symbol', 'Rs.', 'system', 'Currency Symbol', 'text', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(22, 'date_format', 'Y-m-d', 'system', 'Date Format', 'select', 'Y-m-d,d-m-Y,m/d/Y,d/m/Y', 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(23, 'time_format', 'H:i', 'system', 'Time Format', 'select', 'H:i,h:i A', 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31'),
(24, 'pagination_limit', '10', 'system', 'Default Pagination Limit', 'number', NULL, 1, '2025-04-24 09:51:31', '2025-04-24 09:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `client_title` varchar(100) NOT NULL,
  `client_photo` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`testimonial_id`, `user_id`, `client_name`, `client_title`, `client_photo`, `content`, `rating`, `featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Michael Thompson', 'CrossFit Enthusiast', 'assets/images/testimonials/client-1.jpg', 'The CrossFit program at FitZone is incredible! I\'ve been a member for 8 months and have seen dramatic improvements in my strength and conditioning. The coaches push you to your limits but always ensure proper form and safety. The community here is also amazing - we motivate each other to be better every day!', 5, 1, 1, '2025-04-03 13:43:34', '2025-04-03 19:14:05'),
(2, NULL, 'Sarah Martinez', 'Weight Loss Success', 'assets/images/testimonials/client-2.jpg', 'I joined FitZone after struggling with my weight for years. The personalized nutrition plan and training schedule made all the difference. In just 6 months, I\'ve lost 45 pounds and feel like a completely new person! The trainers are supportive and the atmosphere is never intimidating. Best decision I ever made for my health.', 5, 0, 1, '2025-04-03 13:43:34', '2025-04-03 19:14:05'),
(3, NULL, 'David Chen', 'Business Professional', 'assets/images/testimonials/client-3.jpg', 'As a busy executive, finding time for fitness was always challenging. FitZone\'s flexible scheduling and efficient 30-minute HIIT classes fit perfectly into my packed calendar. I\'m more productive at work, have better energy throughout the day, and my stress levels have decreased significantly. Worth every penny!', 4, 0, 1, '2025-04-03 13:43:34', '2025-04-03 19:14:05'),
(4, NULL, 'Jennifer Wilson', 'Yoga & Mindfulness', 'assets/images/testimonials/client-4.jpg', 'The yoga and mindfulness programs at FitZone have transformed not just my body but my entire approach to life. The instructors bring such positive energy and knowledge to each class. My flexibility has improved dramatically, and I\'ve learned breathing techniques that help me stay calm in stressful situations. I leave each session feeling renewed!', 5, 1, 1, '2025-04-03 13:43:34', '2025-04-03 19:14:05'),
(5, NULL, 'Robert Jackson', 'Senior Fitness Member', 'assets/images/testimonials/client-5.jpg', 'At 68 years old, I was hesitant to join a gym, fearing I wouldn\'t fit in. FitZone proved me wrong! Their senior program is thoughtfully designed for our unique needs. The trainers are patient and encouraging, and I\'ve met wonderful people in my age group. My balance has improved, and I feel stronger than I have in years. It\'s never too late to start your fitness journey!', 5, 0, 1, '2025-04-03 13:43:34', '2025-04-03 19:14:05');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `bio` text NOT NULL,
  `experience` int(11) NOT NULL COMMENT 'Experience in years',
  `certification` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `user_id`, `specialization`, `bio`, `experience`, `certification`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Strength Training', 'Former competitive powerlifter with 10+ years of coaching experience.', 12, 'NSCA Certified, ACE Personal Trainer', 'assets/images/trainers/trainer-1.jpg', 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48'),
(2, 2, 'HIIT & Functional Training', 'Former professional dancer turned fitness expert.', 8, 'NASM CPT, TRX Specialist', 'assets/images/trainers/trainer-2.jpg', 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48'),
(3, 3, 'Nutrition & Weight Management', 'Helps clients transform their bodies through proper nutrition and exercise.', 10, 'Precision Nutrition Level 2, ACSM CPT', 'assets/images/trainers/trainer-3.jpg', 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48'),
(4, 4, 'Yoga & Flexibility', 'Mindfulness and alignment expert, focused on mobility and wellness.', 7, '500hr RYT, NASM Corrective Exercise Specialist', 'assets/images/trainers/trainer-4.jpg', 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48'),
(5, 20, 'Pilates & Core Conditioning', 'Laura designs personalized Pilates routines focused on core strength, flexibility, and injury prevention. She helps clients develop strong, balanced bodies through precise movement and controlled breathing.', 6, 'STOTT PILATES Certified Instructor, NASM Corrective Exercise Specialist', 'assets/images/trainers/trainer-15.jpg', 1, '2025-04-20 05:10:54', '2025-04-20 05:10:54'),
(6, 33, 'General Fitness', 'Fitness trainer at FitZone', 1, '', '', 1, '2025-04-24 06:00:40', '2025-04-24 06:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','trainer','member','guest') NOT NULL DEFAULT 'member',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `phone`, `role`, `profile_image`, `is_active`, `created_at`, `updated_at`, `address`, `city`, `date_of_birth`) VALUES
(1, 'michael_reynolds', 'michael@fitzone.com', '$2y$10$abc123hash', 'Michael', 'Reynolds', NULL, 'trainer', NULL, 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48', NULL, NULL, NULL),
(2, 'sarah_johnson', 'sarah@fitzone.com', '$2y$10$abc123hash', 'Sarah', 'Johnson', NULL, 'trainer', NULL, 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48', NULL, NULL, NULL),
(3, 'david_chen', 'david@fitzone.com', '$2y$10$abc123hash', 'David', 'Chen', NULL, 'trainer', NULL, 1, '2025-04-20 05:04:48', '2025-04-20 05:04:48', NULL, NULL, NULL),
(4, 'emma', 'emma@fitzone.com', '$2y$10$x68O9fWOaYxoydKGljumSesJ4dye24n96TQFFIG4wFhHNqULU65NG', 'Emma', 'Williams', NULL, 'trainer', NULL, 1, '2025-04-20 05:04:48', '2025-04-21 05:04:25', NULL, NULL, NULL),
(20, 'laura_pilatesff', 'laura@fitzone.com', '$2y$10$abc123hash', 'Lauraf', 'Andersonr', '2r4f4f4', 'trainer', NULL, 1, '2025-04-20 05:09:43', '2025-04-23 18:51:00', NULL, NULL, NULL),
(21, 'admin', 'admin@fitzone.com', '$2y$10$x68O9fWOaYxoydKGljumSesJ4dye24n96TQFFIG4wFhHNqULU65NG', 'Admin', 'User', '0772339956', 'admin', NULL, 1, '2025-04-21 03:52:06', '2025-04-30 02:19:01', 'Bulugahamulla 81', 'Minuwangoda', '2025-04-26'),
(27, 'kaveesha', 'kaveesha@gmail.com', '$2y$12$f1VfTvVZRjKmldSoG/PrhOCfFFltFBzdIEfRgq/u7BAC2Wd5myhpq', 'kaveesha', 'wijesiriwardana', '0772339956', 'member', '1745380334_OVIN.jpg', 1, '2025-04-21 06:21:08', '2025-04-26 17:33:20', '55/1, Veyangoda Road', 'Minuwangoda', '2001-05-26'),
(29, 'kuchi', 'kuchi@gmail.com', '$2y$12$Z0CvEiLCRx8qp9jzTlntAe5C7wEZ8I869IO9rU8XoRAQOkrJahI3a', 'kuchi', 'baba', '07723scd', 'member', NULL, 1, '2025-04-21 11:55:14', '2025-04-23 08:07:18', NULL, NULL, NULL),
(31, 'gimas', 'gimasdwawaha17@gmail.com', '$2y$10$3KlCuFdFnWO/J5Alf61CGOw/7pg.5JGXZk9bP6qxJQvdZtv7cZx0.', 'Gimd', 'Wickramanayaka', '0773412171dww', 'member', NULL, 1, '2025-04-23 08:07:31', '2025-04-23 08:12:12', NULL, NULL, NULL),
(33, 'trainer', 'gaiya@gmail.com', '$2y$10$dPuUasNITIeqPHHpFlkQTOp8z7ixYnY4NbnZB0/8nGuhIciGfzBk.', 'gayantha', 'madu', '0772339956', 'trainer', NULL, 1, '2025-04-24 06:00:28', '2025-04-24 13:31:29', NULL, NULL, NULL),
(34, 'Shanon', 'shanon@gmail.com', '$2y$10$tTfNdry5LN9iOJBprR37d.b33LPe982LvyYnm6Xpl23tF.wIV9qPG', 'Shanon', 'Gomas', '0773456471', 'member', '1745688954_girl-pic-simple.jpg', 1, '2025-04-26 17:34:38', '2025-04-26 17:35:54', '', '', '0000-00-00'),
(35, 'Gayantha1', 'gayantha1@gmail.com', '$2y$12$JHLB1rFOuZXOEH1UZzDPpu6tu/FlRJbR0OL7qTkp/wccUIW/puxWC', 'Gayantha', 'Madushan', NULL, 'member', NULL, 1, '2025-04-26 17:46:26', '2025-04-26 17:50:12', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_remember_tokens`
--
-- Error reading structure for table fitzone_db.user_remember_tokens: #1932 - Table &#039;fitzone_db.user_remember_tokens&#039; doesn&#039;t exist in engine
-- Error reading data for table fitzone_db.user_remember_tokens: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `fitzone_db`.`user_remember_tokens`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `user_stats`
--

CREATE TABLE `user_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `body_fat` decimal(5,2) DEFAULT NULL,
  `workout_duration` int(11) DEFAULT NULL,
  `calories_burned` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class_bookings`
--
ALTER TABLE `class_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fitness_classes`
--
ALTER TABLE `fitness_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `fk_testimonials_users` (`user_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD KEY `fk_trainers_users` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_stats`
--
ALTER TABLE `user_stats`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class_bookings`
--
ALTER TABLE `class_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fitness_classes`
--
ALTER TABLE `fitness_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `user_stats`
--
ALTER TABLE `user_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `member_subscriptions`
--
ALTER TABLE `member_subscriptions`
  ADD CONSTRAINT `member_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `fk_testimonials_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `fk_trainers_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
