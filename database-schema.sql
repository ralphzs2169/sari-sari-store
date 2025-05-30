-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 05:59 AM
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
-- Database: `sari_sari_store`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateActivityLog` (IN `p_activity_type` VARCHAR(255), IN `p_description` TEXT)   BEGIN
    INSERT INTO activity_logs (activity_type, description)
    VALUES (p_activity_type, p_description);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateCategory` (IN `cat_name` VARCHAR(100), IN `cat_description` TEXT)   BEGIN
    INSERT INTO categories (name, description)
    VALUES (cat_name, cat_description);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateProduct` (IN `p_name` VARCHAR(255), IN `p_category_id` INT, IN `p_unit_id` INT, IN `p_cost_price` DECIMAL(10,2), IN `p_selling_price` DECIMAL(10,2), IN `p_quantity_in_stock` INT, IN `p_image_path` VARCHAR(255))   BEGIN
    INSERT INTO products (
        name, category_id, unit_id, cost_price, selling_price, quantity_in_stock, image_path
    )
    VALUES (
        p_name, p_category_id, p_unit_id, p_cost_price, p_selling_price, p_quantity_in_stock, p_image_path
    );

    SELECT LAST_INSERT_ID() AS product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateSaleItem` (IN `p_sale_id` INT, IN `p_product_id` INT, IN `p_quantity` INT, IN `p_price` DECIMAL(10,2), OUT `p_sale_item_id` INT)   BEGIN
    INSERT INTO sale_items (sale_id, product_id, quantity, price)
    VALUES (p_sale_id, p_product_id, p_quantity, p_price);

    SET p_sale_item_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateTransaction` (IN `p_admin_id` INT, IN `p_total_amount` DECIMAL(10,2), IN `p_payment_method` VARCHAR(20), OUT `p_sale_id` INT)   BEGIN
    INSERT INTO sales ( admin_id, total_amount, payment_method)
    VALUES ( p_admin_id, p_total_amount, p_payment_method);

    SET p_sale_id = LAST_INSERT_ID();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateUnit` (IN `p_name` VARCHAR(50), IN `p_abbreviation` VARCHAR(10))   BEGIN
    INSERT INTO units (name, abbreviation)
    VALUES (p_name, p_abbreviation);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteCategory` (IN `cat_id` INT)   BEGIN
    DELETE FROM categories WHERE category_id = cat_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteProduct` (IN `p_product_id` INT)   BEGIN
    DELETE FROM products WHERE product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUnit` (IN `unitId` INT)   BEGIN
    DELETE FROM units WHERE unit_id = unitId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivityLogsByProductId` (IN `p_product_id` INT)   BEGIN
    SELECT al.*, p.name AS product_name, a.username AS admin_username
    FROM activity_logs al
    LEFT JOIN products p ON al.product_id = p.product_id
    JOIN admins a ON al.admin_id = a.admin_id
    WHERE al.product_id = p_product_id
    ORDER BY al.created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllActivityLogs` ()   BEGIN
    SELECT * FROM activity_logs
    ORDER BY created_at DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllCategories` ()   BEGIN
    SELECT * FROM categories ORDER BY name ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllProducts` ()   BEGIN
    SELECT 
    p.product_id,
    p.name,
    p.category_id,
    c.name AS category_name,
    p.unit_id,
    u.name AS unit_name,
    p.cost_price,
    p.selling_price,
    p.quantity_in_stock,
    p.image_path
FROM products p
JOIN categories c ON p.category_id = c.category_id
JOIN units u ON p.unit_id = u.unit_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllTransactions` ()   BEGIN
    SELECT 
        s.sale_id,
        a.username,
        s.sale_date,
        s.total_amount,
        s.payment_method,
        s.status,
        GROUP_CONCAT(
            CONCAT(
                '{"product_name":"', REPLACE(p.name, '"', '\\"'), '",',
                '"image_path":"', REPLACE(p.image_path, '"', '\\"'), '",',
                '"quantity":', IFNULL(s_item.quantity, 0), ',',
                '"price":', IFNULL(s_item.price, 0), ',',
                '"subtotal":', IFNULL(s_item.subtotal, 0), '}'
            ) SEPARATOR ','
        ) AS items_json
    FROM sales s 
    JOIN admins a ON a.admin_id = s.admin_id
    JOIN sale_items s_item ON s.sale_id = s_item.sale_id
    JOIN products p ON p.product_id = s_item.product_id
    GROUP BY s.sale_id
    ORDER BY s.sale_date DESC, s.sale_id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllUnits` ()   BEGIN
    SELECT * FROM units;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBestSellingProduct` ()   BEGIN
    SELECT p.name, SUM(si.quantity) AS total_sold
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    JOIN sales s ON si.sale_id = s.sale_id
    WHERE s.status != 'void'
    GROUP BY si.product_id
    ORDER BY total_sold DESC
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCategoryById` (IN `cat_id` INT)   BEGIN
    SELECT * FROM categories WHERE category_id = cat_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductById` (IN `p_product_id` INT)   BEGIN
    SELECT * FROM products WHERE product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductCounts` ()   BEGIN
    SELECT
        (SELECT COUNT(*) FROM products) AS total_products,
        (SELECT COUNT(*) FROM products WHERE quantity_in_stock <= 5) AS low_stock_products;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProfitPerformance` (IN `range_type` VARCHAR(10), IN `start_date` DATE, IN `end_date` DATE)   BEGIN
    IF range_type = 'custom' AND start_date IS NOT NULL AND end_date IS NOT NULL THEN
        SELECT DATE(s.sale_date) AS label,
               SUM((si.price - p.cost_price) * si.quantity) AS profit
        FROM sales s
        JOIN sale_items si ON s.sale_id = si.sale_id
        JOIN products p ON si.product_id = p.product_id
        WHERE DATE(s.sale_date) BETWEEN start_date AND end_date
          AND s.status != 'void'
        GROUP BY DATE(s.sale_date)
        ORDER BY DATE(s.sale_date) ASC;

    ELSEIF range_type = 'weekly' THEN
        SELECT DAYNAME(s.sale_date) AS label,
               SUM((si.price - p.cost_price) * si.quantity) AS profit,
               DAYOFWEEK(s.sale_date) AS day_num
        FROM sales s
        JOIN sale_items si ON s.sale_id = si.sale_id
        JOIN products p ON si.product_id = p.product_id
        WHERE YEARWEEK(s.sale_date, 1) = YEARWEEK(CURDATE(), 1)
          AND s.status != 'void'
        GROUP BY DAYOFWEEK(s.sale_date)
        ORDER BY day_num ASC;

    ELSEIF range_type = 'monthly' THEN
        SELECT WEEK(s.sale_date, 1) AS week_num,
               SUM((si.price - p.cost_price) * si.quantity) AS profit
        FROM sales s
        JOIN sale_items si ON s.sale_id = si.sale_id
        JOIN products p ON si.product_id = p.product_id
        WHERE YEAR(s.sale_date) = YEAR(CURDATE())
          AND MONTH(s.sale_date) = MONTH(CURDATE())
          AND s.status != 'void'
        GROUP BY week_num
        ORDER BY week_num ASC;

    ELSE
        SELECT DATE(s.sale_date) AS label,
               SUM((si.price - p.cost_price) * si.quantity) AS profit
        FROM sales s
        JOIN sale_items si ON s.sale_id = si.sale_id
        JOIN products p ON si.product_id = p.product_id
        WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
          AND s.status != 'void'
        GROUP BY DATE(s.sale_date)
        ORDER BY DATE(s.sale_date) ASC;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSaleItemsBySaleId` (IN `p_sale_id` INT)   BEGIN
    SELECT * FROM sale_items WHERE sale_id = p_sale_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSalesByCashier` ()   BEGIN
    SELECT a.username AS cashier, SUM(s.total_amount) AS total_sales
    FROM sales s
    JOIN admins a ON s.admin_id = a.admin_id
    GROUP BY s.admin_id
    ORDER BY total_sales DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSalesByCategory` ()   BEGIN
    SELECT 
        c.name AS category, 
        SUM(si.quantity * si.price) AS total_sales
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    JOIN sales s ON si.sale_id = s.sale_id
    WHERE (s.status = 'active' OR s.status IS NULL)
    GROUP BY c.category_id
    ORDER BY total_sales DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSalesByPaymentMethod` ()   BEGIN
      SELECT 
        payment_method, 
        SUM(total_amount) AS total_sales
    FROM 
        sales
     WHERE status != 'void'
    GROUP BY 
        payment_method
        ;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTodaySalesTotal` ()   BEGIN
    SELECT IFNULL(SUM(total_amount), 0) AS total
    FROM sales
    WHERE DATE(sale_date) = CURDATE()
    AND status != 'void';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTodayTransactionCount` ()   BEGIN
    SELECT COUNT(*) AS total
    FROM sales
    WHERE DATE(sale_date) = CURDATE();
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTopProducts` ()   BEGIN
SELECT 
    p.name,
    SUM(sub.total_quantity) AS total_units_sold
FROM (
    SELECT 
        si.product_id,
        SUM(si.quantity) AS total_quantity
    FROM 
        sales s
    JOIN 
        sale_itemS si ON s.sale_id = si.sale_id
    GROUP BY 
        s.sale_id, si.product_id
) AS sub
JOIN 
    products p ON sub.product_id = p.product_id
GROUP BY 
    sub.product_id
ORDER BY 
    total_units_sold DESC
LIMIT 5;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTotalProfit` ()   BEGIN
    SELECT SUM((si.price - p.cost_price) * si.quantity) AS total_profit
    FROM sale_items si
    JOIN products p ON si.product_id = p.product_id
    JOIN sales s ON si.sale_id = s.sale_id
    WHERE s.status != 'void';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTotalSales` ()   BEGIN
    SELECT SUM(total_amount) AS total_sales
    FROM sales
    WHERE status != 'void';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTotalTransactions` ()   BEGIN
    SELECT COUNT(*) AS total_transactions
    FROM sales
    WHERE status != 'void';
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTransactionById` (IN `p_sale_id` INT)   BEGIN
   SELECT s.*,
		a.username
		FROM sales s JOIN admins a 
        ON s.admin_id = a.admin_id 
        WHERE sale_id = p_sale_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTransactionsByAdmin` (IN `p_admin_id` INT)   BEGIN
    SELECT * FROM sales WHERE admin_id = p_admin_id ORDER BY sale_date DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUnitById` (IN `unitId` INT)   BEGIN
    SELECT * FROM units WHERE unit_id = unitId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserByUsername` (IN `p_username` VARCHAR(50))   BEGIN
    SELECT * FROM admins WHERE username = p_username;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterAdmin` (IN `p_username` VARCHAR(50), IN `p_password` VARCHAR(255))   BEGIN
    -- Check if username already exists
    IF NOT EXISTS (SELECT 1 FROM admins WHERE username = p_username) THEN
        INSERT INTO admins (username, password) VALUES (p_username, p_password);
    ELSE
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Username already exists.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateCategory` (IN `cat_id` INT, IN `cat_name` VARCHAR(100), IN `cat_description` TEXT)   BEGIN
    UPDATE categories
    SET name = cat_name,
        description = cat_description
    WHERE category_id = cat_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateProduct` (IN `p_product_id` INT, IN `p_name` VARCHAR(255), IN `p_category_id` INT, IN `p_unit_id` INT, IN `p_cost_price` DECIMAL(10,2), IN `p_selling_price` DECIMAL(10,2), IN `p_quantity_in_stock` INT, IN `p_image_path` VARCHAR(255))   BEGIN
    UPDATE products
    SET
        name = p_name,
        category_id = p_category_id,
        unit_id = p_unit_id,
        cost_price = p_cost_price,
        selling_price = p_selling_price,
        quantity_in_stock = p_quantity_in_stock,
        image_path = p_image_path
    WHERE product_id = p_product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateUnit` (IN `unitId` INT, IN `unitName` VARCHAR(50), IN `unitAbbr` VARCHAR(10))   BEGIN
    UPDATE units
    SET name = unitName, abbreviation = unitAbbr
    WHERE unit_id = unitId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `VoidTransaction` (IN `p_sale_id` INT)   BEGIN
    UPDATE sales
    SET status = 'void'
    WHERE sale_id = p_sale_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `activity_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`activity_id`, `activity_type`, `description`, `created_at`) VALUES
(14, 'product_deleted', 'Deleted product: ASDAD', '2025-05-27 16:27:40'),
(15, 'product_deleted', 'Deleted product: adksjdlk', '2025-05-27 16:27:45'),
(16, 'product_deleted', 'DASDL - Removed from inventory', '2025-05-27 16:34:46'),
(17, 'product_deleted', 'assdka;ld - Removed from inventory', '2025-05-27 17:11:40'),
(18, 'product_deleted', 'dsalk - Removed from inventory', '2025-05-27 17:11:42'),
(19, 'product_deleted', 'daslkd - Removed from inventory', '2025-05-27 17:11:45'),
(20, 'new_product_added', 'Super Crunch (Big) - 12 pieces added to inventory', '2025-05-27 17:16:35'),
(21, 'product_deleted', 'Super Crunch (Big) - Removed from inventory by ralph', '2025-05-27 17:19:23'),
(24, 'new_product_added', 'DASJKDL - 123 pieces added to inventory by ralph', '2025-05-27 17:52:51'),
(25, 'new_product_added', 'DALSKD - 213 pieces added to inventory by ralph', '2025-05-27 17:53:04'),
(26, 'new_product_added', 'ASDAKLDJ - 123 pieces added to inventory by ralph', '2025-05-28 01:07:07'),
(27, 'product_updated', 'Updated product: DASJKDL', '2025-05-28 01:07:29'),
(28, 'new_product_added', 'Piattos - 8 pieces added to inventory by ralph', '2025-05-28 01:12:22'),
(29, 'product_deleted', 'ASDAKLDJ - Removed from inventory by ralph', '2025-05-28 01:12:34'),
(30, 'product_updated', 'DALSKD: Updated by ralph', '2025-05-28 01:12:38'),
(31, 'product_updated', 'Piattos: Updated by ralph', '2025-05-28 01:17:29'),
(32, 'low_stock_product', 'Piattos: Only 5 pieces remaining', '2025-05-28 01:17:29'),
(33, 'low_stock_alert', 'DASJKDL: Only 0 pieces remaining', '2025-05-28 01:20:20'),
(34, 'product_updated', 'DASJKDL: Updated by ralph', '2025-05-28 01:20:20'),
(35, 'out_of_stock', 'Piattos: 0 pieces remaining (Please restock)', '2025-05-28 01:21:36'),
(36, 'product_updated', 'Piattos: Updated by ralph', '2025-05-28 01:21:36'),
(37, 'low_stock_alert', 'Piattos: Only 5 pieces remaining', '2025-05-28 01:21:56'),
(38, 'product_updated', 'Piattos: Updated by ralph', '2025-05-28 01:21:56'),
(39, 'product_updated', 'Piattos: Updated by ralph', '2025-05-28 01:22:30'),
(40, 'product_renamed', 'Renamed product \'DASJKDL\' to \'Vitamilk (Chocolate)\' by ralph', '2025-05-28 01:41:19'),
(41, 'price_updated', 'Vitamilk (Chocolate): Updated price by ralph', '2025-05-28 01:41:19'),
(42, 'restocked', 'Vitamilk (Chocolate): Restocked with 8 piece(s) by ralph', '2025-05-28 01:41:19'),
(43, 'product_updated', 'Vitamilk (Chocolate): Updated by ralph', '2025-05-28 01:41:19'),
(44, 'cost_price_updated', 'Vitamilk (Chocolate): Cost price changed from ₱25.00 to ₱23.00 by ralph', '2025-05-28 01:43:31'),
(45, 'selling_price_updated', 'Vitamilk (Chocolate): Selling price changed from ₱30.00 to ₱28.00 by ralph', '2025-05-28 01:43:31'),
(46, 'product_updated', 'Vitamilk (Chocolate): Updated by ralph', '2025-05-28 01:43:31'),
(47, 'cost_price_updated', 'Argentina Corned Beef: Cost price changed from ₱40.00 to ₱42.00 by karen', '2025-05-28 01:45:46'),
(48, 'selling_price_updated', 'Argentina Corned Beef: Selling price changed from ₱45.00 to ₱47.00 by karen', '2025-05-28 01:45:46'),
(49, 'restocked', 'Argentina Corned Beef: Restocked with 8 piece(s) by karen', '2025-05-28 01:45:46'),
(50, 'product_updated', 'Argentina Corned Beef: Updated by karen', '2025-05-28 01:45:46'),
(51, 'new_category_added', 'askdaklsdj: New category created by karen', '2025-05-28 01:48:28'),
(52, 'category_renamed', 'Category renamed from \'askdaklsdj\' to \'Household Items\' by karen', '2025-05-28 01:49:39'),
(53, 'category_description_updated', 'Household Items: Description updated by karen', '2025-05-28 01:49:39'),
(54, 'category_updated', 'Household Items: Category updated by karen', '2025-05-28 01:49:39'),
(55, 'new_category_added', 'daslk: New category created by karen', '2025-05-28 01:50:11'),
(56, 'category_deleted', 'daslk: Category deleted by karen', '2025-05-28 01:50:19'),
(57, 'unit_name_updated', 'Unit renamed from \'Pieces\' to \'Piece\' by karen', '2025-05-28 01:55:20'),
(58, 'unit_updated', 'Piece unit updated by karen', '2025-05-28 01:55:20'),
(59, 'unit_name_updated', 'Unit renamed from \'Piece\' to \'Pieces\' by karen', '2025-05-28 01:55:31'),
(60, 'unit_abbreviation_updated', 'Pieces: Abbreviation changed from \'pcs\' to \'pc\' by karen', '2025-05-28 01:55:31'),
(61, 'unit_updated', 'Pieces unit updated by karen', '2025-05-28 01:55:31'),
(62, 'unit_abbreviation_updated', 'Pieces: Abbreviation changed from \'pc\' to \'pcs\' by karen', '2025-05-28 01:55:43'),
(63, 'unit_updated', 'Pieces unit updated by karen', '2025-05-28 01:55:43'),
(64, 'unit_created', 'Pack (pck) unit added by karen', '2025-05-28 01:56:01'),
(65, 'product_updated', 'DALSKD: Updated by karen', '2025-05-28 01:56:30'),
(66, 'category_changed', 'Category changed from \'Canned Goods\' to \'Dairy Products\' for product \'Argentina Corned Beef\' by karen', '2025-05-28 02:12:49'),
(67, 'product_updated', 'Argentina Corned Beef: Updated by karen', '2025-05-28 02:12:49'),
(68, 'category_changed', 'Category changed from \'Dairy Products\' to \'Canned Goods\' for product \'Argentina Corned Beef\' by karen', '2025-05-28 02:13:25'),
(69, 'product_updated', 'Argentina Corned Beef: Updated by karen', '2025-05-28 02:13:25'),
(70, 'product_renamed', 'Renamed product \'DALSKD\' to \'Super Crunch (Small)\' by karen', '2025-05-28 02:16:45'),
(71, 'product_category_changed', 'Super Crunch (Small): Category changed from \'Canned Goods\' to \'Snacks\' by karen', '2025-05-28 02:16:45'),
(72, 'product_unit_changed', 'Super Crunch (Small): Unit changed from \'Pieces\' to \'Pack\' by karen', '2025-05-28 02:16:45'),
(73, 'cost_price_updated', 'Super Crunch (Small): Cost price changed from ₱123.00 to ₱1.00 by karen', '2025-05-28 02:16:45'),
(74, 'selling_price_updated', 'Super Crunch (Small): Selling price changed from ₱1,234.00 to ₱2.00 by karen', '2025-05-28 02:16:45'),
(75, 'restocked', 'Super Crunch (Small): Restocked with 20 piece(s) by karen', '2025-05-28 02:16:45'),
(76, 'product_updated', 'Super Crunch (Small): Updated by karen', '2025-05-28 02:16:45'),
(77, 'low_stock_alert', 'Vitamilk (Chocolate): Only 4 pieces remaining', '2025-05-28 11:47:32'),
(78, 'stock_updated', 'Vitamilk (Chocolate): Stock adjusted to 4 by ralph', '2025-05-28 11:47:32'),
(79, 'product_updated', 'Vitamilk (Chocolate): Updated by ralph', '2025-05-28 11:47:32'),
(80, 'new_product_added', 'X.O Candy - 100 pieces added to inventory by ralph', '2025-05-28 12:53:18'),
(81, 'new_product_added', 'askldak - 342 pieces added to inventory by ralph', '2025-05-28 14:21:11'),
(82, 'new_product_added', 'aslkdal - 12 pieces added to inventory by ralph', '2025-05-28 14:36:13'),
(83, 'new_product_added', 'das - 2113 pieces added to inventory by ralph', '2025-05-28 14:38:25'),
(84, 'new_product_added', 'as;ld; - 1234 pieces added to inventory by ralph', '2025-05-28 14:43:12'),
(85, 'new_product_added', 'dasldk - 12314 pieces added to inventory by ralph', '2025-05-28 14:44:36'),
(86, 'new_product_added', 'sad - 123 pieces added to inventory by ralph', '2025-05-28 14:48:05'),
(87, 'product_deleted', 'das - Removed from inventory by ralph', '2025-05-28 14:48:42'),
(88, 'product_deleted', 'askldak - Removed from inventory by ralph', '2025-05-28 14:48:48'),
(89, 'product_deleted', 'aslkdal - Removed from inventory by ralph', '2025-05-28 14:48:51'),
(90, 'product_deleted', 'as;ld; - Removed from inventory by ralph', '2025-05-28 14:48:54'),
(91, 'product_deleted', 'dasldk - Removed from inventory by ralph', '2025-05-28 14:48:57'),
(92, 'product_deleted', 'sad - Removed from inventory by ralph', '2025-05-28 14:48:59'),
(93, 'new_product_added', 'Kopiko Blanca (Twin Pack) - 15 pieces added to inventory by ralph', '2025-05-28 14:54:43'),
(94, 'product_image_updated', 'Argentina Corned Beef: Product image updated by ralph', '2025-05-28 15:18:47'),
(95, 'product_updated', 'Argentina Corned Beef: Updated by ralph', '2025-05-28 15:18:47'),
(96, 'product_updated', 'Argentina Corned Beef: Updated by ralph', '2025-05-28 15:19:30'),
(97, 'product_image_updated', 'Argentina Corned Beef: Product image updated by ralph', '2025-05-28 15:21:00'),
(98, 'product_updated', 'Argentina Corned Beef: Updated by ralph', '2025-05-28 15:21:00'),
(99, 'product_image_updated', 'Argentina Corned Beef (175g): Product image updated by ralph', '2025-05-28 15:21:48'),
(100, 'product_renamed', 'Renamed product \'Argentina Corned Beef\' to \'Argentina Corned Beef (175g)\' by ralph', '2025-05-28 15:21:48'),
(101, 'product_updated', 'Argentina Corned Beef (175g): Updated by ralph', '2025-05-28 15:21:48'),
(102, 'product_image_updated', 'Vitamilk Double Choco (300ml): Product image updated by ralph', '2025-05-28 15:22:50'),
(103, 'product_renamed', 'Renamed product \'Vitamilk (Chocolate)\' to \'Vitamilk Double Choco (300ml)\' by ralph', '2025-05-28 15:22:50'),
(104, 'product_updated', 'Vitamilk Double Choco (300ml): Updated by ralph', '2025-05-28 15:22:50'),
(105, 'product_image_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Product image updated by ralph', '2025-05-28 15:24:18'),
(106, 'product_renamed', 'Renamed product \'Super Crunch (Small)\' to \'Super Crunch Cornchips Tasty Sweet Corn (55g)\' by ralph', '2025-05-28 15:24:18'),
(107, 'product_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Updated by ralph', '2025-05-28 15:24:18'),
(108, 'product_image_updated', 'Piattos Cheese (40g): Product image updated by ralph', '2025-05-28 15:25:03'),
(109, 'product_renamed', 'Renamed product \'Piattos\' to \'Piattos Cheese (40g)\' by ralph', '2025-05-28 15:25:03'),
(110, 'product_updated', 'Piattos Cheese (40g): Updated by ralph', '2025-05-28 15:25:03'),
(111, 'new_product_added', 'Palmolive Shampoo Sachet (Intensive Moisturizer) - 10 pieces added to inventory by ralph', '2025-05-28 15:26:42'),
(112, 'unit_created', 'Sachet (sach) unit added by ralph', '2025-05-28 15:27:51'),
(113, 'product_unit_changed', 'Palmolive Shampoo Sachet (Intensive Moisturizer): Unit changed from \'Pieces\' to \'Sachet\' by ralph', '2025-05-28 15:28:13'),
(114, 'product_updated', 'Palmolive Shampoo Sachet (Intensive Moisturizer): Updated by ralph', '2025-05-28 15:28:13'),
(115, 'product_image_updated', 'Raw Chicken Breast: Product image updated by ralph', '2025-05-28 15:29:47'),
(116, 'product_renamed', 'Renamed product \'X.O Candy\' to \'Raw Chicken Breast\' by ralph', '2025-05-28 15:29:47'),
(117, 'product_category_changed', 'Raw Chicken Breast: Category changed from \'Snacks\' to \'Frozen Foods\' by ralph', '2025-05-28 15:29:47'),
(118, 'product_unit_changed', 'Raw Chicken Breast: Unit changed from \'Pieces\' to \'Kilogram\' by ralph', '2025-05-28 15:29:47'),
(119, 'cost_price_updated', 'Raw Chicken Breast: Cost price changed from ₱1.00 to ₱200.00 by ralph', '2025-05-28 15:29:47'),
(120, 'selling_price_updated', 'Raw Chicken Breast: Selling price changed from ₱2.00 to ₱180.00 by ralph', '2025-05-28 15:29:47'),
(121, 'stock_updated', 'Raw Chicken Breast: Stock adjusted to 10 by ralph', '2025-05-28 15:29:47'),
(122, 'product_updated', 'Raw Chicken Breast: Updated by ralph', '2025-05-28 15:29:47'),
(123, 'sale_made', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 16:15:57'),
(124, 'sale_made', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 16:16:21'),
(125, 'sale_made', 'Customer Purchased Piattos Cheese (40g) and Super Crunch Cornchips Tasty Sweet Corn (55g)', '2025-05-28 16:16:46'),
(128, 'restocked', 'Vitamilk Double Choco (300ml): Restocked with 20 piece(s) by ralph', '2025-05-28 16:25:38'),
(129, 'product_updated', 'Vitamilk Double Choco (300ml): Updated by ralph', '2025-05-28 16:25:38'),
(130, 'new_sale_transaction_completed', 'Customer Purchased Vitamilk Double Choco (300ml) and Piattos Cheese (40g)', '2025-05-28 16:25:51'),
(131, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 17:36:21'),
(132, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 17:36:51'),
(133, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 17:37:16'),
(134, 'new_sale_transaction_completed', 'Customer Purchased ', '2025-05-28 17:40:19'),
(135, 'new_sale_transaction_completed', 'Customer Purchased ', '2025-05-28 17:41:05'),
(136, 'new_product_added', 'Cornetto (Classic Chocolate) - 14 pieces added to inventory by ralph', '2025-05-28 17:49:57'),
(137, 'new_product_added', 'Cornetto Disc (Black Choco Cookie) - 6 pieces added to inventory by ralph', '2025-05-28 17:53:22'),
(138, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g)', '2025-05-28 17:57:51'),
(139, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g), Vitamilk Double Choco (300ml), Super Crunch Cornchips Tasty Sweet Corn (55g) and Piattos Cheese (40g)', '2025-05-28 17:58:10'),
(140, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g)', '2025-05-28 18:10:37'),
(141, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-28 18:14:17'),
(142, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g)', '2025-05-28 18:14:43'),
(143, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g)', '2025-05-28 18:21:47'),
(144, 'new_sale_transaction_completed', 'Customer Purchased Argentina Corned Beef (175g)', '2025-05-28 18:25:08'),
(145, 'new_product_added', 'Starwax Floor Red Dye Wax (90g) - 8 pieces added to inventory by ralph', '2025-05-28 18:31:19'),
(146, 'new_product_added', 'Egg (small) - 30 pieces added to inventory by ralph', '2025-05-28 18:33:45'),
(147, 'new_product_added', 'Egg (medium) - 25 pieces added to inventory by ralph', '2025-05-28 18:34:25'),
(148, 'new_sale_transaction_completed', 'Customer Purchased Palmolive Shampoo Sachet (Intensive Moisturizer)', '2025-05-28 18:48:14'),
(149, 'new_sale_transaction_completed', 'Customer Purchased Kopiko Blanca (Twin Pack)', '2025-05-28 18:48:30'),
(150, 'new_sale_transaction_completed', 'Customer Purchased Kopiko Blanca (Twin Pack) and Cornetto (Classic Chocolate)', '2025-05-29 05:01:29'),
(151, 'cost_price_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Cost price changed from ₱1.00 to ₱15.00 by ralph', '2025-05-29 06:05:11'),
(152, 'selling_price_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Selling price changed from ₱2.00 to ₱10.00 by ralph', '2025-05-29 06:05:11'),
(153, 'product_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Updated by ralph', '2025-05-29 06:05:11'),
(154, 'new_sale_transaction_completed', 'Customer Purchased Kopiko Blanca (Twin Pack), Super Crunch Cornchips Tasty Sweet Corn (55g), Piattos Cheese (40g) and Vitamilk Double Choco (300ml)', '2025-05-29 09:35:29'),
(155, 'new_sale_transaction_completed', 'Customer Purchased Egg (small), Egg (medium), Cornetto Disc (Black Choco Cookie) and Cornetto (Classic Chocolate)', '2025-05-29 09:36:37'),
(156, 'new_product_added', 'Lucky Me Pancit Canton (Original) - 15 pieces added to inventory by ralph', '2025-05-29 11:59:20'),
(157, 'new_product_added', 'Lucky Me Pancit Canton (Chilimansi) - 13 pieces added to inventory by ralph', '2025-05-29 12:00:06'),
(158, 'new_product_added', 'Lucky Me Pancit Canton (Extra Hot Chili) - 9 pieces added to inventory by ralph', '2025-05-29 12:02:36'),
(159, 'new_sale_transaction_completed', 'Customer Purchased Cornetto Disc (Black Choco Cookie), Lucky Me Pancit Canton (Extra Hot Chili) and Lucky Me Pancit Canton (Chilimansi)', '2025-05-29 12:03:10'),
(160, 'new_sale_transaction_completed', 'Customer Purchased Super Crunch Cornchips Tasty Sweet Corn (55g), Kopiko Blanca (Twin Pack), Vitamilk Double Choco (300ml), Piattos Cheese (40g), Lucky Me Pancit Canton (Chilimansi), Egg (medium) and Egg (small)', '2025-05-29 12:26:44'),
(161, 'new_sale_transaction_completed', 'Customer Purchased Cornetto (Classic Chocolate)', '2025-05-29 12:51:55'),
(162, 'new_sale_transaction_completed', 'Customer Purchased Starwax Floor Red Dye Wax (90g) and Lucky Me Pancit Canton (Original)', '2025-05-29 13:16:52'),
(163, 'new_product_added', 'Ligo Sardines (155g) - 12 pieces added to inventory by karen', '2025-05-29 15:11:29'),
(164, 'new_product_added', 'Century Tuna (100g) - 8 pieces added to inventory by karen', '2025-05-29 15:13:14'),
(165, 'new_sale_transaction_completed', 'Customer Purchased Egg (small) and Cornetto (Classic Chocolate)', '2025-05-29 16:06:02'),
(166, 'restocked', 'Piattos Cheese (40g): Restocked with 10 piece(s) by karen', '2025-05-29 16:11:54'),
(167, 'product_updated', 'Piattos Cheese (40g): Updated by karen', '2025-05-29 16:11:54'),
(168, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-29 16:12:07'),
(169, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g) and Cornetto (Classic Chocolate)', '2025-05-29 16:19:39'),
(170, 'new_sale_transaction_completed', 'Customer Purchased Vitamilk Double Choco (300ml) and Raw Chicken Breast', '2025-05-29 17:24:04'),
(171, 'cost_price_updated', 'Raw Chicken Breast: Cost price changed from ₱200.00 to ₱180.00 by karen', '2025-05-29 17:26:23'),
(172, 'selling_price_updated', 'Raw Chicken Breast: Selling price changed from ₱180.00 to ₱200.00 by karen', '2025-05-29 17:26:23'),
(173, 'product_updated', 'Raw Chicken Breast: Updated by karen', '2025-05-29 17:26:23'),
(174, 'restocked', 'Raw Chicken Breast: Restocked with 120 piece(s) by karen', '2025-05-29 17:26:33'),
(175, 'product_updated', 'Raw Chicken Breast: Updated by karen', '2025-05-29 17:26:33'),
(176, 'new_sale_transaction_completed', 'Customer Purchased Raw Chicken Breast', '2025-05-29 17:26:51'),
(177, 'new_sale_transaction_completed', 'Customer Purchased Raw Chicken Breast', '2025-05-29 17:27:44'),
(178, 'new_sale_transaction_completed', 'Customer Purchased Egg (medium)', '2025-05-29 17:44:22'),
(179, 'new_sale_transaction_completed', 'Customer Purchased Egg (medium)', '2025-05-29 17:44:48'),
(180, 'new_sale_transaction_completed', 'Customer Purchased Century Tuna (100g)', '2025-05-29 18:07:17'),
(181, 'new_product_added', 'Red Horse (330ml) - 20 pieces added to inventory by karen', '2025-05-29 19:26:40'),
(182, 'new_product_added', 'Red Horse (1L) - 20 pieces added to inventory by karen', '2025-05-29 19:29:16'),
(183, 'new_product_added', 'G.S.M Blue Mojito - 10 pieces added to inventory by karen', '2025-05-29 19:32:44'),
(184, 'new_product_added', 'Nescafe Classic (Stick) - 99 pieces added to inventory by karen', '2025-05-29 19:35:22'),
(185, 'new_sale_transaction_completed', 'Customer Purchased Egg (medium)', '2025-05-29 19:39:18'),
(186, 'new_sale_transaction_completed', 'Customer Purchased Cornetto (Chocolate) and Cornetto Disc (Black Choco Cookie)', '2025-05-29 19:39:38'),
(187, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g) and Red Horse (330ml)', '2025-05-29 19:45:58'),
(188, 'new_sale_transaction_completed', 'Customer Purchased Nescafe Classic (Stick) and Vitamilk Double Choco (300ml)', '2025-05-29 19:46:17'),
(189, 'new_sale_transaction_completed', 'Customer Purchased Cornetto (Chocolate)', '2025-05-29 19:46:41'),
(190, 'new_sale_transaction_completed', 'Customer Purchased Cornetto (Chocolate) and Cornetto Disc (Black Choco Cookie)', '2025-05-29 19:47:14'),
(191, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-29 19:47:31'),
(192, 'new_sale_transaction_completed', 'Customer Purchased Kopiko Blanca (Twin Pack) and Super Crunch Cornchips Tasty Sweet Corn (55g)', '2025-05-29 19:51:02'),
(193, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-29 19:51:15'),
(194, 'new_sale_transaction_completed', 'Customer Purchased Egg (small)', '2025-05-29 22:47:27'),
(195, 'new_sale_transaction_completed', 'Customer Purchased Egg (small)', '2025-05-29 22:47:51'),
(196, 'low_stock_alert', 'Nescafe Classic (Stick): Only 4 pieces remaining', '2025-05-29 23:02:44'),
(197, 'stock_updated', 'Nescafe Classic (Stick): Stock adjusted to 4 by karen', '2025-05-29 23:02:44'),
(198, 'product_updated', 'Nescafe Classic (Stick): Updated by karen', '2025-05-29 23:02:44'),
(199, 'low_stock_alert', 'Egg (small): Only 4 pieces remaining', '2025-05-29 23:02:49'),
(200, 'stock_updated', 'Egg (small): Stock adjusted to 4 by karen', '2025-05-29 23:02:49'),
(201, 'product_updated', 'Egg (small): Updated by karen', '2025-05-29 23:02:49'),
(202, 'low_stock_alert', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Only 4 pieces remaining', '2025-05-29 23:20:05'),
(203, 'stock_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Stock adjusted to 4 by karen', '2025-05-29 23:20:05'),
(204, 'product_updated', 'Super Crunch Cornchips Tasty Sweet Corn (55g): Updated by karen', '2025-05-29 23:20:05'),
(205, 'new_sale_transaction_completed', 'Customer Purchased Piattos Cheese (40g)', '2025-05-30 02:24:00'),
(206, 'product_image_updated', 'Red Horse (330ml): Product image updated by karen', '2025-05-30 02:51:18'),
(207, 'product_updated', 'Red Horse (330ml): Updated by karen', '2025-05-30 02:51:18'),
(208, 'product_image_updated', 'Red Horse (1L): Product image updated by karen', '2025-05-30 02:51:37'),
(209, 'product_updated', 'Red Horse (1L): Updated by karen', '2025-05-30 02:51:37'),
(210, 'product_image_updated', 'G.S.M Blue Mojito: Product image updated by karen', '2025-05-30 02:51:48'),
(211, 'product_updated', 'G.S.M Blue Mojito: Updated by karen', '2025-05-30 02:51:48'),
(212, 'new_product_added', 'Kojie San Soap - 20 pieces added to inventory by karen', '2025-05-30 02:54:20'),
(213, 'product_image_updated', 'Nescafe Classic (Stick): Product image updated by karen', '2025-05-30 02:54:43'),
(214, 'product_updated', 'Nescafe Classic (Stick): Updated by karen', '2025-05-30 02:54:43'),
(215, 'new_product_added', 'Purefoods Tender Juicy Hotdog (230g) - 10 pieces added to inventory by karen', '2025-05-30 02:56:08'),
(216, 'new_product_added', 'Virgina Pork Tocino (200g) - 20 pieces added to inventory by karen', '2025-05-30 02:57:33'),
(217, 'new_product_added', 'Trapo - 10 pieces added to inventory by karen', '2025-05-30 03:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'ASDALSDK', '$2y$10$5qnXtyGgpWytL58FCxnFme5OO6C1usCXcdVvCCKjLVOH3Fble7Bee', '2025-05-26 17:07:06'),
(2, 'DAKSDJLAKJ', '$2y$10$IYuLwbcuR40ZyaLg258sIOAwK8cGc0dG3sCEn.ZBVn25LKmX6Kypm', '2025-05-26 17:07:31'),
(3, 'dakjsdlk', '$2y$10$r46JgEAx8a1w14BVLPPmk.uW4CMfTRplwLIdP91t4VE522flYJM9G', '2025-05-26 17:08:16'),
(4, 'aklsdjlak', '$2y$10$fh6AgyprsS2ZJbEoYix56eGjE7I5lWM6ZNBDVXhINY8fsN70ILOvm', '2025-05-26 17:08:55'),
(5, 'ralph', '$2y$10$I1UadCE3Y0i.bkdKJUWFau1siDmEle4gO61cd0SneZDUSXTLq2qsq', '2025-05-26 17:32:23'),
(6, 'ASJDALSKDJ', '$2y$10$OcNoJLIgI15x3LODlW3dLOFtwOOa.duJBvHsDKiXsvsphw9Q/EEii', '2025-05-26 18:12:17'),
(7, 'daslda', '$2y$10$Bjp02eXLAenVHYVOLnzTvexFNdbEKHd9KMVzLpPCRNa6mpChrv7hK', '2025-05-26 18:29:28'),
(8, 'askdjla', '$2y$10$N719QR6XKMXfelJBP.Qc2uGlaqsS4dgIYAZqFcvNmEpiKu5SSs84m', '2025-05-26 23:02:11'),
(9, 'karen', '$2y$10$gvp5BqMW.ucrXcGFxFxCUumGQKA/4e6i4sWLS/j7rDOEiENpONJ7a', '2025-05-28 01:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(26, 'Beverages', 'Drinks including soda, juice, water'),
(27, 'Snacks', 'Chips, nuts, and other snack foods'),
(28, 'Dairy Products', 'Milk, cheese, yogurt, and butter'),
(29, 'Canned Goods', 'Preserved canned vegetables & meat'),
(30, 'Personal Care', 'Soaps, shampoos, and hygiene items'),
(31, 'Frozen Foods', 'Frozen meals, ice cream, and more'),
(32, 'Household Items', 'Detergent, dishwashing liquid, floor wax, trash bags');

-- --------------------------------------------------------

--
-- Table structure for table `credits`
--

CREATE TABLE `credits` (
  `credit_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `due_date` date DEFAULT NULL,
  `last_payment_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_payments`
--

CREATE TABLE `credit_payments` (
  `payment_id` int(11) NOT NULL,
  `credit_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `quantity_in_stock` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category_id`, `unit_id`, `cost_price`, `selling_price`, `quantity_in_stock`, `is_active`, `created_at`, `image_path`) VALUES
(2, 'Argentina Corned Beef (175g)', 29, 5, 42.00, 47.00, 4, 1, '2025-05-27 11:12:50', '/sari-sari-store/assets/images/products/prod_68372a0ce5d9e_ARGENTINA-CORNED-BEEF-175G.jpg'),
(27, 'Vitamilk Double Choco (300ml)', 26, 4, 23.00, 28.00, 10, 1, '2025-05-27 17:52:51', '/sari-sari-store/assets/images/products/prod_68372a4a647f7_0010405_vitamilk-double-choco-shake-300ml_460.jpeg'),
(28, 'Super Crunch Cornchips Tasty Sweet Corn (55g)', 27, 7, 15.00, 10.00, 5, 1, '2025-05-27 17:53:04', '/sari-sari-store/assets/images/products/prod_68372aa2df995_Super-Crunch-Corn-Chips-Tasty-Sweet-Corn-55g.png'),
(30, 'Piattos Cheese (40g)', 27, 2, 15.00, 18.00, 5, 1, '2025-05-28 01:12:22', '/sari-sari-store/assets/images/products/prod_68372acf99e55_1_b7666ca3-ad23-4859-8eb0-6ed33fd4e586.webp'),
(31, 'Raw Chicken Breast', 31, 6, 180.00, 200.00, 92, 1, '2025-05-28 12:53:18', '/sari-sari-store/assets/images/products/prod_68372beb1b1b2_Boneless-Skinless-Chicken-Breast.png'),
(38, 'Kopiko Blanca (Twin Pack)', 26, 7, 15.00, 12.00, 8, 1, '2025-05-28 14:54:43', '/sari-sari-store/assets/images/products/prod_683723b3bfd97_720a1c_2480a56884e342958bbec5c43ec7a096~mv2.avif'),
(39, 'Palmolive Shampoo Sachet (Intensive Moisturizer)', 30, 8, 10.00, 8.00, 9, 1, '2025-05-28 15:26:42', '/sari-sari-store/assets/images/products/prod_68372b32c3495_0004462_palmolive-shampoo-sachet-intensive-moisture.jpeg'),
(40, 'Cornetto (Chocolate)', 27, 2, 20.00, 25.00, 5, 1, '2025-05-28 17:49:57', '/sari-sari-store/assets/images/products/prod_68374cc5bc638_124182518.avif'),
(41, 'Cornetto Disc (Black Choco Cookie)', 27, 2, 27.00, 35.00, 1, 1, '2025-05-28 17:53:22', '/sari-sari-store/assets/images/products/prod_68374d9230079_126930027.avif'),
(42, 'Starwax Floor Red Dye Wax (90g)', 32, 7, 18.00, 21.00, 7, 1, '2025-05-28 18:31:19', '/sari-sari-store/assets/images/products/prod_6837567774690_image_bc4a470b-69b1-467b-bef9-024c17089568.webp'),
(43, 'Egg (small)', 28, 2, 8.00, 10.00, 4, 1, '2025-05-28 18:33:45', '/sari-sari-store/assets/images/products/prod_683757091fdc1_360_F_547369645_Zjj3vnC2N6HBRh6plnCP2gdHBY0OZAb3.jpg'),
(44, 'Egg (medium)', 28, 2, 9.00, 11.00, 19, 1, '2025-05-28 18:34:25', '/sari-sari-store/assets/images/products/prod_68375731c16b2_360_F_547369645_Zjj3vnC2N6HBRh6plnCP2gdHBY0OZAb3.jpg'),
(45, 'Lucky Me Pancit Canton (Original)', 27, 7, 11.00, 15.00, 14, 1, '2025-05-29 11:59:20', '/sari-sari-store/assets/images/products/prod_68384c18d9d78_Lucky-Me-Pancit-Canton-Original-1.png'),
(46, 'Lucky Me Pancit Canton (Chilimansi)', 27, 7, 11.00, 15.00, 12, 1, '2025-05-29 12:00:06', '/sari-sari-store/assets/images/products/prod_68384c460466b_SM102169562-1-6.jpg'),
(47, 'Lucky Me Pancit Canton (Extra Hot Chili)', 27, 7, 11.00, 15.00, 9, 1, '2025-05-29 12:02:36', '/sari-sari-store/assets/images/products/prod_68384cdc5661a_download (2).jpg'),
(48, 'Ligo Sardines (155g)', 29, 5, 28.00, 33.00, 12, 1, '2025-05-29 15:11:29', '/sari-sari-store/assets/images/products/prod_683879212d0e7_414f668c50f51349163c1d1f9e164de9-d5d4773b639b35-ligo-sardines-in-tomato-sauce.jpg'),
(49, 'Century Tuna (100g)', 29, 5, 40.00, 45.00, 8, 1, '2025-05-29 15:13:14', '/sari-sari-store/assets/images/products/prod_6838798aef29b_24-1.webp'),
(50, 'Red Horse (330ml)', 26, 4, 60.00, 75.00, 19, 1, '2025-05-29 19:26:40', '/sari-sari-store/assets/images/products/prod_68391d26eaa2e_SM9796133-6.png'),
(51, 'Red Horse (1L)', 26, 4, 115.00, 130.00, 20, 1, '2025-05-29 19:29:16', '/sari-sari-store/assets/images/products/prod_68391d393553e_1l.jpeg'),
(52, 'G.S.M Blue Mojito', 26, 4, 120.00, 140.00, 10, 1, '2025-05-29 19:32:44', '/sari-sari-store/assets/images/products/prod_68391d4493bc6_96f6a7f7fffb782bf0e31a95e82acdfa_1200x1200.webp'),
(53, 'Nescafe Classic (Stick)', 26, 2, 3.00, 5.00, 4, 1, '2025-05-29 19:35:22', '/sari-sari-store/assets/images/products/prod_68391df315abb_5747-007-21-2023-160749-443.jpg'),
(54, 'Kojie San Soap', 30, 2, 24.00, 28.00, 20, 1, '2025-05-30 02:54:20', '/sari-sari-store/assets/images/products/prod_68391ddc3401a_KS-KOJISANSOAP-135g-adam2021.jpg'),
(55, 'Purefoods Tender Juicy Hotdog (230g)', 31, 7, 76.00, 85.00, 10, 1, '2025-05-30 02:56:08', '/sari-sari-store/assets/images/products/prod_68391e48422ed_9000007749-Purefoods-Tender-Juicy-Hotdog-Classic-230g-210505_5def684a-3517-4cf0-b857-c1f1b2a4610a.webp'),
(56, 'Virgina Pork Tocino (200g)', 31, 7, 80.00, 90.00, 20, 1, '2025-05-30 02:57:33', '/sari-sari-store/assets/images/products/prod_68391e9d18d10_102065138_800x.webp'),
(57, 'Trapo', 32, 2, 10.00, 17.00, 10, 1, '2025-05-30 03:14:34', '/sari-sari-store/assets/images/products/prod_6839229a379d1_136d97a8b247f08e64bd992c0353e3e3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','gcash','credit') DEFAULT 'cash',
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `admin_id`, `sale_date`, `total_amount`, `payment_method`, `status`) VALUES
(1, 5, '2025-05-28 21:22:56', 18.00, 'cash', 'active'),
(2, 5, '2025-05-28 21:29:43', 47.00, 'cash', 'void'),
(3, 5, '2025-05-28 21:30:21', 38.00, 'cash', 'active'),
(4, 5, '2025-05-28 22:00:58', 47.00, 'cash', 'void'),
(5, 5, '2025-05-28 23:41:51', 47.00, 'cash', 'void'),
(6, 5, '2025-05-28 23:42:58', 18.00, 'cash', 'active'),
(7, 5, '2025-05-28 23:57:07', 18.00, 'cash', 'active'),
(8, 5, '2025-05-28 23:57:44', 18.00, 'cash', 'active'),
(9, 5, '2025-05-29 00:00:03', 18.00, 'cash', 'active'),
(10, 5, '2025-05-29 00:01:24', 18.00, 'cash', 'active'),
(11, 5, '2025-05-29 00:13:29', 18.00, 'cash', 'active'),
(12, 5, '2025-05-29 00:14:12', 18.00, 'cash', 'active'),
(13, 5, '2025-05-29 00:15:17', 18.00, 'cash', 'active'),
(14, 5, '2025-05-29 00:15:57', 18.00, 'cash', 'active'),
(15, 5, '2025-05-29 00:16:21', 18.00, 'cash', 'active'),
(16, 5, '2025-05-29 00:16:46', 38.00, 'cash', 'active'),
(17, 5, '2025-05-29 00:22:29', 56.00, 'cash', 'active'),
(18, 5, '2025-05-29 00:22:49', 56.00, 'cash', 'active'),
(19, 5, '2025-05-29 00:25:51', 46.00, 'cash', 'active'),
(20, 5, '2025-05-29 01:36:21', 18.00, 'cash', 'active'),
(21, 5, '2025-05-29 01:36:51', 18.00, 'cash', 'active'),
(22, 5, '2025-05-29 01:37:16', 18.00, 'cash', 'active'),
(23, 5, '2025-05-29 01:40:19', 47.00, 'cash', 'active'),
(24, 5, '2025-05-29 01:41:05', 8.00, 'cash', 'active'),
(25, 5, '2025-05-29 01:57:51', 47.00, 'cash', 'active'),
(26, 5, '2025-05-29 01:58:10', 95.00, 'cash', 'active'),
(27, 5, '2025-05-29 02:10:37', 47.00, 'gcash', 'active'),
(28, 5, '2025-05-29 02:14:17', 18.00, 'gcash', 'active'),
(29, 5, '2025-05-29 02:14:43', 47.00, 'cash', 'void'),
(30, 5, '2025-05-29 02:21:47', 47.00, 'gcash', 'active'),
(31, 5, '2025-05-29 02:25:08', 47.00, 'gcash', 'active'),
(32, 5, '2025-05-29 02:48:14', 8.00, 'cash', 'void'),
(33, 5, '2025-05-29 02:48:30', 12.00, 'gcash', 'active'),
(34, 5, '2025-05-29 13:01:29', 61.00, 'cash', 'active'),
(35, 5, '2025-05-29 17:35:29', 68.00, 'cash', 'active'),
(36, 5, '2025-05-29 17:36:37', 232.00, 'cash', 'void'),
(37, 5, '2025-05-29 20:03:10', 95.00, 'cash', 'void'),
(38, 5, '2025-05-29 20:26:44', 238.00, 'gcash', 'active'),
(39, 5, '2025-05-29 20:51:55', 25.00, 'cash', 'active'),
(40, 9, '2025-05-29 21:16:52', 36.00, 'cash', 'void'),
(41, 9, '2025-05-30 00:06:02', 55.00, 'cash', 'void'),
(42, 9, '2025-05-30 00:12:07', 18.00, 'cash', 'void'),
(43, 9, '2025-05-30 00:19:39', 265.00, 'cash', 'void'),
(44, 9, '2025-05-30 01:24:04', 1828.00, 'cash', 'active'),
(45, 9, '2025-05-30 01:26:51', 2400.00, 'cash', 'active'),
(46, 9, '2025-05-30 01:27:44', 3200.00, 'cash', 'active'),
(47, 9, '2025-05-30 01:44:22', 11.00, 'cash', 'active'),
(48, 9, '2025-05-30 01:44:48', 11.00, 'cash', 'active'),
(49, 9, '2025-05-30 02:07:17', 135.00, 'cash', 'void'),
(50, 9, '2025-05-30 03:39:18', 11.00, 'gcash', 'active'),
(51, 9, '2025-05-30 03:39:38', 60.00, 'cash', 'active'),
(52, 9, '2025-05-30 03:45:58', 111.00, 'cash', 'active'),
(53, 9, '2025-05-30 03:46:17', 33.00, 'cash', 'active'),
(54, 9, '2025-05-30 03:46:41', 25.00, 'gcash', 'active'),
(55, 9, '2025-05-30 03:47:14', 60.00, 'cash', 'active'),
(56, 9, '2025-05-30 03:47:31', 18.00, 'gcash', 'active'),
(57, 9, '2025-05-30 03:51:02', 22.00, 'cash', 'void'),
(58, 9, '2025-05-30 03:51:15', 18.00, 'gcash', 'void'),
(59, 9, '2025-05-30 06:47:27', 10.00, 'cash', 'void'),
(60, 9, '2025-05-30 06:47:51', 10.00, 'cash', 'active'),
(61, 9, '2025-05-30 10:24:00', 18.00, 'cash', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `sale_item_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`sale_item_id`, `sale_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 2, 1, 47.00),
(2, 3, 30, 2, 18.00),
(3, 3, 28, 1, 2.00),
(4, 4, 2, 1, 47.00),
(5, 5, 2, 1, 47.00),
(6, 6, 30, 1, 18.00),
(7, 7, 30, 1, 18.00),
(8, 8, 30, 1, 18.00),
(9, 9, 30, 1, 18.00),
(10, 10, 30, 1, 18.00),
(11, 14, 30, 1, 18.00),
(12, 15, 30, 1, 18.00),
(13, 16, 30, 2, 18.00),
(14, 16, 28, 1, 2.00),
(15, 17, 27, 2, 28.00),
(16, 18, 27, 2, 28.00),
(17, 19, 27, 1, 28.00),
(18, 19, 30, 1, 18.00),
(19, 20, 30, 1, 18.00),
(20, 21, 30, 1, 18.00),
(21, 22, 30, 1, 18.00),
(22, 25, 2, 1, 47.00),
(23, 26, 2, 1, 47.00),
(24, 26, 27, 1, 28.00),
(25, 26, 28, 1, 2.00),
(26, 26, 30, 1, 18.00),
(27, 27, 2, 1, 47.00),
(28, 28, 30, 1, 18.00),
(29, 29, 2, 1, 47.00),
(30, 30, 2, 1, 47.00),
(31, 31, 2, 1, 47.00),
(32, 32, 39, 1, 8.00),
(33, 33, 38, 1, 12.00),
(34, 34, 38, 3, 12.00),
(35, 34, 40, 1, 25.00),
(36, 35, 38, 1, 12.00),
(37, 35, 28, 1, 10.00),
(38, 35, 30, 1, 18.00),
(39, 35, 27, 1, 28.00),
(40, 36, 43, 3, 10.00),
(41, 36, 44, 2, 11.00),
(42, 36, 41, 3, 35.00),
(43, 36, 40, 3, 25.00),
(44, 37, 41, 1, 35.00),
(45, 37, 47, 1, 15.00),
(46, 37, 46, 3, 15.00),
(47, 38, 28, 2, 10.00),
(48, 38, 38, 2, 12.00),
(49, 38, 27, 5, 28.00),
(50, 38, 30, 1, 18.00),
(51, 38, 46, 1, 15.00),
(52, 38, 44, 1, 11.00),
(53, 38, 43, 1, 10.00),
(54, 39, 40, 1, 25.00),
(55, 40, 42, 1, 21.00),
(56, 40, 45, 1, 15.00),
(57, 41, 43, 3, 10.00),
(58, 41, 40, 1, 25.00),
(59, 42, 30, 1, 18.00),
(60, 43, 30, 5, 18.00),
(61, 43, 40, 7, 25.00),
(62, 44, 27, 1, 28.00),
(63, 44, 31, 10, 180.00),
(64, 45, 31, 12, 200.00),
(65, 46, 31, 16, 200.00),
(66, 47, 44, 1, 11.00),
(67, 48, 44, 1, 11.00),
(68, 49, 49, 3, 45.00),
(69, 50, 44, 1, 11.00),
(70, 51, 40, 1, 25.00),
(71, 51, 41, 1, 35.00),
(72, 52, 30, 2, 18.00),
(73, 52, 50, 1, 75.00),
(74, 53, 53, 1, 5.00),
(75, 53, 27, 1, 28.00),
(76, 54, 40, 1, 25.00),
(77, 55, 40, 1, 25.00),
(78, 55, 41, 1, 35.00),
(79, 56, 30, 1, 18.00),
(80, 57, 38, 1, 12.00),
(81, 57, 28, 1, 10.00),
(82, 58, 30, 1, 18.00),
(83, 59, 43, 1, 10.00),
(84, 60, 43, 1, 10.00),
(85, 61, 30, 1, 18.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `log_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `change_type` enum('restock','sale','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `abbreviation` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `name`, `abbreviation`) VALUES
(2, 'Pieces', 'pcs'),
(3, 'Dozen', 'dz'),
(4, 'Bottle', 'btl'),
(5, 'Can', 'can'),
(6, 'Kilogram', 'kg'),
(7, 'Pack', 'pck'),
(8, 'Sachet', 'sach');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `credits`
--
ALTER TABLE `credits`
  ADD PRIMARY KEY (`credit_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `credit_payments`
--
ALTER TABLE `credit_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `credit_id` (`credit_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`sale_item_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `credits`
--
ALTER TABLE `credits`
  MODIFY `credit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_payments`
--
ALTER TABLE `credit_payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `sale_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `credits`
--
ALTER TABLE `credits`
  ADD CONSTRAINT `credits_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `credits_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`);

--
-- Constraints for table `credit_payments`
--
ALTER TABLE `credit_payments`
  ADD CONSTRAINT `credit_payments_ibfk_1` FOREIGN KEY (`credit_id`) REFERENCES `credits` (`credit_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `stock_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
