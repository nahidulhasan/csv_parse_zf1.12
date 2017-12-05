<?php

class Application_Form_Fileupload extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');

        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Your email address:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));

        // Add the file element
        $this->addElement('file', 'file', array(
            'label'       => 'Please Upload File:',
            'required'    => true,
            'MaxFileSize' => 2097152,
            'validators'  => array(
                array('Count', false, 1),
                array('Size', false, 2097152),
                array('Extension', false, 'csv,pdf,txt'),
                #array('Exists', false, '\upload')
            )
        ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
        ));

        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
    }
}

