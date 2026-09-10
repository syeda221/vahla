-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2026 at 03:00 PM
-- Server version: 8.0.46
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `binsult1_yp`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `head_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Debit',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `head_id`, `title`, `account_code`, `opening_balance`, `current_balance`, `type`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Accounts Payable', 'AP', 0.00, -29850000.00, 'Credit', 1, 1, '2026-09-02 18:13:55', '2026-09-09 21:05:14'),
(2, NULL, 'Accounts Receivable', 'AR', 0.00, 612259.00, 'Debit', 1, 1, '2026-09-05 10:52:52', '2026-09-10 09:40:55'),
(3, NULL, 'Sales Revenue', 'SALES', 0.00, -13393.00, 'Credit', 1, 1, '2026-09-05 11:20:10', '2026-09-10 09:45:24'),
(4, 1, 'Cash in Hand', 'ACC-0004', 100000.00, 300000.00, 'Debit', 1, 1, '2026-09-05 11:39:40', '2026-09-09 21:01:32'),
(5, 1, 'Account Receivable', 'ACC-0005', 0.00, 0.00, 'Debit', 1, 1, '2026-09-10 09:13:52', '2026-09-10 09:13:52'),
(6, 1, 'Easypaisa Account', 'ACC-0006', 100000.00, 100000.00, 'Debit', 1, 1, '2026-09-10 09:18:38', '2026-09-10 09:18:38'),
(7, 3, 'cash in hand', 'ACC-0007', 0.00, 0.00, 'Debit', 1, 1, '2026-09-10 09:33:29', '2026-09-10 09:40:55'),
(8, 4, 'Easypaisa Account', 'ACC-0008', 0.00, 0.00, 'Debit', 1, 1, '2026-09-10 09:42:03', '2026-09-10 09:42:03'),
(9, 5, 'EasyPaisa', 'ACC-0009', 10000.00, 1134.00, 'Debit', 1, 1, '2026-09-10 09:43:13', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `account_heads`
--

CREATE TABLE `account_heads` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Asset','Liability','Equity','Revenue','Expense') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` tinyint NOT NULL DEFAULT '1' COMMENT '1=Group, 2=Control, 3=Detail',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_heads`
--

INSERT INTO `account_heads` (`id`, `code`, `parent_id`, `name`, `type`, `level`, `opening_balance`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Current Assets', NULL, 1, 0.00, '2026-09-05 11:39:17', '2026-09-05 11:39:17'),
(2, NULL, NULL, 'Expense', NULL, 1, 0.00, '2026-09-10 09:19:22', '2026-09-10 09:19:22'),
(3, NULL, NULL, 'cash', NULL, 1, 0.00, '2026-09-10 09:33:15', '2026-09-10 09:33:15'),
(4, NULL, NULL, 'Banks', NULL, 1, 0.00, '2026-09-10 09:41:41', '2026-09-10 09:41:41'),
(5, NULL, NULL, 'Bank', NULL, 1, 0.00, '2026-09-10 09:42:55', '2026-09-10 09:42:55');

-- --------------------------------------------------------

--
-- Table structure for table `account_histories`
--

CREATE TABLE `account_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `old_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `new_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `account_histories`
--

INSERT INTO `account_histories` (`id`, `account_id`, `old_balance`, `new_balance`, `user_id`, `user_name`, `note`, `created_at`, `updated_at`) VALUES
(1, 4, 0.00, 100000.00, 1, 'Super Admin', 'Account created with initial balance: Rs 100,000.00', '2026-09-05 11:39:40', '2026-09-05 11:39:40'),
(2, 5, 0.00, 0.00, 1, 'Super Admin', 'Account created with initial balance: Rs 0.00', '2026-09-10 09:13:52', '2026-09-10 09:13:52'),
(3, 6, 0.00, 100000.00, 1, 'Super Admin', 'Account created with initial balance: Rs 100,000.00', '2026-09-10 09:18:38', '2026-09-10 09:18:38'),
(4, 7, 0.00, 0.00, 2, 'atif', 'Account created with initial balance: Rs 0.00', '2026-09-10 09:33:29', '2026-09-10 09:33:29'),
(5, 8, 0.00, 0.00, 1, 'Super Admin', 'Account created with initial balance: Rs 0.00', '2026-09-10 09:42:03', '2026-09-10 09:42:03'),
(6, 9, 0.00, 0.00, 1, 'Super Admin', 'Account created with initial balance: Rs 0.00', '2026-09-10 09:43:13', '2026-09-10 09:43:13'),
(7, 9, 0.00, 0.00, 1, 'Super Admin', 'Opening: Rs 0.00 -> Rs 10,000.00', '2026-09-10 09:43:38', '2026-09-10 09:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `biometric_devices`
--

CREATE TABLE `biometric_devices` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` int NOT NULL DEFAULT '4370',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'STEELEX', '2026-09-05 11:14:48', '2026-09-05 11:14:48');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `show_on_website` tinyint(1) NOT NULL DEFAULT '0',
  `web_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`, `show_on_website`, `web_image`) VALUES
(1, 'Electronics', '2026-09-02 15:53:20', '2026-09-02 15:53:20', 0, NULL),
(2, 'machine', '2026-09-02 15:53:20', '2026-09-02 15:53:20', 0, NULL),
(3, 'Tools', '2026-09-02 15:53:20', '2026-09-02 15:53:20', 0, NULL),
(4, 'Plumbing', '2026-09-02 15:53:20', '2026-09-02 15:53:20', 0, NULL),
(5, 'Hardware', '2026-09-02 15:53:21', '2026-09-02 15:53:21', 0, NULL),
(6, 'Electrical', '2026-09-02 15:53:21', '2026-09-02 15:53:21', 0, NULL),
(7, 'Automotive', '2026-09-02 15:53:21', '2026-09-02 15:53:21', 0, NULL),
(8, 'Men', '2026-09-02 15:53:21', '2026-09-02 15:53:21', 1, NULL),
(9, 'Women', '2026-09-02 15:53:21', '2026-09-02 15:53:21', 1, NULL),
(12, 'UPVC Pipes', '2026-09-05 11:11:59', '2026-09-05 11:11:59', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_spend` decimal(10,2) DEFAULT NULL,
  `max_uses` int DEFAULT NULL,
  `uses` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_synced` tinyint NOT NULL DEFAULT '0',
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name_ur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_address_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_officer_id` bigint UNSIGNED DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL,
  `previous_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_range` decimal(12,2) NOT NULL DEFAULT '0.00',
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_reminder_date` date DEFAULT NULL,
  `reminder_day` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_snoozed_at` date DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Manual'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `uuid`, `is_synced`, `customer_id`, `customer_name`, `customer_name_ur`, `cnic`, `filer_type`, `zone`, `contact_person`, `mobile`, `email_address`, `contact_person_2`, `mobile_2`, `email_address_2`, `customer_type`, `sales_officer_id`, `opening_balance`, `previous_balance`, `balance_range`, `address`, `created_at`, `updated_at`, `status`, `payment_reminder_date`, `reminder_day`, `reminder_snoozed_at`, `source`) VALUES
(1, 'edcfa5fa-77b7-4427-9072-be88cf53ee3c', 0, 'CUST-0001', 'Mumtaz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Main Customer', NULL, 950000.00, 615800.00, 1000000.00, NULL, '2026-09-05 10:52:52', '2026-09-10 09:33:35', 'active', NULL, 'Thursday', NULL, 'Manual'),
(2, '858d1f66-c399-43cd-93ba-a7645b2fa7c3', 0, 'CUST-0002', 'Prowave', NULL, NULL, NULL, NULL, NULL, '03001234567', NULL, NULL, NULL, NULL, 'Main Customer', NULL, 0.00, 8660.00, 0.00, NULL, '2026-09-09 20:46:27', '2026-09-09 23:04:50', 'active', NULL, NULL, NULL, 'Manual'),
(3, '6a3aff1d-d4da-4d7f-9531-6d4c3d85e791', 0, 'CUST-WALK', 'Walking Customer', NULL, NULL, NULL, NULL, NULL, '-', NULL, NULL, NULL, NULL, 'Walking Customer', NULL, 0.00, 0.00, 0.00, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55', 'active', NULL, NULL, NULL, 'Manual');

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledgers`
--

CREATE TABLE `customer_ledgers` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `previous_balance` decimal(12,2) NOT NULL,
  `closing_balance` decimal(12,2) NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_ledgers`
--

INSERT INTO `customer_ledgers` (`id`, `customer_id`, `admin_or_user_id`, `previous_balance`, `closing_balance`, `opening_balance`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0.00, 6237.00, 0.00, 'Sale Invoice #INV-0001', '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(2, 1, 1, 0.00, 950000.00, 950000.00, 'Opening Balance', '2026-09-05 10:52:52', '2026-09-05 11:32:36'),
(4, 1, 1, 955670.00, 955846.00, 0.00, 'Sale Invoice #INV-0003', '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(5, 1, 2, 955846.00, 959416.00, 0.00, 'Sale Invoice #INV-0004', '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(10, 1, 2, 959416.00, 759416.00, 0.00, 'Receipt Voucher RVID-0001', '2026-09-09 21:01:32', '2026-09-09 21:01:32'),
(11, 1, 2, 759416.00, 609416.00, 0.00, 'Party Transfer to Steelex PVT Limited (Ref: TVID-011) - Slip No.56321', '2026-09-09 21:05:14', '2026-09-09 21:05:14'),
(12, 2, 2, 0.00, 1760.00, 0.00, 'Sale Invoice #INV-0005', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(13, 1, 2, 609416.00, 615086.00, 0.00, 'Sale Invoice #INV-0002', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(14, 2, 1, 1760.00, 8660.00, 0.00, 'Sale Invoice #INV-0006', '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(16, 1, 1, 615086.00, 615800.00, 0.00, 'Sale Invoice #INV-0008', '2026-09-10 09:33:35', '2026-09-10 09:33:35');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_types`
--

CREATE TABLE `customer_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_static` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_types`
--

INSERT INTO `customer_types` (`id`, `name`, `description`, `is_static`, `created_at`, `updated_at`) VALUES
(1, 'Main Customer', 'Default customer type', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(2, 'Walking Customer', 'Static customer type for instant upfront payment', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(3, 'Supplier', NULL, 0, '2026-09-09 22:53:41', '2026-09-09 22:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `day_closings`
--

CREATE TABLE `day_closings` (
  `id` bigint UNSIGNED NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `inflow_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `outflow_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expected_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `difference` decimal(15,2) NOT NULL DEFAULT '0.00',
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `day_closings`
--

INSERT INTO `day_closings` (`id`, `opening_balance`, `inflow_amount`, `outflow_amount`, `expected_balance`, `actual_balance`, `difference`, `opened_at`, `closed_at`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2026-09-05 10:42:56', NULL, 'open', NULL, '2026-09-05 10:42:56', '2026-09-05 10:42:56');

-- --------------------------------------------------------

--
-- Table structure for table `drawer_transactions`
--

CREATE TABLE `drawer_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `day_closing_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'settled',
  `returned_in_closing_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_orders`
--

CREATE TABLE `ecommerce_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `web_customer_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_charges` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_screenshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `is_stock_deducted` tinyint(1) NOT NULL DEFAULT '0',
  `order_notes` text COLLATE utf8mb4_unicode_ci,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecommerce_order_items`
--

CREATE TABLE `ecommerce_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `ecommerce_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `quantity` int NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary_structures`
--

CREATE TABLE `employee_salary_structures` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `salary_structure_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `assigned_by` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_vouchers`
--

CREATE TABLE `expense_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `evid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_attendances`
--

CREATE TABLE `hr_attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `check_in_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_in_latitude` decimal(10,8) DEFAULT NULL,
  `check_in_longitude` decimal(11,8) DEFAULT NULL,
  `check_in_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_out_latitude` decimal(10,8) DEFAULT NULL,
  `check_out_longitude` decimal(11,8) DEFAULT NULL,
  `check_out_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `status` enum('present','absent','late','leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `late_minutes` int NOT NULL DEFAULT '0',
  `is_early_in` tinyint(1) NOT NULL DEFAULT '0',
  `early_in_minutes` int NOT NULL DEFAULT '0',
  `is_early_leave` tinyint(1) NOT NULL DEFAULT '0',
  `early_leave_minutes` int NOT NULL DEFAULT '0',
  `total_hours` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_departments`
--

CREATE TABLE `hr_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_designations`
--

CREATE TABLE `hr_designations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `requires_location` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employees`
--

CREATE TABLE `hr_employees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `designation_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED DEFAULT NULL,
  `custom_start_time` time DEFAULT NULL,
  `custom_end_time` time DEFAULT NULL,
  `face_encoding` text COLLATE utf8mb4_unicode_ci,
  `face_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `biometric_device_id` bigint UNSIGNED DEFAULT NULL,
  `device_user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint_enrolled_at` timestamp NULL DEFAULT NULL,
  `last_device_sync_at` timestamp NULL DEFAULT NULL,
  `punch_gap_minutes` int UNSIGNED DEFAULT NULL,
  `pending_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_docs_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `date_of_birth` date DEFAULT NULL,
  `joining_date` date NOT NULL,
  `status` enum('active','non-active','terminated') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employee_documents`
--

CREATE TABLE `hr_employee_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_holidays`
--

CREATE TABLE `hr_holidays` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` enum('public','company','optional') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leaves`
--

CREATE TABLE `hr_leaves` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `leave_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loans`
--

CREATE TABLE `hr_loans` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `installment_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monthly deductible amount, 0 for manual/large sum',
  `status` enum('pending','approved','rejected','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_payments`
--

CREATE TABLE `hr_loan_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary_deduction',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_scheduled_deductions`
--

CREATE TABLE `hr_loan_scheduled_deductions` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deduction_month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','deducted','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payrolls`
--

CREATE TABLE `hr_payrolls` (
  `id` bigint UNSIGNED NOT NULL,
  `employee_id` bigint UNSIGNED NOT NULL,
  `payroll_type` enum('monthly','daily') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `month` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `gross_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `attendance_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `manual_deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `manual_allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carried_forward_deduction` decimal(10,2) NOT NULL DEFAULT '0.00',
  `carried_forward_to_next` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonuses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('generated','reviewed','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generated',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_details`
--

CREATE TABLE `hr_payroll_details` (
  `id` bigint UNSIGNED NOT NULL,
  `payroll_id` bigint UNSIGNED NOT NULL,
  `type` enum('allowance','deduction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_salary_structures`
--

CREATE TABLE `hr_salary_structures` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_structure_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL,
  `salary_type` enum('salary','commission','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'salary',
  `base_salary` decimal(12,2) NOT NULL DEFAULT '0.00',
  `daily_wages` decimal(10,2) DEFAULT NULL,
  `use_daily_wages` tinyint(1) NOT NULL DEFAULT '0',
  `commission_percentage` decimal(5,2) DEFAULT NULL,
  `sales_target` decimal(12,2) DEFAULT NULL,
  `commission_tiers` json DEFAULT NULL,
  `allowances` json DEFAULT NULL,
  `deductions` json DEFAULT NULL,
  `attendance_deduction_policy` json DEFAULT NULL,
  `carry_forward_deductions` tinyint(1) NOT NULL DEFAULT '0',
  `leave_salary_per_day` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_settings`
--

CREATE TABLE `hr_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_settings`
--

INSERT INTO `hr_settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'attendance_punch_gap_minutes', '20', 'integer', 'attendance', 'Punch Gap (Minutes)', 'Minimum minutes between punches to be considered as separate check-in/check-out. Punches within this gap will be ignored as duplicates.', '2026-09-02 15:52:03', '2026-09-02 15:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `hr_shifts`
--

CREATE TABLE `hr_shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `grace_minutes` int NOT NULL DEFAULT '15',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_series`
--

CREATE TABLE `invoice_series` (
  `id` bigint UNSIGNED NOT NULL,
  `prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `next_number` bigint UNSIGNED NOT NULL DEFAULT '1',
  `padding` int NOT NULL DEFAULT '4',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_series`
--

INSERT INTO `invoice_series` (`id`, `prefix`, `next_number`, `padding`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'INV', 11, 4, 1, '2026-09-02 15:53:18', '2026-09-10 09:45:24'),
(2, 'INVSLE', 1, 4, 0, '2026-09-02 15:53:18', '2026-09-02 15:53:18'),
(3, 'SQ', 1, 6, 0, '2026-09-02 15:53:18', '2026-09-02 15:53:18');

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepasses`
--

CREATE TABLE `inward_gatepasses` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `gatepass_date` date DEFAULT NULL,
  `gatepass_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','linked','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepass_items`
--

CREATE TABLE `inward_gatepass_items` (
  `id` bigint UNSIGNED NOT NULL,
  `inward_gatepass_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(12,4) DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint UNSIGNED NOT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_reconciled` tinyint(1) NOT NULL DEFAULT '0',
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `source_type`, `source_id`, `account_id`, `entry_date`, `debit`, `credit`, `description`, `is_reconciled`, `reconciled_at`, `created_at`, `updated_at`, `party_type`, `party_id`) VALUES
(1, 'App\\Models\\VoucherMaster', 1, 2, '2026-09-05', 6237.00, 0.00, 'Sale Invoice #INV-0001', 0, NULL, '2026-09-05 11:20:10', '2026-09-05 11:20:10', 'App\\Models\\Customer', 1),
(2, 'App\\Models\\VoucherMaster', 1, 3, '2026-09-05', 0.00, 6237.00, 'Sale Invoice #INV-0001', 0, NULL, '2026-09-05 11:20:10', '2026-09-05 11:20:10', NULL, NULL),
(3, 'App\\Models\\Customer', 1, 2, '2026-09-05', 950000.00, 0.00, 'Opening Balance', 0, NULL, '2026-09-05 11:32:36', '2026-09-05 11:32:36', 'App\\Models\\Customer', 1),
(6, 'App\\Models\\VoucherMaster', 3, 2, '2026-09-05', 176.00, 0.00, 'Sale Invoice #INV-0003', 0, NULL, '2026-09-05 12:54:14', '2026-09-05 12:54:14', 'App\\Models\\Customer', 1),
(7, 'App\\Models\\VoucherMaster', 3, 3, '2026-09-05', 0.00, 176.00, 'Sale Invoice #INV-0003', 0, NULL, '2026-09-05 12:54:14', '2026-09-05 12:54:14', NULL, NULL),
(8, 'App\\Models\\VoucherMaster', 4, 2, '2026-01-01', 3570.00, 0.00, 'Sale Invoice #INV-0004', 0, NULL, '2026-09-09 10:24:08', '2026-09-09 10:24:08', 'App\\Models\\Customer', 1),
(9, 'App\\Models\\VoucherMaster', 4, 3, '2026-01-01', 0.00, 3570.00, 'Sale Invoice #INV-0004', 0, NULL, '2026-09-09 10:24:08', '2026-09-09 10:24:08', NULL, NULL),
(10, 'App\\Models\\VoucherMaster', 5, 3, '2026-09-09', 11340.00, 0.00, 'Credit Note for Return #SR-0001', 0, NULL, '2026-09-09 10:26:48', '2026-09-09 10:26:48', NULL, NULL),
(11, 'App\\Models\\VoucherMaster', 5, 2, '2026-09-09', 0.00, 11340.00, 'Sale Return #SR-0001', 0, NULL, '2026-09-09 10:26:48', '2026-09-09 10:26:48', 'App\\Models\\Customer', 1),
(12, 'App\\Models\\VoucherMaster', 6, 3, '2026-09-09', 1428.00, 0.00, 'Credit Note for Return #SR-0002', 0, NULL, '2026-09-09 13:08:37', '2026-09-09 13:08:37', NULL, NULL),
(13, 'App\\Models\\VoucherMaster', 6, 2, '2026-09-09', 0.00, 1428.00, 'Sale Return #SR-0002', 0, NULL, '2026-09-09 13:08:37', '2026-09-09 13:08:37', 'App\\Models\\Customer', 1),
(22, 'App\\Models\\VoucherMaster', 11, 4, '2026-09-10', 200000.00, 0.00, 'Payment Received', 0, NULL, '2026-09-09 21:01:32', '2026-09-09 21:01:32', NULL, NULL),
(23, 'App\\Models\\VoucherMaster', 11, 2, '2026-09-10', 0.00, 200000.00, 'Receipt from customer', 0, NULL, '2026-09-09 21:01:32', '2026-09-09 21:01:32', 'App\\Models\\Customer', 1),
(24, 'App\\Models\\Vendor', 1, 1, '2026-09-10', 0.00, 30000000.00, 'Opening Balance', 0, NULL, '2026-09-09 21:03:22', '2026-09-09 21:03:22', 'App\\Models\\Vendor', 1),
(25, 'App\\Models\\VoucherMaster', 12, 2, '2026-09-08', 0.00, 150000.00, 'Party Transfer to Steelex PVT Limited (Ref: TVID-011) - Slip No.56321', 0, NULL, '2026-09-09 21:05:14', '2026-09-09 21:05:14', 'App\\Models\\Customer', 1),
(26, 'App\\Models\\VoucherMaster', 12, 1, '2026-09-08', 150000.00, 0.00, 'Party Transfer from Mumtaz (Ref: TVID-011) - Slip No.56321', 0, NULL, '2026-09-09 21:05:14', '2026-09-09 21:05:14', 'App\\Models\\Vendor', 1),
(27, 'App\\Models\\VoucherMaster', 13, 2, '2026-01-01', 1760.00, 0.00, 'Sale Invoice #INV-0005', 0, NULL, '2026-09-09 21:42:55', '2026-09-09 21:42:55', 'App\\Models\\Customer', 2),
(28, 'App\\Models\\VoucherMaster', 13, 3, '2026-01-01', 0.00, 1760.00, 'Sale Invoice #INV-0005', 0, NULL, '2026-09-09 21:42:55', '2026-09-09 21:42:55', NULL, NULL),
(29, 'App\\Models\\VoucherMaster', 14, 2, '2026-01-01', 5670.00, 0.00, 'Sale Invoice #INV-0002', 0, NULL, '2026-09-09 21:43:23', '2026-09-09 21:43:23', 'App\\Models\\Customer', 1),
(30, 'App\\Models\\VoucherMaster', 14, 3, '2026-01-01', 0.00, 5670.00, 'Sale Invoice #INV-0002', 0, NULL, '2026-09-09 21:43:23', '2026-09-09 21:43:23', NULL, NULL),
(31, 'App\\Models\\VoucherMaster', 15, 2, '2026-01-01', 6900.00, 0.00, 'Sale Invoice #INV-0006', 0, NULL, '2026-09-09 23:04:50', '2026-09-09 23:04:50', 'App\\Models\\Customer', 2),
(32, 'App\\Models\\VoucherMaster', 15, 3, '2026-01-01', 0.00, 6900.00, 'Sale Invoice #INV-0006', 0, NULL, '2026-09-09 23:04:50', '2026-09-09 23:04:50', NULL, NULL),
(35, 'App\\Models\\VoucherMaster', 17, 2, '2026-01-01', 714.00, 0.00, 'Sale Invoice #INV-0008', 0, NULL, '2026-09-10 09:33:35', '2026-09-10 09:33:35', 'App\\Models\\Customer', 1),
(36, 'App\\Models\\VoucherMaster', 17, 3, '2026-01-01', 0.00, 714.00, 'Sale Invoice #INV-0008', 0, NULL, '2026-09-10 09:33:35', '2026-09-10 09:33:35', NULL, NULL),
(37, 'App\\Models\\VoucherMaster', 18, 7, '2026-09-10', 357.00, 0.00, 'Payment received from Invoice #INV-0009', 0, NULL, '2026-09-10 09:40:05', '2026-09-10 09:40:05', NULL, NULL),
(38, 'App\\Models\\VoucherMaster', 18, 3, '2026-09-10', 0.00, 357.00, 'Payment for Invoice #INV-0009', 0, NULL, '2026-09-10 09:40:05', '2026-09-10 09:40:05', NULL, NULL),
(39, 'App\\Models\\VoucherMaster', 19, 7, '2026-09-10', 0.00, 357.00, 'Cash Refund Paid', 0, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55', NULL, NULL),
(40, 'App\\Models\\VoucherMaster', 19, 2, '2026-09-10', 357.00, 0.00, 'Refund to Customer', 0, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55', 'App\\Models\\Customer', 3),
(41, 'App\\Models\\VoucherMaster', 20, 3, '2026-09-10', 357.00, 0.00, 'Credit Note for Return #SR-0003', 0, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55', NULL, NULL),
(42, 'App\\Models\\VoucherMaster', 20, 2, '2026-09-10', 0.00, 357.00, 'Sale Return #SR-0003', 0, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55', 'App\\Models\\Customer', 3),
(43, 'App\\Models\\VoucherMaster', 21, 9, '2026-09-10', 1134.00, 0.00, 'Payment received from Invoice #INV-0010', 0, NULL, '2026-09-10 09:45:24', '2026-09-10 09:45:24', NULL, NULL),
(44, 'App\\Models\\VoucherMaster', 21, 3, '2026-09-10', 0.00, 1134.00, 'Payment for Invoice #INV-0010', 0, NULL, '2026-09-10 09:45:24', '2026-09-10 09:45:24', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_18_203013_create_categories_table', 1),
(6, '2025_07_18_215154_create_brands_table', 1),
(7, '2025_07_18_220702_create_units_table', 1),
(8, '2025_07_18_231906_create_subcategories_table', 1),
(9, '2025_07_19_074903_create_products_table', 1),
(10, '2025_07_20_220859_create_vendors_table', 1),
(11, '2025_07_20_220905_create_warehouses_table', 1),
(12, '2025_07_20_232951_create_branches_table', 1),
(13, '2025_07_21_125354_create_customers_table', 1),
(14, '2025_07_21_135411_add_status_to_customers_table', 1),
(15, '2025_07_21_181654_create_zones_table', 1),
(16, '2025_07_21_185626_create_sales_officers_table', 1),
(17, '2025_07_21_195751_create_permission_tables', 1),
(18, '2025_07_21_203215_create_transports_table', 1),
(19, '2025_07_21_213504_create_purchases_table', 1),
(20, '2025_07_21_214736_add_urdu_fields_to_transports_table', 1),
(21, '2025_08_09_220420_create_narrations_table', 1),
(22, '2025_08_09_231230_create_vouchers_table', 1),
(23, '2025_08_11_071804_add_barcode_path_to_products_table', 1),
(24, '2025_08_11_092258_add_missing_columns_to_products_table', 1),
(25, '2025_08_11_133922_add_brand_id_to_products_table', 1),
(26, '2025_08_13_225511_create_purchase_items_table', 1),
(27, '2025_08_13_225620_create_stocks_table', 1),
(28, '2025_08_17_213427_create_table_vendor_ledgers', 1),
(29, '2025_08_17_221914_create_vendor_payments_table', 1),
(30, '2025_08_17_222748_create_vendor_bilties_table', 1),
(31, '2025_08_17_225451_create_customer_ledgers_table', 1),
(32, '2025_08_17_225912_create_customer_payments_table', 1),
(33, '2025_08_20_235830_create_table_sales', 1),
(34, '2025_08_22_122351_create_product_discounts_table', 1),
(35, '2025_08_22_213952_create_warehouse_stocks_table', 1),
(36, '2025_08_22_214859_create_stock_transfers_table', 1),
(37, '2025_08_28_223934_create_inward_gatepasses_table', 1),
(38, '2025_08_28_224111_create_inward_gatepass_items_table', 1),
(39, '2025_08_31_093412_create_product_bookings_table', 1),
(40, '2025_09_02_164836_create_purchase_returns_table', 1),
(41, '2025_09_02_164843_create_purchase_return_items_table', 1),
(42, '2025_09_10_181016_add_opening_balance_to_customer_ledgers_table', 1),
(43, '2025_09_13_012942_add_part_fields_to_products_table', 1),
(44, '2025_09_13_022223_create_product_boms_table', 1),
(45, '2025_09_13_022411_create_stock_movements_table', 1),
(46, '2025_09_13_055335_add_index_and_view_for_onhand', 1),
(47, '2025_11_09_135454_add_is_auto_pluck_to_stock_movements_table', 1),
(48, '2025_11_09_135841_add_ref_uuid_to_stock_movements_table', 1),
(49, '2025_12_19_072030_create_package_types_table', 1),
(50, '2025_12_22_190616_add_columns_to_products_table', 1),
(51, '2025_12_30_020855_create_sessions_table', 1),
(52, '2026_01_16_000000_create_modules_table', 1),
(53, '2026_01_17_000000_create_hr_module_tables', 1),
(54, '2026_01_17_000001_create_designations_table', 1),
(55, '2026_01_17_000002_add_details_to_hr_employees_table', 1),
(56, '2026_01_17_000003_create_hr_employee_documents_table', 1),
(57, '2026_01_17_000004_create_hr_salary_structures_table', 1),
(58, '2026_01_17_000005_add_commission_tiers_to_hr_salary_structures', 1),
(59, '2026_01_17_000006_create_hr_shifts_table', 1),
(60, '2026_01_17_000007_create_hr_holidays_table', 1),
(61, '2026_01_17_000008_update_hr_attendance_system', 1),
(62, '2026_01_17_000009_add_location_to_hr_attendances', 1),
(63, '2026_01_19_140000_change_employee_status_inactive_to_non_active', 1),
(64, '2026_01_19_150000_add_requires_location_to_designations', 1),
(65, '2026_01_19_163000_create_hr_loans_tables', 1),
(66, '2026_01_19_185007_add_face_encoding_to_hr_employees_table', 1),
(67, '2026_01_20_162642_create_biometric_devices_table', 1),
(68, '2026_01_20_162647_add_biometric_fields_to_hr_employees_table', 1),
(69, '2026_01_21_024920_create_hr_settings_table', 1),
(70, '2026_01_21_025224_add_punch_gap_minutes_to_hr_employees_table', 1),
(71, '2026_01_21_162017_add_early_in_to_hr_attendances_table', 1),
(72, '2026_01_22_000000_remove_parts_and_bom', 1),
(73, '2026_01_22_152000_add_name_to_modules_table', 1),
(74, '2026_01_22_204519_add_daily_wages_to_salary_structures_table', 1),
(75, '2026_01_22_211414_add_attendance_deduction_policy_to_salary_structures_table', 1),
(76, '2026_01_23_004309_enhance_hr_payrolls_table', 1),
(77, '2026_01_23_004533_create_hr_payroll_details_table', 1),
(78, '2026_01_23_004536_add_pending_deductions_to_hr_employees', 1),
(79, '2026_01_23_012507_add_carried_forward_to_next_to_hr_payrolls', 1),
(80, '2026_01_23_154045_create_employee_salary_structures_table', 1),
(81, '2026_01_23_164530_update_salary_structures_for_standalone_and_naming', 1),
(82, '2026_01_23_180926_add_is_custom_to_employee_salary_structures', 1),
(83, '2026_01_23_182108_add_parent_id_to_salary_structures', 1),
(84, '2026_01_23_230000_drop_basic_salary_from_employees', 1),
(85, '2026_01_24_161500_modify_products_table_for_m2_pricing', 1),
(86, '2026_01_24_165800_add_extended_size_mode_fields', 1),
(87, '2026_01_24_170900_add_purchase_fields', 1),
(88, '2026_01_25_000000_migrate_legacy_stocks_to_warehouse_stocks', 1),
(89, '2026_01_26_013600_rename_column_in_products_table', 1),
(90, '2026_01_26_183704_add_description_to_customer_ledgers_table', 1),
(91, '2026_01_27_000000_add_extra_columns_to_sales_table', 1),
(92, '2026_01_27_000001_add_loose_pieces_to_sales_table', 1),
(93, '2026_01_27_134746_add_stock_details_to_warehouse_stocks_table', 1),
(94, '2026_01_27_135604_remove_stock_columns_from_products_table', 1),
(95, '2026_01_27_135755_remove_price_from_warehouse_stocks_table', 1),
(96, '2026_01_27_141644_simplify_warehouse_stocks_table', 1),
(97, '2026_01_27_143933_add_pricing_columns_to_products_table', 1),
(98, '2026_01_27_144520_add_boxes_quantity_to_warehouse_stocks', 1),
(99, '2026_01_27_144857_rename_pieces_per_box_to_total_pieces_in_warehouse_stocks', 1),
(100, '2026_01_27_190000_update_sales_and_create_sale_items', 1),
(101, '2026_01_27_191000_align_sale_items_columns', 1),
(102, '2026_01_28_014000_make_legacy_sales_columns_nullable', 1),
(103, '2026_01_28_015500_drop_product_column_from_sales', 1),
(104, '2026_01_28_020000_drop_legacy_sales_columns', 1),
(105, '2026_01_28_030000_add_ids_to_sale_items', 1),
(106, '2026_01_28_040000_refactor_sales_table_final', 1),
(107, '2026_01_28_050000_reorder_sales_columns', 1),
(108, '2026_01_28_070000_add_balance_range_to_customers', 1),
(109, '2026_01_28_194600_add_previous_balance_to_customers', 1),
(110, '2026_01_30_000000_refactor_sale_status_and_items', 1),
(111, '2026_01_30_000001_create_financial_accounts_tables', 1),
(112, '2026_01_30_000002_create_voucher_tables', 1),
(113, '2026_01_31_000003_create_settings_table', 1),
(114, '2026_01_31_000004_create_erp_voucher_system', 1),
(115, '2026_01_31_000005_create_system_notifications_table', 1),
(116, '2026_01_31_030620_add_party_to_journal_entries', 1),
(117, '2026_01_31_205213_add_opening_balance_to_vendors_table', 1),
(118, '2026_02_01_011152_fix_sale_status_column_type', 1),
(119, '2026_02_02_010450_add_voucher_id_to_customer_payments_table', 1),
(120, '2026_02_02_013524_create_system_settings_table', 1),
(121, '2026_02_02_015156_add_can_approve_returns_to_users_table', 1),
(122, '2026_02_02_153600_add_pieces_per_m2_to_products', 1),
(123, '2026_02_02_175600_add_due_date_to_sales_table', 1),
(124, '2026_02_02_181246_create_notifications_table', 1),
(125, '2026_02_02_181921_add_credit_days_to_sales_table', 1),
(126, '2026_02_03_135824_change_total_m2_column_type_in_products_table', 1),
(127, '2026_02_04_184220_add_size_mode_to_sale_items_table', 1),
(128, '2026_02_05_040428_add_stock_fields_to_products_table', 1),
(129, '2026_02_05_042018_change_total_m2_decimal_precision', 1),
(130, '2026_02_06_170933_add_snapshot_columns_to_purchase_items_table', 1),
(131, '2026_02_07_133524_add_purchase_id_to_purchase_returns_table', 1),
(132, '2026_02_07_142845_create_sale_returns_table', 1),
(133, '2026_02_07_143037_create_sale_return_items_table', 1),
(134, '2026_02_07_143751_drop_old_sales_returns_table', 1),
(135, '2026_02_18_110000_add_sales_officer_id_to_customers_table', 1),
(136, '2026_02_22_173050_fix_purchase_items_columns', 1),
(137, '2026_02_24_022723_add_default_discounts_to_products_table', 1),
(138, '2026_02_28_040416_add_reminder_fields_to_customers_table', 1),
(139, '2026_03_06_214616_add_reminder_day_to_customers_table', 1),
(140, '2026_05_01_213524_add_is_active_to_products_table', 1),
(141, '2026_05_20_231443_add_alert_quantity_to_products_table', 1),
(142, '2026_05_21_043429_create_expense_categories_table', 1),
(143, '2026_05_21_043430_add_reference_no_to_expense_vouchers_table', 1),
(144, '2026_05_30_192619_add_alert_carton_quantity_to_products_table', 1),
(145, '2026_06_01_171926_add_is_booking_to_sales_table', 1),
(146, '2026_06_02_190558_add_additional_discount_to_purchases_table', 1),
(147, '2026_07_08_083556_add_walkin_name_to_sales_table', 1),
(148, '2026_07_08_235807_add_social_links_to_settings', 1),
(149, '2026_07_09_044903_add_color_to_sale_return_items_table', 1),
(150, '2026_07_09_050550_create_day_closings_table', 1),
(151, '2026_07_09_054826_create_drawer_transactions_table', 1),
(152, '2026_07_09_165000_add_color_to_purchase_items_and_purchase_return_items', 1),
(153, '2026_07_11_141000_add_weight_and_convert_stock_columns_to_decimal', 1),
(154, '2026_07_13_120000_add_sync_columns_to_sales_and_customers', 1),
(155, '2026_07_17_150000_make_customer_id_nullable_in_sale_returns', 1),
(156, '2026_07_19_020202_add_manual_product_columns', 1),
(157, '2026_07_19_024330_add_manual_product_columns_to_sale_return_items', 1),
(158, '2026_08_03_000000_create_stock_adjustments_table', 1),
(159, '2026_08_03_164500_create_ecommerce_tables', 1),
(160, '2026_08_03_173328_add_web_main_image_to_products_table', 1),
(161, '2026_08_03_214719_add_website_fields_to_categories_table', 1),
(162, '2026_08_04_010900_add_coupon_fields_to_ecommerce_orders_table', 1),
(163, '2026_08_05_050000_create_account_histories_table', 1),
(164, '2026_08_05_165217_add_is_stock_deducted_to_ecommerce_orders_table', 1),
(165, '2026_08_05_192914_add_easypaisa_fields_to_ecommerce_orders_table', 1),
(166, '2026_08_06_050000_create_invoice_series_table', 1),
(167, '2026_08_09_033500_add_customer_sync_fields', 1),
(168, '2026_08_09_172703_add_paid_amount_to_ecommerce_orders_table', 1),
(169, '2026_08_09_175932_add_courier_fields_to_ecommerce_orders_table', 1),
(170, '2026_08_18_120000_create_customer_types_table', 1),
(171, '2026_08_22_012800_add_change_account_id_to_sales_table', 1),
(172, '2026_09_04_201000_change_qty_to_decimal_in_purchase_items', 2),
(173, '2026_09_04_211500_fix_purchase_paid_amounts', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'home', NULL, NULL),
(2, 'profile', NULL, NULL),
(3, 'products', NULL, NULL),
(4, 'product.bookings', NULL, NULL),
(5, 'discount.products', NULL, NULL),
(6, 'categories', NULL, NULL),
(7, 'subcategories', NULL, NULL),
(8, 'brands', NULL, NULL),
(9, 'units', NULL, NULL),
(10, 'warehouse', NULL, NULL),
(11, 'warehouse.stock', NULL, NULL),
(12, 'stock.transfer', NULL, NULL),
(13, 'stock.adjust', NULL, NULL),
(14, 'stocks', NULL, NULL),
(15, 'purchases', NULL, NULL),
(16, 'purchase.returns', NULL, NULL),
(17, 'vendors', NULL, NULL),
(18, 'vendor.bilties', NULL, NULL),
(19, 'inward.gatepass', NULL, NULL),
(20, 'sales', NULL, NULL),
(21, 'sales.returns', NULL, NULL),
(22, 'customers', NULL, NULL),
(23, 'customer.ledger', NULL, NULL),
(24, 'bookings', NULL, NULL),
(25, 'checkbook', NULL, NULL),
(26, 'chart.of.accounts', NULL, NULL),
(27, 'expense.voucher', NULL, NULL),
(28, 'receipts.voucher', NULL, NULL),
(29, 'journal.voucher', NULL, NULL),
(30, 'payment.voucher', NULL, NULL),
(31, 'income.voucher', NULL, NULL),
(32, 'item.stock.report', NULL, NULL),
(33, 'purchase.report', NULL, NULL),
(34, 'sale.report', NULL, NULL),
(35, 'reporting', NULL, NULL),
(36, 'inventory.onhand', NULL, NULL),
(37, 'users', NULL, NULL),
(38, 'roles', NULL, NULL),
(39, 'permissions', NULL, NULL),
(40, 'branches', NULL, NULL),
(41, 'zones', NULL, NULL),
(42, 'sales.officers', NULL, NULL),
(43, 'narrations', NULL, NULL),
(44, 'package.types', NULL, NULL),
(45, 'hr.departments', NULL, NULL),
(46, 'hr.employees', NULL, NULL),
(47, 'hr.attendance', NULL, NULL),
(48, 'hr.payroll', NULL, NULL),
(49, 'hr.leaves', NULL, NULL),
(50, 'hr.designations', NULL, NULL),
(51, 'hr.shifts', NULL, NULL),
(52, 'hr.holidays', NULL, NULL),
(53, 'hr.salary.structure', NULL, NULL),
(54, 'hr.loans', NULL, NULL),
(55, 'hr.biometric.devices', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `narrations`
--

CREATE TABLE `narrations` (
  `id` bigint UNSIGNED NOT NULL,
  `expense_head` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `narration` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `narrations`
--

INSERT INTO `narrations` (`id`, `expense_head`, `narration`, `created_at`, `updated_at`) VALUES
(1, 'Receipts Voucher', 'Payment Received', '2026-09-09 21:01:32', '2026-09-09 21:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_types`
--

CREATE TABLE `package_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `pvid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `discount_value` text COLLATE utf8mb4_unicode_ci,
  `kg` text COLLATE utf8mb4_unicode_ci,
  `rate` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'home.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(2, 'home.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(3, 'home.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(4, 'home.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(5, 'profile.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(6, 'profile.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(7, 'profile.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(8, 'profile.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(9, 'products.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(10, 'products.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(11, 'products.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(12, 'products.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(13, 'product.bookings.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(14, 'product.bookings.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(15, 'product.bookings.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(16, 'product.bookings.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(17, 'discount.products.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(18, 'discount.products.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(19, 'discount.products.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(20, 'discount.products.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(21, 'categories.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(22, 'categories.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(23, 'categories.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(24, 'categories.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(25, 'subcategories.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(26, 'subcategories.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(27, 'subcategories.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(28, 'subcategories.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(29, 'brands.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(30, 'brands.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(31, 'brands.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(32, 'brands.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(33, 'units.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(34, 'units.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(35, 'units.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(36, 'units.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(37, 'warehouse.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(38, 'warehouse.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(39, 'warehouse.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(40, 'warehouse.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(41, 'warehouse.stock.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(42, 'warehouse.stock.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(43, 'warehouse.stock.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(44, 'warehouse.stock.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(45, 'stock.transfer.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(46, 'stock.transfer.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(47, 'stock.transfer.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(48, 'stock.transfer.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(49, 'stock.adjust.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(50, 'stock.adjust.create', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(51, 'stock.adjust.edit', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(52, 'stock.adjust.delete', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(53, 'stocks.view', 'web', '2026-09-02 15:53:22', '2026-09-02 15:53:22'),
(54, 'stocks.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(55, 'stocks.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(56, 'stocks.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(57, 'purchases.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(58, 'purchases.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(59, 'purchases.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(60, 'purchases.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(61, 'purchase.returns.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(62, 'purchase.returns.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(63, 'purchase.returns.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(64, 'purchase.returns.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(65, 'vendors.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(66, 'vendors.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(67, 'vendors.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(68, 'vendors.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(69, 'vendor.bilties.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(70, 'vendor.bilties.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(71, 'vendor.bilties.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(72, 'vendor.bilties.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(73, 'inward.gatepass.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(74, 'inward.gatepass.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(75, 'inward.gatepass.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(76, 'inward.gatepass.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(77, 'sales.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(78, 'sales.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(79, 'sales.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(80, 'sales.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(81, 'sales.returns.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(82, 'sales.returns.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(83, 'sales.returns.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(84, 'sales.returns.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(85, 'customers.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(86, 'customers.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(87, 'customers.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(88, 'customers.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(89, 'customer_types.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(90, 'customer_types.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(91, 'customer_types.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(92, 'customer_types.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(93, 'customer.ledger.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(94, 'customer.ledger.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(95, 'customer.ledger.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(96, 'customer.ledger.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(97, 'bookings.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(98, 'bookings.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(99, 'bookings.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(100, 'bookings.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(101, 'checkbook.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(102, 'checkbook.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(103, 'checkbook.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(104, 'checkbook.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(105, 'chart.of.accounts.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(106, 'chart.of.accounts.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(107, 'chart.of.accounts.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(108, 'chart.of.accounts.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(109, 'expense.voucher.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(110, 'expense.voucher.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(111, 'expense.voucher.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(112, 'expense.voucher.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(113, 'receipts.voucher.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(114, 'receipts.voucher.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(115, 'receipts.voucher.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(116, 'receipts.voucher.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(117, 'journal.voucher.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(118, 'journal.voucher.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(119, 'journal.voucher.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(120, 'journal.voucher.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(121, 'payment.voucher.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(122, 'payment.voucher.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(123, 'payment.voucher.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(124, 'payment.voucher.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(125, 'income.voucher.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(126, 'income.voucher.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(127, 'income.voucher.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(128, 'income.voucher.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(129, 'item.stock.report.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(130, 'item.stock.report.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(131, 'item.stock.report.edit', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(132, 'item.stock.report.delete', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(133, 'purchase.report.view', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(134, 'purchase.report.create', 'web', '2026-09-02 15:53:23', '2026-09-02 15:53:23'),
(135, 'purchase.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(136, 'purchase.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(137, 'sale.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(138, 'sale.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(139, 'sale.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(140, 'sale.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(141, 'reporting.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(142, 'reporting.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(143, 'reporting.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(144, 'reporting.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(145, 'recovery.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(146, 'recovery.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(147, 'recovery.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(148, 'recovery.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(149, 'payable.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(150, 'payable.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(151, 'payable.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(152, 'payable.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(153, 'parties.balance.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(154, 'parties.balance.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(155, 'parties.balance.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(156, 'parties.balance.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(157, 'aging.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(158, 'aging.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(159, 'aging.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(160, 'aging.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(161, 'balance.sheet.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(162, 'balance.sheet.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(163, 'balance.sheet.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(164, 'balance.sheet.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(165, 'profit.loss.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(166, 'profit.loss.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(167, 'profit.loss.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(168, 'profit.loss.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(169, 'inventory.onhand.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(170, 'inventory.onhand.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(171, 'inventory.onhand.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(172, 'inventory.onhand.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(173, 'vendor.ledger.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(174, 'vendor.ledger.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(175, 'vendor.ledger.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(176, 'vendor.ledger.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(177, 'users.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(178, 'users.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(179, 'users.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(180, 'users.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(181, 'roles.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(182, 'roles.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(183, 'roles.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(184, 'roles.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(185, 'permissions.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(186, 'permissions.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(187, 'permissions.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(188, 'permissions.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(189, 'branches.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(190, 'branches.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(191, 'branches.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(192, 'branches.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(193, 'zones.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(194, 'zones.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(195, 'zones.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(196, 'zones.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(197, 'sales.officers.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(198, 'sales.officers.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(199, 'sales.officers.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(200, 'sales.officers.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(201, 'narrations.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(202, 'narrations.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(203, 'narrations.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(204, 'narrations.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(205, 'executive.report.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(206, 'executive.report.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(207, 'executive.report.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(208, 'executive.report.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(209, 'package.types.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(210, 'package.types.create', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(211, 'package.types.edit', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(212, 'package.types.delete', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(213, 'hr.departments.view', 'web', '2026-09-02 15:53:24', '2026-09-02 15:53:24'),
(214, 'hr.departments.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(215, 'hr.departments.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(216, 'hr.departments.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(217, 'hr.employees.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(218, 'hr.employees.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(219, 'hr.employees.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(220, 'hr.employees.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(221, 'hr.attendance.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(222, 'hr.attendance.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(223, 'hr.attendance.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(224, 'hr.attendance.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(225, 'hr.payroll.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(226, 'hr.payroll.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(227, 'hr.payroll.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(228, 'hr.payroll.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(229, 'hr.leaves.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(230, 'hr.leaves.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(231, 'hr.leaves.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(232, 'hr.leaves.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(233, 'hr.designations.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(234, 'hr.designations.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(235, 'hr.designations.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(236, 'hr.designations.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(237, 'hr.shifts.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(238, 'hr.shifts.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(239, 'hr.shifts.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(240, 'hr.shifts.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(241, 'hr.holidays.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(242, 'hr.holidays.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(243, 'hr.holidays.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(244, 'hr.holidays.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(245, 'hr.salary.structure.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(246, 'hr.salary.structure.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(247, 'hr.salary.structure.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(248, 'hr.salary.structure.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(249, 'hr.loans.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(250, 'hr.loans.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(251, 'hr.loans.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(252, 'hr.loans.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(253, 'hr.biometric.devices.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(254, 'hr.biometric.devices.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(255, 'hr.biometric.devices.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(256, 'hr.biometric.devices.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(257, 'web_products.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(258, 'web_products.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(259, 'web_products.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(260, 'web_products.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(261, 'coupons.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(262, 'coupons.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(263, 'coupons.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(264, 'coupons.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(265, 'web_orders.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(266, 'web_orders.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(267, 'web_orders.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(268, 'web_orders.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(269, 'settings.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(270, 'settings.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(271, 'settings.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(272, 'settings.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(273, 'web_users.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(274, 'web_users.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(275, 'web_users.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(276, 'web_users.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(277, 'website-settings.view', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(278, 'website-settings.create', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(279, 'website-settings.edit', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(280, 'website-settings.delete', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(281, 'website-settings.update', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(282, 'website-settings.upload_manage', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(283, 'purchase_pos.create', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(284, 'web_products.read', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(285, 'web_products.add', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(286, 'coupons.read', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(287, 'coupons.add', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(288, 'web_orders.read', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(289, 'web_orders.add', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(290, 'settings.read', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(291, 'settings.add', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(292, 'settings.update', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(293, 'web_users.read', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(294, 'web_users.add', 'web', '2026-09-02 18:14:08', '2026-09-02 18:14:08'),
(295, 'vouchers.view', 'web', '2026-09-05 15:17:51', '2026-09-05 15:17:51'),
(296, 'vouchers.create', 'web', '2026-09-05 15:17:51', '2026-09-05 15:17:51'),
(297, 'vouchers.edit', 'web', '2026-09-05 15:17:51', '2026-09-05 15:17:51'),
(298, 'vouchers.delete', 'web', '2026-09-05 15:17:51', '2026-09-05 15:17:51'),
(299, 'all.vouchers.view', 'web', '2026-09-05 15:17:52', '2026-09-05 15:17:52'),
(300, 'all.vouchers.create', 'web', '2026-09-05 15:17:52', '2026-09-05 15:17:52'),
(301, 'all.vouchers.edit', 'web', '2026-09-05 15:17:52', '2026-09-05 15:17:52'),
(302, 'all.vouchers.delete', 'web', '2026-09-05 15:17:52', '2026-09-05 15:17:52');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `creater_id` text COLLATE utf8mb4_unicode_ci,
  `category_id` text COLLATE utf8mb4_unicode_ci,
  `sub_category_id` text COLLATE utf8mb4_unicode_ci,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `is_part` tinyint(1) NOT NULL DEFAULT '0',
  `is_assembled` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_web_visible` tinyint(1) NOT NULL DEFAULT '0',
  `show_on_homepage` tinyint(1) NOT NULL DEFAULT '0',
  `promo_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_code` text COLLATE utf8mb4_unicode_ci,
  `unit_id` text COLLATE utf8mb4_unicode_ci,
  `item_name` text COLLATE utf8mb4_unicode_ci,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'by_size',
  `height` decimal(8,2) DEFAULT NULL COMMENT 'Height in cm',
  `width` decimal(8,2) DEFAULT NULL COMMENT 'Width in cm',
  `weight_per_piece` decimal(12,4) DEFAULT '0.0000',
  `pieces_per_box` int NOT NULL DEFAULT '0',
  `pieces_per_m2` decimal(10,2) NOT NULL DEFAULT '0.00',
  `alert_quantity` int DEFAULT NULL,
  `alert_carton_quantity` int DEFAULT NULL,
  `total_m2` decimal(10,2) NOT NULL,
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_price_per_box` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_piece` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_box` decimal(15,2) DEFAULT '0.00',
  `sale_price_per_piece` decimal(15,2) DEFAULT '0.00',
  `web_sale_price` decimal(15,2) DEFAULT NULL,
  `auto_hide_out_of_stock` tinyint(1) NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `wholesale_price` decimal(12,2) DEFAULT '0.00',
  `purchase_price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `color` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `barcode_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hs_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `boxes_quantity` int DEFAULT '0',
  `loose_pieces` int DEFAULT '0',
  `piece_quantity` int DEFAULT '0',
  `total_stock_qty` decimal(10,2) DEFAULT '0.00',
  `purchase_discount_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `sale_discount_percent` decimal(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `creater_id`, `category_id`, `sub_category_id`, `brand_id`, `is_part`, `is_assembled`, `is_active`, `is_web_visible`, `show_on_homepage`, `promo_tag`, `item_code`, `unit_id`, `item_name`, `size_mode`, `height`, `width`, `weight_per_piece`, `pieces_per_box`, `pieces_per_m2`, `alert_quantity`, `alert_carton_quantity`, `total_m2`, `price_per_m2`, `sale_price_per_box`, `purchase_price_per_piece`, `purchase_price_per_box`, `sale_price_per_piece`, `web_sale_price`, `auto_hide_out_of_stock`, `meta_title`, `meta_description`, `wholesale_price`, `purchase_price_per_m2`, `color`, `created_at`, `updated_at`, `deleted_at`, `barcode_path`, `image`, `web_main_image`, `model`, `hs_code`, `boxes_quantity`, `loose_pieces`, `piece_quantity`, `total_stock_qty`, `purchase_discount_percent`, `sale_discount_percent`) VALUES
(1, '1', '12', NULL, 1, 0, 0, 1, 0, 0, NULL, 'ITEM-0001', NULL, 'STEELEX Elbow UPVC', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 320, 0.00, 0.00, 567.00, 567.00, 567.00, 567.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"STEELEX Elbow UPVC\",\"size\":\"4\\\"\",\"color\":\"-\",\"stock\":\"160\",\"sale_price\":567,\"wholesale_price\":320,\"weight_per_piece\":\"1000\",\"purch_price\":567,\"alert\":\"320\",\"barcode\":\"655851\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"},{\"name\":\"STEELEX Elbow UPVC\",\"size\":\"3\\\"\",\"color\":\"-\",\"stock\":\"96\",\"sale_price\":357,\"wholesale_price\":0,\"weight_per_piece\":\"0\",\"purch_price\":357,\"alert\":\"240\",\"barcode\":\"611632\",\"conv_factor\":1,\"is_base_variant\":\"0\",\"unit\":\"Pcs\"},{\"name\":\"STEELEX Elbow UPVC\",\"size\":\"2\\\"\",\"color\":\"-\",\"stock\":\"140\",\"sale_price\":176,\"wholesale_price\":0,\"weight_per_piece\":\"1000\",\"purch_price\":176,\"alert\":\"0\",\"barcode\":\"717021\",\"conv_factor\":1,\"is_base_variant\":\"0\",\"unit\":\"Pcs\"},{\"name\":\"STEELEX Elbow UPVC\",\"size\":\"1\\\"\",\"color\":\"-\",\"stock\":\"180\",\"sale_price\":69,\"wholesale_price\":0,\"weight_per_piece\":\"1000\",\"purch_price\":69,\"alert\":\"0\",\"barcode\":\"365107\",\"conv_factor\":1,\"is_base_variant\":\"0\",\"unit\":\"Pcs\"}]', '2026-09-05 11:18:13', '2026-09-05 11:36:28', NULL, '892543126070', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(2, '1', '12', NULL, 1, 0, 0, 1, 0, 0, NULL, 'ITEM-0002', NULL, 'STEELEX UPVC Pipe 3\" SDR-64', 'by_pieces', 0.00, 0.00, 0.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 3960.00, 3960.00, 3960.00, 3960.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"STEELEX UPVC Pipe 3\\\" SDR-64\",\"size\":\"3\\\" 64\",\"color\":\"-\",\"stock\":\"100\",\"sale_price\":3960,\"wholesale_price\":0,\"weight_per_piece\":\"1000\",\"purch_price\":3960,\"alert\":\"0\",\"barcode\":\"515346\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-09-05 12:49:15', '2026-09-05 12:52:36', NULL, '460014388629', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00),
(3, '1', '12', NULL, 1, 0, 0, 1, 0, 0, NULL, 'ITEM-0003', NULL, 'STEELEX UPVC Pipe 3\" SDR-41', 'by_pieces', 0.00, 0.00, 1000.0000, 1, 0.00, NULL, 0, 0.00, 0.00, 4956.00, 4956.00, 4956.00, 4956.00, NULL, 0, NULL, NULL, 0.00, 0.00, '[{\"name\":\"STEELEX UPVC Pipe 3\\\" SDR-41\",\"size\":\"3\\\" 41\",\"color\":\"-\",\"stock\":0,\"sale_price\":4956,\"wholesale_price\":0,\"weight_per_piece\":\"1000\",\"purch_price\":4956,\"alert\":\"0\",\"barcode\":\"560211\",\"conv_factor\":1,\"is_base_variant\":\"1\",\"unit\":\"Pcs\"}]', '2026-09-05 12:50:34', '2026-09-05 12:50:34', NULL, '427258642650', NULL, NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_bookings`
--

CREATE TABLE `product_bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `customer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` text COLLATE utf8mb4_unicode_ci,
  `product` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_price` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_discount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `per_total` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount_Words` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_bill_amount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_extradiscount` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_net` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `card` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `change` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_items` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_date` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_date` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_discounts`
--

CREATE TABLE `product_discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `actual_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `total_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL DEFAULT '2026-09-02',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_web_images`
--

CREATE TABLE `product_web_images` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `additional_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `extra_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status_purchase` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pieces_per_box` decimal(12,2) NOT NULL DEFAULT '1.00',
  `pieces_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `boxes_qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `loose_qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `length` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qty` decimal(12,4) DEFAULT '0.0000',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `return_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_date` date NOT NULL,
  `return_reason` text COLLATE utf8mb4_unicode_ci,
  `transport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `bill_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `extra_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` decimal(12,4) DEFAULT '0.0000',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts_vouchers`
--

CREATE TABLE `receipts_vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `rvid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `narration_id` text COLLATE utf8mb4_unicode_ci,
  `reference_no` text COLLATE utf8mb4_unicode_ci,
  `row_account_head` text COLLATE utf8mb4_unicode_ci,
  `row_account_id` text COLLATE utf8mb4_unicode_ci,
  `discount_value` text COLLATE utf8mb4_unicode_ci,
  `kg` text COLLATE utf8mb4_unicode_ci,
  `rate` text COLLATE utf8mb4_unicode_ci,
  `amount` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts_vouchers`
--

INSERT INTO `receipts_vouchers` (`id`, `rvid`, `receipt_date`, `entry_date`, `type`, `party_id`, `tel`, `remarks`, `narration_id`, `reference_no`, `row_account_head`, `row_account_id`, `discount_value`, `kg`, `rate`, `amount`, `total_amount`, `processed`, `created_at`, `updated_at`) VALUES
(1, 'RVID-0001', '2026-09-10', '2026-09-10', 'customer', '1', NULL, NULL, '[\"1\"]', 'null', 'null', '[\"4\"]', 'null', NULL, 'null', '[\"200000\"]', 200000.00, 1, '2026-09-09 21:01:32', '2026-09-09 21:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(2, 'branch', 'web', '2026-09-05 10:14:50', '2026-09-05 10:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(142, 1),
(143, 1),
(144, 1),
(145, 1),
(146, 1),
(147, 1),
(148, 1),
(149, 1),
(150, 1),
(151, 1),
(152, 1),
(153, 1),
(154, 1),
(155, 1),
(156, 1),
(157, 1),
(158, 1),
(159, 1),
(160, 1),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(166, 1),
(167, 1),
(168, 1),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(200, 1),
(201, 1),
(202, 1),
(203, 1),
(204, 1),
(205, 1),
(206, 1),
(207, 1),
(208, 1),
(209, 1),
(210, 1),
(211, 1),
(212, 1),
(213, 1),
(214, 1),
(215, 1),
(216, 1),
(217, 1),
(218, 1),
(219, 1),
(220, 1),
(221, 1),
(222, 1),
(223, 1),
(224, 1),
(225, 1),
(226, 1),
(227, 1),
(228, 1),
(229, 1),
(230, 1),
(231, 1),
(232, 1),
(233, 1),
(234, 1),
(235, 1),
(236, 1),
(237, 1),
(238, 1),
(239, 1),
(240, 1),
(241, 1),
(242, 1),
(243, 1),
(244, 1),
(245, 1),
(246, 1),
(247, 1),
(248, 1),
(249, 1),
(250, 1),
(251, 1),
(252, 1),
(253, 1),
(254, 1),
(255, 1),
(256, 1),
(257, 1),
(258, 1),
(259, 1),
(260, 1),
(261, 1),
(262, 1),
(263, 1),
(264, 1),
(265, 1),
(266, 1),
(267, 1),
(268, 1),
(269, 1),
(270, 1),
(271, 1),
(272, 1),
(273, 1),
(274, 1),
(275, 1),
(276, 1),
(277, 1),
(278, 1),
(279, 1),
(280, 1),
(281, 1),
(282, 1),
(283, 1),
(284, 1),
(285, 1),
(286, 1),
(287, 1),
(288, 1),
(289, 1),
(290, 1),
(291, 1),
(292, 1),
(293, 1),
(294, 1),
(295, 1),
(296, 1),
(297, 1),
(298, 1),
(299, 1),
(300, 1),
(301, 1),
(302, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(109, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(121, 2),
(122, 2),
(123, 2),
(124, 2),
(129, 2),
(130, 2),
(131, 2),
(132, 2),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(138, 2),
(139, 2),
(140, 2),
(141, 2),
(142, 2),
(143, 2),
(144, 2),
(145, 2),
(146, 2),
(147, 2),
(148, 2),
(149, 2),
(150, 2),
(151, 2),
(152, 2),
(153, 2),
(154, 2),
(155, 2),
(156, 2),
(157, 2),
(158, 2),
(159, 2),
(160, 2),
(161, 2),
(162, 2),
(163, 2),
(164, 2),
(165, 2),
(166, 2),
(167, 2),
(168, 2),
(169, 2),
(170, 2),
(171, 2),
(172, 2),
(173, 2),
(174, 2),
(175, 2),
(176, 2),
(177, 2),
(178, 2),
(179, 2),
(180, 2),
(181, 2),
(182, 2),
(183, 2),
(184, 2),
(185, 2),
(186, 2),
(187, 2),
(188, 2),
(189, 2),
(190, 2),
(191, 2),
(192, 2),
(193, 2),
(194, 2),
(195, 2),
(196, 2),
(197, 2),
(198, 2),
(199, 2),
(200, 2),
(201, 2),
(202, 2),
(203, 2),
(204, 2),
(205, 2),
(206, 2),
(207, 2),
(208, 2),
(209, 2),
(210, 2),
(211, 2),
(212, 2),
(269, 2),
(270, 2),
(271, 2),
(272, 2),
(283, 2),
(290, 2),
(291, 2),
(292, 2),
(299, 2),
(300, 2),
(301, 2),
(302, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_synced` tinyint NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `walkin_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_status` enum('booked','posted','cancelled','returned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'booked',
  `is_booking` tinyint(1) NOT NULL DEFAULT '0',
  `credit_days` int DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_items` decimal(12,2) DEFAULT NULL,
  `total_bill_amount` decimal(12,2) DEFAULT NULL,
  `total_extradiscount` decimal(12,2) DEFAULT NULL,
  `total_net` decimal(12,2) DEFAULT NULL,
  `cash` decimal(12,2) DEFAULT NULL,
  `card` decimal(12,2) DEFAULT NULL,
  `change` decimal(12,2) DEFAULT NULL,
  `change_account_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount_Words` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `uuid`, `is_synced`, `invoice_no`, `customer_id`, `walkin_name`, `reference`, `sale_status`, `is_booking`, `credit_days`, `due_date`, `total_items`, `total_bill_amount`, `total_extradiscount`, `total_net`, `cash`, `card`, `change`, `change_account_id`, `total_amount_Words`, `created_at`, `updated_at`) VALUES
(1, 'c8be2049-e95c-48ab-86b3-60dd85c55635', 0, 'INV-0001', 1, NULL, NULL, 'returned', 0, 40, '2026-10-15', 20.00, 6237.00, 0.00, 6237.00, 0.00, NULL, -6237.00, NULL, NULL, '2026-09-04 19:00:00', '2026-09-09 10:26:48'),
(2, 'ed6acbd5-95d4-45c4-9cc8-3e7f61096c4e', 0, 'INV-0002', 1, NULL, '12', 'posted', 0, NULL, NULL, 10.00, 5670.00, 0.00, 5670.00, 0.00, NULL, -5670.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-09 21:43:23'),
(8, 'a90ef41d-a7d6-49dc-b8a4-f3a98c80c787', 0, 'INV-0003', 1, NULL, NULL, 'posted', 0, NULL, NULL, 1.00, 176.00, 0.00, 176.00, 0.00, NULL, -176.00, NULL, NULL, '2026-09-04 19:00:00', '2026-09-05 12:54:14'),
(9, '7f6d51e4-d115-437b-8ddc-476a91f2d8b1', 0, 'INV-0004', 1, NULL, NULL, 'posted', 0, NULL, NULL, 10.00, 3570.00, 0.00, 3570.00, 0.00, NULL, -3570.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-09 10:24:08'),
(10, '6c2f353f-91d5-4db5-8920-7543b3416aea', 0, 'INV-0005', 2, NULL, 'Gate Pass No. 45', 'posted', 0, NULL, NULL, 10.00, 1760.00, 0.00, 1760.00, 0.00, NULL, -1760.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-09 21:42:55'),
(11, '748f6d26-09c8-4647-9a68-fbf46259e16a', 0, 'INV-0006', 2, NULL, NULL, 'posted', 0, NULL, NULL, 100.00, 6900.00, 0.00, 6900.00, 0.00, NULL, -6900.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-09 23:04:50'),
(12, '0ff84d46-6036-41d3-8b30-25d2e2066e50', 0, 'INV-0007', 2, NULL, NULL, 'booked', 1, NULL, NULL, 50.00, 8800.00, 0.00, 8800.00, 0.00, NULL, -8800.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-09 23:05:20'),
(13, 'b41915b4-858a-4ad1-aa5d-7ca73341d0a5', 0, 'INV-0008', 1, NULL, NULL, 'posted', 0, NULL, NULL, 2.00, 714.00, 0.00, 714.00, 0.00, NULL, -714.00, NULL, NULL, '2025-12-31 19:00:00', '2026-09-10 09:33:35'),
(14, '2cddec23-27da-4362-bd64-fced64987a82', 0, 'INV-0009', NULL, 'Walk-in Customer', NULL, 'returned', 0, NULL, NULL, 1.00, 357.00, 0.00, 357.00, 357.00, NULL, 0.00, 7, NULL, '2025-12-31 19:00:00', '2026-09-10 09:40:55'),
(19, 'b1e6ac5b-04f8-4556-8e48-a2720d52ed76', 0, 'INV-0010', NULL, 'Walk-in Customer', NULL, 'posted', 0, NULL, NULL, 2.00, 1134.00, 0.00, 1134.00, 1134.00, NULL, 0.00, 7, NULL, '2025-12-31 19:00:00', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `sales_officers`
--

CREATE TABLE `sales_officers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_urdu` text COLLATE utf8mb4_unicode_ci,
  `mobile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `sale_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `vendor_id` bigint UNSIGNED DEFAULT NULL,
  `size_mode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_level` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price` decimal(12,2) DEFAULT '0.00',
  `purchase_price` decimal(12,2) DEFAULT NULL,
  `price_per_piece` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qty` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `total_pieces` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `loose_pieces` int NOT NULL DEFAULT '0',
  `retail_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `is_manual`, `sale_id`, `warehouse_id`, `product_id`, `vendor_id`, `size_mode`, `product_name`, `brand_id`, `category_id`, `sub_category_id`, `unit_id`, `color`, `stock`, `price_level`, `price`, `purchase_price`, `price_per_piece`, `price_per_m2`, `qty`, `total_pieces`, `loose_pieces`, `retail_price`, `discount_percent`, `discount_amount`, `total`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow 4\" UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjRcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoxNjAsInNhbGVfcHJpY2UiOjU2Nywid2hvbGVzYWxlX3ByaWNlIjowLCJ3ZWlnaHRfcGVyX3BpZWNlIjoiMTAwMCIsInB1cmNoX3ByaWNlIjo1NjcsImFsZXJ0IjoiMzIwIiwiYmFyY29kZSI6IjY1NTg1MSIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIxIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjE2MH0=', 0.00, 0.00, 567.00, NULL, 0.00, 0.00, 20.0000, 20.0000, 0, 0.00, 45.00, 5103.00, 6237.00, '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(8, 0, 8, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjJcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTQwIiwic2FsZV9wcmljZSI6MTc2LCJ3aG9sZXNhbGVfcHJpY2UiOjAsIndlaWdodF9wZXJfcGllY2UiOiIxMDAwIiwicHVyY2hfcHJpY2UiOjE3NiwiYWxlcnQiOiIwIiwiYmFyY29kZSI6IjcxNzAyMSIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIwIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjE0MH0=', 0.00, 0.00, 176.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 176.00, '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(9, 0, 9, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjNcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiOTYiLCJzYWxlX3ByaWNlIjozNTcsIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjAiLCJwdXJjaF9wcmljZSI6MzU3LCJhbGVydCI6IjI0MCIsImJhcmNvZGUiOiI2MTE2MzIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjo5Nn0=', 0.00, 0.00, 357.00, NULL, 0.00, 0.00, 10.0000, 10.0000, 0, 0.00, 0.00, 0.00, 3570.00, '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(14, 0, 10, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTYwIiwic2FsZV9wcmljZSI6NTY3LCJ3aG9sZXNhbGVfcHJpY2UiOjMyMCwid2VpZ2h0X3Blcl9waWVjZSI6IjEwMDAiLCJwdXJjaF9wcmljZSI6NTY3LCJhbGVydCI6IjMyMCIsImJhcmNvZGUiOiI2NTU4NTEiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjoxNTB9', 0.00, 0.00, 320.00, NULL, 0.00, 0.00, 10.0000, 10.0000, 0, 0.00, 45.00, 1440.00, 1760.00, '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(15, 0, 2, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjRcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTYwIiwic2FsZV9wcmljZSI6NTY3LCJ3aG9sZXNhbGVfcHJpY2UiOjMyMCwid2VpZ2h0X3Blcl9waWVjZSI6IjEwMDAiLCJwdXJjaF9wcmljZSI6NTY3LCJhbGVydCI6IjMyMCIsImJhcmNvZGUiOiI2NTU4NTEiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjoxNDB9', 0.00, 0.00, 567.00, NULL, 0.00, 0.00, 10.0000, 10.0000, 0, 0.00, 0.00, 0.00, 5670.00, '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(16, 0, 11, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjFcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTgwIiwic2FsZV9wcmljZSI6NjksIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjEwMDAiLCJwdXJjaF9wcmljZSI6NjksImFsZXJ0IjoiMCIsImJhcmNvZGUiOiIzNjUxMDciLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjoxODB9', 0.00, 0.00, 69.00, NULL, 0.00, 0.00, 100.0000, 100.0000, 0, 0.00, 0.00, 0.00, 6900.00, '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(17, 0, 12, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjJcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTQwIiwic2FsZV9wcmljZSI6MTc2LCJ3aG9sZXNhbGVfcHJpY2UiOjAsIndlaWdodF9wZXJfcGllY2UiOiIxMDAwIiwicHVyY2hfcHJpY2UiOjE3NiwiYWxlcnQiOiIwIiwiYmFyY29kZSI6IjcxNzAyMSIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIwIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjEzOX0=', 0.00, 0.00, 176.00, NULL, 0.00, 0.00, 50.0000, 50.0000, 0, 0.00, 0.00, 0.00, 8800.00, '2026-09-09 23:05:20', '2026-09-09 23:05:20'),
(20, 0, 13, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjNcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiOTYiLCJzYWxlX3ByaWNlIjozNTcsIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjAiLCJwdXJjaF9wcmljZSI6MzU3LCJhbGVydCI6IjI0MCIsImJhcmNvZGUiOiI2MTE2MzIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjo5MH0=', 0.00, 0.00, 357.00, NULL, 0.00, 0.00, 2.0000, 2.0000, 0, 0.00, 0.00, 0.00, 714.00, '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(21, 0, 14, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjNcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiOTYiLCJzYWxlX3ByaWNlIjozNTcsIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjAiLCJwdXJjaF9wcmljZSI6MzU3LCJhbGVydCI6IjI0MCIsImJhcmNvZGUiOiI2MTE2MzIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjo4OH0=', 0.00, 0.00, 357.00, NULL, 0.00, 0.00, 1.0000, 1.0000, 0, 0.00, 0.00, 0.00, 357.00, '2026-09-10 09:40:05', '2026-09-10 09:40:05'),
(26, 0, 19, 1, 1, NULL, 'by_pieces', 'STEELEX Elbow UPVC', 1, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjRcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiMTYwIiwic2FsZV9wcmljZSI6NTY3LCJ3aG9sZXNhbGVfcHJpY2UiOjMyMCwid2VpZ2h0X3Blcl9waWVjZSI6IjEwMDAiLCJwdXJjaF9wcmljZSI6NTY3LCJhbGVydCI6IjMyMCIsImJhcmNvZGUiOiI2NTU4NTEiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMSIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjoxNTB9', 0.00, 0.00, 567.00, NULL, 0.00, 0.00, 2.0000, 2.0000, 0, 0.00, 0.00, 0.00, 1134.00, '2026-09-10 09:45:24', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `return_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `bill_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `item_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `extra_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'posted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_returns`
--

INSERT INTO `sale_returns` (`id`, `sale_id`, `return_invoice`, `customer_id`, `warehouse_id`, `return_date`, `bill_amount`, `item_discount`, `extra_discount`, `net_amount`, `paid`, `balance`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'SR-0001', 1, 1, '2026-09-09', 11340.00, 0.00, 0.00, 11340.00, 0.00, 11340.00, NULL, 'posted', '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(2, 9, 'SR-0002', 1, 1, '2026-09-09', 1428.00, 0.00, 0.00, 1428.00, 0.00, 1428.00, NULL, 'posted', '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(3, 14, 'SR-0003', 3, 1, '2026-09-10', 357.00, 0.00, 0.00, 357.00, 357.00, 0.00, NULL, 'posted', '2026-09-10 09:40:55', '2026-09-10 09:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_items`
--

CREATE TABLE `sale_return_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sale_return_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `is_manual` tinyint(1) NOT NULL DEFAULT '0',
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendor_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `color` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL COMMENT 'Total pieces returned',
  `boxes` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Box quantity (can be decimal like 1.2)',
  `loose_pieces` int NOT NULL DEFAULT '0' COMMENT 'Loose pieces',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Price per piece',
  `item_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pc',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_return_items`
--

INSERT INTO `sale_return_items` (`id`, `sale_return_id`, `product_id`, `is_manual`, `product_name`, `vendor_id`, `purchase_price`, `color`, `warehouse_id`, `qty`, `boxes`, `loose_pieces`, `price`, `item_discount`, `unit`, `line_total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjRcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoxNjAsInNhbGVfcHJpY2UiOjU2Nywid2hvbGVzYWxlX3ByaWNlIjowLCJ3ZWlnaHRfcGVyX3BpZWNlIjoiMTAwMCIsInB1cmNoX3ByaWNlIjo1NjcsImFsZXJ0IjoiMzIwIiwiYmFyY29kZSI6IjY1NTg1MSIsImNvbnZfZmFjdG9yIjoxLCJpc19iYXNlX3ZhcmlhbnQiOiIxIiwidW5pdCI6IlBjcyIsImN1cnJlbnRfc3RvY2siOjE2MH0=', 1, 20.00, 20.00, 0, 567.00, 0.00, 'pc', 11340.00, '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(2, 2, 1, 0, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjNcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiOTYiLCJzYWxlX3ByaWNlIjozNTcsIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjAiLCJwdXJjaF9wcmljZSI6MzU3LCJhbGVydCI6IjI0MCIsImJhcmNvZGUiOiI2MTE2MzIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjo5Nn0=', 1, 4.00, 4.00, 0, 357.00, 0.00, 'pc', 1428.00, '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(3, 3, 1, 0, NULL, NULL, NULL, 'eyJuYW1lIjoiU1RFRUxFWCBFbGJvdyBVUFZDIiwic2l6ZSI6IjNcIiIsImNvbG9yIjoiLSIsInN0b2NrIjoiOTYiLCJzYWxlX3ByaWNlIjozNTcsIndob2xlc2FsZV9wcmljZSI6MCwid2VpZ2h0X3Blcl9waWVjZSI6IjAiLCJwdXJjaF9wcmljZSI6MzU3LCJhbGVydCI6IjI0MCIsImJhcmNvZGUiOiI2MTE2MzIiLCJjb252X2ZhY3RvciI6MSwiaXNfYmFzZV92YXJpYW50IjoiMCIsInVuaXQiOiJQY3MiLCJjdXJyZW50X3N0b2NrIjo4OH0=', 1, 1.00, 1.00, 0, 357.00, 0.00, 'pc', 357.00, '2026-09-10 09:40:55', '2026-09-10 09:40:55');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Yasir Pipe', 'string', 'company', 'Company Name', 'Official company name displayed on invoices and reports', '2026-09-02 15:52:44', '2026-09-05 10:18:18'),
(2, 'company_address', NULL, 'text', 'company', 'Company Address', 'Full company address', '2026-09-02 15:52:44', '2026-09-05 10:18:09'),
(3, 'company_phone', NULL, 'string', 'company', 'Phone Number', 'Primary contact number', '2026-09-02 15:52:44', '2026-09-05 10:18:09'),
(4, 'currency_symbol', 'PKR', 'string', 'company', 'Currency Symbol', 'Currency used in the system', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(5, 'debt_warning_days', '7', 'integer', 'sales', 'Debt Warning Days', 'Number of days after which a warning notification is sent for unpaid invoices', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(6, 'debt_critical_days', '10', 'integer', 'sales', 'Debt Critical Days', 'Number of days after which a critical notification is sent for unpaid invoices', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(7, 'invoice_terms', 'Payment due within 30 days. Late payments may incur additional charges.', 'text', 'sales', 'Invoice Terms & Conditions', 'Default terms and conditions displayed on invoices', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(8, 'low_stock_threshold', '10', 'integer', 'inventory', 'Low Stock Threshold', 'Minimum quantity before low stock warning', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(9, 'expiry_alert_days', '30', 'integer', 'inventory', 'Expiry Alert Days', 'Number of days before expiry to show warning', '2026-09-02 15:52:44', '2026-09-02 15:52:44'),
(10, 'facebook_link', NULL, 'string', 'company', 'Facebook Link', 'Facebook page URL for receipts', '2026-09-02 15:53:00', '2026-09-02 15:53:00'),
(11, 'tiktok_link', NULL, 'string', 'company', 'TikTok Link', 'TikTok profile URL for receipts', '2026-09-02 15:53:00', '2026-09-02 15:53:00'),
(12, 'instagram_link', NULL, 'string', 'company', 'Instagram Link', 'Instagram profile URL for receipts', '2026-09-02 15:53:00', '2026-09-02 15:53:00'),
(13, 'website_link', NULL, 'string', 'company', 'Website Link', 'Website URL for receipts', '2026-09-02 15:53:00', '2026-09-02 15:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `reserved_qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variant_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('add','subtract','set') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'add',
  `qty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `old_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `new_stock` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out','assembly_in','assembly_out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `ref_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `ref_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_auto_pluck` tinyint NOT NULL DEFAULT '0',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `type`, `qty`, `ref_type`, `ref_id`, `ref_uuid`, `is_auto_pluck`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 'adjustment', 256.000, 'INIT', NULL, NULL, 0, 'Initial Stock', '2026-09-05 11:18:13', '2026-09-05 11:18:13'),
(2, 1, 'out', -20.000, 'sale', 1, NULL, 0, 'Sale Posted #INV-0001', '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(4, 1, 'out', -1.000, 'sale', 8, NULL, 0, 'Sale Posted #INV-0003', '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(5, 1, 'out', -10.000, 'sale', 9, NULL, 0, 'Sale Posted #INV-0004', '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(6, 1, 'in', 20.000, 'SALE_RETURN', 1, NULL, 0, 'Return #SR-0001', '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(7, 1, 'in', 4.000, 'SALE_RETURN', 2, NULL, 0, 'Return #SR-0002', '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(9, 1, 'in', 1.000, 'sale_in', 10, NULL, 0, 'Sale In #INV-0005', '2026-09-09 20:46:02', '2026-09-09 20:46:02'),
(11, 1, 'in', 1.000, 'sale_in', 10, NULL, 0, 'Sale In #INV-0005', '2026-09-09 20:51:17', '2026-09-09 20:51:17'),
(13, 1, 'in', 10.000, 'sale_in', 10, NULL, 0, 'Sale In #INV-0005', '2026-09-09 20:52:15', '2026-09-09 20:52:15'),
(15, 1, 'in', 10.000, 'sale_in', 10, NULL, 0, 'Sale In #INV-0005', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(16, 1, 'out', -10.000, 'sale', 10, NULL, 0, 'Sale Posted #INV-0005', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(17, 1, 'in', 10.000, 'sale_in', 2, NULL, 0, 'Sale In #INV-0002', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(18, 1, 'out', -10.000, 'sale', 2, NULL, 0, 'Sale Posted #INV-0002', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(19, 1, 'out', -100.000, 'sale', 11, NULL, 0, 'Sale Posted #INV-0006', '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(21, 1, 'in', 2.000, 'sale_in', 13, NULL, 0, 'Sale In #INV-0008', '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(22, 1, 'out', -2.000, 'sale', 13, NULL, 0, 'Sale Posted #INV-0008', '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(23, 1, 'out', -1.000, 'sale', 14, NULL, 0, 'Sale Posted #INV-0009', '2026-09-10 09:40:05', '2026-09-10 09:40:05'),
(24, 1, 'in', 1.000, 'SALE_RETURN', 3, NULL, 0, 'Return #SR-0003', '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(25, 1, 'out', -2.000, 'sale', 19, NULL, 0, 'Sale Posted #INV-0010', '2026-09-10 09:45:24', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `from_warehouse_id` bigint UNSIGNED NOT NULL,
  `to_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `to_shop` tinyint(1) NOT NULL DEFAULT '0',
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Fan', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(2, 'ceiling  Fan', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(3, 'Pedestal  Fan', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(4, 'Fridge', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(5, 'Air-Condition(AC)', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(6, 'Washing Machine', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(7, 'Microwave Oven', 1, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(8, 'Drill Machine', 2, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(9, 'Grinder', 2, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(10, 'Lathe Machine', 2, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(11, 'Milling Machine', 2, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(12, 'Shaper Machine', 2, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(13, 'Hammer', 3, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(14, 'Screwdriver', 3, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(15, 'Wrench', 3, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(16, 'Pliers', 3, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(17, 'Tape Measure', 3, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(18, 'Pipe', 4, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(19, 'Faucet', 4, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(20, 'Valve', 4, '2026-09-02 15:53:20', '2026-09-02 15:53:20'),
(21, 'Toilet', 4, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(22, 'Sink', 4, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(23, 'Nails', 5, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(24, 'Screws', 5, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(25, 'Bolts', 5, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(26, 'Hinges', 5, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(27, 'Brackets', 5, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(28, 'Light', 6, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(29, 'Switch', 6, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(30, 'Wire', 6, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(31, 'Cable', 6, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(32, 'Engine Oil', 7, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(33, 'Brake Pads', 7, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(34, 'Tires', 7, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(35, 'Batteries', 7, '2026-09-02 15:53:21', '2026-09-02 15:53:21'),
(36, 'Filters', 7, '2026-09-02 15:53:21', '2026-09-02 15:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'return_deadline_days', '30', 'integer', 'returns', 'Return Deadline (Days)', 'Number of days customers have to return items after purchase. Set to 0 to disable returns.', '2026-09-02 15:52:48', '2026-09-02 15:52:48'),
(2, 'return_require_approval', '1', 'boolean', 'returns', 'Require Manager Approval', 'If enabled, all returns must be approved by a manager before processing.', '2026-09-02 15:52:48', '2026-09-02 15:52:48'),
(3, 'return_auto_approve_threshold', '0', 'integer', 'returns', 'Auto-Approve Threshold', 'Returns under this amount will be auto-approved. Set to 0 to disable auto-approval.', '2026-09-02 15:52:48', '2026-09-02 15:52:48');

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE `transports` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_ur` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `can_approve_returns` tinyint(1) NOT NULL DEFAULT '0',
  `can_approve_past_deadline_returns` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `usertype` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `can_approve_returns`, `can_approve_past_deadline_returns`, `email_verified_at`, `usertype`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', 0, 0, NULL, 'admin', '$2y$10$b7/dRErP12rDNLXmjppgS.9lEMWB5fdeOQx390w2y.eGegRBpQIkG', 'AT7erSKZqLJrDLe56xLhWnNc8JhGdUNluJgRnct4LEg5BSdwO0hHwjqxvqUE', '2026-09-02 15:53:25', '2026-09-02 15:53:25'),
(2, 'atif', 'admin@admin.com', 0, 0, NULL, 'admin', '$2y$10$gFlw.L2NDhY0vylnr3f01.B4w9U.lr0bFmV7owp7hP92WT3PMxNxm', NULL, '2026-09-05 10:17:08', '2026-09-05 10:17:08');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `email`, `phone`, `address`, `opening_balance`, `created_at`, `updated_at`) VALUES
(1, 'Steelex PVT Limited', NULL, NULL, NULL, '30000000', '2026-09-09 21:03:22', '2026-09-09 21:03:22');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bilties`
--

CREATE TABLE `vendor_bilties` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `bilty_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ledgers`
--

CREATE TABLE `vendor_ledgers` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `opening_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `previous_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `closing_balance` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_ledgers`
--

INSERT INTO `vendor_ledgers` (`id`, `admin_or_user_id`, `vendor_id`, `opening_balance`, `previous_balance`, `closing_balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2', 1, '0', '30000000', '30150000', '2026-09-09 21:05:14', '2026-09-09 21:05:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payments`
--

CREATE TABLE `vendor_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `admin_or_user_id` bigint UNSIGNED NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sales_officer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_head` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `narration` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('draft','posted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_details`
--

CREATE TABLE `voucher_details` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_master_id` bigint UNSIGNED NOT NULL,
  `account_id` bigint UNSIGNED NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `narration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher_details`
--

INSERT INTO `voucher_details` (`id`, `voucher_master_id`, `account_id`, `debit`, `credit`, `narration`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 6237.00, 0.00, 'Sale Invoice #INV-0001', '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(2, 1, 3, 0.00, 6237.00, 'Sale Invoice #INV-0001', '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(5, 3, 2, 176.00, 0.00, 'Sale Invoice #INV-0003', '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(6, 3, 3, 0.00, 176.00, 'Sale Invoice #INV-0003', '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(7, 4, 2, 3570.00, 0.00, 'Sale Invoice #INV-0004', '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(8, 4, 3, 0.00, 3570.00, 'Sale Invoice #INV-0004', '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(9, 5, 3, 11340.00, 0.00, 'Credit Note for Return #SR-0001', '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(10, 5, 2, 0.00, 11340.00, 'Sale Return #SR-0001', '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(11, 6, 3, 1428.00, 0.00, 'Credit Note for Return #SR-0002', '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(12, 6, 2, 0.00, 1428.00, 'Sale Return #SR-0002', '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(21, 11, 4, 200000.00, 0.00, 'Payment Received', '2026-09-09 21:01:32', '2026-09-09 21:01:32'),
(22, 11, 2, 0.00, 200000.00, 'Receipt from customer', '2026-09-09 21:01:32', '2026-09-09 21:01:32'),
(23, 12, 1, 150000.00, 0.00, 'Transfer to Steelex PVT Limited from Mumtaz', '2026-09-09 21:05:14', '2026-09-09 21:05:14'),
(24, 12, 2, 0.00, 150000.00, 'Transfer from Mumtaz to Steelex PVT Limited', '2026-09-09 21:05:14', '2026-09-09 21:05:14'),
(25, 13, 2, 1760.00, 0.00, 'Sale Invoice #INV-0005', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(26, 13, 3, 0.00, 1760.00, 'Sale Invoice #INV-0005', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(27, 14, 2, 5670.00, 0.00, 'Sale Invoice #INV-0002', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(28, 14, 3, 0.00, 5670.00, 'Sale Invoice #INV-0002', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(29, 15, 2, 6900.00, 0.00, 'Sale Invoice #INV-0006', '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(30, 15, 3, 0.00, 6900.00, 'Sale Invoice #INV-0006', '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(33, 17, 2, 714.00, 0.00, 'Sale Invoice #INV-0008', '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(34, 17, 3, 0.00, 714.00, 'Sale Invoice #INV-0008', '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(35, 18, 7, 357.00, 0.00, 'Payment received from Invoice #INV-0009', '2026-09-10 09:40:05', '2026-09-10 09:40:05'),
(36, 18, 3, 0.00, 357.00, 'Payment for Invoice #INV-0009', '2026-09-10 09:40:05', '2026-09-10 09:40:05'),
(37, 19, 7, 0.00, 357.00, 'Cash Refund Paid', '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(38, 19, 2, 357.00, 0.00, 'Refund to Customer', '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(39, 20, 3, 357.00, 0.00, 'Credit Note for Return #SR-0003', '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(40, 20, 2, 0.00, 357.00, 'Sale Return #SR-0003', '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(41, 21, 9, 1134.00, 0.00, 'Payment received from Invoice #INV-0010', '2026-09-10 09:45:24', '2026-09-10 09:45:24'),
(42, 21, 3, 0.00, 1134.00, 'Payment for Invoice #INV-0010', '2026-09-10 09:45:24', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Table structure for table `voucher_masters`
--

CREATE TABLE `voucher_masters` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_type` enum('receipt','payment','expense','journal','contra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','posted','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `voucher_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `fiscal_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `voucher_masters`
--

INSERT INTO `voucher_masters` (`id`, `voucher_type`, `status`, `voucher_no`, `date`, `fiscal_year`, `party_type`, `party_id`, `remarks`, `total_amount`, `created_by`, `posted_at`, `created_at`, `updated_at`) VALUES
(1, 'journal', 'posted', 'JV-2026-0001', '2026-09-05', NULL, 'App\\Models\\Customer', 1, 'Sale Invoice #INV-0001', 6237.00, 1, '2026-09-05 11:20:10', '2026-09-05 11:20:10', '2026-09-05 11:20:10'),
(3, 'journal', 'posted', 'JV-2026-0003', '2026-09-05', NULL, 'App\\Models\\Customer', 1, 'Sale Invoice #INV-0003', 176.00, 1, '2026-09-05 12:54:14', '2026-09-05 12:54:14', '2026-09-05 12:54:14'),
(4, 'journal', 'posted', 'JV-2026-0004', '2026-01-01', NULL, 'App\\Models\\Customer', 1, 'Sale Invoice #INV-0004', 3570.00, 2, '2026-09-09 10:24:08', '2026-09-09 10:24:08', '2026-09-09 10:24:08'),
(5, 'journal', 'posted', 'JV-2026-0005', '2026-09-09', '2025-2026', 'App\\Models\\Customer', 1, 'Sale Return #SR-0001', 11340.00, 2, NULL, '2026-09-09 10:26:48', '2026-09-09 10:26:48'),
(6, 'journal', 'posted', 'JV-2026-0006', '2026-09-09', '2025-2026', 'App\\Models\\Customer', 1, 'Sale Return #SR-0002', 1428.00, 2, NULL, '2026-09-09 13:08:37', '2026-09-09 13:08:37'),
(11, 'receipt', 'posted', 'RE-2026-0001', '2026-09-10', '2025-2026', 'App\\Models\\Customer', 1, ' (Ref: RVID-0001)', 200000.00, 2, NULL, '2026-09-09 21:01:32', '2026-09-09 21:01:32'),
(12, 'journal', 'posted', 'TVID-011', '2026-09-08', NULL, 'App\\Models\\Customer', 1, 'Party Transfer: Mumtaz -> Steelex PVT Limited | Slip No.56321', 150000.00, 2, NULL, '2026-09-09 21:05:14', '2026-09-09 21:05:14'),
(13, 'journal', 'posted', 'JV-2026-0007', '2026-01-01', NULL, 'App\\Models\\Customer', 2, 'Sale Invoice #INV-0005', 1760.00, 2, '2026-09-09 21:42:55', '2026-09-09 21:42:55', '2026-09-09 21:42:55'),
(14, 'journal', 'posted', 'JV-2026-0008', '2026-01-01', NULL, 'App\\Models\\Customer', 1, 'Sale Invoice #INV-0002', 5670.00, 2, '2026-09-09 21:43:23', '2026-09-09 21:43:23', '2026-09-09 21:43:23'),
(15, 'journal', 'posted', 'JV-2026-0009', '2026-01-01', NULL, 'App\\Models\\Customer', 2, 'Sale Invoice #INV-0006', 6900.00, 1, '2026-09-09 23:04:50', '2026-09-09 23:04:50', '2026-09-09 23:04:50'),
(17, 'journal', 'posted', 'JV-2026-0010', '2026-01-01', NULL, 'App\\Models\\Customer', 1, 'Sale Invoice #INV-0008', 714.00, 1, '2026-09-10 09:33:35', '2026-09-10 09:33:35', '2026-09-10 09:33:35'),
(18, 'receipt', 'posted', 'RE-2026-0002', '2026-09-10', '2025-2026', NULL, NULL, 'Auto-Receipt for Sale Invoice #INV-0009. Received: 357', 357.00, 2, NULL, '2026-09-10 09:40:05', '2026-09-10 09:40:05'),
(19, 'payment', 'posted', 'PA-2026-0001', '2026-09-10', '2025-2026', 'App\\Models\\Customer', 3, 'Refund for Return #SR-0003', 357.00, 2, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(20, 'journal', 'posted', 'JV-2026-0011', '2026-09-10', '2025-2026', 'App\\Models\\Customer', 3, 'Sale Return #SR-0003', 357.00, 2, NULL, '2026-09-10 09:40:55', '2026-09-10 09:40:55'),
(21, 'receipt', 'posted', 'RE-2026-0003', '2026-09-10', '2025-2026', NULL, NULL, 'Auto-Receipt for Sale Invoice #INV-0010. Received: 1134', 1134.00, 1, NULL, '2026-09-10 09:45:24', '2026-09-10 09:45:24');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_onhand`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_onhand` (
`product_id` bigint unsigned
,`onhand_qty` decimal(34,3)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `warehouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creater_id` bigint UNSIGNED DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `branch_id`, `warehouse_name`, `creater_id`, `location`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Main Store', 1, 'Karachi', 'Main stock storage', '2026-09-02 15:53:21', '2026-09-02 15:53:21', NULL),
(2, 1, 'Branch A', 1, 'Lahore', 'North region store', '2026-09-02 15:53:21', '2026-09-02 15:53:21', NULL),
(3, 1, 'Branch B', 1, 'Islamabad', 'Capital branch', '2026-09-02 15:53:21', '2026-09-02 15:53:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stocks`
--

CREATE TABLE `warehouse_stocks` (
  `id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `total_pieces` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_stocks`
--

INSERT INTO `warehouse_stocks` (`id`, `warehouse_id`, `product_id`, `quantity`, `total_pieces`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 134, 134.0000, 'Initial Stock', '2026-09-05 11:18:13', '2026-09-10 09:45:24'),
(2, 1, 2, 0, 0.0000, 'Initial Stock', '2026-09-05 12:49:15', '2026-09-05 12:49:15'),
(3, 1, 3, 0, 0.0000, 'Initial Stock', '2026-09-05 12:50:34', '2026-09-05 12:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `web_customers`
--

CREATE TABLE `web_customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint UNSIGNED NOT NULL,
  `web_customer_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint UNSIGNED NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_head_id_foreign` (`head_id`);

--
-- Indexes for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_heads_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `account_histories`
--
ALTER TABLE `account_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_histories_account_id_foreign` (`account_id`);

--
-- Indexes for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biometric_devices_is_active_index` (`is_active`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_user_id_unique` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_id_unique` (`customer_id`),
  ADD UNIQUE KEY `customers_uuid_unique` (`uuid`);

--
-- Indexes for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_ledgers_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_payments_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `customer_types`
--
ALTER TABLE `customer_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_types_name_unique` (`name`);

--
-- Indexes for table `day_closings`
--
ALTER TABLE `day_closings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drawer_transactions_day_closing_id_foreign` (`day_closing_id`),
  ADD KEY `drawer_transactions_returned_in_closing_id_foreign` (`returned_in_closing_id`);

--
-- Indexes for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ecommerce_orders_order_number_unique` (`order_number`),
  ADD KEY `ecommerce_orders_web_customer_id_foreign` (`web_customer_id`),
  ADD KEY `ecommerce_orders_coupon_id_foreign` (`coupon_id`);

--
-- Indexes for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ecommerce_order_items_ecommerce_order_id_foreign` (`ecommerce_order_id`),
  ADD KEY `ecommerce_order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_salary_structures_assigned_by_foreign` (`assigned_by`),
  ADD KEY `employee_salary_structures_employee_id_is_active_index` (`employee_id`,`is_active`),
  ADD KEY `employee_salary_structures_salary_structure_id_is_active_index` (`salary_structure_id`,`is_active`),
  ADD KEY `employee_salary_structures_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_attendances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_departments`
--
ALTER TABLE `hr_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_designations`
--
ALTER TABLE `hr_designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_designations_name_unique` (`name`);

--
-- Indexes for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_employees_email_unique` (`email`),
  ADD KEY `hr_employees_user_id_foreign` (`user_id`),
  ADD KEY `hr_employees_department_id_foreign` (`department_id`),
  ADD KEY `hr_employees_designation_id_foreign` (`designation_id`),
  ADD KEY `hr_employees_shift_id_foreign` (`shift_id`),
  ADD KEY `hr_employees_biometric_device_id_foreign` (`biometric_device_id`),
  ADD KEY `hr_employees_device_user_id_index` (`device_user_id`);

--
-- Indexes for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_employee_documents_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_holidays_date_unique` (`date`);

--
-- Indexes for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_leaves_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loans_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_payments_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_scheduled_deductions_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_payrolls_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payroll_details_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_salary_structures_employee_id_index` (`employee_id`),
  ADD KEY `hr_salary_structures_parent_structure_id_foreign` (`parent_structure_id`);

--
-- Indexes for table `hr_settings`
--
ALTER TABLE `hr_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_settings_key_unique` (`key`);

--
-- Indexes for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_series`
--
ALTER TABLE `invoice_series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_series_prefix_unique` (`prefix`);

--
-- Indexes for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inward_gatepass_items_inward_gatepass_id_foreign` (`inward_gatepass_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entries_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `journal_entries_account_id_entry_date_index` (`account_id`,`entry_date`),
  ADD KEY `journal_entries_entry_date_index` (`entry_date`),
  ADD KEY `journal_entries_party_type_party_id_index` (`party_type`,`party_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_name_unique` (`name`);

--
-- Indexes for table `narrations`
--
ALTER TABLE `narrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `package_types`
--
ALTER TABLE `package_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_types_name_unique` (`name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_bookings`
--
ALTER TABLE `product_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_discounts_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_web_images`
--
ALTER TABLE `product_web_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_web_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_product_id_index` (`product_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `purchase_returns_vendor_id_foreign` (`vendor_id`),
  ADD KEY `purchase_returns_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `purchase_returns_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
  ADD UNIQUE KEY `sales_uuid_unique` (`uuid`),
  ADD KEY `sales_due_date_index` (`due_date`);

--
-- Indexes for table `sales_officers`
--
ALTER TABLE `sales_officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `sale_returns_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`),
  ADD KEY `sale_returns_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_return_items_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stocks_unique_triplet` (`branch_id`,`warehouse_id`,`product_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustments_user_id_foreign` (`user_id`),
  ADD KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `stock_adjustments_product_id_foreign` (`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sm_product_created` (`product_id`,`created_at`),
  ADD KEY `stock_movements_ref_uuid_index` (`ref_uuid`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  ADD KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  ADD KEY `stock_transfers_product_id_foreign` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `system_notifications_type_created_at_index` (`type`,`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_bilties_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_bilties_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_ledgers_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_payments_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_details_voucher_master_id_foreign` (`voucher_master_id`),
  ADD KEY `voucher_details_account_id_foreign` (`account_id`);

--
-- Indexes for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_masters_voucher_no_unique` (`voucher_no`),
  ADD KEY `voucher_masters_party_type_party_id_index` (`party_type`,`party_id`),
  ADD KEY `voucher_masters_voucher_type_index` (`voucher_type`),
  ADD KEY `voucher_masters_status_index` (`status`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouses_creater_id_index` (`creater_id`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_stocks_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `warehouse_stocks_product_id_foreign` (`product_id`);

--
-- Indexes for table `web_customers`
--
ALTER TABLE `web_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `web_customers_email_unique` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_web_customer_id_product_id_unique` (`web_customer_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `account_heads`
--
ALTER TABLE `account_heads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `account_histories`
--
ALTER TABLE `account_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_types`
--
ALTER TABLE `customer_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `day_closings`
--
ALTER TABLE `day_closings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_departments`
--
ALTER TABLE `hr_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_designations`
--
ALTER TABLE `hr_designations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employees`
--
ALTER TABLE `hr_employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loans`
--
ALTER TABLE `hr_loans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_settings`
--
ALTER TABLE `hr_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_series`
--
ALTER TABLE `invoice_series`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `narrations`
--
ALTER TABLE `narrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `package_types`
--
ALTER TABLE `package_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_bookings`
--
ALTER TABLE `product_bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_discounts`
--
ALTER TABLE `product_discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_web_images`
--
ALTER TABLE `product_web_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sales_officers`
--
ALTER TABLE `sales_officers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_details`
--
ALTER TABLE `voucher_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `web_customers`
--
ALTER TABLE `web_customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_stock_onhand`
--
DROP TABLE IF EXISTS `v_stock_onhand`;

CREATE ALGORITHM=UNDEFINED DEFINER=`binsult1_yp`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_onhand`  AS SELECT `stock_movements`.`product_id` AS `product_id`, round(coalesce(sum((case when (`stock_movements`.`type` in ('in','assembly_in')) then abs(`stock_movements`.`qty`) when (`stock_movements`.`type` in ('out','assembly_out')) then -(abs(`stock_movements`.`qty`)) when (`stock_movements`.`type` = 'adjustment') then `stock_movements`.`qty` else 0 end)),0),3) AS `onhand_qty` FROM `stock_movements` GROUP BY `stock_movements`.`product_id` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_head_id_foreign` FOREIGN KEY (`head_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD CONSTRAINT `account_heads_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_histories`
--
ALTER TABLE `account_histories`
  ADD CONSTRAINT `account_histories_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD CONSTRAINT `customer_ledgers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drawer_transactions`
--
ALTER TABLE `drawer_transactions`
  ADD CONSTRAINT `drawer_transactions_day_closing_id_foreign` FOREIGN KEY (`day_closing_id`) REFERENCES `day_closings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drawer_transactions_returned_in_closing_id_foreign` FOREIGN KEY (`returned_in_closing_id`) REFERENCES `day_closings` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ecommerce_orders`
--
ALTER TABLE `ecommerce_orders`
  ADD CONSTRAINT `ecommerce_orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ecommerce_orders_web_customer_id_foreign` FOREIGN KEY (`web_customer_id`) REFERENCES `web_customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ecommerce_order_items`
--
ALTER TABLE `ecommerce_order_items`
  ADD CONSTRAINT `ecommerce_order_items_ecommerce_order_id_foreign` FOREIGN KEY (`ecommerce_order_id`) REFERENCES `ecommerce_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ecommerce_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD CONSTRAINT `employee_salary_structures_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employee_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_salary_structure_id_foreign` FOREIGN KEY (`salary_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD CONSTRAINT `hr_attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD CONSTRAINT `hr_employees_biometric_device_id_foreign` FOREIGN KEY (`biometric_device_id`) REFERENCES `biometric_devices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `hr_departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `hr_designations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `hr_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD CONSTRAINT `hr_employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD CONSTRAINT `hr_leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD CONSTRAINT `hr_loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD CONSTRAINT `hr_loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD CONSTRAINT `hr_loan_scheduled_deductions_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD CONSTRAINT `hr_payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_payrolls_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD CONSTRAINT `hr_payroll_details_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `hr_payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD CONSTRAINT `hr_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_salary_structures_parent_structure_id_foreign` FOREIGN KEY (`parent_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD CONSTRAINT `inward_gatepass_items_inward_gatepass_id_foreign` FOREIGN KEY (`inward_gatepass_id`) REFERENCES `inward_gatepasses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD CONSTRAINT `product_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_web_images`
--
ALTER TABLE `product_web_images`
  ADD CONSTRAINT `product_web_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_returns_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD CONSTRAINT `sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD CONSTRAINT `system_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD CONSTRAINT `vendor_bilties_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_bilties_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD CONSTRAINT `vendor_ledgers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD CONSTRAINT `voucher_details_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `voucher_details_voucher_master_id_foreign` FOREIGN KEY (`voucher_master_id`) REFERENCES `voucher_masters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD CONSTRAINT `warehouse_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_web_customer_id_foreign` FOREIGN KEY (`web_customer_id`) REFERENCES `web_customers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
