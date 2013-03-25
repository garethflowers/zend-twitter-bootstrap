<?php

/**
 * Form
 *
 * @category Twitter
 * @package Twitter_Form
 * @copyright Gareth Flowers
 */
class Twitter_Form extends Zend_Form {

    public function __construct($options = null) {
        // clear existing decorators
        $this->clearDecorators();

        // Decorators for the form itself
        $this->setDecorators(array('FormElements'));

        // Decorators for all the form elements
        $this->setElementDecorators($this->_getElementDecorators());

        parent::__construct($options);
    }

    /**
     * Render
     * @param  Zend_View_Interface $view
     * @return Zend_View
     */
    public function render(Zend_View_Interface $view = null) {
        $formTypes = array(
            'horizontal',
            'inline',
            'vertical',
            'search'
        );

        $set = false;

        foreach ($formTypes as $type) {
            if ($this->getAttrib($type)) {
                $this->removeAttrib($type);
                $this->addDecorator('Form', array('class' => 'form-' . $type));
                $set = true;
            }
        }

        if ($set !== true) {
            $this->addDecorator('Form', array('class' => 'form-horizontal'));
        }

        return parent::render($view);
    }

    /**
     * @see Zend_Form::addDisplayGroup
     *
     * @param array $elements
     * @param string $name
     * @param array|Zend_Config $options
     */
    public function addDisplayGroup(array $elements, $name = null, $options = null) {
        parent::addDisplayGroup($elements, $name, $options);

        if ($name != null && $name !== 'zfBootstrapFormActions') {
            $displayGroup = $this->getDisplayGroup($name);

            if ($displayGroup instanceof Zend_Form_DisplayGroup) {
                $displayGroup->removeDecorator('DtDdWrapper');
                $displayGroup->removeDecorator('HtmlTag');
            }
        }
    }

    /**
     * @see Zend_Form::addElement
     *
     * We have to override this, because we have to set some special decorators
     * on a per-element basis (checkboxes and submit buttons have different
     * decorators than other elements)
     *
     * @param string|Zend_Form_Element $element
     * @param string $name
     * @param array|Zend_Config $options
     */
    public function addElement($element, $name = null, $options = null) {
        parent::addElement($element, $name, $options);

        if (!$element instanceof Zend_Form_Element && $name != null) {
            $element = $this->getElement($name);
        } else {
            $element->clearDecorators();
            $element->setDecorators($this->_getElementDecorators());
        }

        if ($element instanceof Zend_Form_Element_File) {
            $decorators = $this->_getElementDecorators();
            $decorators[0] = 'File';
            $element->setDecorators($decorators);
        }

        // Special style for Zend
        if ($element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Reset || $element instanceof Zend_Form_Element_Button) {
            $class = '';

            if ($element instanceof Zend_Form_Element_Submit && !$element instanceof Zend_Form_Element_Reset && !$element instanceof Zend_Form_Element_Button) {
                $class = 'btn-primary';
            }

            $element->setAttrib('class', trim('btn ' . $class . $element->getAttrib('class')));
            $element->removeDecorator('Label');
            $element->removeDecorator('outerwrapper');
            $element->removeDecorator('innerwrapper');

            $this->_addActionsDisplayGroupElement($element);
        }

        if ($element instanceof Zend_Form_Element_Checkbox) {
            $element->setDecorators(array(
                array(array('labelopening' => 'HtmlTag'), array('tag' => 'label', 'class' => 'checkbox', 'id' => $element->getId() . '-label', 'for' => $element->getName(), 'openOnly' => true)),
                'ViewHelper',
                array('Checkboxlabel'),
                array(array('labelclosing' => 'HtmlTag'), array('tag' => 'label', 'closeOnly' => true)),
                array('Errors', array('placement' => 'append')),
                array('Description', array('tag' => 'span', 'class' => 'help-block')),
                array(array('innerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array(array('outerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group'))
            ));
        }

        if ($element instanceof Zend_Form_Element_Radio ||
                $element instanceof Zend_Form_Element_MultiCheckbox) {
            $multiOptions = array();
            foreach ($element->getMultiOptions() as $value => $label) {
                $multiOptions[$value] = ' ' . $label;
            }

            $element->setMultiOptions($multiOptions);

            $element->setAttrib('labelclass', 'checkbox');

            if ($element->getAttrib('inline')) {
                $element->setAttrib('labelclass', 'checkbox inline');
            }

            if ($element instanceof Zend_Form_Element_Radio) {
                $element->setAttrib('labelclass', 'radio');
            }

            if ($element->getAttrib('inline')) {
                $element->setAttrib('labelclass', 'radio inline');
            }

            $element->setOptions(array('separator' => ''));
            $element->setDecorators(array(
                'ViewHelper',
                array('Errors', array('placement' => 'append')),
                array('Description', array('tag' => 'span', 'class' => 'help-block')),
                array(array('innerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array('Label', array('class' => 'control-label')),
                array(array('outerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group'))
            ));
        }

        if ($element instanceof Zend_Form_Element_Hidden) {
            $element->setDecorators(array('ViewHelper'));
        }

        if ($element instanceof Zend_Form_Element_Textarea &&
                !$element->getAttrib('rows')) {
            $element->setAttrib('rows', '3');
        }

        if ($element instanceof Zend_Form_Element_Captcha) {
            $element->removeDecorator('viewhelper');
        }

        return $this;
    }

    private function _getElementDecorators() {
        return array(
            'ViewHelper',
            array('Errors', array('placement' => 'append')),
            array('Description', array('tag' => 'span', 'class' => 'help-block')),
            array(array('innerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
            array('Label', array('class' => 'control-label')),
            array(array('outerwrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group'))
        );
    }

    private function _addActionsDisplayGroupElement($element) {
        $displayGroup = $this->getDisplayGroup('zfBootstrapFormActions');

        if ($displayGroup === null) {
            $displayGroup = $this->addDisplayGroup(
                    array($element), 'zfBootstrapFormActions', array('decorators' => array(
                    'FormElements', array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))))
            );
        } else {
            $displayGroup->addElement($element);
        }

        return $displayGroup;
    }

}
