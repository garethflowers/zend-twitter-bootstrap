<?php

/**
 * Breadcrumb Helper
 *
 * @category Twitter
 * @package Twitter_View
 * @subpackage Helper_Breadcrumb
 * @copyright Gareth Flowers
 */
class Twitter_View_Helper_Breadcrumb extends Zend_View_Helper_Navigation_Breadcrumbs {

    protected $_class;
    protected $_urlPrefix;

    public function breadcrumb(Zend_Navigation_Container $container = null) {
        $this->setSeparator('/');
        return parent::breadcrumbs($container);
    }

    public function setClass($class) {
        if (is_string($class)) {
            $this->_class = $class;
        }

        return $this;
    }

    public function setUrlPrefix($prefix) {
        if (is_string($prefix)) {
            $this->_urlPrefix = $prefix;
        }

        return $this;
    }

    public function renderStraight(Zend_Navigation_Container $container = null) {
        if (null === $container) {
            $container = $this->getContainer();
        }

        // find deepest active
        if (!$active = $this->findActive($container)) {
            return '';
        }

        $active = $active['page'];

        // put the deepest active page last in breadcrumbs
        if ($this->getLinkLast()) {
            $html = ' <li>' . $this->htmlify($active) . '</li>' . PHP_EOL;
        } else {
            $html = $active->getLabel();
            if ($this->getUseTranslator() && $t = $this->getTranslator()) {
                $html = $t->translate($html);
            }
            $html = ' <li class="active">' . $this->view->escape($html) .
                    '</li>' . PHP_EOL;
        }

        // walk back to root
        while (($parent = $active->getParent()) != null) {
            if ($parent instanceof Zend_Navigation_Page) {
                // prepend crumb to html
                $html = ' <li>' . $this->htmlify($parent) .
                        ' <span class="divider">' . $this->getSeparator() .
                        '</span></li>' . PHP_EOL . $html;
            }

            if ($parent === $container) {
                // at the root of the given container
                break;
            }

            $active = $parent;
        }

        return strlen($html) ? $this->getIndent() . '<ul class="breadcrumb ' .
                $this->_class . '">' . PHP_EOL . $html . '</ul>' . PHP_EOL : '';
    }

}
