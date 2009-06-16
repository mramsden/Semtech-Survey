<?php
/**
 * This class contains a number of helper methods when rendering links in views.
 *
 * @author Marcus Ramsden <mmr@ecs.soton.ac.uk>
 * @package Semtech_View_Helper
 */
class Semtech_View_Helper_CreateHyperlink extends Zend_View_Helper_HtmlElement
{

    /**
     * This function takes the linktext parameter and the href parameter and
     * outputs an HTML link of the form;
     *
     * <a href="$href">$linktext</a>
     *
     * If you set the new window parameter to true then the link will use
     * javascript to open a new window. The HTML link outputted will take the
     * form;
     *
     * <a href="$href" onclick="javascript: window.open('$href'); return false;">$linktext</a>
     *
     * @param <string> $linktext
     * @param <string> $href
     * @param <boolean> $newwindow
     * @return <string>
     */
    public function CreateHyperlink($linktext, $href, $newwindow = false)
    {
        return '<a href="'.
                $href.'"'.
                $newwindow?'onclick="javascript: window.open(\''.$href.'\'); return false;"':''.
                '>'.$linktext.'</a>';
    }

}
?>
