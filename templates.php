<?php
// Sayfa şablonları

// 1. Ana içerik kart yapısı
function contentCardStart($title = '') {
    $html = '<div class="content-card card-glow">';
    if (!empty($title)) {
        $html .= '<h2 class="search-title">' . $title . '</h2>';
    }
    return $html;
}

function contentCardEnd() {
    return '</div>';
}

// 2. Form grupları
function formGroupStart() {
    return '<div class="form-group">';
}

function formGroupEnd() {
    return '</div>';
}

// 3. Uyarı mesajları
function alertMessage($message, $type = 'info') {
    return '<div class="alert alert-' . $type . '">' . $message . '</div>';
}

// 4. Buton
function button($text, $icon = '', $type = 'submit', $class = '') {
    $iconHtml = !empty($icon) ? '<i class="fas fa-' . $icon . '"></i> ' : '';
    return '<button type="' . $type . '" class="btn ' . $class . '">' . $iconHtml . $text . '</button>';
}

// 5. Bağlantı butonu
function linkButton($url, $text, $icon = '', $class = '') {
    $iconHtml = !empty($icon) ? '<i class="fas fa-' . $icon . '"></i> ' : '';
    return '<a href="' . $url . '" class="btn ' . $class . '">' . $iconHtml . $text . '</a>';
}

// 6. Form grid başlangıcı
function formGridStart() {
    return '<div class="form-grid">';
}

// 7. Form grid bitişi
function formGridEnd() {
    return '</div>';
}

// 8. Input alanı
function inputField($name, $label, $type = 'text', $placeholder = '', $value = '', $required = false) {
    $req = $required ? ' required' : '';
    $html = '<label for="' . $name . '">' . $label . ':</label>';
    $html .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" placeholder="' . $placeholder . '" value="' . $value . '"' . $req . '>';
    return $html;
}

// 9. Seçim alanı
function selectField($name, $label, $options = [], $selected = '', $required = false) {
    $req = $required ? ' required' : '';
    $html = '<label for="' . $name . '">' . $label . ':</label>';
    $html .= '<select id="' . $name . '" name="' . $name . '"' . $req . '>';
    
    foreach ($options as $value => $text) {
        $sel = ($selected == $value) ? ' selected' : '';
        $html .= '<option value="' . $value . '"' . $sel . '>' . $text . '</option>';
    }
    
    $html .= '</select>';
    return $html;
}

// 10. Textarea alanı
function textareaField($name, $label, $placeholder = '', $value = '', $required = false) {
    $req = $required ? ' required' : '';
    $html = '<label for="' . $name . '">' . $label . ':</label>';
    $html .= '<textarea id="' . $name . '" name="' . $name . '" placeholder="' . $placeholder . '"' . $req . '>' . $value . '</textarea>';
    return $html;
}

// 11. Tablo başlangıcı
function tableStart($headers = []) {
    $html = '<table>';
    if (!empty($headers)) {
        $html .= '<thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr></thead>';
    }
    $html .= '<tbody>';
    return $html;
}

// 12. Tablo sonu
function tableEnd() {
    return '</tbody></table>';
}

// 13. Tablo satırı
function tableRow($cells = []) {
    $html = '<tr>';
    foreach ($cells as $cell) {
        $html .= '<td>' . $cell . '</td>';
    }
    $html .= '</tr>';
    return $html;
}
?> 