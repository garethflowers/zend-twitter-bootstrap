<?php

/**
 * Button Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Button
 */
class Twitter_View_Helper_Button extends Zend_View_Helper_Abstract {

    private $_content;
    private $_link;
    private $_type;

    public function button($content) {
        $this->_content = $content;

        return $this;
    }

    public function setLink($link) {
        $this->_link = (string) $link;

        return $this;
    }

    public function setType($type) {
        $this->_type = (string) $type;

        return $this;
    }

    /**
     * Renders the Button as an HTML string
     *
     * @return string
     */
    public function render() {
        $html = '<button type="button"';
        if (strlen($this->_link) > 0) {
            $html .= ' onclick="window.location=\'' . $this->_link . '\'"';
        }
        $html .= ' class="btn';
        if (strlen($this->_type) > 0) {
            $html .= ' btn-' . $this->_type;
        }
        $html .= '">';
        $html .= $this->_content;
        $html .= '</button>';

        return $html;
    }

    public function __toString() {
        return $this->render();
    }

}
