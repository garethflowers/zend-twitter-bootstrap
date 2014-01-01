<?php

/**
 * Alert Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Alert
 * @copyright Gareth Flowers
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
        $class = 'alert alert-dismissable';
        if (!empty($this->_type)) {
            $class .= ' alert-' . $this->_type;
        }

        $html = '<div class="' . $class . '">'
                . '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                . $this->_text
                . '</div>';

        return $html;
    }

    public function __toString() {
        return $this->render();
    }

}
