<?php
/*
Plugin Name: Disk Space Dashboard Widget
Description: Показывает свободное место на диске в Dashboard для супер-администраторов сети
Version: 1.0
Author: Andrew Arutunyan & Grok
Network: True
*/

// Регистрируем виджет
add_action('wp_network_dashboard_setup', 'dsm_register_dashboard_widget');
function dsm_register_dashboard_widget() {
    if (is_super_admin()) {
        wp_add_dashboard_widget(
            'disk_space_widget',
            'Монитор дискового пространства',
            'dsm_display_widget'
        );
    }
}

// Функция отображения виджета
function dsm_display_widget() {
    // Получаем информацию о дисковом пространстве
    $free_space = disk_free_space(ABSPATH);
    $total_space = disk_total_space(ABSPATH);
    $used_space = $total_space - $free_space;
    
    // Переводим в гигабайты
    $free_space_gb = round($free_space / 1024 / 1024 / 1024, 2);
    $total_space_gb = round($total_space / 1024 / 1024 / 1024, 2);
    $used_space_gb = round($used_space / 1024 / 1024 / 1024, 2);
    $percent_used = round(($used_space / $total_space) * 100, 1);

    // Форматируем числа с разделителями тысяч
    $free_space_formatted = number_format($free_space_gb, 2, '.', ',');
    $total_space_formatted = number_format($total_space_gb, 2, '.', ',');
    $used_space_formatted = number_format($used_space_gb, 2, '.', ',');

    ?>
    <div class="dsm-widget-content">
        <div class="dsm-row">
            <span class="dsm-label">Всего места:</span>
            <span class="dsm-value"><?php echo $total_space_formatted; ?> GB</span>
        </div>
        <div class="dsm-row">
            <span class="dsm-label">Использовано:</span>
            <span class="dsm-value"><?php echo $used_space_formatted; ?> GB</span>
        </div>
        <div class="dsm-row">
            <span class="dsm-label">Свободно:</span>
            <span class="dsm-value"><?php echo $free_space_formatted; ?> GB</span>
        </div>
        <div class="dsm-row">
            <span class="dsm-label">Процент использования:</span>
            <span class="dsm-value"><?php echo $percent_used; ?>%</span>
        </div>
        
        <div class="dsm-progress">
            <div class="dsm-progress-bar" style="width: <?php echo $percent_used; ?>%;"></div>
        </div>
    </div>
    <?php
}

// Добавляем стили
add_action('admin_head', 'dsm_add_styles');
function dsm_add_styles() {
    global $current_screen;
    if ($current_screen->id === 'dashboard-network') {
        echo '<style>
            #disk_space_widget .inside {
                margin: 0;
                padding: 0;
            }
            .dsm-widget-content {
                padding: 12px;
            }
            .dsm-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
            }
            .dsm-label {
                text-align: right;
                font-weight: bold;
                margin-right: 15px;
            }
            .dsm-value {
                text-align: left;
            }
            .dsm-progress {
                background: #eee;
                height: 20px;
                width: 100%;
                margin: 10px 0 0 0;
                border: 1px solid #0073aa;
            }
            .dsm-progress-bar {
                height: 20px;
                background: #0073aa;
                background-image: linear-gradient(
                    45deg,
                    rgba(255, 255, 255, 0.2) 25%,
                    transparent 25%,
                    transparent 50%,
                    rgba(255, 255, 255, 0.2) 50%,
                    rgba(255, 255, 255, 0.2) 75%,
                    transparent 75%,
                    transparent
                );
                background-size: 20px 20px;
            }
        </style>';
    }
}