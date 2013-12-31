<?php

/**
 * Progress Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Progress
 */
class Twitter_View_Helper_Progress extends Zend_View_Helper_Abstract {

    /**
     *
     * @var int 
     */
    private $_value;

    /**
     * 
     * @param type $value
     * @return Twitter_View_Helper_Progress
     */
    public function progress($value = 0) {
        $this->setValue($value);
        return $this;
    }

    /**
     * 
     * @param type $value
     */
    public function setValue($value) {
        $this->_value = (int) $value;

        if ($this->_value > 100) {
            $this->_value = 100;
        } elseif ($this->_value < 0) {
            $this->_value = 0;
        }
    }

    /**
     * Renders the Progress as an HTML string
     *
     * @return html
     */
    public function render() {
        if ($this->_value >= 90) {
            $type = 'success';
        } elseif ($this->_value >= 50) {
            $type = 'warning';
        } else {
            $type = 'danger';
        }

        $html = '<div class="progress">'
                . '<div class="progress-bar progress-bar-' . $type . '"'
                . ' role="progressbar" aria-valuenow="' . $this->_value . '"'
                . ' aria-valuemin="0" aria-valuemax="100"'
                . ' style="width:' . $this->_value . '%">'
                . '<span class="sr-only">' . $this->_value . '% Complete</span>'
                . '</div>'
                . '</div>';

        return $html;
    }

    /**
     * 
     * @return html
     */
    public function __toString() {
        return $this->render();
    }

}
