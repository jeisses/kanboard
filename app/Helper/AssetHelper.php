<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

function isAbsolutePath($path) {
    if (!is_string($path)) {
        $mess = sprintf('String expected but was given %s', gettype($path));
        throw new \InvalidArgumentException($mess);
    }
    if (!ctype_print($path)) {
       $mess = 'Path can NOT have non-printable characters or be empty';
       throw new \DomainException($mess);
    }
    // Optional wrapper(s).
    $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';
    // Optional root prefix.
    $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';
    // Actual path.
    $regExp .= '(?<path>(?:[[:print:]]*))$%';
    $parts = [];
    if (!preg_match($regExp, $path, $parts)) {
        $mess = sprintf('Path is NOT valid, was given %s', $path);
        throw new \DomainException($mess);
    }
    if ('' !== $parts['root']) {
        return true;
    }
    return false;
}

/**
 * Asset Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class AssetHelper extends Base
{
    /**
     * Add a Javascript asset
     *
     * @param  string $filename Filename
     * @param  bool   $async
     * @return string
     */
    public function js($filename, $async = false)
    {
        if (isAbsolutePath($filename)) {
            $filepath = $filename;
	} else {
            $dir = dirname(__DIR__,2);
            $filepath = $dir.'/'.$filename;
        }

        return '<script '.($async ? 'async' : '').' defer type="text/javascript" src="'.$this->helper->url->dir().$filename.'?'.filemtime($filepath).'"></script>';
    }

    /**
     * Add a stylesheet asset
     *
     * @param  string   $filename   Filename
     * @param  boolean  $is_file    Add file timestamp
     * @param  string   $media      Media
     * @return string
     */
    public function css($filename, $is_file = true, $media = 'screen')
    {
        $dir = dirname(__DIR__,2);
        $filepath = $dir.'/'.$filename;
        return '<link rel="stylesheet" href="'.$this->helper->url->dir().$filename.($is_file ? '?'.filemtime($filepath) : '').'" media="'.$media.'">';
    }

    /**
     * Get custom css
     *
     * @access public
     * @return string
     */
    public function customCss()
    {
        if ($this->configModel->get('application_stylesheet')) {
            return '<style>'.$this->configModel->get('application_stylesheet').'</style>';
        }

        return '';
    }

    /**
     * Get CSS for task colors
     *
     * @access public
     * @return string
     */
    public function colorCss()
    {
        return '<style>'.$this->colorModel->getCss().'</style>';
    }
}
