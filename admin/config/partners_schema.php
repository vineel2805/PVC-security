<?php
/**
 * partners_schema.php
 * Ensures strategic_partners table exists and populates initial default partners if empty.
 */

function ensure_strategic_partners_table_exists($pdo) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `strategic_partners` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `partner_name` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `display_order` INT NOT NULL DEFAULT 0,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $pdo->exec($sql);

        // Check if table is empty
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `strategic_partners`")->fetchColumn();
        if ($count === 0) {
            $stmt = $pdo->prepare("INSERT INTO `strategic_partners` 
                (`partner_name`, `image`, `display_order`, `is_active`) 
                VALUES 
                (:partner_name, :image, :display_order, :is_active)");

            $defaultPartners = [
                ['partner_name' => 'Hikvision', 'image' => 'assets/img/brands/hikvision.png', 'display_order' => 1, 'is_active' => 1],
                ['partner_name' => 'Dahua', 'image' => 'assets/img/brands/dahua.png', 'display_order' => 2, 'is_active' => 1],
                ['partner_name' => 'Securus', 'image' => 'assets/img/brands/securus.png', 'display_order' => 3, 'is_active' => 1],
                ['partner_name' => 'CP Plus', 'image' => 'assets/img/brands/cpplusworld.png', 'display_order' => 4, 'is_active' => 1],
                ['partner_name' => 'Secureye', 'image' => 'assets/img/brands/Secureye.png', 'display_order' => 5, 'is_active' => 1],
                ['partner_name' => 'TP-Link', 'image' => 'assets/img/brands/tplink.png', 'display_order' => 6, 'is_active' => 1],
                ['partner_name' => 'D-Link', 'image' => 'assets/img/brands/dlink.png', 'display_order' => 7, 'is_active' => 1],
                ['partner_name' => 'Prama', 'image' => 'assets/img/brands/Prama.png', 'display_order' => 8, 'is_active' => 1],
                ['partner_name' => 'Dada', 'image' => 'assets/img/brands/dada.png', 'display_order' => 9, 'is_active' => 1],
                ['partner_name' => 'Yadon', 'image' => 'assets/img/brands/yadon.png', 'display_order' => 10, 'is_active' => 1],
                ['partner_name' => 'Seagate', 'image' => 'assets/img/brands/Seagate.png', 'display_order' => 11, 'is_active' => 1],
                ['partner_name' => 'Western Digital', 'image' => 'assets/img/brands/Westerndigital.png', 'display_order' => 12, 'is_active' => 1],
                ['partner_name' => 'Toshiba', 'image' => 'assets/img/brands/Toshiba.png', 'display_order' => 13, 'is_active' => 1],
                ['partner_name' => 'ERD', 'image' => 'assets/img/brands/erd.png', 'display_order' => 14, 'is_active' => 1],
            ];

            foreach ($defaultPartners as $partner) {
                $stmt->execute($partner);
            }
        }
    } catch (Exception $e) {
        error_log("Strategic partners schema setup error: " . $e->getMessage());
    }
}
