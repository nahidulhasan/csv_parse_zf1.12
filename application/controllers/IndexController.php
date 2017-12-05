<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Load upload form
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $form    = new Application_Form_Fileupload();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {

                $upload = new Zend_File_Transfer_Adapter_Http();
                $upload->setDestination(APPLICATION_PATH.'/upload/');
               # $upload->addValidator('Exists', false, APPLICATION_PATH.'\upload');
                if (!$upload->isValid()) {
                    print "Validation failure";
                }

                try {
                    $upload->receive();
                    $files = $upload->getFileInfo();
                    $uploadedData = $form->getValues();
                    // Zend_Debug::dump($upload->getFileInfo());
                } catch (Zend_File_Transfer_Exception $e) {
                    echo $e->message();
                }

               $urlOptions = array('controller'=>'index', 'action'=>'result',
                   'email'=>$uploadedData['email'],'file'=>$files['file']['name']);
                return $this->_helper->redirector->gotoRoute($urlOptions);

            }
        }

        $this->view->fileUploadForm = $form;

    }


    /**
     * Upload a csv file and parsing it
     */
    public function resultAction() {

        $fileName = $this->_getParam('file');
        $email = $this->_getParam('email');

        $file = APPLICATION_PATH.'/upload/' .$fileName;
        $data = $this->csv_to_array($file);

        function sortByName($a, $b)
        {
            $a = $a['Firstname'];
            $b = $b['Firstname'];

            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        }

        usort($data, "sortByName");

        $this->view->data = $data;
        $this->view->email = $email;
    }

    /**
     * Convert csv data into array
     *
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
}

