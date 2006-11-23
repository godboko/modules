<?php

/**
 * Load an image object for further manipulation
 *
 * @param   integer $fileId        The (uploads) file id of the image to load, or
 * @param   string  $fileLocation  The file location of the image to load
 * @param   string  $thumbsdir     (optional) The directory where derivative images are stored
 * @returns object
 * @return an Image_GD (or other) object
 */
function & images_userapi_load_image( $args ) 
{
    extract($args);

    if (empty($fileId) && empty($fileLocation)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'", 
                      '', 'load_image', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    } elseif (!empty($fileId) && !is_string($fileId)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'", 
                      'fileId', 'load_image', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    } elseif (!empty($fileLocation) && !is_string($fileLocation)) {
        $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'", 
                      'fileLocation', 'load_image', 'images');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
        return;
    }

    // if both arguments are specified, give priority to fileId
    if (!empty($fileId) && is_numeric($fileId)) {
        // if we only get the fileId
        if (empty($fileLocation) || !isset($storeType)) {
            $fileInfoArray = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileId));
            $fileInfo = end($fileInfoArray );
            if (empty($fileInfo)) {
                return NULL;
            }
            if (!empty($fileInfo['fileLocation']) && file_exists($fileInfo['fileLocation'])) {
                // pass the file location to Image_Properties
                $location = $fileInfo['fileLocation'];
            } elseif (defined('_UPLOADS_STORE_DB_DATA') && ($fileInfo['storeType'] & _UPLOADS_STORE_DB_DATA)) {
                // pass the file info array to Image_Properties
                $location = $fileInfo;
            }

        // if we get the whole file info
        } elseif (file_exists($fileLocation)) {
            $location = $fileLocation;

        } elseif (defined('_UPLOADS_STORE_DB_DATA') && ($storeType & _UPLOADS_STORE_DB_DATA)) {
            // pass the whole array to Image_Properties
            $location = $args;

        } else {
            $mesg = xarML("Invalid parameter '#(1)' to API function '#(2)' in module '#(3)'", 
                          'fileLocation', 'load_image', 'images');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($mesg));
            return;
        }

    } else {
        $location = $fileLocation;
    }

    if (empty($thumbsdir)) {
        $thumbsdir = xarModGetVar('images', 'path.derivative-store');
    }

    include_once('modules/images/xarclass/image_properties.php');
     
    switch(xarModGetVar('images', 'type.graphics-library')) {
        case _IMAGES_LIBRARY_IMAGEMAGICK:
            include_once('modules/images/xarclass/image_ImageMagick.php');
            $newImage = new Image_ImageMagick($location, $thumbsdir);
            return $newImage;
            break;
        case _IMAGES_LIBRARY_NETPBM:
            include_once('modules/images/xarclass/image_NetPBM.php');
            $newImage = new Image_NetPBM($location, $thumbsdir);
            return $newImage;
            break;
        default:
        case _IMAGES_LIBRARY_GD:
            include_once('modules/images/xarclass/image_gd.php');
            $newImage = new Image_GD($location, $thumbsdir);
            return $newImage;
            break;
    }
}

?>