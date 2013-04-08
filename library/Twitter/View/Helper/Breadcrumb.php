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

    /**
     * Active Title
     *
     * @var string
     */
    private $_active;

    /**
     * Links
     *
     * @var array
     */
    private $_links;

    /**
     * HTML Class
     *
     * @var string
     */
    private $_class;

    /**
     * Creates a new Breadcrumb instance
     *
     * @param string $active
     * @param array $links
     * @param string $class
     * @return Twitter_View_Helper_Breadcrumb
     */
    public function breadcrumb($active = '', array $links = array(), $class = '') {
        $this->setActive($active);
        $this->setLinks($links);
        $this->setClass($class);

        return $this;
    }

    /**
     * Sets the title on the Active item
     *
     * @param string $active
     * @return Twitter_View_Helper_Breadcrumb
     */
    public function setActive($active) {
        $this->_active = (string) $active;
        return $this;
    }

    /**
     * Adds a Link
     *
     * @param string $link
     * @param string $title
     * @return Twitter_View_Helper_Breadcrumb
     */
    public function addLink($link, $title) {
        $this->_links[$link] = $title;
        return $this;
    }

    /**
     * Sets the links used
     *
     * @param array $links
     * @return Twitter_View_Helper_Breadcrumb
     */
    public function setLinks(array $links) {
        $this->_links = $links;
        return $this;
    }

    /**
     * Sets the HTML Class for the Breadcrumb
     *
     * @param string $class
     * @return Twitter_View_Helper_Breadcrumb
     */
    public function setClass($class) {
        $this->_class = (string) $class;
        return $this;
    }

    /**
     * Renders the Breadcrumb as an HTML string
     *
     * @return string
     */
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
