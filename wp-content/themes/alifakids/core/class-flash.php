<?php

/**
 * Class Flash_Messages
 */
class Flash_Messages
{

    /**
     * construct session
     */
    public function __construct()
    {
        // create the session array if it doesn't already exist
        if(!isset($_SESSION['flash_message']))
        {
            $_SESSION['flash_message'] = array(
                'type' => null,
                'message' => null
            );
        }
    }

    /**
     * save flash message to session
     * @param $type
     * @param $message
     */
    public function add($type, $message)
    {
        $_SESSION['flash_message'] = array(
            'type' => $type,
            'message' => $message
        );
    }

    /**
     * recall flash message from session and display
     * @return string
     */
    public function show()
    {
        if(!is_null($_SESSION['flash_message']['type']))
        {
            $type = $_SESSION['flash_message']['type'];
            $message = $_SESSION['flash_message']['message'];
            unset($_SESSION['flash_message']); // unset flash_message key
            return '<div class="alert alert-'.$type.'" role="alert">
                <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span></button>'.$message.'</div>';
        } else {
            return false;
        }
    }

}