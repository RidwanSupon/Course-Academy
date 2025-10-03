-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2025 at 01:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ilm_path`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `name`, `email`, `created_at`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'Super Admin', 'admin@example.com', '2025-10-01 07:53:43'),
(2, 'add', '$2y$10$dAWWPdekoDH7mpHdXaHFPODiHNmW/59g.kOV.rK2tLu6fIu2T/mPK', 'Admin Name', 'admin@example.com', '2025-10-01 08:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `title`, `subtitle`, `link`, `sort_order`, `active`, `created_at`) VALUES
(8, '68ddff245d08d2.01982280.png', '', '', '', 0, 1, '2025-10-02 04:27:16'),
(9, '68de000ae16654.34782682.png', '', '', '', 0, 1, '2025-10-02 04:31:06'),
(10, '68de190692aab5.37942357.png', '', '', '', 0, 1, '2025-10-02 06:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_desc` text DEFAULT NULL,
  `long_desc` text DEFAULT NULL,
  `teacher` varchar(150) DEFAULT NULL,
  `gender` enum('Male','Female','Both') DEFAULT 'Both',
  `price` decimal(10,2) DEFAULT 0.00,
  `duration` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `short_desc`, `long_desc`, `teacher`, `gender`, `price`, `duration`, `active`, `created_at`) VALUES
(1, 'hi', 'hgfd', 'xdcfgvhbjnkml,kjhgfd', 'rster', 'Both', 500.00, '6month', 1, '2025-10-01 14:13:33'),
(2, ';kjhgfds', ',kjhgf', ',lkjhg', 'mkjh', 'Both', 0.00, '', 1, '2025-10-01 14:13:53'),
(3, ';lkjhgf', 'l,kjhglhg', 'mlkjh', 'l,kjhg', 'Both', 0.00, 'mknjh', 1, '2025-10-01 14:14:04'),
(4, 'l,kjhv', 'lkjh', 'kjhg', 'lkjh', 'Male', 0.00, ',mkjb', 1, '2025-10-01 14:14:17');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `payment_method` enum('Cash','bKash') NOT NULL,
  `bkash_txn_id` varchar(255) DEFAULT NULL,
  `extra` text DEFAULT NULL,
  `status` enum('Pending','Approved','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `course_id`, `user_id`, `name`, `email`, `location`, `phone`, `payment_method`, `bkash_txn_id`, `extra`, `status`, `created_at`) VALUES
(70, 2, NULL, 'Ridwanur Rahman', 'ridwansupon@gmail.com', 'dhaka', '01871032697', 'Cash', '', NULL, 'Pending', '2025-10-02 22:27:13'),
(76, 4, 10, 'rster', 'rster@gmail.com', 'cumilla', '01677689098', 'Cash', '', NULL, 'Pending', '2025-10-02 22:51:23'),
(77, 2, 4, 'Ridwanur Rahman', 'ridwansupon123@gmail.com', 'dhaka', '01871032697', 'Cash', '', NULL, 'Pending', '2025-10-02 22:53:07'),
(78, 1, 4, 'Ridwanur Rahman', 'ridwansupon123@gmail.com', 'dhaka', '01871032697', 'Cash', '', NULL, 'Pending', '2025-10-02 22:55:49'),
(79, 4, 4, 'Ridwanur Rahman', 'ridwansupon123@gmail.com', 'dhaka', '01871032697', 'bKash', 'txt-121sasa', NULL, 'Pending', '2025-10-02 22:59:57'),
(80, 3, 4, 'Ridwanur Rahman', 'ridwansupon123@gmail.com', 'dhaka', '01871032697', 'Cash', '', NULL, 'Pending', '2025-10-02 23:05:56'),
(84, 4, 3, 'Ridwanur Rahman', NULL, NULL, NULL, 'Cash', '', NULL, 'Pending', '2025-10-03 11:33:25'),
(85, 3, 3, 'Ridwanur Rahman', NULL, 'dhaka', NULL, 'Cash', '', NULL, 'Pending', '2025-10-03 11:39:07'),
(86, 2, 3, 'Ridwanur Rahman', NULL, 'dha', NULL, 'Cash', '', NULL, 'Pending', '2025-10-03 11:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `free_class_requests`
--

CREATE TABLE `free_class_requests` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('New','Contacted','Scheduled','Cancelled') DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `free_class_requests`
--

INSERT INTO `free_class_requests` (`id`, `course_id`, `user_id`, `name`, `email`, `phone`, `preferred_date`, `preferred_time`, `message`, `status`, `created_at`) VALUES
(2, 2, 3, 'Ridwanur Rahman', 'ridwansupon@gmail.com', '01871032697', '2025-10-26', '16', 'i am interested', 'New', '2025-10-02 21:24:35'),
(3, 1, 3, 'Ridwanur Rahman', 'ridwansupon@gmail.com', '01871032697', NULL, NULL, 'khg', 'New', '2025-10-02 21:26:59');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image`, `caption`, `created_at`) VALUES
(9, 'img_68de13ba83f0d6.22779616.png', '', '2025-10-02 05:55:06'),
(10, 'img_68de13ba8496e3.06784339.png', '', '2025-10-02 05:55:06'),
(11, 'img_68de13ba8503d6.39812410.png', '', '2025-10-02 05:55:06'),
(12, 'img_68de13c71afd31.28907426.png', '', '2025-10-02 05:55:19'),
(13, 'img_68de13e7a00fc1.50055508.png', '', '2025-10-02 05:55:51'),
(14, 'img_68de13e7a092c0.41653255.png', '', '2025-10-02 05:55:51'),
(15, 'img_68de13e7a0fed2.39140563.png', '', '2025-10-02 05:55:51'),
(16, 'img_68de13e7a15f61.70797937.png', '', '2025-10-02 05:55:51'),
(17, 'img_68de1915deb661.78358760.jpg', '', '2025-10-02 06:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`id`, `name`, `email`, `phone`, `bio`, `photo`, `specialization`, `created_at`) VALUES
(5, 'Ridwanur Rahman', 'ridwansupon@gmail.com', '01871032697', 'there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline there is no set way to be a good but you have to follow regular routine and discipline', 'mentor_68deb42bb10751.61476723.jpg', 'cse', '2025-10-02 17:19:39'),
(7, 'Hasan', 'ridwansupon@gmail.com', '01871032697', 'hello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thinghello i am here for making crazy thing', 'mentor_68dec46f2c06a8.55107370.png', 'physics', '2025-10-02 18:29:03');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `country`, `message`, `image`, `rating`, `created_at`) VALUES
(1, 'Hasan Al Banna', NULL, 'I am very happy to as student of this academy. you guys can take any courses from this may your course will worth it.', 'rev_68de1741253293.29749600.png', 5, '2025-10-02 06:10:09'),
(2, 'Banna', NULL, 'oiuhygtr', 'rev_68de1741261969.44226315.jpg', 5, '2025-10-02 06:10:09'),
(3, 'Hasan Al Banna', NULL, ',lkjhgvfd', 'rev_68de19f0c3d2d4.35212608.jpg', 5, '2025-10-02 06:21:36'),
(4, 'Hasan Al Banna', NULL, 'sasadfghjhghfdsa', '', 5, '2025-10-02 08:54:55'),
(5, 'Hasan Al Banna', NULL, 'asdfghj', '', 4, '2025-10-02 08:55:25'),
(6, 'hasab', NULL, 'asdfg', '', 5, '2025-10-02 08:55:25'),
(7, 'Hasan Al Banna', NULL, 'jhgfd', 'rev_68de3e5b11e611.01525027.jpg', 5, '2025-10-02 08:56:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `created_at`) VALUES
(3, 'Ridwanur Rahman', 'ridwansupon@gmail.com', '+8801871032697', '$2y$10$PK6u/YqiqHhpWYYUh1W5Fuf/AoacONrsjfMw70G37V8K75qFIDUbu', '2025-10-01 17:39:16'),
(4, 'Ridwanur Rahman', 'ridwansupon123@gmail.com', '01871032697', '$2y$10$3zL/yCZt00ypbxpoqr5AIOy4QQi30GdqLvTnguFs.htp1Le6FHNhu', '2025-10-01 18:03:12'),
(5, 'hasan', 'rridwansupon@gmail.com', '01575757', '$2y$10$T5OidX5ceMJ.rB8ioAFaAOcX6n3uoawP2iFW/EG0kSH0ca9UkINZC', '2025-10-01 19:12:44'),
(6, 'rafi', 'rafi@gmail.com', '0157777777', '$2y$10$ltTQElHJsYTR.GlCtodqXeZIPwVdBbiRkvOud53V6bAe/a5VTO7Z.', '2025-10-01 19:19:07'),
(7, 'Mahmud Hasan', 'mahmud123@gmail.com', '015454454545', '$2y$10$j4g3yPAzhHKrNAW1CryQo.D7cjzF3MRPXpUCVAzJEg0POU6.Oc.iG', '2025-10-01 19:25:47'),
(8, 'rafik', 'rafik@gmail.com', '01545454545', '$2y$10$2foj4Dx1nOkTtRfHLqQ.b.0WC6Hy8D88fzqPIht.iPHD/NII5VJ/C', '2025-10-01 20:00:42'),
(10, 'rster', 'rster@gmail.com', '01677689098', '$2y$10$eAUf.LivFQOh5tx2K3kB6uQhiiVwAX34KO8TJx3KC59.TF14uA.a6', '2025-10-02 22:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `url`, `description`, `created_at`) VALUES
(1, 'Surah Yasin', 'https://www.youtube.com/watch?v=VLyktW15HXI&t=224s', 'hear this every day', '2025-10-01 08:28:36'),
(2, 'hjhj', 'https://www.youtube.com/watch?v=AXa8vqz-wHw', 'nkjhbvgfdfgvh', '2025-10-01 08:29:05'),
(3, 'a', 'https://www.youtube.com/watch?v=tLLzaNle5Bk', '', '2025-10-02 14:44:48'),
(4, 'hi', 'https://www.youtube.com/watch?v=tLLzaNle5Bk', '', '2025-10-02 14:45:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `free_class_requests`
--
ALTER TABLE `free_class_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `free_class_requests`
--
ALTER TABLE `free_class_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `free_class_requests`
--
ALTER TABLE `free_class_requests`
  ADD CONSTRAINT `fk_free_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_free_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
