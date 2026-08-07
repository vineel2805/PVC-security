<?php
/**
 * hero_slides_schema.php
 * Ensures hero_slides table exists and populates initial default slides if empty.
 */

function ensure_hero_slides_table_exists($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `hero_slides` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) DEFAULT NULL,
            `subtitle` VARCHAR(255) DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `desktop_image` VARCHAR(255) NOT NULL,
            `mobile_image` VARCHAR(255) DEFAULT NULL,
            `button_text` VARCHAR(100) DEFAULT NULL,
            `button_link` VARCHAR(255) DEFAULT NULL,
            `display_order` INT NOT NULL DEFAULT 0,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $pdo->exec($sql);

        // Check if table is empty
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `hero_slides`")->fetchColumn();
        if ($count === 0) {
            $stmt = $pdo->prepare("INSERT INTO `hero_slides` 
                (`title`, `subtitle`, `description`, `desktop_image`, `mobile_image`, `button_text`, `button_link`, `display_order`, `status`) 
                VALUES 
                (:title, :subtitle, :description, :desktop_image, :mobile_image, :button_text, :button_link, :display_order, :status)");

            $defaultSlides = [
                [
                    'title' => '',
                    'subtitle' => '',
                    'description' => '',
                    'desktop_image' => 'assets/img/carousel/1.svg',
                    'mobile_image' => 'assets/img/carousel/1.svg',
                    'button_text' => '',
                    'button_link' => '',
                    'display_order' => 1,
                    'status' => 1
                ],
                [
                    'title' => '',
                    'subtitle' => '',
                    'description' => '',
                    'desktop_image' => 'assets/img/carousel/2 copy.svg',
                    'mobile_image' => 'assets/img/carousel/2.svg',
                    'button_text' => '',
                    'button_link' => '',
                    'display_order' => 2,
                    'status' => 1
                ],
                [
                    'title' => '',
                    'subtitle' => '',
                    'description' => '',
                    'desktop_image' => 'assets/img/carousel/3 copy.svg',
                    'mobile_image' => 'assets/img/carousel/3 copy 2.svg',
                    'button_text' => '',
                    'button_link' => '',
                    'display_order' => 3,
                    'status' => 1
                ]
            ];

            foreach ($defaultSlides as $slide) {
                $stmt->execute($slide);
            }
        }
    } catch (Exception $e) {
        error_log("Hero slides schema setup error: " . $e->getMessage());
    }
}
