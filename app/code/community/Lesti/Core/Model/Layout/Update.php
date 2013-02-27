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
        $methods = array('addJs', 'addCss', 'addItem');
        foreach($xml->children() as $handle => $child){
            foreach($methods as $method) {
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