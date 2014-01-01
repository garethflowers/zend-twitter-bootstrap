<?php

/**
 * Form
 *
 * @category Twitter
 * @package Twitter_Form
 * @copyright Gareth Flowers
 */
class Twitter_Form extends Zend_Form {

    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_INLINE = 'inline';

    /**
     * Form Type
     * @var string
     */
    private $_type;

    public function __construct($options = null) {
        $this->setName(strtolower(get_called_class()));
        $this->setAttrib('role', 'form');

        $iframeid = $this->getName() . '_results';
        $this->setAttrib('target', $iframeid);

        $decorator = array('FormElements');
        $decorator[] = array(array('Results-Frame' => 'HtmlTag'),
            array('tag' => 'iframe', 'id' => $iframeid, 'class' => 'hidden', 'placement' => 'append'));
        $decorator[] = 'Form';
        $this->setDecorators($decorator);

        parent::__construct($options);
    }

    /**
     * Set the Form Type to Horizontal
     * @param bool $flag
     * @return Twitter_Form
     */
    public function setFormTypeHorizontal($flag = true) {
        $this->_type = (bool) $flag ? self::TYPE_HORIZONTAL : null;
        return $this;
    }

    /**
     * Set the Form Type to Inline
     * @param bool $flag
     * @return Twitter_Form
     */
    public function setFormTypeInline($flag = true) {
        $this->_type = (bool) $flag ? self::TYPE_INLINE : null;
        return $this;
    }

    /**
     * Render
     * @param  Zend_View_Interface $view
     * @return Zend_View
     */
    public function render(Zend_View_Interface $view = null) {
        if (!empty($this->_type)) {
            $this->setAttrib('class', trim('form-' . $this->_type . ' ' . $this->getAttrib('class')));
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

        if ($name != null && $name !== 'form-group-actions') {
            $displayGroup = $this->getDisplayGroup($name);

            if ($displayGroup instanceof Zend_Form_DisplayGroup) {
                $displayGroup->removeDecorator('DtDdWrapper');
                $displayGroup->removeDecorator('HtmlTag');
            }
        }

        return $this;
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
        $element->setDisableLoadDefaultDecorators(true);

        parent::addElement($element, $name, $options);

        if (!$element instanceof Zend_Form_Element && $name !== null) {
            $element = $this->getElement($name);
        } else {
            $element->setDecorators($this->_getDefaultElementDecorator($element));
        }

        if ($element instanceof Zend_Form_Element_File) {
            $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);

            if (!$element->getDescription()) {
                $element->setDescription('Add File...');
            }
        }

        if ($element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Reset || $element instanceof Zend_Form_Element_Button) {
            if (!$element instanceof Zend_Form_Element_Reset && !$element instanceof Zend_Form_Element_Button) {
                $class = 'btn-primary';
            } else if ($element instanceof Zend_Form_Element_Button && $element->getAttrib('type') === 'submit') {
                $class = 'btn-primary';
            } else {
                $class = 'btn-default';
            }

            $element->setAttrib('class', trim('btn ' . $class . ' ' . $element->getAttrib('class')));
            $this->_addActionsDisplayGroupElement($element);
        } else {
            $element->setAttrib('class', trim('form-control ' . $element->getAttrib('class')));
        }

        if ($element instanceof Zend_Form_Element_Radio || $element instanceof Zend_Form_Element_MultiCheckbox) {
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
        }

        if ($element instanceof Zend_Form_Element_Hidden) {
            $element->setDecorators(array('ViewHelper'));
        }

        if ($element instanceof Zend_Form_Element_Textarea && !$element->getAttrib('rows')) {
            $element->setAttrib('rows', 3);
        }

        if ($element->isRequired()) {
            $element->setAttrib('required', 'required');
        }

        return $this;
    }

    /**
     * 
     * @param Zend_Form_Element $element
     * @return string[]
     */
    private function _getDefaultElementDecorator(Zend_Form_Element $element) {
        $decorator = array();

        if ($element instanceof Zend_Form_Element_Checkbox) {
            $decorator[] = array(array('Label-Open' => 'HtmlTag'), array('tag' => 'label', 'class' => 'checkbox', 'id' => $element->getId() . '-label', 'for' => $element->getName(), 'openOnly' => true));
            $decorator[] = 'ViewHelper';
            $decorator[] = array('CheckBoxLabel');
            $decorator[] = array(array('Label-Closing' => 'HtmlTag'), array('tag' => 'label', 'closeOnly' => true));
            $decorator[] = array('Errors', array('placement' => 'append'));
            $decorator[] = array('Description', array('tag' => 'span', 'class' => 'help-block'));
            $decorator[] = array(array('Inner-Wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-10'));
            $decorator[] = array(array('Outer-Wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-group'));
            return $decorator;
        }

        if ($element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Reset || $element instanceof Zend_Form_Element_Button) {
            $decorator[] = 'ViewHelper';
            return $decorator;
        }

        if ($element instanceof Zend_Form_Element_File) {
            $decorator[] = 'File';
            $decorator[] = 'Errors';
            $decorator[] = array('Description', array('tag' => 'span', 'class' => 'input-file', 'placement' => 'prepend'));
            $decorator[] = array(array('File-Wrapper' => 'HtmlTag'), array('tag' => 'span', 'class' => 'btn btn-default'));
        } else {
            $decorator[] = 'ViewHelper';
            $decorator[] = 'Errors';
            $decorator[] = array('Description', array('tag' => 'span', 'class' => 'help-block'));
        }

        if ($this->_type === self::TYPE_HORIZONTAL) {
            $decorator[] = array(array('Inner-Wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-10'));
            $decorator[] = array('Label', array('class' => 'control-label col-sm-2'));
            $decorator[] = array(array('Outer-Wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-group'));
        } else {
            $decorator[] = 'Label';
            $decorator[] = array('HtmlTag', array('tag' => 'div', 'class' => 'form-group'));
        }

        return $decorator;
    }

    /**
     * 
     * @param Zend_Form_Element_Submit $element
     * @return type
     */
    private function _addActionsDisplayGroupElement(Zend_Form_Element_Submit $element) {
        $displayGroup = $this->getDisplayGroup('form-group-actions');

        if (!$displayGroup instanceof Zend_Form_DisplayGroup) {
            $decorator = array('FormElements');

            if ($this->_type === self::TYPE_HORIZONTAL) {
                $decorator[] = array(array('Inner-Wrapper' => 'HtmlTag'),
                    array('tag' => 'div', 'class' => 'col-sm-offset-2 col-sm-10'));
                $decorator[] = array(array('Outer-Wrapper' => 'HtmlTag'),
                    array('tag' => 'div', 'class' => 'form-group'));
            }

            $displayGroup = $this->addDisplayGroup(array($element), 'form-group-actions', array('decorators' => $decorator));
        } else {
            $displayGroup->addElement($element);
        }

        return $displayGroup;
    }

}
