<?php

/**
 * Breadcrumb Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Breadcrumb
 * @copyright Gareth Flowers
 */
class Twitter_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract {

    private $_active;
    private $_links;
    private $_class;

    public function breadcrumb($active = '', array $links = array(), $class = '') {
        $this->setActive($active);
        $this->setLinks($links);
        $this->setClass($class);

        return $this;
    }

    public function setActive($active) {
        $this->_active = (string) $active;
        return $this;
    }

    public function addLink($link, $title) {
        $this->_links[$link] = $title;
        return $this;
    }

    public function setLinks(array $links) {
        $this->_links = $links;
        return $this;
    }

    public function setClass($class) {
        $this->_class = (string) $class;
        return $this;
    }

    public function __toString() {
        $html = '<ul class="breadcrumb ' . $this->_class . '">';
        foreach ($this->_links as $link => $title) {
            $html .= '<li>';
            $html .= $this->view->urlLink($link, $title);
            $html .= '<span class="divider">/</span>';
            $html .= '</li>';
        }
        $html .= '<li class="active">';
        $html .= $this->_active;
        $html .= '</li>';
        $html .= '</ul>';

        return $html;
    }

}
