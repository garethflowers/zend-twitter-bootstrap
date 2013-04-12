<?php

/**
 * Alert Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Alert
 */
class Twitter_View_Helper_Alert extends Zend_View_Helper_Abstract {

    private $_type;
    private $_text;

    public function alert($type, $text = '') {
        $this->_type = $type;
        $this->_text = $text;
        return $this;
    }

    /**
     * Renders the Alert as an HTML string
     *
     * @return string
     */
    public function render() {
        $html = '<div class="alert alert-' . $this->_type . '">';
        $html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        $html .= $this->_text;
        $html .= '</div>';

        return $html;
    }

    public function __toString() {
        return $this->render();
    }

}
