<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.02.13
 * Time: 09:35
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Core_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{

    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        $xml = parent::getFileLayoutUpdatesXml($area, $package, $theme, $storeId);
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $methods = array();
        if($shouldMergeJs) {
            $methods[] = 'addJs';
        }
        if($shouldMergeCss) {
            $methods[] = 'addCss';
        }
        if($shouldMergeJs || $shouldMergeCss) {
            $methods[] = 'addItem';
        }
        foreach($methods as $method) {
            foreach($xml->children() as $handle => $child){
                $items = $child->xpath(".//action[@method='".$method."']");
                foreach($items as $item) {
                    $params = $item->xpath("params");
                    if(count($params)) {
                        foreach($params as $param){
                            $param->{0} = (string)$param . ' handle="' . $handle . '"';
                        }
                    } else {
                        $item->addChild('params', 'handle="'.$handle.'"');
                    }
                }
            }
        }
        return $xml;
    }

}