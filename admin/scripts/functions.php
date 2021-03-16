<?php

function redirect_to($location = null)
{
    if (null != $location) {
        header('Location: ' . $location);
        exit;
    }
}
