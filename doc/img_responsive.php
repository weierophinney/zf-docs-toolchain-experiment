<?php // @codingStandardsIgnoreFile
/**
 * Swaps the generated HTML for hand-crafted HTML in the landing page.
 *
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

$rdi = new RecursiveDirectoryIterator(__DIR__ . '/html');
$rii = new RecursiveIteratorIterator($rdi);
$files = new HtmlFilter($rii);

$process = function () use ($files) {
    $file = $files->current()->getRealPath();
    $html = file_get_contents($file);
    if (! preg_match('#\<p\>\<img alt\="[^"]*" src\="[^"]+" \/\>\<\/p\>#s', $html)) {;
        return;
    }
    $html = preg_replace(
        '#(\<p\>\<img alt\="[^"]*" src\="[^"]+" )(\/\>\<\/p\>)#s',
        '$1class="img-responsive"$2',
        $html
    );
    file_put_contents($file, $html);
};

iterator_apply($files, $process);

class HtmlFilter extends FilterIterator
{
    public function accept()
    {
        $file = $this->getInnerIterator()->current();

        if (! $file instanceof SplFileInfo) {
            return false;
        }

        if (! $file->isFile()) {
            return false;
        }

        if ($file->getBasename('.html') === $file->getBasename()) {
            return false;
        }

        return true;
    }
}
