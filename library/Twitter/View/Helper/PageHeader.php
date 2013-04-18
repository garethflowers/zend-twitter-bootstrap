<?php

/**
 * Page Header Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_PageHeader
 */
class Twitter_View_Helper_PageHeader extends Zend_View_Helper_Abstract {

    private $_title;
    private $_subText;

    public function pageHeader($title, $subText = '') {
        $this->_title = $title;
        $this->_subText = $subText;
        return $this;
    }

    /**
     * Renders the Page Header as an HTML string
     *
     * @return string
     */
    public function render() {
        $html = '<div class="page-header"><h1>';
        $html .= $this->_title;
        if (strlen($this->_subText)) {
            $html .= '<small' . $this->_subText . '</small>';
        }
        $html .= '<h1></div>';

        return $html;
    }

    public function __toString() {
        return $this->render();
    }

}
